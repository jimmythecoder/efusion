<?php
	ini_set('display_errors',1);
	
	//Load environment configuration
	if(!($ENVIRONMENT = getenv('ENVIRONMENT')))
		$ENVIRONMENT = 'production';
		
	define('SITE_ROOT_DIR',realpath(dirname(__FILE__).'/../'));
	
	require(SITE_ROOT_DIR . '/config/constants.php');
	require(SITE_ROOT_DIR . '/app/core/helpers/cache.php');
	
	$first_argument = $_SERVER['argv'][1];

	if(!empty($first_argument))
	{
		$cache_groups = cache::get_cache_group_data();
		
		if($first_argument == 'all' || $first_argument == '-a')
		{
			foreach($cache_groups as $cache_group => $observed_models)
			{
				$cached_files = cache::get_cache_files_in_dir(CACHE_DIR . '/' . $cache_group);
			
				foreach($cached_files as $file)
				{
					$filename_without_extension = basename($file,CACHE_EXTENSION);
					
					if(cache::is_cache_file_expired($file))
						cache::delete_cache_id($filename_without_extension, $cache_group);
				}
			}
		}
		else
		{
			if(array_key_exists($first_argument,$cache_groups))
			{
				$cached_files = cache::get_cache_files_in_dir(CACHE_DIR . '/' . $first_argument);
			
				foreach($cached_files as $file)
				{
					$filename_without_extension = basename($file,CACHE_EXTENSION);
					
					if(cache::is_cache_file_expired($file))
						cache::delete_cache_id($filename_without_extension, $first_argument);
				}
			}
			else
				exit('Cache group `' . $first_argument . '` does not exist!');
		}
		
		echo "Stale cache has been removed\n";
	}
	else
		echo "Usage: php clear_stale_cache.php (all|[cache group name]) \n  e.g. php clear_stale_cache.php product\n";
?>