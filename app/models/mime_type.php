<?php
/**
 * File system mime types
 * 
 * @package efusion
 * @subpackage models
 */
class mime_type extends model
{
	/**
	 * Primary key
	 * @var int
	 */
	var $id;
	
	/**
	 * Mime type extension e.g. gif, jpg, png without the preceeding full stop
	 * @var string
	 */
	var $extension;
	
	/**
	 * RFC header specification type for this mime, e.g. image/png
	 * @var string
	 */
	var $type;
	
	
	function validate()
	{
		$this->validates_presence_of('extension');
		$this->validates_presence_of('type');
		
		if($this->type)
			$this->validates_uniqueness_of('type');
		
		return parent::validate();
	}
}

?>