<?php
/**
 * Country names in the World
 * 
 * @package efusion
 * @subpackage models
 */
class country extends model
{
	/**
	 * Primary key
	 * @var int
	 */
	var $id;
	
	/**
	 * FK to region table - Country region (Oceana, Asia etc...)
	 * @var int
	 */
	var $region_id;
	
	/**
	 * Name of city
	 * @var string
	 */
	var $name;
	
	/**
	 * Formal name of the country
	 * @var string 
	 */
	var $formal_name;
	
	/**
	 * Capital of the country
	 * @var string 
	 */
	var $capital;
	
	/**
	 * Currency code (NZD)
	 * @var string
	 */
	var $currency_code;
	
	/**
	 * Formal name for the currency
	 * @var string
	 */
	var $currency_name;
	
	/**
	 * Telephone number prefix
	 * @var string
	 */
	var $telephone_preifx;
	
	/**
	 * Extension used for this country e.g. .co.nz
	 * @var string
	 */
	var $domain_extension;
	
	/**
	 * Preferred sort order to show the countries in a list
	 */
	var $sort_order;
	
	function validate()
	{
		$this->validates_foriegnkey_exists('region_id');
		
		if($this->validates_presence_of('name'))
			$this->validates_uniqueness_of('name');
		
		return parent::validate();
	}
	
	function find_by_name($name)
	{
		return $this->find_by_field('name',$name);
	}
	
	function get_countries_as_list()
	{
		$countries = $this->find_all(array('order' => 'sort_order DESC, name'));
		
		$options = array();
		
		foreach($countries as $id => $country)
			$options[$id] = $country['name'];
		
		return $options;
	}

	/**
	 * Finds all countries that belong to a region, searches by region id
	 * @param string $region_name Name of the region
	 * @return boolean/array false if not found, else an associative array of countries
	 */	
	function find_all_countries_by_region_id($region_id)
	{
		return $this->find_all(array('where' => 'region_id = ' . (int)$region_id, 'order' => 'country.name'));
	}
	
	/**
	 * Finds all countries that belong to a region, searches by region name
	 * @param string $region_name Name of the region
	 * @return boolean/array false if not found, else an associative array of countries
	 */
	function find_all_countries_by_region_name($region_name)
	{
		$obj_region =& model::create('region');
		
		if(!$obj_region->find_by_name($region_name))
			return false;
			
		return $this->find_all_countries_by_region_id($obj_region->id);
	}
}
?>