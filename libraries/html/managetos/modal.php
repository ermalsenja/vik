<?php
/** 
 * @package     VikRentCar - Libraries
 * @subpackage  html.managetos
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2024 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

$field = $displayData['field'];

?>

<div style="padding: 10px;">

	<form id="tos-form-<?php echo (int) $field['id']; ?>">

		<div class="vrc-admin-container vrc-params-container-wide">

			<div class="vrc-params-container">

				<!-- Name - Textarea -->

				<div class="vrc-param-container">
					<div class="vrc-param-label"><?php echo JText::_('VRPVIEWCUSTOMFONE'); ?> <sup>*</sup></div>
					<div class="vrc-param-setting">
						<textarea name="name" style="resize: vertical;height: 80px;"><?php echo JText::_($field['name']); ?></textarea>
					</div>
				</div>

				<!-- Popup Link - Textarea -->

				<div class="vrc-param-container">
					<div class="vrc-param-label"><?php echo JText::_('VRNEWCUSTOMFEIGHT'); ?> <sup>*</sup></div>
					<div class="vrc-param-setting">
						<textarea name="poplink" style="resize: vertical;height: 80px;"><?php echo JText::_($field['poplink']); ?></textarea>
					</div>
				</div>

			</div>

		</div>

		<input type="hidden" name="id" value="<?php echo (int) $field['id']; ?>" />

	</form>

</div>
