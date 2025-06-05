<?php
/**
 * @package     VikRentCar
 * @subpackage  com_vikrentcar
 * @author      Alessio Gaggii - e4j
 * @copyright   Copyright (C) 2018 e4j
 * @license     GNU General Public License version 2 or later
 * @link        https://vikwp.com
 */

defined('ABSPATH') or die('No script kiddies please!');

/**
 * Some special tags between curly brackets can be used to display certain values such as:
 * {logo}, {company_name}, {order_id}, {confirmnumb}, {order_status}, {order_date}, {customer_info}, {item_name},
 * {pickup_date}, {pickup_location}, {dropoff_date}, {dropoff_location}, {order_details}, {order_total},
 * {customfield 2}, {order_link}, {footer_emailtext}, {vrc_add_pdf_page}, etc.
 *
 * $order_details (array) and the $customer array can also be accessed as needed for more customization.
 */

// -------------------
// PDF Page Parameters
// -------------------
define('VRC_PAGE_PDF_PAGE_ORIENTATION', 'P'); // P=portrait, L=landscape
define('VRC_PAGE_PDF_UNIT', 'mm'); 
define('VRC_PAGE_PDF_PAGE_FORMAT', 'A4'); 
define('VRC_PAGE_PDF_MARGIN_LEFT', 10);
define('VRC_PAGE_PDF_MARGIN_TOP', 10);
define('VRC_PAGE_PDF_MARGIN_RIGHT', 10);
define('VRC_PAGE_PDF_MARGIN_HEADER', 1);
define('VRC_PAGE_PDF_MARGIN_FOOTER', 5);
define('VRC_PAGE_PDF_MARGIN_BOTTOM', 5);
define('VRC_PAGE_PDF_IMAGE_SCALE_RATIO', 1.25);

$page_params = array(
    'show_header' => 0, //0 = no header, 1 = show header
    'header_data' => array(),
    'show_footer' => 1,
    'pdf_page_orientation' => 'VRC_PAGE_PDF_PAGE_ORIENTATION',
    'pdf_unit' => 'VRC_PAGE_PDF_UNIT',
    'pdf_page_format' => 'VRC_PAGE_PDF_PAGE_FORMAT',
    'pdf_margin_left' => 'VRC_PAGE_PDF_MARGIN_LEFT',
    'pdf_margin_top' => 'VRC_PAGE_PDF_MARGIN_TOP',
    'pdf_margin_right' => 'VRC_PAGE_PDF_MARGIN_RIGHT',
    'pdf_margin_header' => 'VRC_PAGE_PDF_MARGIN_HEADER',
    'pdf_margin_footer' => 'VRC_PAGE_PDF_MARGIN_FOOTER',
    'pdf_margin_bottom' => 'VRC_PAGE_PDF_MARGIN_BOTTOM',
    'pdf_image_scale_ratio' => 'VRC_PAGE_PDF_IMAGE_SCALE_RATIO',
    'header_font_size' => '10',
    'body_font_size' => '10',
    'footer_font_size' => '8'
);
defined('_VIKRENTCAR_PAGE_PARAMS') OR define('_VIKRENTCAR_PAGE_PARAMS', '1');
?>

<!-- 
     ===========================
     HEADER / ORDER INFORMATION
     ===========================
-->

<div style="text-align: center; width: 100%;">
    <table align="center">
        <tr>
            <!-- The {logo} tag will be replaced with your company’s logo, if set -->
            <td>{logo}</td>
        </tr>
    </table>
</div>

<p><br/><br/></p>

