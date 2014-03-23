<?php
/**
 * Admin statistical data for products, orders, visitors
 * 
 * @package efusion
 * @subpackage controllers
 */
class statistics_controller extends admin_controller
{
	function statistics_controller(&$application)
	{
		parent::admin_controller($application);
		
		$this->breadcrumb[] = array('admin/statistics/index' => 'Statistics');	
	}
	
	function index()
	{
		$google_maps_api_key = config::get('core','google_maps_api_key');
		$this->template_data['is_google_maps_api_key_set'] = !empty($google_maps_api_key);
	}
	
	function product_views()
	{
		$pager_params = $this->_get_pager_url_paramters();
		if(empty($pager_params['order_by']))
			$pager_params['order_by'] = 'views DESC';
		
		$pager_options = array('select' => 'product.name,product.url_name,sum("view_count") as "views"', 
								'join' => 'INNER JOIN product ON product.id = product_view.product_id',
								'where' => ($pager_params['filter_by']) ? "LOWER(product.name) LIKE '%".$this->application->db->escape_string($pager_params['filter_by'])."%'" : null, 
								'group' => 'product.id, product.name, product.url_name',
								'escape' => false, 
								'order' => $this->application->db->escape_string($pager_params['order_by']));
	
		$product_view =& model::create('product_view');
		$this->template_data['products_paged'] = $product_view->find_all_paged($pager_options,config::get('admin','results_per_page'),$pager_params['page']);
	
		$this->breadcrumb[] = array('admin/statistics/product-views' => 'Product Views');
	}

	/**
	 * Most sold products
	 */
	function products_sold()
	{
		$pager_params = $this->_get_pager_url_paramters();
		if(empty($pager_params['order_by']))
			$pager_params['order_by'] = 'units_sold DESC';
		
		$pager_options = array('select' => 'product.name,product.url_name,SUM(order_product.quantity) as units_sold, order_product.sale_price, order_product.cost_price', 
								'join' => 'INNER JOIN order_product ON order_product.product_id = product.id',
								'where' => ($pager_params['filter_by']) ? "LOWER(product.name) LIKE '%".$this->application->db->escape_string($pager_params['filter_by'])."%'" : null, 
								'group' => 'product.id, product.name, product.url_name, "order_product".sale_price, "order_product".cost_price',
								'escape' => false, 
								'order' => $this->application->db->escape_string($pager_params['order_by']));
	
		$product_view =& model::create('product');
		$this->template_data['products_paged'] = $product_view->find_all_paged($pager_options,config::get('admin','results_per_page'),$pager_params['page']);
	
		$this->breadcrumb[] = array('admin/statistics/products-sold' => 'Products Sold');
	}
	
	function website_referrers()
	{
		$pager_params = $this->_get_pager_url_paramters();
		if(empty($pager_params['order_by']))
			$pager_params['order_by'] = '"hits" DESC';
			
		$referrers =& model::create('referer');
		$this->template_data['referrers'] = $referrers->find_all_paged(array('select' => '"url","hits"', 'order' => $pager_params['order_by'],'escape' => false),config::get('admin','results_per_page'),$pager_params['page']);

		$this->breadcrumb[] = array('admin/statistics/website-referrers' => 'Website Referrers');
	}
	
	function order_referrers()
	{
		$sql = 'SELECT count("order".id) AS number_of_orders, 
					   sum("order"."total") AS order_value, 
					   referer."url"
				FROM "order"
				INNER JOIN referer ON referer.id = "order".referer_id
				GROUP BY referer.id, referer.url 
				ORDER BY "order_value" DESC
				LIMIT 50';

		$this->template_data['order_referrers'] = $this->application->db->query_as_array($sql);
		$this->breadcrumb[] = array('admin/statistics/order-referrers' => 'Orders by Referrer');
	}
	
	function orders_today()
	{
		$orders =& model::create('order');
		
		$a_day_back_from_now = date(SQL_DATETIME_FORMAT,strtotime('-1 day'));
		
		$orders_by_hour = $orders->find_all(array(	'escape' => false,
													'select' => 'EXTRACT(HOUR FROM created_at) AS id, COUNT(id) AS order_count, SUM("total") AS total_order_value',
													'where' => "created_at > '".$a_day_back_from_now."'",
													'group' => 'EXTRACT(HOUR FROM created_at)'));
		
		
		$chart_data = array();
		for($i = 0;$i < 24; $i++)
			$chart_data[$this->convert_24hr_to_12hr_notation($i)] = isset($orders_by_hour[$i]) ? $orders_by_hour[$i]['order_count'] : 0;
		
		$this->_generate_chart($chart_data,'orders_today.png');

		$this->breadcrumb[] = array('admin/statistics/orders-today' => 'Orders made today');		
	}
	
