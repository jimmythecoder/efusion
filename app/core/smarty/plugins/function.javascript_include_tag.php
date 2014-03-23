<?php
/**
 * Includes an external javascript file in the layout
 * @param array params name of the javascript file to include without the path or .js suffix
 */
function smarty_function_javascript_include_tag($params, &$smarty)
{
	$smarty->append('javascript_files',$params['file']);
}
?>