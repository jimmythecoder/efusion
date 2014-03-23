<?php
/**
 * File system Image meta information
 * 
 * @package efusion
 * @subpackage models
 */
class image extends model
{
	/**
	 * Primary key
	 * @var int
	 */
	var $id;
	
	/**
	 * Foriegn key to mime_type table
	 * @var int
	 */
	var $mime_type_id;
	
	/**
	 * Image caption
	 * @var string
	 */
	var $caption;
	
	/**
	 * Filename to the image, relative or absolute
	 * @var string
	 */
	var $filename;
	
	/**
	 * Size in bytes of the image
	 * @var int
	 */
	var $size;
	
	
	function validate()
	{
		$this->validates_foriegnkey_exists('mime_type_id');
		$this->validates_numericality_of('size');
		
		return parent::validate();
	}
	
	/**
	 * Uploads a new image to the filesystem, creates a new image record
	 * @param string $field_name name of the html file upload field
	 * @param string $save_to_path relative pathname from site root dir to the save folder
	 * @return int image id for the file on success, else false
	 */
	function upload_image($field_name, $save_to_dir = IMAGE_UPLOADS_DIR, $preferred_filename = null)
	{
		if($this->is_valid_file_upload($_FILES[$field_name]['error']))
		{
			if($this->is_image_mime_type($_FILES[$field_name]['type']))
			{
				if(is_uploaded_file($_FILES[$field_name]['tmp_name']))
				{
					$file_extension = $this->set_mime_type($_FILES[$field_name]['type'],$_FILES[$field_name]['name']);
					$this->size = $_FILES[$field_name]['size'];
					
					if($preferred_filename && !image::does_uploaded_image_file_exist($preferred_filename . '.' . $file_extension))
						$this->filename = $preferred_filename . '.' . $file_extension;
					else
						$this->filename = image::generate_unique_filename($file_extension);
					
					$this->save();
					
					//Move and resize the uploaded image
					$filename_and_path = $save_to_dir . '/' . $this->filename;
					
					if (move_uploaded_file($_FILES[$field_name]['tmp_name'], $filename_and_path)) 
					{
						chmod($filename_and_path, 0755);
					   	$this->save();
					   	return $this->id;
					} 
					else 
					{
						$this->_errors[] = 'System error: Could not move uploaded file, please check '.$save_to_dir.' is writable.';
						$this->delete($this->id);
					}
				}
				else
					$this->_errors[] = 'System error: Bad file upload, please try again';
			}
			else
				$this->_errors[] = 'The file you uploaded was not an image';
		}
		else
			$this->_errors[] = image::get_file_upload_error_description($_FILES[$field_name]['error']);

		//Remove uploaded file if it exists on error
		if(file_exists($_FILES[$field_name]['tmp_name']) && is_writable($_FILES[$field_name]['tmp_name']))
			unlink($_FILES[$field_name]['tmp_name']);
		
		return false;		
	}
	
	/**
	 * Generate a unique random image filename
	 * @param string $file_extension file extension to use, defaults to .jpg
	 * @return string unique randomly genrated name for an image
	 */
	function generate_unique_filename($file_extension = 'jpg')
	{
		do
		{
			$random_string = substr(md5(uniqid(rand(), true)),0,8);
			
			$filename = $random_string . '.' . $file_extension;
		}
		while(image::does_uploaded_image_file_exist($filename));
		
		return $filename;
	}

	/**
	 * Creates a new image record from a file already inplace on the filesystem
	 * @param string $file_name name of the file that has been placed in the image uploads dir
	 * @return int image id for the file on success, else false
	 */
	function create_image_from_file($image_path_and_filename)
	{
		$image_mime_type = mime_content_type($image_path_and_filename);
		
		if(image::is_image_mime_type($image_mime_type) && file_exists($image_path_and_filename))
		{
			$this->set_mime_type($image_mime_type, basename($image_path_and_filename));
			$this->size = filesize($image_path_and_filename);
			$this->filename = basename($image_path_and_filename);
			$this->save();
			
			$image_id = $this->id;
			$this->clear_field_values();
			
			return $image_id;
		}

		return false;		
	}

