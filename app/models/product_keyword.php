<?php
/**
 * Content keyword frequencies
 * 
 * @package efusion
 * @subpackage models
 */
class product_keyword extends model
{
	/**
	 * Primary key
	 * @var int
	 */
	var $id;
	
	/**
	 * Foriegn key to the keyword table
	 * @var int
	 */
	var $keyword_id;
	
	/**
	 * Foriegn key to the product table
	 * @var int
	 */
	var $product_id;
	
	/**
	 * Frequency that this keyword appears in the product (scaled by field weights)
	 * @var int
	 */
	var $frequency;

	
	function validate()
	{
		$this->validates_foriegnkey_exists('keyword_id');
		$this->validates_foriegnkey_exists('product_id');
		$this->validates_presence_of('frequency');
		$this->validates_uniqueness_of(array('product_id','keyword_id'));
		
		return parent::validate();
	}
}

?>