<?php
/** 
 * @package     VikWP - Libraries
 * @subpackage  adapter.html
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

JLoader::import('adapter.utilities.array');

/**
 * Utility class for creating HTML select lists.
 *
 * @since 10.1.16
 */
abstract class JHtmlSelect
{
	/**
	 * Default values for options. Organized by option group.
	 *
	 * @var array
	 */
	protected static $optionDefaults = array(
		'option' => array(
			'option.attr' => null,
			'option.disable' => 'disable',
			'option.id' => null,
			'option.key' => 'value',
			'option.key.toHtml' => true,
			'option.label' => null,
			'option.label.toHtml' => true,
			'option.text' => 'text',
			'option.text.toHtml' => true,
			'option.class' => 'class',
			'option.onclick' => 'onclick',
		),
	);

	/**
	 * Generates a grouped HTML selection list from nested arrays.
	 *
	 * @param   array   $data     An array of groups, each of which is an array of options.
	 * @param   string  $name     The value of the HTML name attribute
	 * @param   array   $options  Options, an array of key/value pairs. Valid options are:
	 *                            Format options.
	 *                            Selection options. See {@see JHtmlSelect::options()}.
	 *                            group.id: The property in each group to use as the group id
	 *                            attribute. Defaults to none.
	 *                            group.label: The property in each group to use as the group
	 *                            label. Defaults to "text". If set to null, the data array index key is
	 *                            used.
	 *                            group.items: The property in each group to use as the array of
	 *                            items in the group. Defaults to "items". If set to null, group.id and
	 *                            group. label are forced to null and the data element is assumed to be a
	 *                            list of selections.
	 *                            id: Value to use as the select element id attribute. Defaults to
	 *                            the same as the name.
	 *                            list.attr: Attributes for the select element. Can be a string or
	 *                            an array of key/value pairs. Defaults to none.
	 *                            list.select: either the value of one selected option or an array
	 *                            of selected options. Default: none.
	 *                            list.translate: Boolean. If set, text and labels are translated via
	 *                            JText::_().
	 *
	 * @return  string  HTML for the select list
	 *
	 * @throws  RuntimeException If a group has contents that cannot be processed.
	 */
	public static function groupedlist($data, $name, $options = array())
	{
		// Set default options and overwrite with anything passed in
		$options = array_merge(
			JHtml::$formatOptions,
			array('format.depth' => 0, 'group.items' => 'items', 'group.label' => 'text', 'group.label.toHtml' => true, 'id' => false),
			$options
		);

		// Apply option rules
		if ($options['group.items'] === null)
		{
			$options['group.label'] = null;
		}

		$attribs = '';

		if (isset($options['list.attr']))
		{
			if (is_array($options['list.attr']))
			{
				$attribs = ArrayHelper::toString($options['list.attr']);
			}
			else
			{
				$attribs = $options['list.attr'];
			}

			if ($attribs !== '')
			{
				$attribs = ' ' . $attribs;
			}
		}

		$id = $options['id'] !== false ? $options['id'] : $name;
		$id = str_replace(array('[', ']', ' '), '', $id);

		// Disable groups in the options.
		$options['groups'] = false;

		$baseIndent = str_repeat($options['format.indent'], $options['format.depth']++);
		$html = $baseIndent . '<select' . ($id !== '' ? ' id="' . $id . '"' : '') . ' name="' . $name . '"' . $attribs . '>' . $options['format.eol'];
		$groupIndent = str_repeat($options['format.indent'], $options['format.depth']++);

		foreach ($data as $dataKey => $group)
		{
			$label = $dataKey;
			$id = '';
			$noGroup = is_int($dataKey);

			if ($options['group.items'] == null)
			{
				// Sub-list is an associative array
				$subList = $group;
			}
			elseif (is_array($group))
			{
				// Sub-list is in an element of an array.
				$subList = $group[$options['group.items']];

				if (isset($group[$options['group.label']]))
				{
					$label = $group[$options['group.label']];
					$noGroup = false;
				}

				if (isset($options['group.id']) && isset($group[$options['group.id']]))
				{
					$id = $group[$options['group.id']];
					$noGroup = false;
				}
			}
			elseif (is_object($group))
			{
				// Sub-list is in a property of an object
				$subList = $group->{$options['group.items']};

				if (isset($group->{$options['group.label']}))
				{
					$label = $group->{$options['group.label']};
					$noGroup = false;
				}

				if (isset($options['group.id']) && isset($group->{$options['group.id']}))
				{
					$id = $group->{$options['group.id']};
					$noGroup = false;
				}
			}
			else
			{
				throw new RuntimeException('Invalid group contents.', 1);
			}

			if ($noGroup)
			{
				$html .= static::options($subList, $options);
			}
			else
			{
				$html .= $groupIndent . '<optgroup' . (empty($id) ? '' : ' id="' . $id . '"') . ' label="'
					. ($options['group.label.toHtml'] ? htmlspecialchars($label, ENT_COMPAT, 'UTF-8') : $label) . '">' . $options['format.eol']
					. static::options($subList, $options) . $groupIndent . '</optgroup>' . $options['format.eol'];
			}
		}

		$html .= $baseIndent . '</select>' . $options['format.eol'];

		return $html;
	}

