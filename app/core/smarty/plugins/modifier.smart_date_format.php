<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Include the {@link shared.make_timestamp.php} plugin
 */
require_once $smarty->_get_plugin_filepath('shared','make_timestamp');
/**
 * Smarty date_format modifier plugin
 *
 * Type:     modifier<br>
 * Name:     date_format<br>
 * Purpose:  format datestamps via date<br>
 * Input:<br>
 *         - string: input date string
 *         - format: date format for output
 *         - default_date: default date if $string is empty
 */
function smarty_modifier_smart_date_format($string, $format="d-m-Y")
{
	return date($format,strtotime($string));
}

/* vim: set expandtab: */

?>
