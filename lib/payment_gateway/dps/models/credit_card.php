<?php
/**
 * Credit card model
 * 
 * @package efusion
 * @subpackage dps
 */
class credit_card extends model
{
	/**
	 * The name printed on the card
	 * @var string
	 */
	var $cardholder_name;
	
	/**
	 * Credit Card Number
	 * @var string
	 */
	var $card_number;
	
	/**
	 * Expiry date on the card in format mm/YY e.g. 01/07 for January 2007
	 * @var string
	 */
	var $card_expiry_date;
	
	/**
	 * CVC code written on the back of the credit card, 3 or more digits, numeric
	 * @var int
	 */
	var $card_cvc_code;
	
	function validate()
	{
		$this->validates_presence_of('cardholder_name');
		$this->validates_presence_of('card_number');
		$this->validates_presence_of('card_expiry_date');
		$this->validates_presence_of('card_cvc_code');
		
		$this->validates_numericality_of('card_cvc_code');
		
		$this->validates_length_of('card_cvc_code',3);
		$this->validates_length_of('card_expiry_date',5); // MM/YY
		
		$this->validates_credit_card_number('card_number','Your credit card number is incorrect');

		return parent::validate();
	}
	
	/**
	 * Validates a credit card number
	 * @param string $field the field to validate as a credit card number
	 * @param string $message message to display on failure (optional)
	 * @return boolean true if valid, else false
	 */
	function validates_credit_card_number($field, $message = null)
	{
		$result = false;
		$length = strlen($this->$field);

		for ($i = 0, $r = 0; $i < $length; $i++) 
		{
			$q = substr($this->$field, $length - $i - 1, 1) * (($i % 2) + 1); 
			$r += ($q %	10)	+ (int)($q / 10); 
		}

		if ($length < 13 or $length > 16 or $r % 10) 
			 $this->_errors[] = ($message == null) ? ($field.' is not valid') : $message;	
		else
			$result = true;	
			
		return $result;
	}
}
?>