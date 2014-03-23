<?php
/**
 * Weight based shipping pricing
 * 
 * @package efusion
 * @subpackage models
 */
class shipping_tier extends model
{
	/**
	 * Primary key
	 * @var int
	 */
	var $id;
	
	/**
	 * Foriegn key to shipping zone table
	 * @var int
	 */
	var $shipping_zone_id;
	
	/**
	 * End/max weight for this tier
	 * @var float
	 */
	var $max_weight;
	
	/**
	 * Tier price amount
	 * @var float
	 */
	var $amount;

		
	function validate()
	{
		$this->validates_foriegnkey_exists('shipping_zone_id');
		$this->validates_presence_of('max_weight');
		$this->validates_presence_of('amount');
		
		return parent::validate();
	}

	/**
	 * Finds the shipping tier record for the given weight and shipping tier. Will find the tier with a higher max_weight than the given weight
	 * @param float $weight weight in kilograms
	 * @param int $shipping_zone_id shipping zone record id
	 * @return boolean true if a tier was found for this weight and zone, else false
	 */
	function find_by_weight_and_shipping_zone_id($weight, $shipping_zone_id)
	{
		return $this->find_by_sql('SELECT * FROM "' . $this->get_table_name() . '" WHERE max_weight > ' . (float)$weight . ' AND shipping_zone_id = ' . (int)$shipping_zone_id . ' ORDER BY max_weight LIMIT 1');
	}
}

?>