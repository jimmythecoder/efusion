<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Includes all files in modules with the given name
 * @param array params name of the template file to include in the module dirs
 * @return string  returns compiled html source
 */
function smarty_function_include_from_modules($params, &$smarty)
{
   	$arr_modules = config::get('modules');

	$html_sections = array();

	foreach($arr_modules as $module => $descriptor)
	{
		$template_path_and_filename_to_include = MODULES_DIR . '/' . $module . '/' . $descriptor['views'] . $params['file'];

		if(file_exists($template_path_and_filename_to_include))
			$html_sections[] = $smarty->fetch($template_path_and_filename_to_include);
	}

	return implode("\n",$html_sections);
}

?>
