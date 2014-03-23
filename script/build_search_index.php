#!/usr/bin/php
<?php
	ini_set('display_errors',1);
	
	//Load environment configuration
	if(!($ENVIRONMENT = getenv('ENVIRONMENT')))
		$ENVIRONMENT = 'production';
		
	define('SITE_ROOT_DIR',realpath(dirname(__FILE__).'/../'));
	
	require(SITE_ROOT_DIR . '/config/constants.php');
	require(SITE_ROOT_DIR . '/app/core/model.php');

$command = $_SERVER['argv'][1];

	$database_config_file 	= parse_ini_file(SITE_ROOT_DIR . '/config/database.ini',true);
	$database_config 		= $database_config_file[$ENVIRONMENT];
	
	if($database_config['adapter'] == 'mysql')
		require(SITE_ROOT_DIR."/app/core/db_drivers/mysql.php");
	else if($database_config['adapter'] == 'postgresql')
		require(SITE_ROOT_DIR."/app/core/db_drivers/postgresql.php");
	else
		exit('Unsupported adapter in '.SITE_ROOT_DIR.'/config/database.ini');

	//Get our global db connection
	$db =& new db();
	$db->connect($database_config);
	
if($command == 'recreate')
{
	echo "Rebuilding product search indexes (This may take a while)...\n";
	
	$index_count = 0;

	//Remove old indexes
	echo "Removing previous indexes... \n";
	$db->query("TRUNCATE product_keyword");
	$db->query("TRUNCATE keyword");
	
	//Fetch products
	$product 	=& model::create('product');
	$search 	=& model::create('search');
	
	$products 	= $product->find_all();
	
	foreach($products as $id => $product)
	{
		echo 'indexing product: ' . $product['name']. "\n";
		
		$search->add_product_to_search_index($product['id']);
		
		$index_count++;
	}
	
	echo "Search index rebuilt successfully.\n\n";
	echo "Total products indexed: " . $index_count . "\n";
}
else if($command == 'drop')
{
	$search =& model::create('search');
	
	$db->query("TRUNCATE product_keyword");
	$db->query("TRUNCATE keyword");
	
	echo "Search index dropped.\n\n";
}
else
{
	echo "- Rebuild product search indexes\n";
	echo "Usage: php script/build_search_index.php (recreate|drop|help)\n";
	echo ".........................................\n\n";	
}

$db->destroy();
?>
