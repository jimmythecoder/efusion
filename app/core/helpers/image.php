<?php
/**
 * Image manipulation helper
 * 
 * @package efusion
 * @subpackage helpers
 */
class image_helper
{		
	/**
	 * Resizes and image with no proportional scale
	 */
	static function resize_image_fixed($src_image, $dest_image, $width, $height, $quality = 80)
	{
		if(!function_exists('imagecopyresampled'))
	    	throw new Exception('PHP GD lib is required. The function imagecopyresampled does not exist');
	   
	   	if(!file_exists($src_image))
	   		throw new Exception('Image does not exist');
	   
		list($image_width, $image_height, $image_type, $image_attr) = getimagesize($src_image);
		$image_resource = image_helper::get_image_resource($src_image);
		
		$x_proportion = $image_width / $width;
        $y_proportion = $image_height / $height;
        
        //If no resizing is required
        if ($x_proportion <= 1 && $y_proportion <= 1) 
        	return image_helper::save_image_resource_to_file($image_resource, $dest_image, 'jpg', $quality);
        
        //Scale By X and Y
        $resized_image_canvas 	= imagecreatetruecolor($width, $height);
		
		//Try to copy a resampled version (best quality), if that fails just copy by pixel (less quality)
	    if(!imagecopyresampled($resized_image_canvas, $image_resource, 0, 0, 0, 0, $width, $height, $image_width, $image_height))
	    	throw new Exception('Could not resample image');
	    
	    return image_helper::save_image_resource_to_file($resized_image_canvas, $dest_image, 'jpg', $quality);
	}
	
	static function resize_image_proportional($src_image, $dest_image, $width, $height, $quality = 80)
	{
		$original_dimensions = image_helper::get_image_dimensions($src_image);
		
		$arr_resize_to = image_helper::get_proportional_resized_dimensions($original_dimensions['width'],$original_dimensions['height'], $width, $height);
		
		return image_helper::resize_image_fixed($src_image, $dest_image, $arr_resize_to['width'], $arr_resize_to['height'], $quality);
	}
	
	static function is_image($filename)
	{
		$mime = mime_content_type($filename);
		list($kind,$format) = explode('/',$mime);
		
		return $kind == 'image';
	}
	
	static function get_mime_type($filename)
	{
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		
		return finfo_file($finfo, $filename);
	}
	
	static function get_image_dimensions($filename)
	{
		list($image_width, $image_height, $image_type, $image_attr) = getimagesize($filename);
        
        return array('width' => $image_width, 'height' => $image_height);
	}	
	
	static function get_image_resource($filename)
	{
		$type = image_helper::get_mime_type($filename);
		
		switch($type)
		{
		  case 'image/gif':
			$image_resource = imagecreatefromgif($filename);
		  break;
		  
		  case 'image/jpg':
		  case 'image/jpeg':
		  	$image_resource = imagecreatefromjpeg($filename);
		  break;
		  
		  case 'image/png':
		  	$image_resource = imagecreatefrompng($filename);
		  break;
		  
		  default:
		  	throw new Exception('Loading of image '.$filename.' failed. Image type '.$type.' not supported');
		} 
		
		return $image_resource;
	}
	
	
	/**
	 * Calculates the dimensions of an image after it has been resized to fit within a height and width
	 * @param int $current_width The current width of the image to be resized
	 * @param int $current_height The current height of the image to be resized
	 * @param int $max_width The maximum width the resized image is allowed to be
	 * @param int $max_height The maximum height the resized_image is allowed to be
	 * @return array associative array containing the resized dimensions width and height
	 */
	static function get_proportional_resized_dimensions($current_width, $current_height, $max_width, $max_height)
	{
		$x_proportion = $current_width / $max_width;
        $y_proportion = $current_height / $max_height;
        
        //If no resizing is required
        if ($x_proportion <= 1 && $y_proportion <= 1) 
        	return array('width' => $current_width, 'height' => $current_height);
        elseif ($x_proportion > $y_proportion) 
        {
        	//Scale By X
            $resized_height = round(($max_width / $current_width) * $current_height, 0);
        	return array('width' => $max_width, 'height' => $resized_height);

	    }
        else 
        {
        	//Scale By Y
        	$resized_width = round(($max_height / $current_height) * $current_width, 0);
        	return array('width' => $resized_width, 'height' => $max_height);
        }
	}
	
	/**
	 * Saves a GD image resorce to a jpeg image file
	 * @param GD resource $image_resource resource handle to the image data
	 * @param string $image_path_and_filename Absolute path and filename to save the image to
	 * @return boolean true on success, else false
	 */
	static function save_image_resource_to_file($image_resource, $dest_filename, $format = 'jpg', $quality = 80)
	{
		switch($format){
			case 'gif':
				if(!imagegif($image_resource, $dest_filename))
					throw new Exception('Saving of gif image '.$dest_filename.' failed');
			break;
			case 'png':
				if(!imagepng($image_resource, $dest_filename, $quality))
					throw new Exception('Saving of png image '.$dest_filename.' failed');
			default:
				if(!imagejpeg($image_resource, $dest_filename, $quality))
					throw new Exception('Saving of jpg image '.$dest_filename.' failed');
		}
		
		return true;
	}	
	
	/**
	 * Generate a unique random image filename
	 * @param string $file_extension file extension to use, defaults to .jpg
	 * @return string unique randomly genrated name for an image
	 */
	static function generate_unique_filename($file_extension = 'jpg')
	{
		do
		{
			$random_string = substr(md5(uniqid(rand(), true)),0,10);
			
			$filename = $random_string . '.' . $file_extension;
		}
		while(self::does_uploaded_image_file_exist($filename));
		
		return $filename;
	}	
	
	/**
	 * Tests if a file exists as an uploaded image
	 * @param string $filename local filename of the image to test for in product uploads dir
	 * @return boolean true if file exists, else false
	 * @see file_exists
	 */
	static function does_uploaded_image_file_exist($filename)
	{
		return file_exists(IMAGE_UPLOADS_DIR . '/' . $filename);
	}	
}
?>
