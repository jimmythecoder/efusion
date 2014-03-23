<?php
/**
 * Configuration model, uses ini files for storage rather than the DB
 * Requires on cache and constants
 * 
 * @package efusion
 * @subpackage models
 */
class configuration
{
	/**
	 * What environment we are working from
	 */
	var $environment;
	
	/**
	 * Section for this config item
	 * @var string
	 */
	var $section;
	
	/**
	 * Entry name
	 * @var string
	 */
	var $key;
	
	/**
	 * Value for config entry
	 * @var string
	 */
	var $value;
	
	/**
	 * Array of errors 
	 * @var array
	 */
	var $_errors;
	
	/**
	 * Validates the configuration record
	 */
	function validate()
	{
		//Validate environment config file actually exists
		$environment_files 	= glob(ENVIRONMENTS_DIR . '/*' . CONFIG_FILE_EXTENSION);
		$valid_environments = array();
		
		foreach($environment_files as $path_and_filename_of_config_file)
			$valid_environments[] = basename($path_and_filename_of_config_file,CONFIG_FILE_EXTENSION);
		
		if(!in_array($this->environment,$valid_environments))
			$this->_errors[] = 'Invalid environment settings of [' . $this->environment . ']. Must be one of [' . implode(', ',$valid_environments) . ']';
	
		//Validate required fields
		if(empty($this->section))
			$this->_errors[] = 'Config section is required';
			
		if(empty($this->key))
			$this->_errors[] = 'Config key is required';
					
		return empty($this->_errors);
	}
	
	function save()
	{
		if(!$this->validate())
			return false;
		
		$config_entries = $this->load_configuration_file($this->environment);
		
		$config_entries[$this->section][$this->key] = $this->value;
			
		if($this->save_all_entries_from_array($config_entries))
			cache::clear_cache_groups_from_cache_id('configuration');
		else
			return false;
	}
	
	/**
	 * Writes out the entire configuration file
	 * @param array $config_entries associative array of config params e.g. 
	 *   array('core' => array('gst' => 0.125, 'timezone' => 'EST'))
	 * @return boolean true on successfull save else false
	 */
	function save_all_entries_from_array($config_entries)
	{
		$config_file_path_and_filename = ENVIRONMENTS_DIR . '/' . $this->environment . CONFIG_FILE_EXTENSION;
	
		if(file_exists($config_file_path_and_filename))
		{
			if(is_writable($config_file_path_and_filename))
			{
				if($this->write_array_to_ini_file($config_file_path_and_filename, $config_entries))
					return true;
				else
					$this->_errors[] = 'Could not write config file to [' . $config_file_path_and_filename . ']';
			}
			else
				$this->_errors[] = 'Environment file [' . $config_file_path_and_filename . '] is not writable';
		}
		else
			$this->_errors[] = 'Environment file [' . $config_file_path_and_filename . '] does not exist';
			
		return false;
	}
	
	function load_configuration_file($environment)
	{
		$config_file_path_and_filename = ENVIRONMENTS_DIR . '/' . $environment . CONFIG_FILE_EXTENSION;
	
		if(file_exists($config_file_path_and_filename))
		{
			if(is_readable($config_file_path_and_filename))
				return parse_ini_file($config_file_path_and_filename,true);
			else
				$this->_errors[] = 'Environment file [' . $config_file_path_and_filename . '] is not readable';
		}
		else
			$this->_errors[] = 'Environment file [' . $config_file_path_and_filename . '] does not exist';		
	
		return false;
	}
	
	function get_errors()
	{
		return $this->_errors;
	}
	
	function reset_errors()
	{
		$this->_errors = array();
	}
	
	/**
	 * Writes an associative array out to an .ini file
	 */
	function write_array_to_ini_file($path_and_filename, $assoc_array)
	{
	   $content 	= '';
	   $sections 	= '';
	
	   foreach ($assoc_array as $key => $item)
	   {
	       if (is_array($item))
	       {
	           $sections .= "[{$key}]\n";
	           foreach ($item as $key2 => $item2)
	           {
	               if (is_numeric($item2) || is_bool($item2))
	                   $sections .= $key2 . ' = '.(string)$item2."\n";
	               else
	                   $sections .= $key2.' = "'.$item2.'"'."\n";
	           }     
	       }
	       else
	       {
	           if(is_numeric($item) || is_bool($item))
	               $content .= $key.' = '.(string)$item ."\n";
	           else
	               $content .= "{$key} = \"{$item}\"\n";
	       }
	   }     
	
	   $content .= $sections;
	
	   if (!$handle = fopen($path_and_filename, 'w'))
	       return false;
	  
	   if (!fwrite($handle, $content))
	       return false;
	  
	   fclose($handle);
	   
	   return true;
	} 
}
?>