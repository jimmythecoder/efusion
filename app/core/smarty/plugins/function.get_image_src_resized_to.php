<?php
/**
 * Includes an external stylesheet file in the layout
 * @param array params name of the stylesheet file to include without the path or .css suffix
 * @return string  returns an absolute URL to the resized image in cache
 */
function smarty_function_get_image_src_resized_to($params, &$smarty)
{
	if(empty($params['filename']) || empty($params['width']) || empty($params['height']))
		return false;
	
	$image_path_and_filename = IMAGE_UPLOADS_DIR . '/' . $params['filename'];
	
	if(!file_exists($image_path_and_filename))
	{
		//Use the default image instead
		$image = model::create('image');
		$image->find(DEFAULT_IMAGE_ID);
		
		$image_path_and_filename = IMAGE_UPLOADS_DIR . '/' . $image->filename;
	}
	else	
		model::include_model('image');
	
	list($current_image_width, $current_image_height) = getimagesize($image_path_and_filename);
	
	$resized_image_dimensions = image::get_calculated_resized_to_fit_dimensions($current_image_width, $current_image_height, $params['width'], $params['height']);
	
	$filename_split_by_dot = explode('.',$params['filename']);
	$file_extension = array_pop($filename_split_by_dot);
	$filename_without_extension = implode('.',$filename_split_by_dot);

	$cached_image_filename = 	$filename_without_extension . 
								'-' . 
								$resized_image_dimensions['width'] . 
								'x' . 
								$resized_image_dimensions['height'] . 
								'.' . $file_extension;
											
	$cached_image_path_and_filename = 	IMAGE_CACHE_DIR . '/' . $cached_image_filename;
	
	
	if(!file_exists($cached_image_path_and_filename))
	{	
		$resized_image_resource = image::resize_image($resized_image_dimensions['width'], $resized_image_dimensions['height'], $image_path_and_filename);
	
		image::save_image_resource_to_file($resized_image_resource, $cached_image_path_and_filename);
	}
	
	$image_cache_path = substr(IMAGE_CACHE_DIR,strlen(PUBLIC_DIR));
	
	$cached_image_url = config::get('current_location') . $image_cache_path . '/' . $cached_image_filename;

	return $cached_image_url;
}
?>