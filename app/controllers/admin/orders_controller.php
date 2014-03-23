<?php
/**
 * Admin order management
 * 
 * @package efusion
 * @subpackage controllers
 */
class orders_controller extends admin_controller
{
	function orders_controller(&$application)
	{
		parent::admin_controller($application);
		
		$this->breadcrumb[] = array('admin/orders/index' => 'Orders');
	}
		
	function index()
	{
		$pager_params = $this->_get_pager_url_paramters();
		if(empty($pager_params['order_by']) || $pager_params['order_by'] == 'id')
			$pager_params['order_by'] = 'id DESC';
			
		$product =& model::create('order');
				
		$pager_options = array('select' => '"order".*,address_book.first_name,address_book.last_name', 
								'join' => 'INNER JOIN address_book ON address_book.id = "order".billing_address_id',
								'where' => ($pager_params['escaped_filter_by']) ? '"order".reference_code = \'' . $pager_params['escaped_filter_by'] . "' OR (address_book.first_name) LIKE '%".$pager_params['escaped_filter_by']."%' OR LOWER(address_book.last_name) LIKE '%".$pager_params['escaped_filter_by']."%'" : null, 
								'escape' => false, 
								'order' => $pager_params['order_by']);
								
		$this->template_data['orders_paged'] = $product->find_all_paged($pager_options,config::get('admin','results_per_page'),$pager_params['page']);
		
	}
	
	function edit()
	{
		$order_id = (int)$this->params['url_params'];
		
		$order =& model::create('order');
		if(!$order->find($order_id))
			$this->redirect_to('admin/orders','index','https');
		
		if(isset($this->params['save']))
		{
			if($order->payment_method == 'bank_deposit')
				$order->amount_paid = $this->params['order']['amount_paid'];
				
			$order->tracking_number = $this->params['order']['tracking_number'];	
			$order->status = $this->params['order']['status'];
			$order->comments = $this->params['order']['comments'];
			$order->save();
			
			$email =& model::create('email',EMAIL_ORDER_UPDATED);
			$email->to = $order->email_address;
			
			$substitutions = array('order_number' => $order->reference_code,
									'amount_paid' => number_format($order->amount_paid,2),
									'tracking_number' => $order->tracking_number,
									'status' => strtoupper($order->status), 
									'comments' => $order->comments);
									
			$email->parse_message($substitutions);
			$email->send();
			
			$this->flash['notice'][] = 'Order status updated, customer has been notified via email.';
			$this->redirect_to('admin/orders','index','https');
		}
		
		$order->find_foreign_key('billing_address_id');
		$order->find_foreign_key('delivery_address_id');
		$order->find_foreign_key('account_id');
		
		$this->template_data['order'] = $order->fields_as_associative_array();
		$this->template_data['order']['billing_address'] = $order->billing_address->fields_as_associative_array();
		$this->template_data['order']['delivery_address'] = $order->delivery_address->fields_as_associative_array();
		$this->template_data['order']['account'] = $order->account->fields_as_associative_array();
		
		//Find country names
		$obj_country =& model::create('country');
		$obj_country->find($this->template_data['order']['billing_address']['country_id']);
		$this->template_data['order']['billing_address']['country'] = $obj_country->fields_as_associative_array();
		
		$obj_country->find($this->template_data['order']['delivery_address']['country_id']);
		$this->template_data['order']['delivery_address']['country'] = $obj_country->fields_as_associative_array();
		
		//Find all the products that go with this order
		$order_product =& model::create('order_product');
		$this->template_data['order']['products'] = $order_product->find_all_for_order_id($order->id);
		
		$this->template_data['order_status_options'] = array('pending' => 'Pending','processed' => 'Processed','shipped' => 'Shipped','cancelled' => 'Cancelled');
		
		$this->breadcrumb[] = array('admin/orders/edit/'.$order->id => 'View Order #'.$order->reference_code);
	}
	
	function _get_pager_url_paramters()
	{
		$params = array();
		
		$valid_sort_columns = array(
			'id' 			=> 'id DESC', 
			'order-number' 	=> 'reference_code', 
			'status' 		=> 'status', 
			'total' 		=> 'total', 
			'created-at' 	=> 'created_at', 
			'customer-name' => 'first_name, last_name');
		
		if(empty($_GET['sort']) || !array_key_exists($_GET['sort'],$valid_sort_columns))
			$_GET['sort'] = 'id';
			
		$params['order_by'] = $valid_sort_columns[$_GET['sort']];

		if(!empty($_GET['filter_by']))
			$params['escaped_filter_by'] = $this->application->db->escape_string(strtolower($_GET['filter_by']));
		else
			$params['escaped_filter_by'] = null;
					
		if(!empty($_GET['page']))
			$params['page'] = (int)$_GET['page'];
		else
			$params['page'] = 1;
			
		return $params;
	}
}

?>