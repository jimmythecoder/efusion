<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty trim modifier plugin
 *
 * Type:     modifier<br>
 * Name:     trim<br>
 * Purpose:  Strip whitespace (or other characters) from the beginning and end of a string
 * 
 * @link http://www.php.net/trim
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @version  1.0
 * @param string
 * @param string
 * @return string
 */
function smarty_modifier_trim($text)
{
    return trim($text);
}

/* vim: set expandtab: */

?>
