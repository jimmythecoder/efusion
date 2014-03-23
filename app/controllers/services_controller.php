<?php
/**
 * Web Services controller, processes XML HTTP requests
 * 
 * @package efusion
 * @subpackage controllers
 */
class services_controller extends application_controller
{
	function services_controller(&$application)
	{
		parent::application_controller($application);
		
		$application->content_type = 'text/xml';	
	}
	
	/**
	 * Retrieves the latest product list on the site	
	 */
	function product_list()
	{
		$product =& model::create('product');
		$this->template_data['products'] = $product->find_all(array('where' => 'is_active = 1','order' => 'created_at DESC','limit' => 100));
	}
	
	function get_product_details()
	{
		$product =& model::create('product');
		if($product->find($this->params['id']))
		{
			$product->find_foreign_key('category_id');
			
			$this->template_data['product'] 			= $product->fields_as_associative_array();	
			$this->template_data['product']['category'] = $product->category->fields_as_associative_array();	
		}
	}
	
	function product_review()
	{
		//If a logged in user submits a product review
		if(isset($this->params['submit_review']) && isset($_SESSION['account_id']))
		{
			$product_review =& model::create('product_review');
			$product_review->product_id = $this->params['product_id'];
			$product_review->account_id = $_SESSION['account_id'];
			$product_review->rating = (int)$this->params['rating'];
			$product_review->comment = $this->params['comment'];

			if($product_review->save())
			{
				$this->template_data['average_rating'] = $product_review->get_average_rating($product_review->product_id);
				$this->template_data['reviewed_at'] = $product_review->reviewed_at;
				
				$_SESSION['has_user_submitted_review_for_product_' . $product_review->product_id] = true;
			}
			else
				$this->flash['error'] = $product_review->_errors;
			
			//If user submitted the form via post, redirect them back to product page
			if($this->params['method'] == 'form')
				$this->redirect_to('product',$this->params['product_url_name'] . '?rating=' . (int)$product_review->rating);
		}
	}
	
	function live_search()
	{
		if(!empty($_GET['q']))
		{
			$search =& model::create('search');
			
			$search->search_query 		= $_GET['q'];
			$search->results_per_page 	= 10;
			$search->in_category_id 	= isset($_GET['c']) ? (int)$_GET['c'] : null;
			$search->page_number 		= isset($_GET['page']) ? (int)$_GET['page'] : 1;
			
			$search->find_all_products();
			
			$this->template_data['search_results'] = $search->search_results;
		}
	}
	
	/**
	 * Retrieves the longitude & latitude of where each order was made
	 */
	function order_geocoding_data()
	{
		if(empty($_SESSION['account_group']) || $_SESSION['account_group'] != 'administrators')
			exit('Not logged in');
		
		$address_book =& model::create('address_book');
		
		$this->template_data['orders'] = $address_book->find_all(array('select' => 'order.id, address_book.latitude, address_book.longitude, SUM("order".total) AS total_order_value', 'join' => 'INNER JOIN "order" ON "order".delivery_address_id = address_book.id', 'group' => 'address_book.longitude, address_book.latitude', 'escape' => false));		
	}
	
	/**
	 * Calcuates delivery charges
	 */
	function get_delivery_charges()
	{
		$required_params 	= array('weight','deliver-to-address-book-id');
		$errors 			= array();
		
		foreach($required_params as $param)
		{
			if(empty($_GET[$param]))
				$errors[] = $param . ' is required!';
		}
		
		if(empty($errors))
		{
			$obj_shipping_zone 	=& model::create('shipping_zone');
			$obj_shipping_tier 	=& model::create('shipping_tier');
			$obj_address_book	=& model::create('address_book');
			
			if($obj_address_book->find($_GET['deliver-to-address-book-id']))
				$obj_address_book->find_foreign_key('country_id');
			else
				$errors[] = 'Could not find address book with ID: ' . $_GET['deliver-to-address-book-id'];
				
			if(!$obj_shipping_zone->find_by_city_and_country($obj_address_book->city, $obj_address_book->country_id))
				$errors[] = 'Could not find shipping zone for city and country [' . $obj_address_book->city . '|' . $obj_address_book->country_id . ']';
			
			if(!$obj_shipping_tier->find_by_weight_and_shipping_zone_id($_GET['weight'], $obj_shipping_zone->id))
				$errors[] = 'Could not find shipping tier for weight and zone [' . $_GET['weight'] . '|' . $obj_shipping_zone->id . ']';
			
			$this->template_data['from_city'] 	= config::get('shipping','city');
			$this->template_data['to_city'] 	= $obj_address_book->city;
			$this->template_data['weight'] 		= $_GET['weight'];
			$this->template_data['zone'] 		= $obj_shipping_zone->fields_as_associative_array();
			$this->template_data['total_cost_of_delivery'] = $obj_shipping_tier->amount;
		}
		
		$this->template_data['errors'] 			= $errors;
		$this->template_data['is_successfull'] 	= empty($errors);
	}
}
?>