	/**
	 * Creates a new image record from a URL
	 * @param string $image_url Absolute URL of the image file that needs to be uploaded
	 * @return int image id for the file on success, else false
	 * @see image::create_image_from_file
	 */
	function create_image_from_url($image_url)
	{
		//Get a unique name for the file
		$image_filename = basename($image_url);
		$file_extension = image::get_file_extension($image_filename);
		
		if(image::does_uploaded_image_file_exist($image_filename))
			$image_filename = image::generate_unique_filename($file_extension);
		
		$uploaded_path_and_filename = IMAGE_UPLOADS_DIR . '/' . $image_filename;
		
		//Upload the file
		if(!copy($image_url,$uploaded_path_and_filename))
			return false;

		//Create database record of the image and return the image id
		return $this->create_image_from_file($uploaded_path_and_filename);		
	}
	
	/**
	 * Tests if a file exists as an uploaded image
	 * @param string $filename local filename of the image to test for in product uploads dir
	 * @return boolean true if file exists, else false
	 * @see file_exists
	 */
	function does_uploaded_image_file_exist($filename)
	{
		return file_exists(IMAGE_UPLOADS_DIR . '/' . $filename);
	}
		
	/**
	 * Checks for PHP thrown upload errors, returns true if upload successfull else false 
	 * and assigns and error
	 * @param int $upload_error_code PHP upload error code from $_FILES['xxx']['error']
	 * @link http://nz.php.net/manual/en/features.file-upload.errors.php list of error code constants
	 */
	function is_valid_file_upload($upload_error_code)
	{
		if($upload_error_code == UPLOAD_ERR_OK)
			return true;
		else
			return false;
	}
	
	function get_file_upload_error_description($upload_error_code)
	{
		switch($upload_error_code)
		{
		  case UPLOAD_ERR_INI_SIZE:
			return 'The uploaded file is to large, please use a smaller one';
			break; 
		  case UPLOAD_ERR_FORM_SIZE:
			return 'The uploaded file exceeds the maximum allowed file size, please use a smaller one';
			break; 
		  case UPLOAD_ERR_PARTIAL:
			return 'The uploaded file was only partially uploaded, please try again';
			break; 
		  case UPLOAD_ERR_NO_FILE:
			return 'No file was uploaded';
			break; 
		  case UPLOAD_ERR_NO_TMP_DIR:
			return 'System error: Missing a temporary folder! Please contact server administrator';
			break; 
		  case UPLOAD_ERR_CANT_WRITE:
			return 'System error: Failed to write file to disk';
			break; 
		}		
		
		return false;
	}
	
	/**
	 * Checks if a given mime type is of type image
	 * @param string $mime_type raw mime string e.g. image/jpeg
	 * @return bool true if image mime, else false
	 */
	function is_image_mime_type($mime_type)
	{
		$mime_parts = explode('/',$mime_type);
		
		if($mime_parts[0] == 'image')
			return true;
		else
			return false;	
	}
	
