<?php
/**
 * Compress all js includes into a single packed file
 */
function smarty_function_compress_js_files($params, &$smarty)
{
	$compress_js_files 		= config::get('core','compress_js_files');
	$js_include_files 		= $smarty->get_template_vars('javascript_files');
	$controller_view_data 	= $smarty->get_template_vars('controller');
		
	if($compress_js_files && !empty($js_include_files))
	{
		$packed_and_combined_filename 	= (empty($controller_view_data['module']) ? 'root' : $controller_view_data['module']) . '.' . $controller_view_data['name'].'.'.$controller_view_data['action'];
		$compressed_js_file 			= PACKED_JS_DIR . '/' . $packed_and_combined_filename . '.js';
		
		//If the production packed file already exists
		if(file_exists($compressed_js_file)){
			$js_include_files = array('packed/' . $packed_and_combined_filename);
		}
		else
		{
			logger::log_debug('Packed file does not exist, trying to compile it...');
			
			require VENDOR_DIR . '/jspacker/class.JavaScriptPacker.php';
			
			try{
				
				if(!is_writable(PACKED_JS_DIR))
					throw new Exception('Packed js dir is not writable');
					
				$packed_and_combined_file_contents = '';
				
				//Reverse the array, so layout js files get put in first, view js files go in last
				$js_include_files = array_reverse($js_include_files);
				
				//Pack and combine each file
				foreach($js_include_files as $file)
				{		
					logger::log_debug("Packing file $file ...");
					
					$src_filename = JS_DIR . '/' . $file . '.js';
					
					$script = file_get_contents($src_filename);
	
					$packer = new JavaScriptPacker($script, 'Normal', true, false);
					$packed = $packer->pack();
					
					unset($packer);
					unset($script);
					
					if(IS_DEVELOPMENT_ENV)
						$packed_and_combined_file_contents .= "//$file.js ".math::bytes_to_kilobytes(strlen($packed))."KB \n";	
					
					$packed_and_combined_file_contents .= $packed . "\n\n";		
				}	
				
				if(!file_put_contents($compressed_js_file,$packed_and_combined_file_contents))
					throw new Exception('Could not write compressed file out to ' . $compressed_js_file);
				
				$js_include_files = array('packed/' . $packed_and_combined_filename);
			}
			catch(Exception $e){
				//Something went wrong, lets log it and the standard unpacked files will be included as a result
				logger::log_warn($e->getMessage());
			}
		}
		
		//Overwrite js include files and just include a single packed / combined file, less http streams so its faster
		$smarty->assign('javascript_files',$js_include_files);
	}
}
?>