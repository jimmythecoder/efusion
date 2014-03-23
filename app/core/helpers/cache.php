<?php
/**
 * Cache helper class, performs data caching using files
 * 
 * @package efusion
 * @subpackage helpers
 */
class cache
{
	/**
	 * Checks if a cache id has a valid cache file
	 * @param string $cache_id unique cache id that was used to cache the data
	 * @param string $group_name cache group name this cache file belongs to
	 * @return bool true if cached, else false
	 */
	function is_cached($cache_id, $group_name = null)
	{
		if(!CACHE_ENABLED)
			return false;
		
		$cache_path_and_filename = cache::get_cache_id_path_and_filename($cache_id, $group_name);
		
		if(!file_exists($cache_path_and_filename))
			return false;
		
		return !cache::is_cache_file_expired($cache_path_and_filename);
	}

	/**
	 * Checks if a cached file is expired (stale)
	 * @param string $cache_path_and_filename full path and filename of the cache file to check
	 * @return bool true if cache file has expired, else false
	 */	
	function is_cache_file_expired($cache_path_and_filename)
	{
		//Check for cache time expiry
		if(filemtime($cache_path_and_filename) > (time() - CACHE_LIFETIME))
			return false;
		else
			return true;		
	}
	
		
	/**
	 * Loads the cache data from the given cache id file
	 * @param string $cache_id unique cache id to load
	 * @param string $group_name cache group name this cache file belongs to
	 * @return mixed original cache data on success, else false
	 */
	function get($cache_id, $group_name = null)
	{
		if(!cache::is_cached($cache_id, $group_name))
			return false;
		
		$path_and_filename = cache::get_cache_id_path_and_filename($cache_id, $group_name);
		
		if(!is_readable($path_and_filename))
			return false;
			
		$file_contents = file_get_contents($path_and_filename);
		if($cache_data = @unserialize($file_contents))
			return $cache_data;
		else
			return false;
	}
	
	/**
	 * Saves raw cache data to file
	 * @param mixed $cache_data the data to save to cache
	 * @param string $cache_id unique cache id
	 * @param string $group_name cache group name this cache file belongs to
	 * @return boolean true on success, else false
	 */
	function save($cache_data, $cache_id, $group_name = null)
	{
		$path_and_filename = cache::get_cache_id_path_and_filename($cache_id, $group_name);
			
		$serialized_cache_data = serialize($cache_data);
		
		return cache::write_data_to_file($serialized_cache_data, $path_and_filename);
	}

	/**
	 * Deletes a single cache file
	 * @param string $cache_id cache file id
	 * @param string $group_name cache group name this cache file belongs to
	 * @return bool true if deleted, else false
	 */
	function delete_cache_id($cache_id, $group_name = null)
	{
		$cache_path_and_filename = cache::get_cache_id_path_and_filename($cache_id, $group_name);
		
		if(file_exists($cache_path_and_filename))
		{
			touch(CACHE_DIR);
			
			return unlink($cache_path_and_filename);
		}
		else
			return false;
	}
	
	/**
	 * Loads the cache_groups.ini file and parses it
	 * @return array associative array of cache_groups => observer_models
	 */
	function get_cache_group_data()
	{
		if(isset($_SESSION['cache']['cache_groups']))
			return $_SESSION['cache']['cache_groups'];
		
		$cache_groups_path_and_filename = CACHE_DIR . '/' . 'cache_groups.ini';
		
		//Check the cache group data file exists
		if(!file_exists($cache_groups_path_and_filename))
			return false;
			
		$cache_group_data = parse_ini_file($cache_groups_path_and_filename);	
		
		foreach($cache_group_data as $cache_group => $observer_models)
			$cache_group_data[$cache_group] = explode(',',$cache_group_data[$cache_group]);
		
		$_SESSION['cache']['cache_groups'] = $cache_group_data;
		
		return $cache_group_data;	
	}
	
