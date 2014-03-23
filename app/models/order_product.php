<?php
/**
 * Products within an order
 * 
 * @package efusion
 * @subpackage models
 */
class order_product extends model
{
	/**
	 * Primary key
	 * @var int
	 */
	var $id;
	
	/**
	 * Foriegn key for the order this belongs to
	 * @var int
	 */
	var $order_id;

	/**
	 * Foriegn key for the product the customer purchased
	 * @var int
	 */
	var $product_id;
		
	/**
	 * Cost price of the product at the time of the order
	 * @var currency
	 */
	var $cost_price;

	/**
	 * Sale price of the product at the time of the order
	 * @var currency
	 */
	var $sale_price;
	
	/**
	 * Total number of these products purchased
	 * @var int
	 */
	var $quantity;	

	/**
	 * Serialized array of selected variations
	 * @var string
	 */
	var $serialized_variations;	

	
	function order_product($id = null)
	{
		parent::model($id);
		
		$this->set_protected_fields(array('serialized_variations'));
	}
	
	function validate()
	{
		$this->validates_foriegnkey_exists('order_id');
		$this->validates_foriegnkey_exists('product_id');
		$this->validates_presence_of('cost_price');
		$this->validates_presence_of('sale_price');
		$this->validates_presence_of('quantity');
		
		return parent::validate();
	}
	
	/**
	 * Finds all order products for a specific order and joins product name
	 * @param int $order_id order table id
	 * @return array order product table rows with product name
	 */
	function find_all_for_order_id($order_id)
	{
		$sql = 'SELECT 
					order_product.*, 
					product.name,
					product.image_id, 
 					product.url_name
				FROM 
					order_product
				INNER JOIN 
					product ON product.id = order_product.product_id
				WHERE 
					order_id = ?';
					
		$sql_result = $this->execute_sql_query($sql, array($order_id));
		
		if($this->_db->query_result_rows($sql_result) > 0)
		{
			$result = array();
			$image =& model::create('image');
			
			while($row = $this->_db->get_next_row($sql_result))
			{
				if($image->find($row['image_id']))
					$row['image'] = $image->fields_as_associative_array();
				
				if($row['serialized_variations'])
					$row['variants'] = unserialize($row['serialized_variations']);
					
				$result[] = $row;
			}
		}
		else
			$result = null;
		
		return $result;
	}
}

?>