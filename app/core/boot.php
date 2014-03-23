<?php 		
	//Load environment configuration
	if(!($ENVIRONMENT = getenv('ENVIRONMENT')))
		$ENVIRONMENT = 'production';
	
	define('ENVIRONMENT',$ENVIRONMENT);
	define('IS_DEVELOPMENT_ENV',$ENVIRONMENT == 'development');
	define('IS_PRODUCTION_ENV',$ENVIRONMENT == 'production');
	define('IS_SCRIPT',isset($argc));
	
	if(IS_DEVELOPMENT_ENV)
	{
    	error_reporting(E_ALL & ~E_STRICT);
    	ini_set('display_errors',1);
	}
	else
	{
		error_reporting(0);
    	ini_set('display_errors',0);
	}
	
	@ini_set('magic_quotes_runtime', 0);
			
	//Application components	
	require SITE_ROOT_DIR . '/config/constants.php';
	require CORE_DIR . '/model.php';
	require CORE_DIR . '/singleton.php';

	//Helpers
	require HELPERS_DIR . '/logger.php';
	require HELPERS_DIR . '/http.php';
	require HELPERS_DIR . '/config.php';
	require HELPERS_DIR . '/cache.php';
	require HELPERS_DIR . '/browser.php';
	require HELPERS_DIR . '/math.php';
	require HELPERS_DIR . '/image.php';
	
	//Configure database
	$database_config_file 			= parse_ini_file(CONFIG_DIR . '/database.ini',true);
	$database_config 				= $database_config_file[$ENVIRONMENT];
   	$db_driver_path_and_filename 	= DB_DRIVERS_DIR . '/' . $database_config['adapter'] . '.php';
   	
    if(file_exists($db_driver_path_and_filename))
    	require $db_driver_path_and_filename;
    else
    	throw new Exception('Driver unsupported in ' . CONFIG_DIR . '/database.ini',E_USER_ERROR);
 
    //Initialize and process application
	$db = new db();
	$db->connect($database_config);
