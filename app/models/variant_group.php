<?php
/**
 * Product variation group/set (size, colour, brand etc)
 * 
 * @package efusion
 * @subpackage models
 */
class variant_group extends model
{
	/**
	 * Primary key
	 * @var int
	 */
	var $id;
	
	/**
	 * Name of the variation group
	 * @var string
	 */
	var $name;

	/**
	 * Label to appear on the product page for this option
	 * @var string
	 */
	var $label;
		
	/**
	 * A short description of the variation group
	 * @var string
	 */
	var $description;
	
	
	function validate()
	{
		$this->validates_presence_of('name');
		$this->validates_presence_of('label');
		
		return parent::validate();
	}
	
	function after_save()
	{
		cache::clear_cache_groups_from_cache_id('variant_group');
	}
	
	function delete($id)
	{
		//Delete cascade all product variants and product variant groups
		$this->execute_sql_query('DELETE FROM product_variant WHERE variant_group_id = ?',array($id));
		
		$this->execute_sql_query('DELETE FROM product_variant_group WHERE variant_group_id = ?',array($id));
		
		cache::clear_cache_groups_from_cache_id('variant_group');
		
		return parent::delete($id);
	}
}

?>