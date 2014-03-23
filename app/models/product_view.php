<?php
/**
 * Product view statistics
 * 
 * @package efusion
 * @subpackage models
 */
class product_view extends model
{
	/**
	 * Primary key
	 * @var int
	 */
	var $id;
	
	/**
	 * Foriegn key to product table 
	 * @var int
	 */
	var $product_id;
	
	/**
	 * Number of times this product has been viewed today
	 * @var int
	 */
	var $view_count;
	
	/**
	 * The day the counter is valid for on this product
	 * @var date
	 */
	var $viewed_on;
	
	function validate()
	{
		$this->validates_foriegnkey_exists('product_id');
		$this->validates_presence_of('view_count');
		$this->validates_presence_of('viewed_on');
		
		$this->set_protected_fields(array('viewed_on'));
		
		return parent::validate();
	}
	
	/**
	 * Records a user view on a product
	 * @param int $product_id unique id of the product to log a hit against
	 * @param string $remote_ip_address clients ip address who viewed the product
	 * @return boolean true if product view added, else false if ignored
	 */
	function log_hit($product_id, $remote_ip_address)
	{
		if(isset($_SESSION['product_view'][$product_id]))
			return false;
			
		if($this->find_by_field_array(array('product_id' => $product_id, 'viewed_on' => date(SQL_DATE_FORMAT))))
		{
			$this->view_count++;
		}
		else
		{
			$this->product_id = $product_id;
			$this->view_count = 1;
			$this->viewed_on = date(SQL_DATE_FORMAT);
		}

		$_SESSION['product_view'][$product_id] = true;
					
		return $this->save();
	}
}

?>