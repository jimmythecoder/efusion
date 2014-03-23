<?php
/**
 * @package efusion
 * @subpackage config
 * 
 * Global application constants
 */
	 
	//Static content pages
	define('HOME_PAGE_CONTENT_ID',1);
	define('TERMS_AND_CONDITIONS_CONTENT_ID',2);
	define('PRIVACY_POLICY_CONTENT_ID',4);
	define('CONTACT_US_CONTENT_ID',3);
	define('ERROR_404_CONTENT_ID',5);
	define('CONFIRM_ORDER_CONTENT_ID',6);
	define('ORDER_COMPLETED_CONTENT_ID',7);
	 
	//Core application settings
	define('ROOT_CATEGORY_NODE',0);
	define('ENABLE_QUERY_LOGGING',0);
	define('MIN_PASSWORD_LENGTH',5);
	define('MAX_ADDRESS_BOOKS_ALLOWED',5);
	define('MAX_BRUTEFORCE_ATTEMPTS',10);
	define('BRUTEFORCE_BAN_DURATION',172800);	//48hrs
	define('HOME_PAGE_LATEST_PRODUCTS',6);
	define('DEFAULT_AVERAGE_PRODUCT_RATING',3);
	define('DEFAULT_IMAGE_ID',1);
	define('WEBMASTER_EMAIL_ADDRESS','webmaster@domain.com');
	define('CHART_WIDTH',600);
	define('CHART_HEIGHT',300);
	define('EMAIL_REGEX','/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/');
	define('CORE_RSS_ENTRIES',30);	//Number of products to load in RSS feed
	define('CORE_PAGINATION_PAGES',10);
	define('CORE_PASSWORD_SALT','text used to obscure a password hash');
	define('CONFIG_FILE_EXTENSION','.ini');

	//Dir paths
	define('APPLICATION_DIR', SITE_ROOT_DIR . '/app');
	define('CORE_DIR', APPLICATION_DIR . '/core');
	define('CONTROLLERS_DIR', APPLICATION_DIR . '/controllers');
	define('MODELS_DIR', APPLICATION_DIR . '/models');
	define('VIEWS_DIR', APPLICATION_DIR . '/views');
	define('VIEW_LAYOUTS_DIR', VIEWS_DIR . '/layouts');
	define('DB_DRIVERS_DIR', CORE_DIR . '/db_drivers');
	define('HELPERS_DIR', CORE_DIR . '/helpers');
	define('LOGS_DIR', SITE_ROOT_DIR . '/logs');
	define('TEMP_DIR', SITE_ROOT_DIR . '/tmp');
	define('LIBRARY_DIR', SITE_ROOT_DIR . '/lib');
	define('SCRIPTS_DIR', SITE_ROOT_DIR . '/scripts');
	define('CONFIG_DIR', SITE_ROOT_DIR . '/config');
	define('IONCUBE_DIR', SITE_ROOT_DIR . '/ioncube');
	define('DB_DIR', SITE_ROOT_DIR . '/db');
	define('MIGRATIONS_DIR', DB_DIR . '/migrations');
	define('PUBLIC_DIR', SITE_ROOT_DIR . '/public');	
	define('IMAGE_DIR', PUBLIC_DIR . '/images');
	define('IMAGE_UPLOADS_DIR', IMAGE_DIR . '/products');
	define('BANNER_UPLOADS_DIR', IMAGE_DIR . '/banners');
	define('IMAGE_CACHE_DIR', IMAGE_DIR . '/cache');
	define('CACHE_DIR', TEMP_DIR . '/cache');
	define('COMPILED_TEMPLATES_DIR', TEMP_DIR . '/templates_c');
	define('VENDOR_DIR', SITE_ROOT_DIR . '/vendor');
	define('MODULES_DIR', VENDOR_DIR . '/modules');
	define('ENVIRONMENTS_DIR', CONFIG_DIR . '/environments');
	
	//Cache constants
	define('CACHE_EXTENSION','.php');	//File extension to use for cached files
	define('CACHE_LIFETIME',86400); 	//Max cache file lifetime in seconds
	define('CACHE_ENABLED',1);			//Enable caching
	
	//Session constants
	define('SESSION_LIFETIME',"7200");	//Time in seconds the session is valid for without being accessed
	define('SESSION_GC_PROBABILITY',"10");//Probability as a percentage that a session has expired by the time we call session_start
	
	//Default database adapter ports
	define('DEFAULT_PSQL_PORT',5432);
	define('DEFAULT_MYSQL_PORT',3306);

	//Default email templates
	define('EMAIL_NEW_PASSWORD',1);
	define('EMAIL_CONTACT_US',2);
	define('EMAIL_NEW_ACCOUNT',3);
	define('EMAIL_ORDER_COMPLETE',4);
	define('EMAIL_ORDER_UPDATED',5);
	define('EMAIL_ACTIVATE_EMAIL',6);
	
	//Shipping information
	define('SHIP_SAME_CITY',1);
	define('SHIP_SAME_COUNTRY',2);
	define('SHIP_SAME_REGION',3);
	define('DEFAULT_COUNTRY_ID',125);
	
	//Encryption Block Mode constants
	define('ENCRYPT_MODE_ECB',0);
	define('ENCRYPT_MODE_CBC',1);
	
	//Payment gateway
	define('PAYMENT_GATEWAY_MAX_BYTES_TO_READ',2000000);
	define('PAYMENT_GATEWAY_READ_BYTE_SIZE',128);
	define('PAYMENT_GATEWAY_DIR',SITE_ROOT_DIR . '/lib/payment_gateway');
	
	//Google maps geocoding
	define('GOOGLE_MAPS_GEOCODE_URL','http://maps.google.com/maps/geo');
?>