	function orders_this_week()
	{
		$orders =& model::create('order');
		
		$a_week_back_from_now = date(SQL_DATETIME_FORMAT,strtotime('-1 week'));
		
		$orders_by_day = $orders->find_all(array(	'escape' => false,
													'select' => 'EXTRACT(DAY FROM created_at) AS id, COUNT(id) AS order_count, SUM("total") AS total_order_value',
													'where' => "created_at > '".$a_week_back_from_now."'",
													'group' => 'EXTRACT(DAY FROM created_at)'));
		
		
		$chart_data = array();
		$days_in_past_week = array();
		
		for($i = 6;$i >= 0; $i--)
			$days_in_past_week[] = date('j-D-S',strtotime("-$i days"));
		
		foreach($days_in_past_week as $day)
		{
			$day_info = explode('-',$day);
			$day_as_int = $day_info[0];
			$day_as_string = $day_info[1];
			$day_suffix = $day_info[2];
			
			$chart_data[$day_as_string.' '.$day_as_int.$day_suffix] = isset($orders_by_day[$day_as_int]) ? $orders_by_day[$day_as_int]['order_count'] : 0;
		}
		
		$this->_generate_chart($chart_data,'orders_this_week.png');

		$this->breadcrumb[] = array('admin/statistics/orders-this-week' => 'Orders made this week');		
	}

	function orders_this_year()
	{
		$orders =& model::create('order');
		
		$a_year_back_from_now = date(SQL_DATETIME_FORMAT,strtotime('-1 year'));
		
		$orders_by_month = $orders->find_all(array(	'escape' => false,
													'select' => 'EXTRACT(MONTH FROM created_at) AS id, COUNT(id) AS order_count, SUM("total") AS total_order_value',
													'where' => "created_at > '".$a_year_back_from_now."'",
													'group' => 'EXTRACT(MONTH FROM created_at)'));
		
		$chart_data = array();
		$months_in_past_year = array();
		
		for($i = 11;$i >= 0; $i--)
			$months_in_past_year[] = date('n-M',strtotime("-$i months"));
		
		foreach($months_in_past_year as $month)
		{
			$month_info = explode('-',$month);
			$month_as_int = $month_info[0];
			$month_as_string = $month_info[1];
			
			$chart_data[$month_as_string] = isset($orders_by_month[$month_as_int]) ? $orders_by_month[$month_as_int]['order_count'] : 0;
		}
		
		$this->_generate_chart($chart_data,'orders_this_year.png');

		$this->breadcrumb[] = array('admin/statistics/orders-this-week' => 'Orders made this week');		
	}
	
	function order_locations()
	{
		$this->template_data['google_maps_api_key'] = config::get('core','google_maps_api_key');
		
		$this->breadcrumb[] = array('admin/statistics/order-locations' => 'Orders by location');		
	}
	
		
	function _generate_chart($chart_data, $filename)
	{
		$chart =& model::create('chart');
		
		// set titles
		$chart->setGraphTitles('', 'Time period','Number of orders');
		
		// set format of number on Y axe
		$chart->setYNumberFormat('integer');
		
		// set number of ticks on Y axe
		$chart->setYTicks(10);
		
		// set data
		$chart->setData($chart_data);
		
		$chart->setBackgroundColor(array(255,255,255));
		
		$chart->setTextColor(array(144,144,144));
		
		// set orientation of text on X axe
		$chart->setXTextOrientation('horizontal');
		
		// prepare image
		$chart->drawImage();
		
		// print image to the screem
		$chart_path_and_filename = IMAGE_CACHE_DIR . '/' . $filename;
		$chart->save_graph_as_image($chart_path_and_filename);	
	}
	
	function convert_24hr_to_12hr_notation($hours)
	{
		if($hours > 12)
			return $hours - 12 . 'pm';
		else
			return ($hours == 0) ? 12 . 'pm' : $hours . 'am';
	}
	
	function _get_pager_url_paramters()
	{
		$params = array();
		
		if(!empty($_GET['sort']))
			$params['order_by'] = '"'.$_GET['sort'].'"';
			
		if(!empty($_GET['filter_by']))
			$params['filter_by'] = strtolower($_GET['filter_by']);
		else
			$params['filter_by'] = null;
					
		if(!empty($_GET['page']))
			$params['page'] = (int)$_GET['page'];
		else
			$params['page'] = 1;
			
		return $params;
	}
}
?>