<?php
/**
 * Default controller, processes all public site sections
 * 
 * @package efusion
 * @subpackage controllers
 */
class store_controller extends application_controller
{
    var $cart;
    
	function store_controller(&$application)
	{
		parent::application_controller($application);
		
		//Load product categories
		if($this->params['action'] != 'catalog' && $this->params['action'] != 'product')
		{
			$selected_category_id = isset($_SESSION['selected_category_id']) ? $_SESSION['selected_category_id'] : ROOT_CATEGORY_NODE;
			$categories_cache_key = 'selected_category:' . $selected_category_id;
			
			if(! ($this->template_data['categories'] = cache::get($categories_cache_key,'categories')) )
			{
				$this->template_data['selected_category_id'] = $selected_category_id;
				
				$categories =& model::create('category');
				$category_named_path = $categories->get_category_named_path($selected_category_id);
				
				$this->template_data['categories'] = $categories->load_category_path($category_named_path);
				
				cache::save($this->template_data['categories'],$categories_cache_key,'categories');
			}
		}
		
		//Set the user group in the template data
		if(isset($_SESSION['account_group']))
			$this->template_data['account_group'] = $_SESSION['account_group'];
		
		//Find which site the user came from
		if(!empty($_SERVER['HTTP_REFERER']) && empty($_SESSION['referer_id']))
		{
			$arr_referer_url = parse_url($_SERVER['HTTP_REFERER']);

			//We save the referer even if its our own site
			$referer =& model::create('referer');
			$_SESSION['referer_id'] = $referer->log_url($arr_referer_url['host']);
		}
		
		$this->breadcrumb[] = array('' => 'Home');
		
		//Load items in cart
		$this->cart =& model::create('cart');
		$this->template_data['cart'] = $this->cart->get_products_in_cart();
	}
	
	
	/**
	 * Display home page
	 */
	function index()
	{
		if($home_page_cache_data = cache::get('home_page','home'))
			$this->template_data = $this->template_data + $home_page_cache_data;
		else
		{
			//Load home page content
			$content =& model::create('content',HOME_PAGE_CONTENT_ID);
			$this->template_data['content'] = $content->fields_as_associative_array();

			//Load featured products
			$product_model =& model::create('product');
			$this->template_data['latest_products'] = $product_model->find_all_with_images(array('where' => 'is_active = 1','order' => 'created_at DESC', 'limit' => HOME_PAGE_LATEST_PRODUCTS));
			
			//Save the loaded data to cache
			$cache_data_to_save = array('latest_products' => $this->template_data['latest_products'], 'content' => $this->template_data['content']);
			cache::save($cache_data_to_save,'home_page','home');
		}
	}
	
	
	/**
	 * Display product catagories
	 */
	function catalog()
	{
		//Find and save selected category id
		$category =& model::create('category');
		if(isset($this->params['url_params']))
		{
			if(ctype_digit($this->params['url_params']))
				$category->find($this->params['url_params']);
			else
				$category->find_by_field('url_name',$this->params['url_params']);
		}
		
		$selected_category_id = $category->id ? $category->id : ROOT_CATEGORY_NODE;
		$this->template_data['selected_category_id'] = $selected_category_id;
		$_SESSION['selected_category_id'] = $selected_category_id;
		
		//Load category breadcrumb and expanded category menu items
		$categories =& model::create('category');
		$category_named_path = $categories->get_category_named_path($selected_category_id);
		$this->template_data['categories'] = $categories->load_category_path($category_named_path);
		
		//Find category title
		if(!$category->id)
			$category->find($selected_category_id);
		
		$this->template_data['category'] = $category->fields_as_associative_array();
		
		//Find all the products in this category or featured products if no category selected
		$products =& model::create('product');
		$products_per_page = config::get('catalogue','products_per_page');
		
		if($selected_category_id)
		{
			$sort_columns 			= array('price' => 'product.sale_price','name' => 'product.name','newest' => 'product.created_at DESC');
			
			$sanitized_order_by 	= (isset($_GET['sort']) && isset($sort_columns[$_GET['sort']])) ? $_GET['sort'] : 'newest';
			
			$order_by_column 		= $sort_columns[$sanitized_order_by];
					
			$current_page_number 	= isset($this->params['page']) ? abs((int)$this->params['page']) : 1;
			
			$unique_page_cache_id 	= $selected_category_id . ':' . $current_page_number . ':' . $sanitized_order_by;
			
			if($cached_page_data = cache::get($unique_page_cache_id,'catalog'))
			{
				$category_products = $cached_page_data['category_products'];
				$this->template_data['pagination'] = $cached_page_data['pagination'];
			}
			else
			{
				$child_categories 		= $categories->get_all_child_categories($selected_category_id);
				
				//Calculate pagenation 
				$product_count 			= $products->count(array('where' => 'category_id IN('.implode(',',$child_categories).') AND is_active = 1'));
				
				$number_of_product_pages = ceil($product_count / $products_per_page);

				if(($current_page_number > $number_of_product_pages) || ($current_page_number < 1))
					$current_page_number = 1;
				
				$start_from_row 		= ($current_page_number - 1) * $products_per_page;
			
				$category_products 		= $products->find_all_with_images(array('where' => 'category_id IN('.implode(',',$child_categories).') AND is_active = 1','order' => $order_by_column,'limit' => $products_per_page . ' OFFSET ' . $start_from_row));
				
				$current_page_set 		= ceil($current_page_number / CORE_PAGINATION_PAGES);
				
				$previous_page_number 	= (($current_page_set - 1) * CORE_PAGINATION_PAGES);
				
				$next_page_number 		= ($current_page_set * CORE_PAGINATION_PAGES) + 1;
				
				$this->template_data['pagination'] = array(
														'number_of_items' 		=> $product_count,
														'number_of_pages' 		=> $number_of_product_pages,
														'current_page_number' 	=> $current_page_number,
														'pagination_set_size'	=> CORE_PAGINATION_PAGES,
														'show_previous_arrow' 	=> ($current_page_number > CORE_PAGINATION_PAGES),
														'show_next_arrow' 		=> ($next_page_number <= $number_of_product_pages),
														'previous_page_number'	=> $previous_page_number,
														'next_page_number' 		=> $next_page_number,
														'upper_page_limit'		=> ($next_page_number > $number_of_product_pages) ? $number_of_product_pages : $next_page_number - 1);

				if(count($category_products))
					cache::save(array('category_products' => $category_products, 'pagination' => $this->template_data['pagination']),$unique_page_cache_id,'catalog');
			}
		}
		else
		{
			if(!$category_products = cache::get('featured_products','catalog'))
			{
				$category_products = $products->find_all_with_images(array('where' => 'is_active = 1 AND is_featured = 1','order' => 'created_at DESC','limit' => $products_per_page));
				
				cache::save($category_products,'featured_products','catalog');
			}
		}
		
		$this->breadcrumb[] = array('catalog' => 'Catalogue');
		
		array_shift($category_named_path);	//Pop off root element, as we dont want this in the breadcrumb
		foreach($category_named_path as $index => $category)
			$this->breadcrumb[] = array($category['url'] => $category['name']);
				
		$this->template_data['category_products'] = $category_products;
	}
	
	
	/**
	 * Display a product
	 */
	function product()
	{
		$product_review =& model::create('product_review');
		
		$product_cache_id = $this->params['url_params'];
		
		if(! ($this->template_data['product'] = cache::get($product_cache_id,'product')) )
        {
        	$obj_product 				=& model::create('product');
			$obj_product_variant_group 	=& model::create('product_variant_group');
			$obj_product_variant 		=& model::create('product_variant');
			
			if(!$obj_product->find_by_field('url_name',$this->params['url_params']))
				HTTP::exit_on_header(404);
			
    		$obj_product->find_foreign_key('image_id');
    		
			$this->template_data['product'] 			= $obj_product->fields_as_associative_array();
			$this->template_data['product']['image'] 	= $obj_product->image->fields_as_associative_array();
			
    		//Load all product variants			
			$product_variant_groups_as_array = (array)$obj_product_variant_group->find_all_variant_groups_for_product($obj_product->id);
			
			foreach($product_variant_groups_as_array as $key => $arr_product_variant_group)
			{
				$this->template_data['product']['variant_groups'][$arr_product_variant_group['id']] = $arr_product_variant_group;
				$this->template_data['product']['variant_groups'][$arr_product_variant_group['id']]['variants'] = $obj_product_variant->find_all_variants_in_group($arr_product_variant_group['id']);
			}  
		
			//Load product reviews
        	$this->template_data['product']['reviews'] = $product_review->find_all_by_product_id($obj_product->id);
        	
        	if(!$average_rating = $product_review->get_average_rating($obj_product->id))
        		$average_rating = DEFAULT_AVERAGE_PRODUCT_RATING;

        	$this->template_data['product']['average_review_rating'] = $average_rating;
        	
        	cache::save($this->template_data['product'],$product_cache_id,'product');
      	} 
	      				
		//Load category tree
		$categories =& model::create('category');
		$selected_category_id = $this->template_data['product']['category_id'];
		$category_named_path = $categories->get_category_named_path($selected_category_id);
		$this->template_data['categories'] = $categories->load_category_path($category_named_path);
		$this->template_data['selected_category_id'] = $selected_category_id;
	
	    //Get breadcrumb
		$this->breadcrumb[] = array('catalog' => 'Catalog');
		array_shift($category_named_path);	//Pop off root element, as we dont want this in the breadcrumb   
   	 	foreach($category_named_path as $index => $category)
			$this->breadcrumb[] = array($category['url'] => $category['name']);
    
    	$this->breadcrumb[] = array('product/'.$this->template_data['product']['url_name'] => $this->template_data['product']['name']);
    	
    	//Log product view
    	$product_view =& model::create('product_view');
    	$product_view->log_hit($this->template_data['product']['id'], HTTP::remote_ip_address());
    	
    	$user_submitted_review_session_key = 'user_product_review:' . $this->template_data['product']['id'];
    	if(isset($_SESSION[$user_submitted_review_session_key]))
    		$this->template_data['product']['has_user_submitted_review'] = $_SESSION[$user_submitted_review_session_key];
    	else
    	{
    		if(empty($_SESSION['account_id']))
    			$this->template_data['product']['has_user_submitted_review'] = false;
    		else
    			$this->template_data['product']['has_user_submitted_review'] =  $product_review->find_by_account_id_and_product_id($_SESSION['account_id'],$this->template_data['product']['id']);
    	
    		$_SESSION[$user_submitted_review_session_key] = (int)$this->template_data['product']['has_user_submitted_review'];
    	}
    	
		$account =& model::create('account');
		if(isset($_SESSION['account_id']) && $account->find($_SESSION['account_id']))
			$this->template_data['is_user_email_activated'] = $account->is_email_activated;
		else
			$this->template_data['is_user_email_activated'] = false;
	}
	
