<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {dhtml_calendar_init} function plugin
 *
 * Type:     function<br>
 * Name:     dhtml_calendar_init<br>
 * Purpose:  interface to jscalendar-0.9.6
 * Author:   boots
 * @link http://www.dynarch.com/projects/calendar/ {dhtml calendar}
 *          (dynarch.com)
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_dhtml_calendar_init($params, &$smarty)
{

    $defaults = array(
      'css'       => "$params[webDir]/styles/calendar-aqua.css"
    , 'src'       => "$params[webDir]/javascripts/calendar.js"
    , 'lang'      => "$params[webDir]/javascripts/calendar-en.js"
    , 'setup_src' => "$params[webDir]/javascripts/calendar-setup.js"
    );
    foreach($defaults as $field=>$default) {
        $_field = "_$field";
        if (array_key_exists($field, $params)) {
            $$_field = (empty($params[$field])) ? $default : $params[$field];
        } else {
            $$_field = $default;
        }
    }

$_out = <<<EOF
    <link rel="stylesheet" type="text/css" media="all" href="{$_css}">
    <script type="text/javascript" src="{$_src}"></script>
    <script type="text/javascript" src="{$_lang}"></script>
    <script type="text/javascript" src="{$_setup_src}"></script>
EOF;

return $_out;
}

/* vim: set expandtab: */

?>