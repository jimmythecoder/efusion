<?php
/**
 * Shipping zone partitions
 * 
 * @package efusion
 * @subpackage models
 */
class shipping_zone extends model
{
	/**
	 * Primary key
	 * @var int
	 */
	var $id;
	
	/**
	 * Shipping zone name (Based on delivery point to point distance), e.g. Across town or International
	 * @var string
	 */
	var $name;
	
	/**
	 * Name to display to the customer for this zone
	 */
	var $display_name;
	
	
	function validate()
	{
		$this->validates_presence_of('name');
		
		if($this->name)
			$this->validates_uniqueness_of('name');
		
		return parent::validate();
	}
	
	function find_by_name($name)
	{
		return $this->find_by_field('name',$name);
	}
	
	/**
	 * Finds the correct shipping zone record to use for a delivery to a city and a country
	 */
	function find_by_city_and_country($ship_to_city_name, $ship_to_country_id)
	{
		$ship_from_city_name 	= config::get('shipping','city');
		$ship_from_country_id	= config::get('shipping','country');
		
		$obj_ship_from_country 	=& model::create('country');
		$obj_ship_to_country 	=& model::create('country');
		
		if(!$obj_ship_from_country->find($ship_from_country_id))
			$this->_errors[] = 'Could not find ship from country';
			
		if(!$obj_ship_to_country->find($ship_to_country_id))
			$this->_errors[] = 'Could not find ship to country';
		
		//Is same city
		if(strcasecmp(trim($ship_to_city_name), trim($ship_from_city_name)) == 0)
			return $this->find(SHIP_SAME_CITY);
		else if($ship_to_country_id == $ship_from_country_id)
			return $this->find(SHIP_SAME_COUNTRY);
		else if($obj_ship_to_country->region_id == $obj_ship_from_country->region_id)
			return $this->find(SHIP_SAME_REGION);
		else
		{
			//Match by region
			$obj_region =& model::create('region');
			$obj_region->find($obj_ship_to_country->region_id);
			
			return $this->find_by_name($obj_region->name);
		}
	}
}

?>