	/**
	 * Search the product catalog using a keyword weighted search
	 */
	function search()
	{		
		//If the search form was submitted with something in it
		if(!empty($_GET['q']))
		{
			$search =& model::create('search');
			
			$search->search_query 		= $_GET['q'];
			$search->results_per_page 	= config::get('search','results_per_page');
			$search->in_category_id 	= isset($_GET['c']) ? (int)$_GET['c'] : null;
			$search->page_number 		= isset($_GET['page']) ? (int)$_GET['page'] : 1;
			
			if($search->count_products() === false)
				$this->flash['error'] = $search->_errors;
    		
    		if($search->find_all_products() === false)
    			$this->flash['error'] = $search->_errors;

			//If we only have 1 search result, automatically redirect to that page
			if($search->total_number_of_search_results == 1)
				$this->redirect_to('product',urlencode($search->search_results[0]['url_name']));

			//Calculate search pagination
    		$start_from_record_number = $search->calculate_record_offset();
    		
    		if($search->is_last_page())
    			$last_search_record_number = $search->total_number_of_search_results;
    		else
    			$last_search_record_number = $start_from_record_number + $search->results_per_page;
    					
    		$this->template_data['search']['results'] 			= $search->search_results;
			$this->template_data['search']['page_number'] 		= $search->page_number;
			$this->template_data['search']['results_count'] 	= $search->total_number_of_search_results;
			$this->template_data['search']['page_count'] 		= $search->calculate_number_of_pages();
			$this->template_data['search']['start_from_row'] 	= $start_from_record_number;
			$this->template_data['search']['last_record'] 		= $last_search_record_number;
			$this->template_data['search']['query'] 			= $search->search_query;
			$this->template_data['search']['keywords'] 			= $search->keywords_to_search_for;
			$this->template_data['search']['is_active'] 		= true;
		}

		//Find all possible categories to filter by
		if(!$product_categories = cache::get('product_categories','categories'))
		{
			$category =& model::create('category');

			$product_categories = $category->get_categories_as_list();
			
			cache::save($product_categories,'product_categories','categories');
		}
				
		$this->template_data['product']['categories'] = $product_categories;
		$this->template_data['search']['in_category'] = isset($_GET['c']) ? $_GET['c'] : ROOT_CATEGORY_NODE;
		
		$this->breadcrumb[] = array('search' => 'Search');
	}
	
