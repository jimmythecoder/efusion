<?php
/**
 * Product variation value (eg. small, med, large)
 * 
 * @package efusion
 * @subpackage models
 */
class product_variant extends model
{
	/**
	 * Primary key
	 * @var int
	 */
	var $id;
	
	/**
	 * Foriegn key to the variant group table
	 * @var int
	 */
	var $variant_group_id;
	
	/**
	 * Product variation name to display, e.g. small, medium, large
	 * @var string
	 */
	var $name;
	
	/**
	 * The system value for this variation, must be unique e.g. 1, 2, 3
	 * @var mixed
	 */
	var $value;
	
	
	function validate()
	{
		$this->validates_foriegnkey_exists('variant_group_id');
		$this->validates_presence_of('name');
		$this->validates_presence_of('value');
		
		return parent::validate();
	}
	
	/**
	 * Finds all the variants for a given group
	 * @param int $group_id id of the group to find variants in
	 * @return array array of variant options (variant_id => variant_name)
	 */
	function find_all_variants_in_group($group_id)
	{
		$group_id_as_int = (int)$group_id;
		
		return $this->find_all(array('where' => 'variant_group_id = ' . $group_id_as_int));
	}
	
	function after_save()
	{
		cache::clear_cache_groups_from_cache_id('product_variant');
	}
	
	function delete($id)
	{
		cache::clear_cache_groups_from_cache_id('product_variant');
		
		return parent::delete($id);
	}
}

?>