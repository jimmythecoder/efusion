#!/usr/bin/php
<?php
	ini_set('display_errors',1);
	
	//Load environment configuration
	if(!($ENVIRONMENT = getenv('ENVIRONMENT')))
		$ENVIRONMENT = 'production';
		
	$site_root_dir = realpath(dirname(__FILE__).'/../');
	define('SITE_ROOT_DIR',$site_root_dir);
	define('ENVIRONMENT',$ENVIRONMENT);

	require SITE_ROOT_DIR . '/config/constants.php';
	require SITE_ROOT_DIR . '/app/core/model.php';
	require SITE_ROOT_DIR . '/app/core/singleton.php';
	require SITE_ROOT_DIR . '/app/core/helpers/cache.php';
	require SITE_ROOT_DIR . '/app/core/helpers/http.php';
	require SITE_ROOT_DIR . '/app/core/helpers/config.php';
	
	$database_config_file = parse_ini_file(SITE_ROOT_DIR . '/config/database.ini',true);
	$database_config = $database_config_file[$ENVIRONMENT];
   
    if($database_config['adapter'] == 'postgresql')
    	require SITE_ROOT_DIR . '/app/core/db_drivers/postgresql.php';
    else
    	require SITE_ROOT_DIR . '/app/core/db_drivers/mysql.php';
    	
    //Initialize and process application
	$db =& new db();
	$db->connect($database_config);
			
	$geocoding_api_key = config::get('core','google_maps_api_key');
	$geocoding_post_url = GOOGLE_MAPS_GEOCODE_URL . '?key=' . $geocoding_api_key . '&output=csv&q=';
	
	$address_book_obj =& model::create('address_book');
	$address_books_with_no_geocoding = $address_book_obj->find_all(array('select' => 'country.name AS country_name, address_book.*','where' => 'longitude IS NULL OR latitude IS NULL','join' => 'INNER JOIN country ON address_book.country_id = country.id'));
	
	foreach($address_books_with_no_geocoding as $id => $address_book)
	{
		$fully_qualified_address_on_1_line = get_address_in_csv_format($address_book);
	
		$url = $geocoding_post_url . urlencode($fully_qualified_address_on_1_line);
		
		echo "Geocoding address: $fully_qualified_address_on_1_line \n";
		
		if(!$geocoding_data = HTTP::post_request(array(),$url))
			continue;
		
		echo "Geocode response: $geocoding_data\n";
			
		list($status_code, $accuracy, $latitude, $longitude) = explode(',',$geocoding_data);
		
		if(!$address_book_obj->find($id))
			exit('Could not find address book id: ' . $id);
		
		$address_book_obj->latitude = $latitude;
		$address_book_obj->longitude = $longitude;
		
		if(!$address_book_obj->save())
			exit(print_r($address_book_obj,true));
		
		$address_book_obj->clear_field_values();
	}
	
	function get_address_in_csv_format($address_book)
	{
		$address = array();
		$address[] = $address_book['street'];
		$address[] = $address_book['suburb'];
		$address[] = $address_book['city'];
		$address[] = $address_book['country_name'];
		
		return implode(',',$address);
	}
?>