	/**
	 * Process checkout
	 */
	function checkout()
	{
		//Verify the user has atleast 1 item in there cart before proceeding if logged in
		if(isset($_SESSION['account_id']) && $this->cart->get_total_items() == 0)
		{
			$this->flash['notice'][] = 'Please add atleast one product to your cart before checking out';
			$this->redirect_to('catalog');
		}
			
		//Process a login attempt from checkout page
		if(isset($this->params['login']))
		{
			$this->login($redirect_back_to_checkout = true);
		}
		else if(isset($this->params['signup']))
		{
			$this->_signup(); //Process a new signup
		}
		else if(isset($_SESSION['account_id']) && isset($_SESSION['account_group']) && $_SESSION['account_group'] == 'members')
		{			
			//If user is already logged in as member so we display there cart, shipping info and payment form
			$this->template_data['is_logged_in_as_member'] = true;
	
			//Check if any cart events have occured and process them (update quantities etc)
			$this->cart();
					
			$payment_methods = config::get('payment_method');
			$accepted_payment_methods = array('credit_card','bank_deposit');
			
			if(isset($this->params['payment_method']) && in_array($this->params['payment_method'],$accepted_payment_methods))
				$selected_payment_method = $this->params['payment_method'];
			else
				$selected_payment_method = 'credit_card';
			
			//Confirm users order details and proceed to order confirmation page
			if(isset($this->params['confirm_order']))
			{
				//Check that the selected addresses are valid
				$address_book =& model::create('address_book');
				if($address_book->find_by_id_and_account_id($this->params['delivery_address_book_id'],$_SESSION['account_id']))
					$_SESSION['ship_to_address_book_id'] = $address_book->id;
				else
					$this->flash['error'][] = 'Your selected delivery address is not valid';

				if($address_book->find_by_id_and_account_id($this->params['billing_address_book_id'],$_SESSION['account_id']))
					$_SESSION['billing_address_book_id'] = $address_book->id;
				else
					$this->flash['error'][] = 'Your selected billing address is not valid';
				
				//If no errors were found, proceed to order confirmation
				if(!isset($this->flash['error']) || count($this->flash['error']) == 0)
				{
					$_SESSION['payment_method'] = $selected_payment_method;
					$_SESSION['order_comments'] = $this->params['order']['comments'];
					
					$this->redirect_to('confirm-order',null,'https');
				}
				else
				{
					//Save order data if invalid
					$this->template_data['order']['comments'] = $this->params['order']['comments'];
				}
			}
			
			//Set users delivery address
			$delivery_address =& model::create('address_book');
			
			if(!isset($_SESSION['ship_to_address_book_id']) || !$delivery_address->find($_SESSION['ship_to_address_book_id']))
			{
				$delivery_address->find_by_field_array(array('account_id' => $_SESSION['account_id'], 'is_primary' => 1, 'is_locked' => 0));
				$_SESSION['ship_to_address_book_id'] = $delivery_address->id;
			}
							
			//Set users billing address
			$billing_address =& model::create('address_book');
			if(!isset($_SESSION['billing_address_book_id']) || !$billing_address->find($_SESSION['billing_address_book_id']))
			{
				$billing_address->find_by_field_array(array('account_id' => $_SESSION['account_id'], 'is_primary' => 1, 'is_locked' => 0));
				$_SESSION['billing_address_book_id'] = $billing_address->id;
			}
						
			//Calculate shipping costs
			$obj_shipping_zone =& model::create('shipping_zone');
			$obj_shipping_tier =& model::create('shipping_tier');
			
			$order_weight 		= $this->cart->get_total_weight();
			$obj_shipping_zone->find_by_city_and_country($delivery_address->city, $delivery_address->country_id);
			$obj_shipping_tier->find_by_weight_and_shipping_zone_id($order_weight, $obj_shipping_zone->id);

			$delivery_address->find_foreign_key('country_id');
			
			$this->template_data['delivery_address'] 		= $delivery_address->fields_as_associative_array();
			$this->template_data['delivery_address']['country']= $delivery_address->country->fields_as_associative_array();
			$this->template_data['billing_address'] 		= $billing_address->fields_as_associative_array();			
			$this->template_data['total_weight'] 			= $order_weight;
			$this->template_data['shipping_zone'] 			= $obj_shipping_zone->fields_as_associative_array();
			$this->template_data['total_cost_of_shipping'] 	= $obj_shipping_tier->amount;
			
			//Find all address entries for this account
			$obj_country =& model::create('country');
			$address_book =& model::create('address_book');
			$this->template_data['address_books'] = $address_book->find_all(array('where' => 'account_id = '.$_SESSION['account_id'].' AND is_locked = 0'));
			foreach($this->template_data['address_books'] as $address_id => $address)
			{
				$obj_country->find($address['country_id']);
				$this->template_data['address_books'][$address_id]['country'] = $obj_country->fields_as_associative_array();	
			}
			
			//Set payment methods
			$this->template_data['payment_method'] = $payment_methods;
		}
		else
		{
			//User is not logged in so we give the user the option of logging in or filling out the signup form
			$obj_country =& model::create('country');
			$this->template_data['countries'] = $obj_country->get_countries_as_list();
			$this->template_data['address_book']['country_id'] = DEFAULT_COUNTRY_ID;
		}		
		
		$this->breadcrumb[] = array('checkout' => 'Checkout');
	}
	
