<?php
/**
 * Product variation group (eg. size, colour)
 * 
 * @package efusion
 * @subpackage models
 */
class product_variant_group extends model
{
	/**
	 * Primary key
	 * @var int
	 */
	var $id;
	
	/**
	 * Foriegn key to the product table 
	 * @var int
	 */
	var $product_id;
	
	/**
	 * Foriegn key to the variant_group table
	 * @var int
	 */
	var $variant_group_id;
	
	
	function validate()
	{
		$this->validates_foriegnkey_exists('product_id');
		$this->validates_foriegnkey_exists('variant_group_id');
		
		return parent::validate();
	}
	
	/**
	 * Finds all the variant group ids that exist for this product
	 * @param int $product_id Product id
	 * @return array variant group table id's
	 */
	function find_all_variant_groups_for_product($product_id)
	{
		$product_id_as_int = (int)$product_id;
		
		return $this->find_all(array('select' 	=> 'variant_group.id, variant_group.name, variant_group.label',
									 'join' 	=> 'INNER JOIN variant_group ON variant_group.id = product_variant_group.variant_group_id',
									 'where' 	=> 'product_id = ' . $product_id_as_int));
	}
	
	function after_save()
	{
		cache::clear_cache_groups_from_cache_id('product_variant_group');
	}
	
	/**
	 * Deletes all the product variant groups for a given product id
	 * @param int $product_id primary key for product table
	 */
	function delete_all_product_variant_groups_for_product_id($product_id)
	{
		$this->execute_sql_query('DELETE FROM ' . $this->get_table_name() . ' WHERE product_id = ?',array($product_id));
		cache::clear_cache_groups_from_cache_id('product_variant_group');
	}
}

?>