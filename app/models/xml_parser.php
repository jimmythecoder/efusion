<?php
/**
 * XML Parser, uses the default PHP SAX
 * 
 * @package efusion
 * @subpackage models
 */
class xml_parser
{
	/**
	 * PHP SAX parser object
	 * @var object
	 * @private
	 */
	var $parser;
	
	/**
	 * XML to process
	 * @var string
	 * @private
	 */
	var $xml;
	
	
	function xml_parser($xml = '')
	{
		$this->set_xml($xml);		
		$this->parser = xml_parser_create();
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, 0);
   		xml_parser_set_option($this->parser, XML_OPTION_SKIP_WHITE, 1);
   		
		if(!class_exists('xml_node'))
			trigger_error('xml_node class is required before any xml parsing',E_USER_ERROR);
	}	
	
	function set_xml($xml)
	{
		$this->xml = $xml;
	}
	
	/**
	 * Parses an XML string into a tree of xml_node objects that map the hierachy of the XML structure
	 * @param string $xml xml to parse (optional)
	 * @param boolean $lowercase_xml_tags whether to transform all tags into lowercase
	 */
	function parse_xml_string_into_node_tree($xml = null, $lowercase_xml_tags = true)
	{
		if(!is_null($xml))
			$this->set_xml($xml);
			
		$node_tree =& new xml_node();
		
		$current_node =& $node_tree;	
		
		$last_open_node = null;
		
		$arr_node_values = array();
		
		if(!xml_parse_into_struct($this->parser, $this->xml, $arr_node_values))
			return false;
		
		foreach($arr_node_values as $index => $arr_node)
		{
			if($lowercase_xml_tags)
				$tag_name = strtolower($arr_node['tag']);
			else
				$tag_name = $arr_node['tag'];
				
			$node_type = strtolower($arr_node['type']);
			
			if($node_type == 'open')
			{
				$last_open_node =& $current_node;
				
				if(!$current_node->property_exists($tag_name))
				{
					$current_node->$tag_name =& new xml_node();
					
					$current_node =& $current_node->$tag_name;	
				}
				else if(is_object($current_node->$tag_name))
				{
					$current_node->$tag_name = array(0 => $current_node->$tag_name);
					
					$current_node->{$tag_name}[1] =& new xml_node();

					$current_node =& $current_node->{$tag_name}[1];
				}
				else if(is_array($current_node->$tag_name))
				{
					$current_node->{$tag_name}[] =& new xml_node();
					
					$current_index = count($current_node->$tag_name) - 1;
					
					$current_node =& $current_node->{$tag_name}[$current_index];	
				}
			}
			else if($node_type == 'complete')
				$current_node->$tag_name = isset($arr_node['value']) ? $arr_node['value'] : null;	
			else if($node_type == 'close')
				$current_node =& $last_open_node;
		}

		return $node_tree;
	}

	/**
	 * Parses an XML string into an associative array
	 */
	function parse_xml_to_array($xml)
	{
		if(!is_null($xml))
			$this->set_xml($xml);
		
		$tags = array();
		$elements = array();
	   	$stack = array();
	   
	   	xml_parse_into_struct($this->parser, $this->xml, $tags );

	   foreach ( $tags as $tag )
	   {
	       $index = count( $elements );
	       $node_type = strtolower($tag['type']);
	       
	       if ( $node_type == "complete" || $node_type == "open" )
	       {
	           $elements[$index] = array();
	           $elements[$index]['name'] = $tag['tag'];
	           $elements[$index]['attributes'] = $tag['attributes'];
	           $elements[$index]['content'] = $tag['value'];
	          
	           if ( $node_type == "open" )
	           {    # push
	               $elements[$index]['children'] = array();
	               $stack[count($stack)] = &$elements;
	               $elements = &$elements[$index]['children'];
	           }
	       }
	      
	       if ( $node_type == "close" )
	       {    # pop
	           $elements = &$stack[count($stack) - 1];
	           unset($stack[count($stack) - 1]);
	       }
	   }
	   
	   return $elements[0];
	}
	
	/**
	 * Frees memory and destroys the instance of the XML Parser
	 */
	function cleanup()
	{
		xml_parser_free($this->parser);
		unset($this->xml);
	}
}

/**
 * XML node, a blank object which will represent the xml structure
 */
class xml_node
{
	function property_exists($property_name)
	{
		return array_key_exists($property_name,get_object_vars($this));
	}
}
?>