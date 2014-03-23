#!/usr/bin/php
<?php
/**
 * These website scrapers are experimental and only compatable with PHP 5 and above. 
 * It requires php tidy extension aswell as the simplexml extension
 * eFusion scraper, demo one which extracts the product data from the full product list
 */

define('URL','http://dev.efusion.co.nz/show-all-products');

$tidy_config = array();
$tidy_config["show-body-only"] = true;
$tidy_config['output-xhtml'] = true;

$clean_html_data = tidy_repair_file(URL,$tidy_config,"utf8");
$clean_html_data = trim($clean_html_data);

$clean_html_data = html2txt($clean_html_data);

$obj_html = simplexml_load_string($clean_html_data);

$price_list = array();

foreach($obj_html->div[3]->div->table->tr as $row_number => $table_row)
{
	if(isset($table_row->td))
	{
		$name = (string)$table_row->td[0]->a;
		$name = trim($name);
		$name = preg_replace('/[^a-z0-9]+/i',' ',$name);
		
		$price = (string)$table_row->td[1];
		$price = preg_replace('/[^0-9\.]+/','',$price);
		
		$price_list[] = array('name' => $name,'price' => $price);
	}
}

print_r($price_list);

function html2txt($document)
{
	$search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
	               '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
	               '@<![\s\S]*?--[ \t\n\r]*>@'        // Strip multi-line comments including CDATA
	);
	
	$text = preg_replace($search, '', $document);
	
	return $text;
}
?>