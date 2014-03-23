#!/usr/bin/php
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
		if($first_argument == 'all' || $first_argument == '-a')
		{
			cache::delete_all();
			echo 'All cache has been cleared';
		}
		else
		{
			if(cache::delete_cache_group($first_argument))
				echo 'Cache group ' . $first_argument . ' has been cleared';
			else
				echo 'Cache group ' . $first_argument . ' could not be cleared, usually because this cache group does not exist or is not writable...';
		}
	}
	else
		echo "Usage: php clear_cache.php (all|[cache group name]) \n  e.g. php clear_cache.php product";
	
	echo "\n";
		
?>
