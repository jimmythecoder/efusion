<?php
/**
 * Direct Payment Solutions payment gateway
 * This implements the PX Pay hosted solution
 * @link http://dps.co.nz DPS online credit cart merchant
 * 
 * @package efusion
 * @subpackage dps
 */
 
require(PAYMENT_GATEWAY_DIR . '/dps/models/payment_request.php');
require(PAYMENT_GATEWAY_DIR . '/dps/models/payment_request_response.php');
require(PAYMENT_GATEWAY_DIR . '/dps/models/payment_confirmation_request.php');
require(PAYMENT_GATEWAY_DIR . '/dps/models/payment_confirmation_response.php');
require(PAYMENT_GATEWAY_DIR . '/dps/models/credit_card.php');

class payment_gateway
{
	/**
	 * Fully qualified URL to the payment processor
	 * @var string
	 */
	var $url;
	
	/**
	 * Unique identifier for each transaction, 16 chars
	 * @var string
	 */
	var $transaction_id;
	
	/**
	 * Array of errors generated while processing the requests
	 * @var array
	 */
	var $_errors;
	
	/**
	 * Object containing the payment response from the gateway
	 * @access private
	 */
	var $_payment_confirmation_response_obj;
	
	
	function payment_gateway()
	{
		$this->url = config::get('payment_gateway','address');
		$this->_errors = array();	
		
		if(isset($_SESSION['payment_gateway']['transaction_id']))
			$this->transaction_id = $_SESSION['payment_gateway']['transaction_id'];
	}
	
	function start_transaction()
	{
		$this->transaction_id = substr(md5(uniqid(rand(), true)),0,16);
		$_SESSION['payment_gateway']['transaction_id'] = $this->transaction_id;
	}
	
	function end_transaction()
	{
		unset($_SESSION['payment_gateway']['transaction_id']);
		$this->transaction_id = null;
	}
	
	function generate_payment_request($amount)
	{
		$customer_account_obj = model::create('account',$_SESSION['account_id']);
		
		$payment_request_obj =& new payment_request();
		
		$payment_request_obj->PxPayUserId = config::get('payment_gateway','username');
		
		$payment_request_obj->PxPayKey = config::get('payment_gateway','password');
		
		$payment_request_obj->TxnType = config::get('payment_gateway','transaction_type');
		
		$payment_request_obj->CurrencyInput = config::get('payment_gateway','currency');
		
		$payment_request_obj->UrlSuccess = config::get('https_location') . '/order-placed';
		
		$payment_request_obj->UrlFail = config::get('https_location') . '/order-failed';
		
		$payment_request_obj->TxnId = $this->transaction_id;
		
		$payment_request_obj->EnableAddBillCard = 0;
		
		$payment_request_obj->set_AmountInput($amount);
		
		$payment_request_obj->MerchantReference = $customer_account_obj->email;
		
		if($payment_gateway_response = $payment_request_obj->send($this->url))
			return $payment_gateway_response;
		
		$this->_errors = $payment_request_obj->_errors;
		return false;
	}
	
	function get_hosted_payment_page_url($payment_gateway_response_xml)
	{
		$payment_request_response_obj =& new payment_request_response();
		
		if($payment_request_response_obj->set_field_values_from_xml($payment_gateway_response_xml))
			return $payment_request_response_obj->uri;
		
		$this->_errors = $payment_request_response_obj->_errors;
		return false;
	}
	
	
	function get_payment_response_xml($response)
	{
		$payment_confirmation_request_obj =& new payment_confirmation_request();
		
		$payment_confirmation_request_obj->PxPayUserId = config::get('payment_gateway','username');
		
		$payment_confirmation_request_obj->PxPayKey = config::get('payment_gateway','password');
		
		$payment_confirmation_request_obj->Response = $response;
		
		if($payment_response_xml = $payment_confirmation_request_obj->send($this->url))
			return $payment_response_xml;
	
		$this->_errors = $payment_confirmation_request_obj->_errors;
		return false;		
	}
	
	function set_payment_confirmation_response_object_from_xml($payment_response_xml)
	{
		$this->_payment_confirmation_response_obj =& new payment_confirmation_response();
		
		if($this->_payment_confirmation_response_obj->set_field_values_from_xml($payment_response_xml))
			return true;
		
		$this->_errors = $this->_payment_confirmation_response_obj->_errors;
		return false;
	}
	
	function is_payment_authorized()
	{
		if(empty($this->_payment_confirmation_response_obj))
			return false;
		
		if($this->_payment_confirmation_response_obj->is_payment_authorized())
			return true;
		
		$this->_errors = $this->_payment_confirmation_response_obj->_errors;
		return false;
	}
	
	function get_payment_response_value($attribute)
	{
		if(empty($this->_payment_confirmation_response_obj))
			return false;
			
		return $this->_payment_confirmation_response_obj->$attribute;		
	}
}
?>