	/**
	 * Resizes an image to width x height with no proportion contraints
	 * @param int $resized_width width in pixels to resize the image to
	 * @param int $resized_height height in pixels to resize the image to
	 * @param string $filename Image filename only within the image uploads path, e.g. test.jpg
	 * @return binary Resized image data, does not write to image file
	 */
	function resize_image($resized_width, $resized_height, $image_path_and_filename)
	{
		//Check that we have a valid image to generate a thumbnail from
		if(!file_exists($image_path_and_filename))
		{
			trigger_error('Image does not exist in resize_image for: '.$image_path_and_filename,E_USER_WARNING);
			return false;
		}
		
		//Check that we have a valid GD version install
		if(!function_exists('imagecopyresampled'))
	    	trigger_error('GD Version 2.0.28 or greater is required. The function imagecopyresampled does not exist!',E_USER_ERROR);
	    
		//Get the current image attributes and load its data
		list($image_width, $image_height, $image_type, $image_attr) = getimagesize($image_path_and_filename);
        
        $image_handle = image::load_image_from_file($image_path_and_filename);
        
        $x_proportion = $image_width / $resized_width;
        $y_proportion = $image_height / $resized_height;
        
        //If no resizing is required
        if ($x_proportion <= 1 && $y_proportion <= 1) 
        	return $image_handle;
        else
        {
        	//Scale By X and Y
        	$resized_image_canvas = imagecreatetruecolor($resized_width, $resized_height);

			//Try to copy a resampled version (best quality), if that fails just copy by pixel (less quality)
	     	if(!imagecopyresampled($resized_image_canvas, $image_handle, 0, 0, 0, 0, $resized_width, $resized_height, $image_width, $image_height))
	        	imagecopyresized($resized_image_canvas, $image_handle, 0, 0, 0, 0, $resized_width, $resized_height, $image_width, $image_height);
	        	
	        return $resized_image_canvas;
        }	
	}

	/**
	 * Resizes an image to fit within a rectangular boundry
	 * @param int $max_width maximum width in pixels to contrain the image to
	 * @param int $max_height maximum height in pixels to constrain the image to
	 * @param string $filename Image filename only within the image uploads path, e.g. test.jpg
	 * @return binary Resized image data, does not write to image file
	 */
	function resize_image_to_fit($max_width, $max_height, $image_path_and_filename)
	{
		//Check that we have a valid image to generate a thumbnail from
		if(!file_exists($image_path_and_filename))
			return false;

		//Get the current image attributes and load its data
		list($image_width, $image_height, $image_type, $image_attr) = getimagesize($image_path_and_filename);
     
		$resized_image_dimensions = get_calculated_resized_to_fit_dimensions($image_width, $image_height, $max_width, $max_height);
        
        return image::resize_image($resized_image_dimensions['width'], $resized_image_dimensions['height'], $image_path_and_filename);
	}
	
	/**
	 * Creates a rectangular image by first resizing to the longest side, and then cropping it down to the required dimensions
	 * @param int $width_and_height maximum width and height the image should be
	 * @param int $image_path_and_filename absolute path and filename of the src image to be resized
	 */
	function resize_image_and_crop($width, $height, $image_path_and_filename)
	{
		//Check that we have a valid src image
		if(!file_exists($image_path_and_filename))
			return false;
	    
		//Get the src image dimensions
		list($image_width, $image_height, $image_type, $image_attr) = getimagesize($image_path_and_filename);
        
        $original_image_canvas = image::load_image_from_file($image_path_and_filename);
        
        $x_proportion = $image_width / $width;
        $y_proportion = $image_height / $height;
        
        //If no resizing is required
        if ($x_proportion <= 1 && $y_proportion <= 1) 
        	return $original_image_canvas;
        elseif ($x_proportion <= $y_proportion) 
        {
        	//Scale By X
            $resize_height_to = round(($width / $image_width) * $image_height, 0);
        	
        	//Resize the image to the longest side
        	$resized_image_canvas = image::resize_image($width, $resize_height_to, $image_path_and_filename);
	    }
        else 
        {
        	//Scale By Y
        	$resize_width_to = round(($height / $image_height) * $image_width, 0);
			
			//Resize the image to the longest side
			$resized_image_canvas = image::resize_image($resize_width_to, $height, $image_path_and_filename);
        }	

		//Now crop the image
		$cropped_image_canvas = imagecreatetruecolor($width, $height);
		imagecopy($cropped_image_canvas, $resized_image_canvas, 0, 0, 0, 0, $width, $height);
			        
        return $cropped_image_canvas;
	}
		
