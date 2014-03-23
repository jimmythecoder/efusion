<?php
/**
 * Account groups, admin, member, anonymous
 * 
 * @package efusion
 * @subpackage models
 */
class group extends model
{
	/**
	 * Primary key
	 * @var int
	 */
	var $id;
	
	/**
	 * Group name
	 * @var string
	 */
	var $name;
	
	
	function validate()
	{
		$this->validates_presence_of('name');
		
		if($this->name)
			$this->validates_uniqueness_of('name');
		
		return parent::validate();
	}
}

?>