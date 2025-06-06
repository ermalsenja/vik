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

// render inspector to manage ToS fields
echo JHtml::_(
	'bootstrap.renderModal',
	'jmodal-tos-' . $field['id'],
	array(
		'title'       => JText::_('VRMAINCUSTOMFTITLE'),
		'closeButton' => true,
		'keyboard'    => false,
		'width'       => 70,
		'height'      => 80,
		'footer'      => '<button type="button" class="btn btn-success" id="tos-save-' . $field['id'] . '">' . JText::_('JAPPLY') . '</button>',
	),
	JLayoutHelper::render('html.managetos.modal', $displayData)
);

// render modal script
echo VikRentCar::getVrcApplication()->getJmodalScript();

JText::script('JLIB_APPLICATION_SAVE_SUCCESS');
JText::script('FATAL_ERROR');
?>

<script>
	(function($) {
		'use strict';

		$(function() {
			// get ToS table row
			var tr = $('input[name="cid[]"][value="<?php echo (int) $field['id']; ?>"]').closest('tr');

			// get column that contains the field name
			var nameTD = tr.children().eq(1);

			// wrap name within a div
			nameTD.html('<div style="float: left;"> ' + nameTD.html() + ' </div>');

			// create edit button
			var editButton = $('<a href="javascript:void(0)" style="float: right;"><i class="<?php echo VikRentCarIcons::i('pen-square'); ?>" style="font-size: 18px;"></i></a>');

			// register click event
			editButton.on('click', function() {
				// open modal
				vrcOpenJModal('tos-<?php echo (int) $field['id']; ?>');
			});

			// append edit button
			nameTD.append(editButton);

			// register save event
			$('#tos-save-<?php echo (int) $field['id']; ?>').on('click', function() {
				// get form containing the field value
				var form = $('form#tos-form-<?php echo (int) $field['id']; ?>');

				// make save request
				$.ajax({
					// request end-point
					url: 'admin-ajax.php?action=vikrentcar&task=customf.savetosajax',
					type: 'post',
					// serialize form
					data: form.serialize(),
				}).done((data) => {
					if (typeof data === 'string') {
						data = JSON.parse(data);
					}

					// update name within table column
					nameTD.find('.name').html(data.name);

					// auto-close modal on successful save
					$('#jmodal-tos-<?php echo (int) $field['id']; ?>').modal('toggle');

					VRCToast.enqueue(new VRCToastMessage({
						title:  Joomla.JText._('JLIB_APPLICATION_SAVE_SUCCESS'),
						icon:   '<?php echo VikRentCarIcons::i('check-circle'); ?> vrc-chart-icon-positive',
						delay:  3000,
						action: () => {
							VRCToast.dispose(true);
						},
					}));
				}).fail((error) => {
					VRCToast.enqueue(new VRCToastMessage({
						title:  Joomla.JText._('FATAL_ERROR'),
						body:   error.responseText || 'Unknown.',
						icon:   '<?php echo VikRentCarIcons::i('times-circle'); ?> vrc-chart-icon-negative',
						delay:  5000,
						action: () => {
							VRCToast.dispose(true);
						},
					}));
				});
			});
		});
	})(jQuery);
</script>
