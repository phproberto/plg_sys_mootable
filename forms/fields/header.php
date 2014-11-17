<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.Mootable
 *
 * @author      Roberto Segura <roberto@phproberto.com>
 * @copyright   (c) 2012 Roberto Segura. All Rights Reserved.
 * @license     GNU/GPL 2, http://www.gnu.org/licenses/gpl-2.0.htm
 */

defined('_JEXEC') or die;

JLoader::import('joomla.form.formfield');

/**
 * Fake field to display a header in settings
 *
 * @package     Joomla.Plugin
 * @subpackage  System.Mootable
 * @since       1.1.0
 */
class MootableFormFieldHeader extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.1.0
	 */
	var	$type = 'header';

	/**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribute to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   1.1.0
	 */
	function getInput()
	{
		return '';
	}

	/**
	 * Method to get the field label markup.
	 *
	 * @return  string  The field label markup.
	 *
	 * @since   1.1.0
	 */
	function getLabel()
	{
		return '<h4 class="settings-header" style="clear: both;">' . JText::_((string) $this->element['label']) . ':</h4>';
	}
}
