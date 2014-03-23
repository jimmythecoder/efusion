<?php
/**
 * Core application class, dispatches requests to appropreate controllers and methods
 * 
 * @package efusion
 * @subpackage core
 */
class application
{
	/**
	 * Name of the module the URL is mapped to, null if none
	 * @var string
	 */
	var $module_name;
	
	/**
	 * Path and filename of the controller for the module, e.g. /app/controllers/admin_controller.php
	 * @var string
	 */
	var $module_controller;
	
	/**
	 * Reference to the controller object
	 * @var object
	 */
	var $controller;	
	
	/**
	 * Name of the controller from the mapped route
	 * @var string
	 */
	var $controller_name;
	
	/**
	 * Name of the controller with the _controller affixation
	 * @var string
	 */
	var $controller_name_with_affixation;
	
	/**
	 * Path to look for the local controller
	 * @var string
	 */
	var $controller_path;
	
	/**
	 * Path and filename of the current controller
	 */
	var $controller_path_and_filename;
	
	/**
	 * Path and filename for the view template
	 * @var string
	 */
	var $view_path_and_filename;
	
	/**
	 * Path and filename for the layout template
	 * @var string
	 */
	var $view_layout_path_and_filename;
	
	/**
	 * View template name
	 */
	var $view_template;
	
	/**
	 * View layout name
	 */
	var $view_layout;
	
	/**
	 * Name of the action called
	 * @var string
	 */
	var $action_name;	
	
	/**
	 * Is the URL a vendor module
	 * @var boolean
	 */
	var $is_vendor_module;
	
	/**
	 * Name of the module currently active
	 * @var string
	 */
	var $active_vendor_module;
	
	/**
	 * Absolute path to the currently active module (Only set when a module is active)
	 * @var string
	 */
	var $active_module_path;
	/**
	 * Application parameters from get and post
	 * @var array
	 */
	var $params;
	
	/**
	 * Smarty templating engine object
	 * @var object
	 */
	var $smarty;
	
	/**
	 * Client browser preferred encoding string (lang.charset) e.g. en.UTF-8
	 * @var string
	 */
	var $preferred_encoding;
	
	/**
	 * Content type header to be sent e.g. text/html
	 */
	var $content_type;
	
	/**
	 * Database engine object
	 * @var object
	 */
	var $db;	
	
	var $charset;

	
	/**
	 * Initializes the application, sets up actions, controllers, encodings and templates
	 * @param array $mapped_url_parameters Application URL parameters (module, controller, action, url_params)
	 */
	function application(&$db, &$mapped_url_parameters)
	{
		$this->db 				= $db;		
		$this->params 			= $mapped_url_parameters;
				
		$this->_setup_parameters();
		
		$this->_setup_paths();
			
		$this->_setup_sessions();
						
		$this->_setup_controllers();
		
		$this->_setup_encoding();
		
		$this->_setup_view_engine();
	}
	
	/**
	 * Set the application parameters
	 */
	function _setup_parameters()
	{
		if(isset($_GET['page']))
			$this->params['page'] = (int)$_GET['page'];	
			
		//If any form was posted, set the post params
		foreach($_POST as $form => $values)
			$this->params[$form] = $values;		
	}
	
	/**
	 * Sets paths and filenames for the module, controller, action and view
	 */
	function _setup_paths()
	{
		$this->module_name 						= isset($this->params['module']) ? str_replace('-','_',$this->params['module']) : null;
		$this->controller_name 					= str_replace('-','_',$this->params['controller']);
		$this->controller_name_with_affixation 	= $this->controller_name . '_controller';
		$this->action_name 						= str_replace('-','_',$this->params['action']);	
			
		if(is_null($this->module_name))		
		{
			$this->module_controller 	= null;
			$this->controller_path 		= CONTROLLERS_DIR;
		}
		else
		{
			$this->module_controller 	= CONTROLLERS_DIR . '/' . $this->module_name . '_controller.php';
			$this->controller_path 		= CONTROLLERS_DIR . '/' . $this->module_name;
		}
	
		$arr_vendor_modules 	= config::get('modules');
		$this->is_vendor_module = array_key_exists($this->controller_name, $arr_vendor_modules);
		
		if(!$this->is_vendor_module)
			$this->controller_path_and_filename = $this->controller_path . '/' . $this->controller_name_with_affixation . '.php';
		else
		{
			$this->active_vendor_module = $arr_vendor_modules[$this->controller_name];
			$this->active_module_path 	= MODULES_DIR . '/' . $this->controller_name;
			
			$this->controller_path_and_filename =  $this->active_module_path . '/' . $this->active_vendor_module['controllers'] . $this->controller_name_with_affixation . '.php';
		}
		
		$this->set_view($this->action_name);
		$this->set_layout($this->controller_name);
	}
	
