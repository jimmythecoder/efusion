<?php
/**
 * DPS payment request response model
 * @link http://www.dps.co.nz/technical_resources/ecommerce_hosted/pxpay.html#Request XML structure
 * 
 * @package efusion
 * @subpackage dps
 */
class payment_request_response extends model
{
	var $uri;
	
	function validate()
	{
		$this->clear_model_errors();
		
		$this->validates_presence_of('uri');
		
		return parent::validate();
	}
	
	/**
	 * Sets the internal class attributes from the xml string
	 * @param string $xml string/xml The XML string response from DPS
	 * @return boolean true if xml is valid and successfully parsed, else false
	 */
	function set_field_values_from_xml($xml)
	{
		if(!$response_obj = $this->_get_xml_as_object($xml))
			return false;
			
		$this->uri = $response_obj->request->uri;
		
		return $this->validate();
	}
	
	/**
	 * Parses an XML string and returns it as an object with nodes
	 */
	function _get_xml_as_object($xml)
	{
		$xml_parser =& model::create('xml_parser');
		
		if(!$obj_response = $xml_parser->parse_xml_string_into_node_tree($xml))
			return false;
			
		$xml_parser->cleanup();
		
		return $obj_response;
	}
}
?>