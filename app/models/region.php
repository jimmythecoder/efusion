<?php
/**
 * Regions of the World, used for shipping calculations
 * 
 * @package efusion
 * @subpackage models
 */
class region extends model
{
	/**
	 * Primary key
	 * @var int
	 */
	var $id;
	
	/**
	 * Region name
	 * @var string
	 */
	var $name;
	
	function validate()
	{
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
		$regions = $this->find_all(array('order' => 'name'));
		
		$options = array();
		
		foreach($regions as $id => $region)
			$options[$id] = $region['name'];
		
		return $options;
	}
}
?>