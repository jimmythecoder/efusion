<?php
/**
 * Newsletter List. A List/Group name for a regular newsletter sendout, e.g. special offers
 */
class newsletter_list extends model
{
	/**
	 * Primary key
	 * @var int
	 */
	var $id;
	
	/**
	 * Name of the newsletter list
	 * @var string
	 */
	var $name;
	 
	/**
	 * A simple description of what these newsletters are about
	 * @var string
	 */
	var $description;
	
	/**
	 * Is this list still in use
	 */
	var $is_active;
	
	function validate()
	{
		$this->validates_presence_of('name','Please enter a name for this newsletter list');
	}
}
?>