	/**
	 * Displays a confirm page for the order before processing
	 */
	function confirm_order()
	{
		//Validate user has passed through checkout first
		if(!isset($_SESSION['ship_to_address_book_id']) || !isset($_SESSION['billing_address_book_id']))
		{
			$this->flash['error'][] = 'Please confirm order details before proceeding';
			$this->redirect_to('checkout',null,'https');
		}	

		//Load users delivery & billing address
		$delivery_address 	=& model::create('address_book',$_SESSION['ship_to_address_book_id']);
		$billing_address 	=& model::create('address_book',$_SESSION['billing_address_book_id']);

		$delivery_address->find_foreign_key('country_id');
		$billing_address->find_foreign_key('country_id');
		
		$this->template_data['delivery_address'] 		= $delivery_address->fields_as_associative_array();
		$this->template_data['delivery_address']['country']= $delivery_address->country->fields_as_associative_array();
		$this->template_data['billing_address'] 		= $billing_address->fields_as_associative_array();
		$this->template_data['billing_address']['country'] = $billing_address->country->fields_as_associative_array();

		//Calculate shipping cost
		$obj_shipping_zone =& model::create('shipping_zone');
		$obj_shipping_tier =& model::create('shipping_tier');
			
		$order_weight 		= $this->cart->get_total_weight();
		$obj_shipping_zone->find_by_city_and_country($delivery_address->city, $delivery_address->country_id);
		$obj_shipping_tier->find_by_weight_and_shipping_zone_id($order_weight, $obj_shipping_zone->id);

		//Find the order totals
		$this->template_data['shipping_zone'] 			= $obj_shipping_zone->fields_as_associative_array();
		$this->template_data['order_weight'] 			= $order_weight;
		$this->template_data['total_cost_of_shipping'] 	= $obj_shipping_tier->amount;
		
		//Load confirmation message content
		$content =& model::create('content',CONFIRM_ORDER_CONTENT_ID);
		$this->template_data['content'] = $content->fields_as_associative_array();
		
		//Calculate GST Component
		$order_total = $this->template_data['cart']['price_total'] + $this->template_data['total_cost_of_shipping'];
		$this->template_data['order']['gst_component'] = $order_total * config::get('core','gst');
		$this->template_data['order_comments'] = $_SESSION['order_comments'];
		
		$this->breadcrumb[] = array('cart' => 'Shopping Cart');
		$this->breadcrumb[] = array('checkout' => 'Checkout');
		$this->breadcrumb[] = array('confirm-order' => 'Confirm Your Order');
		
		//If they clicked the place order button
		if(isset($_POST['place_order_x']))
		{
			//If payment method is credit card, rediret to payment gateway
			if($_SESSION['payment_method'] == 'credit_card')
			{
				require(PAYMENT_GATEWAY_DIR . '/dps/payment_gateway.php');
				
				$payment_gateway =& new payment_gateway();
				$payment_gateway->start_transaction();
				
				if($payment_gateway_response_xml = $payment_gateway->generate_payment_request($order_total))
				{
					if($hosted_payment_page_url = $payment_gateway->get_hosted_payment_page_url($payment_gateway_response_xml))
						$this->redirect_to_url($hosted_payment_page_url);
					else
					{
						$this->flash['error'] = $payment_gateway->_errors;	
						$this->redirect_to('checkout',null,'https');		
					}
				}
				else
				{
					$this->flash['error'] = $payment_gateway->_errors;	
					$this->redirect_to('checkout',null,'https');	
				}
			}		
			
			$this->redirect_to('order-placed',null,'https');	
		}
	}
	
