<?php
/**
 * Search index keywords
 * 
 * @package efusion
 * @subpackage models
 */
class keyword extends model
{
	/**
	 * Primary key
	 * @var int
	 */
	var $id;
	
	/**
	 * Single word representing a search keyword
	 * @var string
	 */
	var $keyword;
	
	
	function validate()
	{
		$this->validates_presence_of('keyword');
		
		if($this->keyword)
			$this->validates_uniqueness_of('keyword');
		
		return parent::validate();
	}
}

?>