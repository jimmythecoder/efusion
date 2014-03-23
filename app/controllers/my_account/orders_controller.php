<?php
/**
 * Customers orders controller
 * 
 * @package efusion
 * @subpackage controllers
 */
class orders_controller extends my_account_controller
{
	function orders_controller(&$application)
	{
		parent::my_account_controller($application);
		
		$this->breadcrumb[] = array('my-account/orders/index' => 'My Orders');
	}
	
	/**
	 * Display all customers orders
	 */
	function index()
	{
		$allowed_sort_columns = array('reference_code','status','total','created_at');
		
		if(!empty($_GET['sort']) && in_array($_GET['sort'],$allowed_sort_columns))
			$order_by = '"'.$this->application->db->escape_string($_GET['sort']).'"';
		else
			$order_by = 'created_at DESC';

		if(!empty($_GET['page']))
			$current_page_index = (int)$_GET['page'];
		else
			$current_page_index = 1;
			
		$pager_options = array('select' => '"order".*', 
								'where' => 'account_id = '.$_SESSION['account_id'], 
								'escape' => false, 
								'order' => $order_by);
									
		$orders =& model::create('order');
		$this->template_data['orders_paged'] = $orders->find_all_paged($pager_options,config::get('admin','results_per_page'),$current_page_index);
	}
	
	/**
	 * View details of a specific order
	 */
	function view()
	{
		$order =& model::create('order');
		if($order->find($this->params['url_params']))
		{
			if($order->account_id == $_SESSION['account_id'])
			{
				$order->find_foreign_key('billing_address_id');
				$order->find_foreign_key('delivery_address_id');
				
				$this->template_data['order'] 						= $order->fields_as_associative_array();
				$this->template_data['order']['billing_address'] 	= $order->billing_address->fields_as_associative_array();
				$this->template_data['order']['delivery_address'] 	= $order->delivery_address->fields_as_associative_array();
				
				//Find country names
				$obj_country =& model::create('country');
				$obj_country->find($this->template_data['order']['billing_address']['country_id']);
				$this->template_data['order']['billing_address']['country'] = $obj_country->fields_as_associative_array();
				
				$obj_country->find($this->template_data['order']['delivery_address']['country_id']);
				$this->template_data['order']['delivery_address']['country'] = $obj_country->fields_as_associative_array();
				
				//Find all the products that go with this order
				$order_product =& model::create('order_product');
				$this->template_data['order']['products'] = $order_product->find_all_for_order_id($order->id);
			}
			else
			{
				//User is trying to view someone elses order
				$this->flash['error'][] = 'You are not authorized to view this order';
				$this->redirect_to('my-account/orders','index','https');
			}
		}
		else
			$this->redirect_to('my-account/orders','index','https'); //No order with this id exists
		
		$this->breadcrumb[] = array('my-account/orders/view/'.$order->id => 'View Order');
	}

}
?>