	function _setup_controllers()
	{
		//If module has been set check that its valid
		if(!is_null($this->module_name))
		{
			if(is_dir($this->controller_path) && file_exists($this->module_controller))
				require_once($this->module_controller);
			else
				HTTP::exit_on_header(404);	//Module does not exist;
		}
	
		//Validate controller exists
		if(file_exists($this->controller_path_and_filename))
			require_once($this->controller_path_and_filename);
		else
			HTTP::exit_on_header(404);	//Controller file does not exist
		
		//Check for an incorrect class name within the controller
		if(!class_exists($this->controller_name_with_affixation))
			throw new Exception('Controller class does not exist for controller: ' . $this->controller_name_with_affixation);

		//Create instance of the requested controller
		$this->controller = new $this->controller_name_with_affixation($this);
			
		//Validate the requested action exists
		if(!method_exists($this->controller,$this->action_name))
			HTTP::exit_on_header(404);	
	}
	
	/**
	 * Setup session information
	 */
	function _setup_sessions()
	{
		//Configure session, we need to find the root domain name so we can pass the
		//Session over from non-ssl to ssl domain, and domain aliases can vary between the 2
		//E.g. http://www.example.com -> https://secure.example.com cookie domain must be set for example.com
		$cookie_domain = config::get('host','domain');
		if(empty($cookie_domain))
			$cookie_domain = config::get('host','http');
		
		ini_set('session.gc_maxlifetime',SESSION_LIFETIME);
		ini_set('session.gc_probability',SESSION_GC_PROBABILITY);
		ini_set('session.auto_start',0);		//Do not automatically start the session, as we explicity call session_start
		
		$def_session_params = session_get_cookie_params();
		session_set_cookie_params($def_session_params['lifetime'],$def_session_params['path'],$cookie_domain,$def_session_params['secure']);
		session_start();	
	}
	
	/**
	 * Find the preferred browser encoding
	 */
	function _setup_encoding()
	{
	    if(!$this->preferred_encoding = al2gt(array('en.UTF-8','en.ISO-8859-1','en_nz.UTF-8','en_US.UTF-8','en_GB.UTF-8')))
			$this->preferred_encoding = 'en.UTF-8'; //No encoding specified, fall back to en.UTF-8
	    	
	    setlocale(LC_ALL, $this->preferred_encoding);		
	}
	
	/**
	 * Setup the view using smarty
	 */
	function _setup_view_engine()
	{
		$this->smarty 					= new Smarty();
	    $this->smarty->template_dir 	= VIEWS_DIR;
		$this->smarty->compile_dir 		= COMPILED_TEMPLATES_DIR;
		$this->smarty->cache_dir 		= CACHE_DIR;
		$this->smarty->config_dir 		= CONFIG_DIR;
		
		$this->smarty->register_outputfilter(array($this,'_set_output_encoding'));	
		
		if(!is_writable($this->smarty->compile_dir))
			throw new Exception('Template dir is not writable! ['.$this->smarty->compile_dir.']');
	}
	
	
	/**
	 * Calls the controller action function and calls render
	 */
	function execute()
	{
		//Call the action function on the controller
		$this->controller->{$this->action_name}();
		$this->render();
	}
	
