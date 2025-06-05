<?php
/**
 * @package     VikRentCar
 * @subpackage  com_vikrentcar
 * @author      Custom
 * @license     GNU General Public License version 2 or later
 */

defined('ABSPATH') or die('No script kiddies please!');

JLoader::import('adapter.payment.payment');

/**
 * Stripe payment integration for VikRentCar.
 *
 * This implementation uses Stripe Checkout to process
 * credit card payments.
 */
class VikRentCarStripePayment extends JPayment
{
    /**
     * @override
     * Configuration parameters for the payment.
     *
     * @return array
     */
    protected function buildAdminParameters()
    {
        return [
            'publishable_key' => [
                'type'  => 'text',
                'label' => 'Publishable Key',
            ],
            'secret_key' => [
                'type'  => 'password',
                'label' => 'Secret Key',
            ],
        ];
    }

    /**
     * @override
     * Begins the payment transaction by creating a Checkout session
     * and rendering the redirect button.
     *
     * @return void
     */
    protected function beginTransaction()
    {
        $http      = new JHttp;
        $secretKey = $this->getParam('secret_key');

        // amount in the smallest currency unit
        $amount = round($this->get('total_to_pay') * 100);

        $payload = [
            'payment_method_types[]'                     => 'card',
            'line_items[0][price_data][currency]'       => $this->get('transaction_currency'),
            'line_items[0][price_data][product_data][name]' => $this->get('transaction_name'),
            'line_items[0][price_data][unit_amount]'    => $amount,
            'line_items[0][quantity]'                   => 1,
            'mode'                                      => 'payment',
            'success_url'                               => $this->get('notify_url') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'                                => $this->get('return_url'),
        ];

        $response = $http->post('https://api.stripe.com/v1/checkout/sessions', $payload, [
            'Authorization' => 'Bearer ' . $secretKey,
        ]);

        if ($response->code != 200 && $response->code != 201) {
            echo '<p>Unable to initialize payment.</p>';
            return;
        }

        $body = json_decode($response->body, true);
        $sessionId = $body['id'];

        $depositmess = '';
        if ($this->get('leave_deposit')) {
            $depositmess = '<p class="vrc-leave-deposit"><span>' . JText::_('VRLEAVEDEPOSIT') . '</span>' . $this->get('currency_symb') . ' ' . VikRentCar::numberFormat($this->get('total_to_pay')) . '</p><br/>';
        }

        $info = $this->get('payment_info');

        echo $depositmess;
        if (VRCPlatformDetection::isWordPress()) {
            echo wpautop($info['note']);
        } else {
            echo $info['note'];
        }

        ?>
        <button id="stripe-pay-button" class="button">Pay with Card</button>
        <script src="https://js.stripe.com/v3/"></script>
        <script>
        (function(){
            var stripe = Stripe('<?php echo addslashes($this->getParam('publishable_key')); ?>');
            document.getElementById('stripe-pay-button').addEventListener('click', function(){
                stripe.redirectToCheckout({ sessionId: '<?php echo $sessionId; ?>' });
            });
        })();
        </script>
        <?php
    }

    /**
     * @override
     * Validates the payment using the session ID returned by Stripe.
     *
     * @param JPaymentStatus $status
     *
     * @return boolean
     */
    protected function validateTransaction(JPaymentStatus &$status)
    {
        $sessionId = JFactory::getApplication()->input->getString('session_id');
        if (!$sessionId) {
            $status->appendLog('Missing session_id');
            return false;
        }

        $http      = new JHttp;
        $secretKey = $this->getParam('secret_key');
        $response  = $http->get('https://api.stripe.com/v1/checkout/sessions/' . $sessionId, [
            'Authorization' => 'Bearer ' . $secretKey,
        ]);

        if ($response->code != 200) {
            $status->appendLog('Stripe API error: ' . $response->body);
            return false;
        }

        $session = json_decode($response->body, true);
        if (!isset($session['payment_status']) || $session['payment_status'] !== 'paid') {
            $status->appendLog('Payment not verified.');
            return false;
        }

        $totPaid = isset($session['amount_total']) ? $session['amount_total'] / 100 : 0;
        $status->paid($totPaid);
        $status->verified();

        return true;
    }

    /**
     * @override
     * Finalises the payment process.
     *
     * @param boolean $res
     *
     * @return void
     */
    protected function complete($res)
    {
        $app    = JFactory::getApplication();
        $itemid = $this->getItemID();
        $url  = 'index.php?option=com_vikrentcar&view=order&sid=' . $this->get('sid') . '&ts=' . $this->get('ts') . (!empty($itemid) ? '&Itemid=' . $itemid : '');

        if ($res < 1) {
            $app->enqueueMessage(JText::_('VRPAYMENTNOTVER'), 'error');
        } else {
            $app->enqueueMessage(JText::_('VRTHANKSONE'));
        }

        $app->redirect(JRoute::_($url, false));
        $app->close();
    }

    /**
     * Returns the proper Item ID to use.
     *
     * @return integer
     */
    protected function getItemID()
    {
        $app    = JFactory::getApplication();
        $input  = $app->input;
        $itemid = $input->getInt('Itemid');

        if (!$itemid) {
            $model  = JModel::getInstance('vikrentcar', 'shortcodes', 'admin');
            $itemid = $model->all('post_id', true);
            if (count($itemid)) {
                $itemid = $itemid[0]->post_id;
            }
        }

        return (int) $itemid;
    }
}