	function order_placed()
	{
		if(!isset($_SESSION['account_id']) || !isset($_SESSION['ship_to_address_book_id']) || !isset($_SESSION['billing_address_book_id']))
		{
			//Is this the payment gateway performing a test before redirecting the customer
			if(isset($_GET['result']))
				exit('200 OK');
			
			//Otherwise a user has incorrectly visited to this page
			$this->flash['error'][] = 'Please confirm order details before proceeding';
			$this->redirect_to('checkout',null,'https');
		}	
		
		$account =& model::create('account',$_SESSION['account_id']);

		//Load users delivery & billing address
		$delivery_address =& model::create('address_book',$_SESSION['ship_to_address_book_id']);
		$billing_address =& model::create('address_book',$_SESSION['billing_address_book_id']);
		
		$delivery_address->find_foreign_key('country_id');
		$billing_address->find_foreign_key('country_id');
		
		//Calculate shipping cost
		$obj_shipping_zone =& model::create('shipping_zone');
		$obj_shipping_tier =& model::create('shipping_tier');
			
		$order_weight 		= $this->cart->get_total_weight();
		$obj_shipping_zone->find_by_city_and_country($delivery_address->city, $delivery_address->country_id);
		$obj_shipping_tier->find_by_weight_and_shipping_zone_id($order_weight, $obj_shipping_zone->id);
		
		//Find the order totals
		$order_total = $this->template_data['cart']['price_total'] + $obj_shipping_tier->amount;

		//Assign all our template data so we can generate and send off a confirmation email
		$this->template_data['delivery_address'] 		= $delivery_address->fields_as_associative_array();
		$this->template_data['delivery_address']['country']= $delivery_address->country->fields_as_associative_array();
		$this->template_data['billing_address'] 		= $billing_address->fields_as_associative_array();
		$this->template_data['billing_address']['country'] = $billing_address->country->fields_as_associative_array();
		
		$this->template_data['total_cost_of_shipping'] = $obj_shipping_tier->amount;
		$this->template_data['order']['gst_component'] = $order_total * config::get('core','gst');
		$this->template_data['order_comments'] = $_SESSION['order_comments'];
	
		$order =& model::create('order');
		
		//If payment method is credit card confirm payment was accepted with payment gateway
		if($_SESSION['payment_method'] == 'credit_card')
		{
			require(PAYMENT_GATEWAY_DIR . '/dps/payment_gateway.php');
			
			$payment_gateway =& new payment_gateway();
			
			if(!$payment_response_xml = $payment_gateway->get_payment_response_xml($_GET['result']))
				$this->redirect_to_with_error('checkout',null,'https',$payment_gateway->_errors);
			
			if(!$payment_gateway->set_payment_confirmation_response_object_from_xml($payment_response_xml))
				$this->redirect_to_with_error('checkout',null,'https',$payment_gateway->_errors);
				
			if(!$payment_gateway->is_payment_authorized())
				$this->redirect_to_with_error('checkout',null,'https',$payment_gateway->_errors);
				
			$order->transaction_reference = $payment_gateway->get_payment_response_value('DpsTxnRef');
			$payment_gateway->end_transaction();
		}
		
		//Create order entry in order table
		$order->account_id = $account->id;
		$order->email_address = $account->email;
		$order->status = 'pending';
		$order->referer_id = !empty($_SESSION['referer_id']) ? $_SESSION['referer_id'] : null;
		$order->comments = $_SESSION['order_comments'];
		$order->payment_method = $_SESSION['payment_method'];
		$order->amount_paid = ($_SESSION['payment_method'] == 'credit_card') ? $order_total : 0.00;
		$order->shipping_total = $obj_shipping_tier->amount;
		$order->gst_component = $order_total * config::get('core','gst');
		$order->total = $order_total;
		$order->set_address_from_array('delivery',$delivery_address->fields_as_associative_array());
		$order->set_address_from_array('billing',$billing_address->fields_as_associative_array());

		if($order->save())
		{
			//Create order product records
			$cart_products = $this->cart->get_products_in_cart();
			$order_product =& model::create('order_product');
			
			foreach($cart_products['products'] as $SKU => $cart_product)
			{
				$order_product->order_id = $order->id;
				$order_product->product_id = $cart_product['id'];
				$order_product->quantity = $cart_product['quantity'];
				$order_product->cost_price = $cart_product['cost_price'];
				$order_product->sale_price = $cart_product['sale_price'];
				$order_product->serialized_variations = isset($cart_product['variants']) ? serialize($cart_product['variants']) : null;
				
				$order_product->insert();
				
				$order_product->clear_field_values();
			}
			
			//Order has been successfully completed, send confirmation email and complete the order
			foreach($this->template_data as $key => $data)
				$this->assign_template_data($key,$data);
							
			$order_summary = $this->fetch_template('_order_email');
			
			$email_order_complete =& model::create('email',EMAIL_ORDER_COMPLETE);
			$email_order_complete->to = $account->email;
			$email_order_complete->parse_message(array('order_summary' => $order_summary,'order_number' => $order->reference_code));
			$email_order_complete->send();
				
			//Clear the order session data and redirect to confirmation
			$this->cart->flush_cart();
			unset($_SESSION['payment_method'], $_SESSION['order_comments'], $_SESSION['ship_to_address_book_id'], $_SESSION['billing_address_book_id']);
		}
		else
		{
			//Saving of the order failed! This is a critical error, particularly if the user has paid
			mail(config::get('contact','email'),'Saving of an order failed! Please review',"A dump of the order that failed: " . print_r($order,true) . ' The products in the cart were: ' . print_r($this->cart->get_products_in_cart(),true),"From: webmaster@" . config::get('host','domain'));
			
			$this->flash['error'] = $order->_errors;	
			$this->flash['notice'][] = 'The store owner has automatically been notified of this error and will contact you if required.';
			$this->redirect_to('checkout',null,'https');
		}
				
		$content =& model::create('content',ORDER_COMPLETED_CONTENT_ID);
		$this->template_data['order_placed_page'] = $content->fields_as_associative_array();
		$order_placed_page_content = str_replace('{email_address}',$account->email,$content->content);
		$order_placed_page_content = str_replace('{order_number}',$order->reference_code,$order_placed_page_content);
		
		$this->template_data['order_placed_page']['content'] = $order_placed_page_content;
		$this->template_data['order_number'] = $order->reference_code;
		
		$this->breadcrumb[] = array('order-complete' => 'Order complete');
	}
	