<!-- Basic info table -->
<table width="100%" border="0" cellspacing="0" cellpadding="4">
    <tr>
        <td align="center"><strong><?php echo JText::_('VRCORDERNUMBER'); ?></strong></td>
        <td align="center"><strong><?php echo JText::_('VRCCONFIRMATIONNUMBER'); ?></strong></td>
        <td align="center"><strong><?php echo JText::_('VRLIBSEVEN'); ?></strong></td>
        <td align="center"><strong><?php echo JText::_('VRLIBEIGHT'); ?></strong></td>
    </tr>
    <tr>
        <td align="center">{order_id}</td>
        <td align="center">{confirmnumb}</td>
        <td align="center">
            <span style="color: {order_status_class};">{order_status}</span>
        </td>
        <td align="center">{order_date}</td>
    </tr>
</table>

<h4><?php echo JText::_('VRLIBNINE'); ?>:</h4>
<p>{customer_info}</p>

<p><strong><?php echo JText::_('VRLIBTEN'); ?>:</strong> {item_name}</p>

<table width="100%" border="0" cellspacing="0" cellpadding="4">
    <tr>
        <td align="center"><strong><?php echo JText::_('VRLIBELEVEN'); ?></strong></td>
        <td align="center"><strong><?php echo JText::_('VRRITIROCAR'); ?></strong></td>
        <td>&nbsp;</td>
        <td align="center"><strong><?php echo JText::_('VRLIBTWELVE'); ?></strong></td>
        <td align="center"><strong><?php echo JText::_('VRRETURNCARORD'); ?></strong></td>
    </tr>
    <tr>
        <td align="center">{pickup_date}</td>
        <td align="center">{pickup_location}</td>
        <td>&nbsp;</td>
        <td align="center">{dropoff_date}</td>
        <td align="center">{dropoff_location}</td>
    </tr>
</table>

<p><br/><br/></p>

<h4><?php echo JText::_('VRCORDERDETAILS'); ?>:</h4>
<br/>
<!-- Order details table -->
<table width="100%" align="left" style="border: 1px solid #DDDDDD;" cellspacing="0" cellpadding="4">
    <tr>
        <td bgcolor="#C9E9FC" width="30%" style="border: 1px solid #DDDDDD;"></td>
        <td bgcolor="#C9E9FC" width="10%" align="center" style="border: 1px solid #DDDDDD;"><?php echo JText::_('VRCPDFDAYS'); ?></td>
        <td bgcolor="#C9E9FC" width="20%" style="border: 1px solid #DDDDDD;"><?php echo JText::_('VRCPDFNETPRICE'); ?></td>
        <td bgcolor="#C9E9FC" width="20%" style="border: 1px solid #DDDDDD;"><?php echo JText::_('VRCPDFTAX'); ?></td>
        <td bgcolor="#C9E9FC" width="20%" style="border: 1px solid #DDDDDD;"><?php echo JText::_('VRCPDFTOTALPRICE'); ?></td>
    </tr>
    {order_details}
    {order_total}
</table>

<p><br/><br/></p>

<p>
    <br/>
    <small>
        <strong>{customfield 2} {customfield 3}, <?php echo JText::_('VRLIBTENTHREE'); ?>:</strong><br/>
        {order_link}
    </small>
    <br/>
</p>
<small>{footer_emailtext}</small>

<?php
// ============================
// BEGIN: Contract/Agreement
// ============================
?>

 {vrc_add_pdf_page}

<h3><?php echo JText::_('VRCAGREEMENTTITLE'); ?></h3>
<?php
echo JText::sprintf(
    'VRCAGREEMENTSAMPLETEXT',
    '{customfield 2}',
    '{customfield 3}',
    '{company_name}',
    '{order_date}',
    '{dropoff_date}'
);
// This will produce something like:
// "This agreement between {customfield 2} {customfield 3} and {company_name}
//  was made on the {order_date} and is valid until the {dropoff_date}."
?>

<!-- 
     =======================================
     TERMS AND CONDITIONS (13 Articles)
     =======================================
-->
<p><br/><br/></p>

