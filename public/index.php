<?php
/**
 * @package eFusion
 * @description eCommerce shopping cart system
 * @author James Harris
 */
 	define('PRODUCT_KEY','EF10-69B1-1047-720E-E785');
 
 	//We escape quoted strings ourselves
 	@ini_set('magic_quotes_runtime', 0);
 	
	//Load environment configuration
	if(!($ENVIRONMENT = getenv('ENVIRONMENT')))
		$ENVIRONMENT = 'production';

	$site_root_dir = realpath(dirname(__FILE__).'/../');
	define('SITE_ROOT_DIR',$site_root_dir);
	define('ENVIRONMENT',$ENVIRONMENT);
	define('IS_DEVELOPMENT_ENV',$ENVIRONMENT == 'development');
	define('IS_PRODUCTION_ENV',$ENVIRONMENT == 'production');
	define('IS_SCRIPT',!empty($argv[0]));
	
	//Application components	
	require SITE_ROOT_DIR . '/config/constants.php';
	require CORE_DIR . '/model.php';
	require CORE_DIR . '/controller.php';
	require CORE_DIR . '/smarty/Smarty.class.php';
	require CORE_DIR . '/localization.php';
	require CORE_DIR . '/application.php';
	require CORE_DIR . '/singleton.php';
	require CONFIG_DIR . '/routes.php';
	include CONTROLLERS_DIR . '/application_controller.php';
	
	//Helpers
	require HELPERS_DIR . '/logger.php';
	require HELPERS_DIR . '/http.php';
	require HELPERS_DIR . '/config.php';
	require HELPERS_DIR . '/cache.php';
	
	//Get URL path to map
	if(isset($_GET['request']))
	{
		//Remove trailing slash
		if (substr( $_GET['request'], -1 ) == '/') 
			$url_path_to_map = substr( $_GET['request'], 0, -1 );
		else
			$url_path_to_map = $_GET['request'];
	}
	else
		$url_path_to_map = '';
			
	//Map the URL to a controller/action
	$route =& new route();

	if(!$mapped_url_route = $route->map_url_route($url_path_to_map))
    	HTTP::exit_on_header(404);

	//Configure application
	$database_config_file 			= parse_ini_file(CONFIG_DIR . '/database.ini',true);
	$database_config 				= $database_config_file[ENVIRONMENT];
   	$db_driver_path_and_filename 	= DB_DRIVERS_DIR . '/' . $database_config['adapter'] . '.php';
   	
    if(file_exists($db_driver_path_and_filename))
    	require $db_driver_path_and_filename;
    else
    	trigger_error('Driver unsupported in ' . CONFIG_DIR . '/database.ini',E_USER_ERROR);
    	
    //Initialize and process application
	$db =& new db();
	$db->connect($database_config);
    $application =& new application($db, $mapped_url_route);
	$application->execute();
?>