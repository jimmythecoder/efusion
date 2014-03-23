<?php
/**
 * Admin delivery management
 * 
 * @package efusion
 * @subpackage controllers
 */
class delivery_controller extends admin_controller
{
	function delivery_controller(&$application)
	{
		parent::admin_controller($application);
		
		$this->breadcrumb[] = array('admin/delivery/index' => 'Delivery Charges');	
	}
	
	function index()
	{
		$shipping_tier =& model::create('shipping_tier');
		$shipping_zone =& model::create('shipping_zone');
		
		if(isset($this->params['save']))
		{
			foreach($this->params['shipping_tier'] as $shipping_tier_id => $data)
			{
				if($shipping_tier->find($shipping_tier_id))
				{
					$shipping_tier->max_weight = $data['max_weight'];
					$shipping_tier->amount = $data['amount'];
					
					if(!$shipping_tier->save())
					{
						$this->flash['error'] = $shipping_tier->_errors;
						$this->redirect_to('admin/delivery','index','https');
					}
					
					$shipping_tier->clear_field_values();
				}
				else
					$this->flash['error'][] = 'Could not find shipping tier ID: ' . $shipping_tier_id . '. Please check the database for consistancy';
			}
				
			$this->flash['notice'][] = 'Delivery charges updated successfully.';
			$this->redirect_to('admin/delivery','index','https');	
		}
		else if(isset($this->params['cancel']))
			$this->redirect_to('admin','index','https');
		
		$shipping_zones = $shipping_zone->find_all();
		$shipping_tiers = $shipping_tier->find_all(array('order' => 'shipping_zone_id,max_weight'));
		
		//Group tiers into there zones
		$delivery_matrix = array();
		foreach($shipping_tiers as $key => $tier)
			$delivery_matrix[$shipping_zones[$tier['shipping_zone_id']]['name']][] = $tier;
		
		$this->template_data['delivery_matrix'] = $delivery_matrix;
	}
}
?>