<h2>Article 1: Purpose of the Agreement</h2>
<ul>
  <li>This agreement grants the lessee the right to use the specified vehicle (camper, caravan, motorhome, etc.) for the duration noted on the rental form. The vehicle provided will be the one described in this agreement, or an equivalent model if the specified vehicle is unavailable due to circumstances beyond the lessor’s control. The agreement becomes effective upon the signature of both parties.</li>
</ul>

<h2><br></h2><h2>Article 2: Reservation Fee and Cancellations</h2>
<ul>
  <li><strong>Reservation Fee:</strong> To confirm the reservation, the lessee must pay a reservation fee of €300 at the time of booking. This fee will be deducted from the total rental cost. The remaining rental amount is due when the vehicle is picked up.</li>
</ul>
<p><strong>Cancellations:</strong></p>
<ul>
  <li><strong>More than 30 days before the rental period:</strong> The reservation fee of €300 will be fully refunded.</li>
  <li><strong>Less than 30 days before the rental period:</strong> The reservation fee of €300 will be retained by the lessor.</li>
  <li><strong>Lessor’s inability to provide the vehicle:</strong> If the lessor cannot provide the vehicle due to reasons not covered under “force majeure,” the lessee is entitled to a full refund of any payments made.</li>
</ul>

<h2><br></h2><h2>Article 3: Security Deposit</h2>
<ul>
  <li>A security deposit of €1,000 is required at the time of vehicle pickup. This deposit is intended to cover any potential damages, penalties, or breaches of the agreement terms. The deposit will be refunded in full within two days after the vehicle is returned, provided there is no damage or violation of the contract terms.</li>
</ul>
<p><strong>Payment Methods:</strong></p>
<p>The security deposit can be paid by:</p>
<table width="100%" style="border: none;">
  <tr>
    <td style="width: 25%">[  ] Cash</td>
    <td style="width: 25%">[  ] Credit Card</td>
    <td style="width: 25%">[  ] Bank Transfer</td>
  </tr>
</table>



<ul>
  <li><strong>Refund Policy:</strong> Refunds of the security deposit will be made preferably by cash or bank transfer. If the deposit was paid by credit card, the refund will be processed via bank transfer, and a 2.5% POS transaction fee will be deducted unless exceptional circumstances apply (e.g., the lessee’s bank cannot accept IBAN transfers).</li>
</ul>

<h2><br></h2><h2>Article 4: Payment Terms</h2>
<ul>
  <li>The remaining rental amount (after the €300 reservation fee) is due upon pickup of the vehicle.</li>
  <li>Payment can be made using: Cash, Credit Card, or Bank Transfer.</li>
</ul>
<p><strong>Fuel Policy:</strong></p>
<ul>
  <li>The lessee must ensure the vehicle is returned with a full tank of fuel. If the vehicle is returned with less than a full tank, the lessee will be charged for refueling at the current market rate, plus a service fee of €20.</li>
  <li><strong>Optional “Fuel Up Front” Option:</strong> The lessee has the option to pre-pay for a full tank of fuel at the start of the rental period. This allows the lessee to return the vehicle with any amount of fuel remaining without additional refueling charges. This option must be selected at the time of pickup.</li>
</ul>

 {vrc_add_pdf_page}

<h2><br></h2><h2>Article 5: Use of the Vehicle</h2>
<p>The lessee agrees to operate the vehicle safely, following all applicable traffic laws and regulations. The vehicle must not be used for any unauthorized activities, including but not limited to:</p>
<ul>
  <li>Driving under the influence of alcohol or drugs.</li>
  <li>Overloading the vehicle beyond its maximum permitted weight or passenger capacity.</li>
  <li>Driving the vehicle off-road or on terrains unsuitable for the vehicle type.</li>
  <li>Allowing unauthorized persons to drive the vehicle.</li>
