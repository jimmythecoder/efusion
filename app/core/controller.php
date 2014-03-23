<?php
/**
 * Controller base class
 * 
 * @package efusion
 * @subpackage core
 * @abstract All controllers must inherit from this class
 */
class controller
{
	/**
	 * Variables assigned to the smarty templates
	 * @var array
	 */
	var $template_data;
	
	/**
	 * 
	 * Reference to the core application object
	 * @var object
	 */
	var $application;
	
	/**
	 * Notice and error messages to flash by the user
	 * @var array
	 */
	var $flash;
	
	/**
	 * Application parameters (Get + Post)
	 * @var array
	 */
	var $params;
	
	/**
	 * Breadcrumb links to display
	 * @var array
	 */
	var $breadcrumb;
	
	/**
	 * Absolute paths for module controllers to views, models and controllers
	 */
	var $module_paths;
	
	
	function controller(&$application)
	{
		$this->application = $application;		
		$this->template_data = array();				//Templating engine variables
		$this->flash = array();						//User messages to be displayed
		$this->params = $application->params;		//Application parameters
		$this->breadcrumb = array();
		
		if(isset($_SESSION['flash']))
			$this->flash = $_SESSION['flash'];
		
		$_SESSION['flash'] = null;
		
		if($application->is_vendor_module)
		{
			$this->module_paths['views'] 		= $application->active_module_path . '/' . $application->active_vendor_module['views'];
			$this->module_paths['models'] 		= $application->active_module_path . '/' . $application->active_vendor_module['models'];
			$this->module_paths['controllers'] 	= $application->active_module_path . '/' . $application->active_vendor_module['controllers'];
		}
	}
	
	/**
	 * Saves the current flash messages to session
	 */
	function save_flash()
	{
		$_SESSION['flash'] = $this->flash;
	}
	
	/**
     * Saves flash messages and redirects to a controller / action
     */
    function redirect_to($controller_name,$action_name = null, $protocol = 'http')
	{
		$this->save_flash();
		session_write_close();
		
		//Check if the site supports SSL (has an SSL cert)
		if($protocol == 'https' && config::get('host','enable_ssl') == false)
			$protocol = 'http';
		
		header('Location: '.$protocol.'://'.$this->template_data['config']['host'][$protocol].'/'.$controller_name.(($action_name != null) ? '/'.$action_name : ''));
		exit(0);
	}
	
	/**
	 * Same as redirect_to but adds an error message to the flash
	 */
	function redirect_to_with_error($controller_name, $action_name = null, $protocol = 'http', $error_message)
	{
		if(is_array($error_message))
			$this->flash['error'] = $error_message;
		else
			$this->flash['error'][] = $error_message;
		
		$this->redirect_to($controller_name, $action_name, $protocol);
	}
	
	/**
	 * Safely redirects the user away to an external URL after saving the session and stops the application
	 * @param string $url absolute url to redirect to
	 */
	function redirect_to_url($url)
	{
		$this->save_flash();
		session_write_close();
		
		header('Location: '.$url);
		exit(0);		
	}
	
	function fetch_template($template, $controller = null, $module = null)
	{
		return $this->application->fetch_template($template, $controller, $module);
	}
	
	public function fetch_partial($template)
	{
		return $this->application->fetch_partial($template);
	}
	
	function assign_template_data($assign_data_as, $data)
	{
		$this->application->set_view_data($assign_data_as, $data);
	}
	
	function set_view($view)
	{
		$this->application->set_view($view);
	}
	
	function set_layout($layout)
	{
		$this->application->set_layout($layout);
	}
	
	public function set_content_type($content_type)
    {
    	$this->application->set_content_type($content_type);	
    }
}
?>