	function order_failed()
	{
		$this->flash['error'][] = 'Your payment was declined.'; 
		$this->redirect_to('checkout',null,'https');
	}
	
	/**
	 * Display and update users cart
	 */
	function cart()
	{
		//If the user adds a product to their cart
		if(isset($this->params["product"]["add_product_to_cart"]) && isset($this->params['product']["id"]))
		{
			$product =& model::create('product');
			if($product->find($this->params['product']["id"]))
			{
				$quantity = empty($this->params['product']['quantity']) ? 1 : abs((int)$this->params['product']['quantity']);
				$variants = isset($this->params["product"]["variants"]) ? $this->params["product"]["variants"] : null;	//Grouped form array of product variant list boxes
			
				if($this->cart->add_to_cart($product->id, $quantity, $variants) !== false)
	            {
	                $this->flash['notice'][] = 'Added product '.$product->name.' to your cart';
	                $this->template_data['cart'] = $this->cart->get_products_in_cart();		
	            }
	            else
	                $this->flash['error'][] = 'Could not add this item to your cart';
			}
			else
				$this->flash['error'][] = 'Product does not exist!';
        }
        else if(isset($this->params["cart"]["update"]))
		{
			//Remove checked items from the cart
			if(isset($this->params["cart"]["remove_product"]))
			{
				foreach($this->params["cart"]["remove_product"] as $key => $shopping_cart_unit)
					$this->cart->remove_from_cart($shopping_cart_unit);
			}
			
			//Update cart quantities
			foreach($this->params["cart"]["quantity"] as $shopping_cart_unit => $quantity)
				$this->cart->set_quantity($shopping_cart_unit,$quantity);
				
			$this->flash['notice'][] = 'Your cart has been updated.';
            $this->template_data['cart'] = $this->cart->get_products_in_cart();	
		}
		
		$product =& model::create('product');
		$cart_product_ids = $this->cart->get_product_ids_in_cart();
		$this->template_data['related_and_featured_products'] = $product->find_all_with_images(array('where' => 'is_active = 1 AND is_featured = 1 ' . (empty($cart_product_ids) ? '' : 'AND id NOT IN(' . implode(',',$cart_product_ids) . ')'),'limit' => 3));
		
		$this->breadcrumb[] = array('cart' => 'Shopping Cart');
	}
	
	
	/**
	 * Display a dynamic cms page
	 */
	function page()
	{
		//Load dynamic page content
		$content =& model::create('content');
		if(ctype_digit($this->params['url_params']))
			$content->find($this->params['url_params']);
		else
			$content->find_by_field('url_name',$this->params['url_params']);
		
		if($content->id == null)
			$content->find(ERROR_404_CONTENT_ID);
			
		$this->template_data['content'] = $content->fields_as_associative_array();	
	
		$this->breadcrumb[] = array('page/'.$content->url_name => $content->title);
	}
	