</ul>
<p>Any damages resulting from such prohibited activities will be considered a breach of contract, and the lessee will be fully responsible for all repair costs. These costs must be paid upfront by the lessee, as the final amount may vary depending on the extent of the damage.</p>
<p><strong>Incident Reporting:</strong> If an accident or incident occurs, the lessee must report it to the lessor and the insurance company within 48 hours. The lessee must also provide all necessary documentation, including a police report, if applicable. Failure to report an incident or provide required documents may result in the lessee being liable for all damages.</p>

<h2><br></h2><h2>Article 6: Insurance Coverage</h2>
<ul>
  <li>The vehicle is covered by comprehensive insurance, which includes accidental damage, theft, and third-party liability. The insurance policy covers 90% of damage costs; the remaining 10% is the lessee’s responsibility.</li>
</ul>
<p><strong>Additional Insurance Coverage:</strong></p>
<p>Third-party liability insurance is included. However, certain damages are excluded from the coverage, such as those resulting from negligence or misuse, including driving under the influence or off-road driving.</p>
<ul>
  <li><strong>Deductible Fee</strong>
    <ul>
      <li>€ 100 for accidents within Albania.</li>
      <li>€ 290 for accidents outside Albania.</li>
    </ul>
  </li>
  <li><strong>Exclusions — The insurance does not cover:</strong>
    <ul>
      <li>Interior damage (e.g., broken furniture, damage to upholstery).</li>
      <li>Loss or theft of keys.</li>
      <li>Damage caused by pets.</li>
      <li>Damage resulting from unauthorized activities.</li>
    </ul>
  </li>
</ul>
<p>If the lessee opts for excess insurance, they are still responsible for paying any damage invoices upfront and must seek reimbursement from their insurance provider independently.</p>

<h2><br></h2><h2>Article 7: Maintenance and Repairs</h2>
<ul>
  <li>The lessor ensures the vehicle is in good working condition at the beginning of the rental period. The lessee is responsible for maintaining the vehicle’s basic condition during the rental period, including checking tire pressure and using the correct fuel.</li>
</ul>
<p><strong>Breakdowns and Repairs:</strong></p>
<ul>
  <li>If any mechanical breakdowns occur, the lessee must notify the lessor immediately. The lessee must not attempt any repairs without the lessor’s written consent. All repair costs due to the lessee’s negligence (such as using the wrong type of fuel) are the responsibility of the lessee.</li>
</ul>

 {vrc_add_pdf_page}

<h2><br></h2><h2>Article 8: Mileage Limit and Additional Costs</h2>
<ul>
  <li>The rental includes a daily mileage limit of 200 kilometers. The total mileage allowance is calculated by multiplying the number of rental days by 200 km × (number of rental days). (e.g., 10-day rental = 2,000 km).</li>
</ul>
<p><strong>Excess Mileage Fee:</strong></p>
<p>If the lessee exceeds this limit, an additional fee of €50 per 200 kilometers will apply. This fee will be calculated upon return of the vehicle. The lessee may choose to pay this fee immediately or have it deducted from the security deposit.</p>

<h2><br></h2><h2>Article 9: Optional Extras and Additional Drivers</h2>
<p><strong>Additional Drivers:</strong> Additional drivers are allowed at no extra charge, but they must be registered at the time of vehicle pickup. There are no minimum age requirements for additional drivers.</p>
<p><strong>Optional Extras:</strong> The following optional extras are available for rent and must be reserved at the time of booking:</p>
<ul>
  <li>Bicycles (€10 per day)</li>
  <li>Camping Set (Air Mattress + Tent) (€5 per day)</li>
  <li>Baby Chair (€5 per day)</li>
  <li>Beach Chairs (€5 per day)</li>
  <li>Camping Furniture (Table + Chairs) (€5 per day)</li>
  <li>BBQ Grill (€5 per day)</li>
</ul>
<p>The lessee is responsible for any damage or loss of these items.</p>