	/**
	 * Loads an image from the filesystem and returns a GD handle
	 * @param string $image_path_and_filename Absolute path to image including the filename
	 * @return resource/bool Image resource GD handle on success, else false
	 */
	function load_image_from_file($image_path_and_filename)
	{
		//Get the image format by extension or mime type
		$image_format = array_pop(explode('.',$image_path_and_filename));
		
		if(empty($image_format) || strlen($image_format) < 3 || strlen($image_format) > 4)
			$image_format = array_pop(explode('/',mime_content_type($image_path_and_filename)));
		
		switch($image_format)
		{
		  case 'gif':
			$image_resource = imagecreatefromgif($image_path_and_filename);
		  break;
		  
		  case 'jpg':
		  case 'jpeg':
		  	$image_resource = imagecreatefromjpeg($image_path_and_filename);
		  break;
		  
		  case 'png':
		  	$image_resource = imagecreatefrompng($image_path_and_filename);
		  break;
		  
		  default:
		  	trigger_error('Loading of image '.$image_path_and_filename.' failed. Image type not supported',E_USER_ERROR);
		  	return false;
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
	function get_calculated_resized_to_fit_dimensions($current_width, $current_height, $max_width, $max_height)
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
	function save_image_resource_to_file($image_resource, $image_path_and_filename)
	{
		if(imagejpeg($image_resource, $image_path_and_filename,80))
			return true;

		trigger_error('Saving of image '.$image_path_and_filename.' failed. Image type not supported',E_USER_ERROR);
		return false;
	}
	
	/**
	 * Returns the file extension for a filename
	 * @param string $filename takes any type of filename, with or without path
	 * @return string file extension without the . (dot) e.g jpg
	 */
	function get_file_extension($filename)
	{
    	$path_info = pathinfo($filename);
    	
    	return $path_info['extension'];	
	}
	
	/**
	 * Sets the mime type of the image from a mime type string, e.g. image/gif if that fails we then try to use the filename extension if given
	 * @param string $mime mime type string to set
	 * @param string $filename optional filename of the original file so we can use its extension to determine mime type if the mime header files
	 * @return string mime type extension which was set, null if not found
	 */
	function set_mime_type($mime, $filename = false)
	{
		$mime_type =& model::create('mime_type');
		
		if($mime_type->find_by_field('type',$mime))
			$this->mime_type_id = $mime_type->id;
		else if($filename)
		{
			//Extract mime type from file extension (a bit dodge but ok)
			$file_extension = array_pop(explode('.',$filename));
			if($mime_type->find_by_field('extension',$file_extension))
				$this->mime_type_id = $mime_type->id;
			else
			{
				$this->_errors[] = 'Could not find image mime type, please upload a valid image file.';
				trigger_error('Failed to set mime type of image',E_USER_WARNING);
			}
		}
		else
			$this->_errors[] = 'Could not find image mime type, please upload a valid image file.';
		
		return $mime_type->extension;
	}

	function after_save()
	{
		cache::clear_cache_groups_from_cache_id('image');
	}
		
	function delete($id, $image_dir = IMAGE_UPLOADS_DIR, $delete_image_from_filesystem = true)
	{
		if($delete_image_from_filesystem && $this->find($id))
		{
			//Delete image from the filesystem 
			$image_path_and_filename = $image_dir . '/' .$this->filename;
			
			if(file_exists($image_path_and_filename) && is_writable($image_path_and_filename))
				unlink($image_path_and_filename);
			else
				trigger_error('File does not exist or is not writable for deletion: '.$image_path_and_filename,E_USER_WARNING);

			//Delete dynamically resized cache files
			$filename_split_by_dot = explode('.',$this->filename);
			array_pop($filename_split_by_dot); //Discard file extension
			$filename_without_extension = implode('.',$filename_split_by_dot);
					
			foreach (glob(IMAGE_CACHE_DIR . '/' . $filename_without_extension . '.*') as $cached_filename)
				unlink($cached_filename);
		}
		
		cache::clear_cache_groups_from_cache_id('image');
		
		//Delete image record from DB
		return parent::delete($id);
	}
}

?>