	/**
	 * Display contact us form and information
	 */
	function contact_us()
	{
		//Load contact us content
		$content =& model::create('content',CONTACT_US_CONTENT_ID);
	
		if(isset($this->params['send']))
		{
			$this->template_data['content'] = $content->fields_as_associative_array();
			
			//Check that user has a valid session to prevent form spam
			if($this->params['user_form_key'] != session_id())
				$this->flash['error'][] = 'Please enable cookies in your browser (spam protection)';
				
			//Validate form
			if(!isset($this->params['contact']['name']) || $this->params['contact']['name'] == '')
				$this->flash['error'][] = 'Your full name is required';

			if(!isset($this->params['contact']['email']) || $this->params['contact']['email'] == '')
				$this->flash['error'][] = 'Your E-Mail address is required';
			else if(!preg_match(EMAIL_REGEX,$this->params['contact']['email']))
				$this->flash['error'][] = 'Your E-Mail address is not correct';
				
			if(!isset($this->params['contact']['enquiry']) || $this->params['contact']['enquiry'] == '')
				$this->flash['error'][] = 'Please enter your enquiry first';
											
			if(!isset($this->flash['error']) || count($this->flash['error']) == 0)
			{
				$email =& model::create('email',EMAIL_CONTACT_US);
				$email->to = config::get('contact','email');
				$email->parse_message(array('name' => $this->params['contact']['name'], 
											'email' => $this->params['contact']['email'], 
											'phone' => $this->params['contact']['phone'],
											'subject' => $this->params['contact']['subject'],
											'enquiry' => $this->params['contact']['enquiry']));
				$email->send();
				
				$this->template_data['form_has_been_sent'] = true;	
			}
			
			$this->template_data['contact'] = $this->params['contact'];
		}
		
		$this->template_data['session_key'] = session_id();
		
		$this->breadcrumb[] = array('contact-us' => 'Contact Us');
	}
	
	/**
	 * Reset and send a users password via email
	 */
	function email_password()
	{		
		if(isset($this->params['account']['email']))
		{				
			$ip_bruteforce_ban =& model::create('ip_bruteforce_ban');
			
			//Check if this IP is banned
			if(!$ip_bruteforce_ban->is_ip_address_banned(HTTP::remote_ip_address(),'email-password'))
			{
				$account =& model::create('account');
				$account->find_by_field('email',$this->params['account']['email']);
				if($account->id)
				{
					$new_password = $account->generate_random_password();
					$account->password_hash = $account->hash_password($new_password);
					$account->save();
					
					$email =& model::create('email',EMAIL_NEW_PASSWORD);
					$email->to = $account->email;
					$email->parse_message(array('password' => $new_password, 'site_title' => config::get('content','title'), 'domain_name' => config::get('host','http')));
					$email->send();
					
					$this->flash['notice'][] = 'Your password has been reset and emailed to you.';
				}
				else
				{
					$ip_bruteforce_ban->log_failed_attempt(HTTP::remote_ip_address(),'email-password');		
					$this->flash['error'][] = 'Account does not exist';
				}
			}
			else
				$this->flash['error'][] = 'To many failed attempts! Please try again later';
		}
		
		$this->breadcrumb[] = array('email-password' => 'Forgot Your Password?');
	}
	
