<?php
/**
 * Feeds controller, loads RSS, Atom feeds for the site
 * 
 * @package efusion
 * @subpackage controllers
 */
class feeds_controller extends application_controller
{
	function feeds_controller(&$application)
	{
		parent::application_controller($application);
		
		$application->content_type = 'text/xml';	
	}
	
	/**
	 * Display an RSS feed for the latest products
	 */
	function rss($limit = null)
	{
		$product =& model::create('product');
		$this->template_data['products'] = $product->find_all(array(
			'select' 	=> 'product.*, image.filename AS image_filename',
			'join' 		=> 'INNER JOIN image ON product.image_id = image.id',
			'where' 	=> 'is_active = 1', 
			'order' 	=> 'created_at DESC', 
			'limit' 	=> $limit ? $limit : CORE_RSS_ENTRIES));
	}
	
	function media()
	{
		$this->rss(100);
	}
}
?>