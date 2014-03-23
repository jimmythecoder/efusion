<?php
/**
 * DPS payment confirmation request model
 * @link http://www.dps.co.nz/technical_resources/ecommerce_hosted/pxpay.html#ProcessResponse XML structure
 * 
 * @package efusion
 * @subpackage dps
 */
class payment_confirmation_request extends model
{
	/**
	 * Your account's UserId
	 * @var string
	 */
	var $PxPayUserId;
	
	/**
	 * DPS merchant account's 64 character key
	 * @var string
	 */
	var $PxPayKey;
	
	/**
	 * The encrypted URL response from DPS, which you can get from "result" parameter in the URL string that is returned to your response page.
	 * @var string
	 */
	var $Response;
	
	function validate()
	{
		$this->validates_presence_of('PxPayUserId');
		$this->validates_presence_of('PxPayKey');
		$this->validates_presence_of('Response');
		
		$this->validates_length_of('PxPayKey',64,64,'The DPS PX Pay Key is not 64 characters long');

		return parent::validate();
	}
	
	/**
	 * Sends the payment confirmation request to DPS server via HTTP Post and returns the string result
	 * @param string $url The url to post the request to
	 * @return string/xml The XML string response from DPS
	 */
	function send($url)
	{
		if(!$this->validate())
			return false;
			
		$payment_gateway_request = $this->_get_payment_confirmation_request_as_xml();
			
		if($payment_gateway_response = HTTP::post_request($payment_gateway_request, $url))
			return $payment_gateway_response;
			
		$this->_errors[] = 'There was a problem communicating to the credit card processing station, please try again or email the website owner.';
		return false;		
	}
	
	/**
	 * Generates a DPS compatible XML string to post as a payment confirmation request
	 * @return string XML request string
	 */
	function _get_payment_confirmation_request_as_xml()
	{
		$fields_as_array = $this->field_names_as_array();
		
		$xml =  '<ProcessResponse>';
		
		foreach($fields_as_array as $key => $field)
			$xml .= '<' . $field . '>' . htmlentities($this->$field,ENT_NOQUOTES,'UTF-8') . '</' . $field . '>';
			
		$xml .= '</ProcessResponse>';
		
		return $xml;	
	}
}
?>