	/**
	 * Create an object that represents an option in an option list.
	 *
	 * @param   string   $value    The value of the option
	 * @param   string   $text     The text for the option
	 * @param   mixed    $optKey   If a string, the returned object property name for
	 *                             the value. If an array, options. Valid options are:
	 *                             attr: String|array. Additional attributes for this option.
	 *                             Defaults to none.
	 *                             disable: Boolean. If set, this option is disabled.
	 *                             label: String. The value for the option label.
	 *                             option.attr: The property in each option array to use for
	 *                             additional selection attributes. Defaults to none.
	 *                             option.disable: The property that will hold the disabled state.
	 *                             Defaults to "disable".
	 *                             option.key: The property that will hold the selection value.
	 *                             Defaults to "value".
	 *                             option.label: The property in each option array to use as the
	 *                             selection label attribute. If a "label" option is provided, defaults to
	 *                             "label", if no label is given, defaults to null (none).
	 *                             option.text: The property that will hold the the displayed text.
	 *                             Defaults to "text". If set to null, the option array is assumed to be a
	 *                             list of displayable scalars.
	 * @param   string   $optText  The property that will hold the the displayed text. This
	 *                             parameter is ignored if an options array is passed.
	 * @param   boolean  $disable  Not used.
	 *
	 * @return  stdClass
	 */
	public static function option($value, $text = '', $optKey = 'value', $optText = 'text', $disable = false)
	{
		$options = array(
			'attr' => null,
			'disable' => false,
			'option.attr' => null,
			'option.disable' => 'disable',
			'option.key' => 'value',
			'option.label' => null,
			'option.text' => 'text',
		);

		if (is_array($optKey))
		{
			// Merge in caller's options
			$options = array_merge($options, $optKey);
		}
		else
		{
			// Get options from the parameters
			$options['option.key'] = $optKey;
			$options['option.text'] = $optText;
			$options['disable'] = $disable;
		}

		$obj = new stdClass;
		$obj->{$options['option.key']}  = $value;
		$obj->{$options['option.text']} = trim($text) ? $text : $value;

		/*
		 * If a label is provided, save it. If no label is provided and there is
		 * a label name, initialise to an empty string.
		 */
		$hasProperty = $options['option.label'] !== null;

		if (isset($options['label']))
		{
			$labelProperty = $hasProperty ? $options['option.label'] : 'label';
			$obj->$labelProperty = $options['label'];
		}
		elseif ($hasProperty)
		{
			$obj->{$options['option.label']} = '';
		}

		// Set attributes only if there is a property and a value
		if ($options['attr'] !== null)
		{
			$obj->{$options['option.attr']} = $options['attr'];
		}

		// Set disable only if it has a property and a value
		if ($options['disable'] !== null)
		{
			$obj->{$options['option.disable']} = $options['disable'];
		}

		return $obj;
	}