<h2><br></h2><h2>Article 10: Vehicle Return and Cleaning</h2>
<p>The lessee must return the vehicle in the same condition as received, including:</p>
<p><strong>Cleaning Fees:</strong></p>
<ul>
  <li>A mandatory final cleaning fee of €30 will be deducted from the rental amount.</li>
  <li>If the vehicle is not emptied of black water, an additional cleaning fee of €50 will apply.</li>
  <li>Smoking inside the vehicle is strictly prohibited. If evidence of smoking is found, a penalty fee of €50 will be charged, plus an additional €20 for deep cleaning to eliminate odors, totaling €70. This amount is in addition to the mandatory cleaning fee of €30, bringing the total cleaning charge to €100.</li>
</ul>

<h2><br></h2><h2>Article 11: Post-Return Inspection and Additional Charges</h2>
<p><strong>1. Additional Damage Charges:</strong></p>
<ul>
  <li>The Lessor reserves the right to impose additional charges for any damages to the vehicle that were not identified at the time of its return by the Lessee.</li>
</ul>
<p><strong>2. Inspection Period:</strong></p>
<ul>
  <li>The Lessor may conduct a more detailed inspection of the vehicle within eight (8) working days after it has been returned.</li>
  <li>If any damages are discovered during this period that were not identified upon return, the Lessor may levy charges for the cost of repairs.</li>
</ul>

 {vrc_add_pdf_page}
<p><strong>3. Condition for Charges:</strong></p>
<ul>
  <li>These charges will only apply if the vehicle has not been rented to another party during the eight-day inspection period.</li>
</ul>

<p><strong>4. Notification to Lessee:</strong></p>
<ul>
  <li>The Lessee will be notified in writing of any additional charges within this eight-day period. The notification will include a description of the damages and the amount to be charged.</li>
</ul>

<p><strong>5. Traffic Violations and Police Processing Fees:</strong></p>
<ul>
  <li>If an accident occurs and law enforcement authorities (police) are involved, the Lessee is responsible for all fines, penalties, and administrative fees related to the retrieval of police reports or other required documentation. A fixed administrative fee of €70 will be charged to cover these costs. This amount may be deducted from the security deposit if unpaid at the time of vehicle return.</li>
</ul>

<h2><br></h2><h2>Article 12: Roadside Assistance</h2>
<ul>
  <li>The rental includes roadside assistance for towing services within the national territory (Albania) at no cost to the lessee. If towing services are required outside Albania, all costs will be the responsibility of the lessee.</li>
</ul>

<h2><br></h2><h2>Article 13: Jurisdiction and Dispute Resolution</h2>
<ul>
  <li>All disputes arising from this agreement will be resolved exclusively under the jurisdiction of the Judicial Court of Berat District, Albania, regardless of the lessee's country of residence.</li>
</ul>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>
<!-- Acknowledgment and Signatures -->
<p style="text-align: center; margin-top: 30px;">
  <strong>Acknowledgment by Parties</strong>
</p>
<p></p>
<p></p>
<p></p>
<p></p>
<p></p>

<div style="margin-top: 40px;">
  <table width="100%" border="0" cellspacing="0" cellpadding="4">
    <tr>
      <td align="center" width="50%">
        _______________________________<br/>
        Signature of the Lessor<br/>
        Milika Nito<br/>
        Represented by Kristi Nito
      </td>
      <td align="center" width="50%">
        _______________________________<br/>
        Signature of the Lessee
      </td>
    </tr>
  </table>
</div>

<!-- =======================
     Inline CSS for styling
     ======================= -->
<style type="text/css">
p {
    font-size: 12px;
    margin: 5px 0 10px;
}
h1 {
    font-size: 20px;
    font-weight: bold;
    text-align: center;
}
h2 {
    font-size: 16px;
    font-weight: bold;
    margin-top: 20px;
}
h3 {
    font-size: 16px;
    font-weight: bold;
}
h4 {
    font-size: 14px;
    font-weight: bold;
}
span.confirmed {
    color: #009900;
}
span.standby {
    color: #ff0000;
}
</style>


