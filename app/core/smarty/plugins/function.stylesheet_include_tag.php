<?php
/**
 * Includes an external stylesheet file in the layout
 * @param array params name of the stylesheet file to include without the path or .css suffix
 */
function smarty_function_stylesheet_include_tag($params, &$smarty)
{
	$smarty->append('stylesheet_files',$params['file']);
}
?>