	/**
	 * Generates the option tags for an HTML select list (with no select tag
	 * surrounding the options).
	 *
	 * @param   array    $arr        An array of objects, arrays, or values.
	 * @param   mixed    $optKey     If a string, this is the name of the object variable for
	 *                               the option value. If null, the index of the array of objects is used. If
	 *                               an array, this is a set of options, as key/value pairs. Valid options are:
	 *                               -Format options.
	 *                               -groups: Boolean. If set, looks for keys with the value
	 *                                "&lt;optgroup>" and synthesizes groups from them. Deprecated. Defaults
	 *                                true for backwards compatibility.
	 *                               -list.select: either the value of one selected option or an array
	 *                                of selected options. Default: none.
	 *                               -list.translate: Boolean. If set, text and labels are translated via
	 *                                JText::_(). Default is false.
	 *                               -option.id: The property in each option array to use as the
	 *                                selection id attribute. Defaults to none.
	 *                               -option.key: The property in each option array to use as the
	 *                                selection value. Defaults to "value". If set to null, the index of the
	 *                                option array is used.
	 *                               -option.label: The property in each option array to use as the
	 *                                selection label attribute. Defaults to null (none).
	 *                               -option.text: The property in each option array to use as the
	 *                               displayed text. Defaults to "text". If set to null, the option array is
	 *                               assumed to be a list of displayable scalars.
	 *                               -option.attr: The property in each option array to use for
	 *                                additional selection attributes. Defaults to none.
	 *                               -option.disable: The property that will hold the disabled state.
	 *                                Defaults to "disable".
	 *                               -option.key: The property that will hold the selection value.
	 *                                Defaults to "value".
	 *                               -option.text: The property that will hold the the displayed text.
	 *                               Defaults to "text". If set to null, the option array is assumed to be a
	 *                               list of displayable scalars.
	 * @param   string   $optText    The name of the object variable for the option text.
	 * @param   mixed    $selected   The key that is selected (accepts an array or a string)
	 * @param   boolean  $translate  Translate the option values.
	 *
	 * @return  string  HTML for the select list.
	 */
	public static function options($arr, $optKey = 'value', $optText = 'text', $selected = null, $translate = false)
	{
		$options = array_merge(
			JHtml::$formatOptions,
			static::$optionDefaults['option'],
			array('format.depth' => 0, 'groups' => true, 'list.select' => null, 'list.translate' => false)
		);

		if (is_array($optKey))
		{
			// Set default options and overwrite with anything passed in
			$options = array_merge($options, $optKey);
		}
		else
		{
			// Get options from the parameters
			$options['option.key'] = $optKey;
			$options['option.text'] = $optText;
			$options['list.select'] = $selected;
			$options['list.translate'] = $translate;
		}

		$html = '';
		$baseIndent = str_repeat($options['format.indent'], $options['format.depth']);

		foreach ($arr as $elementKey => &$element)
		{
			$attr = '';
			$extra = '';
			$label = '';
			$id = '';

			if (is_array($element))
			{
				$key = $options['option.key'] === null ? $elementKey : $element[$options['option.key']];
				$text = $element[$options['option.text']];

				if (isset($element[$options['option.attr']]))
				{
					$attr = $element[$options['option.attr']];
				}

				if (isset($element[$options['option.id']]))
				{
					$id = $element[$options['option.id']];
				}

				if (isset($element[$options['option.label']]))
				{
					$label = $element[$options['option.label']];
				}

				if (isset($element[$options['option.disable']]) && $element[$options['option.disable']])
				{
					$extra .= ' disabled="disabled"';
				}
			}
			elseif (is_object($element))
			{
				$key = $options['option.key'] === null ? $elementKey : $element->{$options['option.key']};
				$text = $element->{$options['option.text']};

				if (isset($element->{$options['option.attr']}))
				{
					$attr = $element->{$options['option.attr']};
				}

				if (isset($element->{$options['option.id']}))
				{
					$id = $element->{$options['option.id']};
				}

				if (isset($element->{$options['option.label']}))
				{
					$label = $element->{$options['option.label']};
				}

				if (isset($element->{$options['option.disable']}) && $element->{$options['option.disable']})
				{
					$extra .= ' disabled="disabled"';
				}

				if (isset($element->{$options['option.class']}) && $element->{$options['option.class']})
				{
					$extra .= ' class="' . $element->{$options['option.class']} . '"';
				}

				if (isset($element->{$options['option.onclick']}) && $element->{$options['option.onclick']})
				{
					$extra .= ' onclick="' . $element->{$options['option.onclick']} . '"';
				}
			}
			else
			{
				// This is a simple associative array
				$key = $elementKey;
				$text = $element;
			}

			/*
			 * The use of options that contain optgroup HTML elements was
			 * somewhat hacked for J1.5. J1.6 introduces the grouplist() method
			 * to handle this better. The old solution is retained through the
			 * "groups" option, which defaults true in J1.6, but should be
			 * deprecated at some point in the future.
			 */

			$key = (string) $key;

			if ($key === '<OPTGROUP>' && $options['groups'])
			{
				$html .= $baseIndent . '<optgroup label="' . ($options['list.translate'] ? JText::_($text) : $text) . '">' . $options['format.eol'];
				$baseIndent = str_repeat($options['format.indent'], ++$options['format.depth']);
			}
			elseif ($key === '</OPTGROUP>' && $options['groups'])
			{
				$baseIndent = str_repeat($options['format.indent'], --$options['format.depth']);
				$html .= $baseIndent . '</optgroup>' . $options['format.eol'];
			}
			else
			{
				// If no string after hyphen - take hyphen out
				$splitText = explode(' - ', $text, 2);
				$text = $splitText[0];

				if (isset($splitText[1]) && $splitText[1] !== '' && !preg_match('/^[\s]+$/', $splitText[1]))
				{
					$text .= ' - ' . $splitText[1];
				}

				if (!empty($label) && $options['list.translate'])
				{
					$label = JText::_($label);
				}

				if ($options['option.label.toHtml'])
				{
					$label = htmlentities($label);
				}

				if (is_array($attr))
				{
					$attr = ArrayHelper::toString($attr);
				}
				else
				{
					$attr = trim($attr);
				}

				$extra = ($id ? ' id="' . $id . '"' : '') . ($label ? ' label="' . $label . '"' : '') . ($attr ? ' ' . $attr : '') . $extra;

				if (is_array($options['list.select']))
				{
					foreach ($options['list.select'] as $val)
					{
						$key2 = is_object($val) ? $val->{$options['option.key']} : $val;

						if ($key == $key2)
						{
							$extra .= ' selected="selected"';
							break;
						}
					}
				}
				elseif ((string) $key === (string) $options['list.select'])
				{
					$extra .= ' selected="selected"';
				}

				if ($options['list.translate'])
				{
					$text = JText::_($text);
				}

				// Generate the option, encoding as required
				$html .= $baseIndent . '<option value="' . ($options['option.key.toHtml'] ? htmlspecialchars($key, ENT_COMPAT, 'UTF-8') : $key) . '"'
					. $extra . '>';
				$html .= $options['option.text.toHtml'] ? htmlentities(html_entity_decode($text, ENT_COMPAT, 'UTF-8'), ENT_COMPAT, 'UTF-8') : $text;
				$html .= '</option>' . $options['format.eol'];
			}
		}

		return $html;
	}
}