	/**
	 * Deletes all cache files that belong to a particular cache group
	 * All cache groups have there own cache folder
	 * @param string $group_name cache group name
	 * @return boolean true on success, else false
	 */
	function delete_cache_group($group_name)
	{
		$absolute_cache_group_dir = CACHE_DIR . '/' . $group_name;
		
		if(!file_exists($absolute_cache_group_dir))
			return false;
		
		foreach (cache::get_cache_files_in_dir($absolute_cache_group_dir) as $cached_path_and_filename)
			unlink($cached_path_and_filename);
		
		touch(CACHE_DIR);
		
		return true;
	}

	/**
	 * Finds all cache files that are within a given directory
	 * @param string $absolute_path_name absolute path and dirname to look in
	 * @return array a singular array of absolute cache path and filenames residing in that dir
	 */	
	function get_cache_files_in_dir($absolute_path_name)
	{
		$cached_files = array();
		
		foreach (glob($absolute_path_name . '/*' . CACHE_EXTENSION) as $cached_filename)
			$cached_files[] = $cached_filename;
			
		return $cached_files;
	}
	
	/**
	 * Deletes all cache files from all groups
	 */
	function delete_all()
	{
		$cache_groups = cache::get_cache_group_data();
		
		foreach($cache_groups as $cache_group_name => $observed_models)
			cache::delete_cache_group($cache_group_name);
	}
	
	/**
	 * Retrieves the cache group names the given cache id belongs to
	 * @param string $cache_id cache id to find (aka observed_model)
	 * @return array array of cache group names
	 */
	function get_cache_groups_from_cache_id($cache_id)
	{
		if(!$cache_groups = cache::get_cache_group_data())
			return false;	
		
		$associated_cache_groups = array();
			
		foreach($cache_groups as $cache_group_name => $cache_ids)
		{
			if(in_array($cache_id,$cache_ids))
				$associated_cache_groups[] = $cache_group_name;
		}

		return $associated_cache_groups;
	}
	
	/**
	 * When a model that is being observed by an item in the cache changes
	 */
	function clear_cache_groups_from_cache_id($cache_id)
	{
		$affected_cache_groups = cache::get_cache_groups_from_cache_id($cache_id);
		
		foreach($affected_cache_groups as $cache_group)
			cache::delete_cache_group($cache_group); 
	}

	/**
	 * Retrieves all cache id's which belong in the same groups
	 * @param string $cache_id cache id to find
	 * @return array array of cache i
	 */
	function get_group_related_cache_ids($cache_id)
	{
		if(!$cache_group_data = cache::get_cache_group_data())
			return false;	
		
		$associated_cache_groups = array();
			
		foreach($cache_group_data as $cache_group => $cache_ids)
		{
			if(in_array($cache_id,$cache_ids))
				$associated_cache_groups = array_merge($associated_cache_groups,$cache_ids);
		}

		return array_unique($associated_cache_groups);
	}
		
	/**
	 * Returns an absolute path and filename to the given cache id file
	 * @param string $cache_id file cache id
	 * @param string $group_name cache group name to search for cache under
	 * @return string absolute path and filename to cache file
	 */
	function get_cache_id_path_and_filename($cache_id, $group_name = null)
	{
		return CACHE_DIR . '/' . ($group_name ? $group_name . '/' : '') . md5($cache_id) . CACHE_EXTENSION;
	}
	
	/**
	 * Clone of file_put_contents for PHP 4
	 * @param string $data raw data to write out to the file
	 * @param string $path_and_filename Absolute path and filename to write to
	 * @return bool true if successfull else false
	 */
	function write_data_to_file($data, $path_and_filename)
	{
		if(file_exists($path_and_filename) && !is_writable($path_and_filename))
			return false;
			
		if($file_handle = @fopen($path_and_filename,'wb'))
		{
			fwrite($file_handle,$data);
			fclose($file_handle);
		}
		else
			return false;
			
		touch(CACHE_DIR);
	}
}
?>