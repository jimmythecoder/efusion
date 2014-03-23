<?php
/**
 * Customer orders
 * 
 * @package efusion
 * @subpackage models
 */
class order extends model
{
	/**
	 * Primary key
	 * @var int
	 */
	var $id;
	
	/**
	 * Foriegn key for the customers account this order belongs to
	 * @var int
	 */
	var $account_id;
	
	/**
	 * The date and time this order was created at
	 * @var datetime
	 */
	var $created_at;

	/**
	 * Foriegn key for the referrer name the user originally came from
	 * @var int
	 */
	var $referer_id;
	
	/**
	 * Foriegn key for the address this order was shipped to
	 * @var int
	 */
	var $delivery_address_id;
	
	/**
	 * Foriegn key for the address this order was billed to
	 * @var int
	 */
	var $billing_address_id;
		
	/**
	 * The status of this order (pending|processed|shipped|cancelled)
	 * @var enum
	 */
	var $status;
		
	/**
	 * Customers email address when the order was created
	 * @var string
	 */
	var $email_address;
	
	/**
	 * Courier tracking number
	 * @var string
	 */
	var $tracking_number;
	
	/**
	 * Additional info / special requests made for the order and updates added
	 * @var text
	 */
	var $comments;
	
	/**
	 * Method of payment used for the order, credit-card or bank-deposit
	 * @var enum
	 */
	var $payment_method;
	
	/**
	 * Reference code for the transaction if order purchased via credit-card
	 * @var string
	 */
	var $transaction_reference;
	
	/**
	 * A unique alphanumeric reference code for the customer
	 * @var string
	 */
	var $reference_code;
	
	/**
	 * GST amount at the time of the order, e.g. 0.125
	 * @var currency
	 */
	var $gst_component;
	
	/**
	 * Total cost of shipping
	 * @var currency
	 */
	var $shipping_total;
	
	/**
	 * Total amount paid by the customer towards this order
	 * @var currency
	 */
	var $amount_paid;
	
	/**
	 * Total cost of the order including GST. Calculated from product in cart total + shipping total
	 * @var currency
	 */
	var $total;
	
	
	function order($id = null)
	{
		parent::model($id);

		$this->set_foreign_key('delivery_address_id','address_book');
		$this->set_foreign_key('billing_address_id','address_book');
	}
	
	function validate()
	{
		$this->validates_foriegnkey_exists('account_id');
		$this->validates_foriegnkey_exists('delivery_address_id');
		$this->validates_foriegnkey_exists('billing_address_id');
		$this->validates_presence_of('email_address');
		$this->validates_presence_of('created_at');
		$this->validates_presence_of('status');
		$this->validates_presence_of('payment_method');
		$this->validates_presence_of('shipping_total');
		$this->validates_presence_of('total');
		$this->validates_presence_of('amount_paid');
		$this->validates_presence_of('gst_component');
		$this->validates_presence_of('reference_code');
		
		$this->validates_uniqueness_of('reference_code');
		
		return parent::validate();
	}
	
	/**
	 * Sets the created_at date on creation of a new product
	 */
	function before_save()
	{
		//Clean currency input amount
		$this->amount_paid = str_replace(array('$',',',' '),'',$this->amount_paid);
	}
	
	function before_insert()
	{
		$this->_generate_unique_reference_code();
		$this->created_at = date(SQL_DATETIME_FORMAT);	
	}
	
	/**
	 * Sets the orders delivery address 
	 * @param string $type delivery or billing address type
	 * @param array $delivery_address fields
	 * @return int/bool id of the delivery address record or false on failure
	 */
	function set_address_from_array($type = 'delivery', $address)
	{
		$address_book =& model::create('address_book');
		$address_book->set_field_values_from_array($address);
		$address_book->is_locked = 1;  	//Cannot edit this address
		$address_book->is_primary = 0;
		
		if($address_book->insert())
			$this->{$type.'_address_id'} = $address_book->id;
		else
			trigger_error('Failed to create address book entry! '.serialize($address_book->_errors),E_USER_ERROR);

		return (int)$this->{$type.'_address_id'};
	}
	
	/**
	 * Generates a new unique 8 character reference code for the order, called on insert
	 */
	function _generate_unique_reference_code()
	{
		do
		{
			$this->reference_code = strtoupper(substr(md5(uniqid(rand(), true)),0,8));
		}
		while(!$this->validates_uniqueness_of('reference_code'));
	}
	
	/**
	 * Find an order by a unique reference code
	 */
	function find_by_reference_code($reference_code)
	{
		return $this->find_by_field('reference_code',$reference_code);
	}
	
	function delete($id)
	{
		//Delete all order products for this order
		$this->execute_sql_query('DELETE FROM order_product WHERE order_id = ?',array($this->id));
		
		//Delete the order record
		return parent::delete();
	}
}

?>