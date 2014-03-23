<?php
/**
 * @package efusion
 * @subpackage config
 * 
 * Process default application routes
 * This file has been optimized for speed and efficiency
 */
class route
{
	var $routes;
	
	function route()
	{
		$this->routes = array();
		
		////////////////////////////
		//Add applcation routes here, placing the most specific and high priority at the top
		/////////////////////////////
		
		//[/cart]
		$this->add_route('/^([a-z-]+)$/',array('controller' => 'store', 'action' => 1));
		
		//[/product/toyota-corrola]
		$this->add_route('/^(catalog|product|page)\/([a-z0-9-]+)$/',array('controller' => 'store', 'action' => 1, 'url_params' => 2));
		
		//[/admin/index]
		$this->add_route('/^([a-z-]+)\/([a-z-]+)$/',array('controller' => 1, 'action' => 2));
		
		//[/admin/orders/list]
		$this->add_route('/^(admin|my-account)\/([a-z-]+)\/([a-z-]+)$/',array('module' => 1,'controller' => 2, 'action' => 3));
		
		//[/my-account/orders/view/3]
		$this->add_route('/^(admin|my-account)\/([a-z-]+)\/([a-z-]+)\/([0-9]+)$/',array('module' => 1, 'controller' => 2, 'action' => 3, 'url_params' => 4));

		//[/store/activate-account/xxxx]
		$this->add_route('/^([a-z-]+)\/([a-z-]+)\/([a-z0-9-]+)$/',array('controller' => 1, 'action' => 2, 'url_params' => 3));
			
		//Default route for /
		$this->add_route('/^$/',array('controller' => 'store', 'action' => 'index'));
	}
	
	/**
	 * Map an application URL route
	 * @param array in URL to match this route to
	 * @param array out URL to replace with
	 */
	function add_route($match_on, $route)
	{
		$this->routes[$match_on] = $route;
	}	
	
	/**
	 * Maps a route and returns new or current route depending on any route translations found
	 * @param array route current URL route
	 * @return array The new mapped route
	 */
	function map_url_route($url)
	{
		$matches = null;

		foreach($this->routes as $match_on => $route)
		{
			if(preg_match($match_on, $url, $matches))
			{
				//Replace route vars with the URL match
				foreach($route as $url_part => $value)
				{	
					if(ctype_digit((string)$value))
						$route[$url_part] = $matches[$value];	
				}
				
				return $route;	
			}	
		}
		
		//No url routes found
		return false;
	}
	
	/**
	 * Maps url parts back into a URL
	 */
	function map_reverse_url_route($module = null, $controller = null, $action = null, $url_params = null)
	{
		$url = '';
		$url .= ($module) ? $module.'/' : '';
		$url .= ($controller) ? $controller.'/' : '';
		$url .= ($action) ? $action.'/' : '';
		$url .= ($url_params) ? $url_params : '';
		
		return $url;
	}
}
?>