	/**
	 * Process account logins
	 */
	function login($redirect_back_to_checkout = false)
	{		
		if(isset($this->params['login']))
		{
			$ip_bruteforce_ban =& model::create('ip_bruteforce_ban');
			
			//Check if this IP is banned
			if(!$ip_bruteforce_ban->is_ip_address_banned(HTTP::remote_ip_address(),'login'))
			{
				$account =& model::create('account');
				if($account->login($this->params['login']['email'], $this->params['login']['password']))
				{
					//If user wishes to stay logged in permanently
					if(!empty($this->params['login']['save_details']))
					{
						$login_credentials = array('email' => $this->params['login']['email'],'hashed_password' => $account->hash_password($this->params['login']['password']));	
						$serialized_credentials = serialize($login_credentials);
						$encoded_credentials = base64_encode($serialized_credentials);
						
						setcookie('auto_login',$encoded_credentials,time()+2629743,'/',config::get('host','domain'),false);
					}
					
					if($account->group->name == 'members')
					{
						//If the users session cart and saved cart are set and have atleast 1 product in both
						if(!empty($account->serialized_cart))
						{
							if(count(unserialize($account->serialized_cart)) > 0 && $this->cart->get_total_items() > 0)
								$this->flash['notice'][] = 'Your cart has been merged with previous';
						}
						
						//Synchronize users session cart with database
						$this->cart->find_cart($account->id);
						
						if($redirect_back_to_checkout)
							$this->redirect_to('checkout',null,'https');
						else
							$this->redirect_to('my-account','index','https');
					}
					else if($account->group->name == 'administrators')
						$this->redirect_to('admin','index','https');
					else
						$this->flash['error'][] = 'User account does not belong to a user group!';
				}
				else
				{
					$ip_bruteforce_ban->log_failed_attempt(HTTP::remote_ip_address(),'login');
					$this->flash['error'][] = 'Your E-Mail or Password is incorrect.';
				}
			}
			else
				$this->flash['error'][] = 'To many failed attempts! Please try again later';
			
			$this->template_data['login']['email'] = $this->params['login']['email'];
		}	
		
		//Process any signup events, load country list
		$this->_signup();
		
		$this->breadcrumb[] = array('login' => 'Log-In to your account');
	}
	
	/**
	 * Display a raw product list, used for site indexing
	 */
	function show_all_products()
	{
		$product =& model::create('product');
		$product_list = $product->find_all(array('where' => 'is_active = 1','order' => 'created_at DESC', 'limit' => '1000'));
		
		$this->template_data['products'] = $product_list;
		
		$this->breadcrumb[] = array('show-all-products' => 'Product Listing');
	}
	
		
	/**
	 * Create a new member account
	 */
	function _signup()
	{
		if(isset($this->params['account']))
		{
			$account =& model::create('account');
			$account->set_field_values_from_array($this->params['account']);
			
			if(isset($this->params['account']['password']) && strlen($this->params['account']['password']) >= MIN_PASSWORD_LENGTH)
				$account->password_hash = $account->hash_password($this->params['account']['password']);
			
			//Set user to belong to members group
			$group =& model::create('group');
			$group->find_by_field('name','members');
			$account->group_id = $group->id;
				
			if(strcmp($this->params['account']['password'],$this->params['account']['password_confirm']) != 0)
				$this->flash['error'][] = 'Please check your passwords match';
			else if($account->insert())
			{
				$address_book =& model::create('address_book');
				$address_book->set_field_values_from_array($this->params['address_book']);
				$address_book->account_id = $account->id;
				$address_book->is_primary = 1;
				$address_book->is_locked = 0;
				
				if($address_book->insert())
				{
					$account->send_new_account_created_email();
				
					//Log the user in, save cart to database and destroy the session cart
					$this->cart->save_cart($account->id);
								
					$account->find_foreign_key('group_id');
					$_SESSION['account_id'] = $account->id;
					$_SESSION['account_group'] = $account->group->name;
					
					//Destroy there session cart as its now saved to database when logged in
					unset($_SESSION['cart']);
					
					$this->flash['notice'][] = 'Your new account has been created. A confirmation E-Mail has been sent to the provided email address.';
					
					//If the user signed up from the main signup page else they used the checkout page
					if($this->params['action'] == 'login')
						$this->redirect_to('my-account','index','https');
					else
						$this->redirect_to('checkout',null,'https');
				}
				else
				{
					$this->flash['error'] = $address_book->_errors;
					
					//Rollback transaction
					$account->delete($account->id);
				}
			}
			else
				$this->flash['error'] = $account->_errors;
						
			$this->template_data['account'] = $this->params['account'];
			$this->template_data['address_book'] = $this->params['address_book'];
		}
		else
			$this->template_data['address_book']['country_id'] = DEFAULT_COUNTRY_ID; //Set default country
		
		$obj_country =& model::create('country');
		$this->template_data['countries'] = $obj_country->get_countries_as_list();
	}
	
	/**
	 * Shortcut to admin controller, from /admin redirects to /admin/index
	 */
	function admin()
	{
		$this->redirect_to('admin','index','https');
	}
	
	/**
	 * Method only accessed once when the application is first installed
	 * Deletes the /install dir and all its files
	 */
	function installation_complete()
	{
		$install_dir = SITE_ROOT_DIR . '/public/install';
		
        if(is_dir($install_dir))
        	$dir_handle = opendir($install_dir);
        	
        if($dir_handle && is_writable($install_dir))
        {
        	while($file = readdir($dir_handle))  
        	{
                if($file != "." && $file != "..")  
                {
                	if (!is_dir($install_dir."/".$file))
                    	@unlink($install_dir."/".$file);                     
                }
        	}
        	
      		closedir($dir_handle);
        	@rmdir($install_dir);
        }
     
        if(file_exists($install_dir))
        	$this->flash['error'][] = 'Could not delete /install directory, you must delete this manually';
	}
	
	function activate_account()
	{
		$account =& model::create('account');
		
		$this->template_data['activation_successfull'] = $account->activate_email_by_key($this->params['url_params']);
	}
}
?>