	/**
	 * Renders the template and displays it to the browser
	 */
	function render()
	{
		$this->_set_view_data();
		
		if(file_exists($this->view_path_and_filename))
			$this->smarty->assign('content_for_layout',$this->smarty->fetch($this->view_path_and_filename));
		else
	    	throw new Exception('View template [' . $this->view_path_and_filename . '] does not exist');

		$this->_set_view_output_filters();
		
		if(file_exists($this->view_layout_path_and_filename))			
			$this->smarty->display($this->view_layout_path_and_filename);
		else
			throw new Exception('View layout [' . $this->view_layout_path_and_filename . '] does not exist');
	}
	
	/**
	 * Sets the view data from the application and controllers template data
	 */
	function _set_view_data()
	{
		$this->smarty->assign('flash',$this->controller->flash);
		$this->smarty->assign('stylesheet_files',array());
		$this->smarty->assign('javascript_files',array());
				
		foreach($this->controller->template_data as $key => $data)
			$this->smarty->assign($key,$data);
		
		$controller_view_data = array(
									'name' 		=> $this->controller_name,
									'action' 	=> $this->params['action'], 
									'layout' 	=> $this->view_layout,
									'module' 	=> $this->module_name,
									'view' 		=> $this->view_template);
		
		$this->smarty->assign('controller',$controller_view_data);	
		$this->smarty->assign('breadcrumb', $this->controller->breadcrumb);
	}
	
	function _set_view_output_filters()
	{
		if(config::get('core','trimwhitespace'))
			$this->smarty->load_filter('output','trimwhitespace');
			
		if(config::get('core','gzipcompress'))
			$this->smarty->load_filter('output','gzip');
			
		header('Content-Type: '.$this->content_type.'; charset='.$this->charset); 						
	}
	
	/**
	 * Fetches a template without echoing it and returns its parsed contents
	 */
	function fetch_template($template, $controller = null, $module = null)
	{
		$template_path_and_filename = VIEWS_DIR . '/' . (is_null($module) ? $this->module_name : $module) . '/' . (is_null($controller) ? $this->controller_name : $controller) . '/' . $template.'.tpl';
		
		return $this->smarty->fetch($template_path_and_filename);
	}

	function fetch_partial($template)
	{
		$template_path_and_filename = VIEWS_DIR . '/partials/' . $template.'.tpl';
		
		return $this->smarty->fetch($template_path_and_filename);
	}
		
    /**
     * Sets template encoding (smarty callback)
     */
    function _set_output_encoding($tpl_output, &$smarty)
    {
    	$lang_split = explode(".",$this->preferred_encoding);
		$charset = $lang_split[1];
		$language = $lang_split[0];
		
    	if($charset != 'utf-8' && extension_loaded('iconv'))
    		$result = iconv('utf-8',$charset,$tpl_output);
    	else
    		$result = $tpl_output;

		$this->charset = $charset;
		
		//Set the default content type if none set 
		if(!$this->content_type)
			$this->content_type = 'text/html';
    	
    	return $result;
    }
    
    /**
     * Sets the view template
     * @param string $view Name of the view template to use without the file extension
     */
    function set_view($view)
    {
    	$this->view_template 				= $view;
    	$view_path 							= (($this->module_name != null) ? $this->module_name . '/' : '') . $this->controller_name . '/' . $view . '.tpl';
    	
    	if($this->is_vendor_module)
    		$this->view_path_and_filename 	= $this->active_module_path . '/' . $this->active_vendor_module['views'] . $view_path;
    	else
    		$this->view_path_and_filename 	= VIEWS_DIR . '/' . $view_path;
    }
    
    /**
     * Sets the layout template
     * @param string $layout Name of the layout to use without the file extension
     */
    function set_layout($layout)
    {
       	$this->view_layout 							= $layout;
       	
       	if($this->is_vendor_module)
       		$this->view_layout_path_and_filename 	= $this->active_module_path . '/' . $this->active_vendor_module['views'] . '/layouts/' . $layout . '.tpl';
       	else
    		$this->view_layout_path_and_filename 	= VIEW_LAYOUTS_DIR . '/' . $layout . '.tpl';	
    }
    
    public function set_content_type($content_type)
    {
    	$this->content_type = $content_type;	
    }
    
    function set_view_data($assign_data_as, $data)
    {
    	$this->smarty->assign($assign_data_as, $data);
    }
}
?>