<?php
/**
 * Administration controller
 * 
 * @package efusion
 * @subpackage controllers
 */
class admin_controller extends application_controller
{
	function admin_controller(&$application)
	{
		parent::application_controller($application);

		//Filter admin only access
		if(!isset($_SESSION['account_id']) || $_SESSION['account_id'] == null)
			$this->redirect_to('login');
		else
		{
			if($_SESSION['account_group'] != 'administrators')
			{
				$this->flash['error'][] = 'You do not have permission to view this page';
				$this->redirect_to('login');
			}
		}
		
		$this->set_layout('admin');
		
		$this->breadcrumb[] = array('admin/index' => 'Administration');
	}
	
	function index()
	{
		//Count the total number of new orders
		$order =& model::create('order');
		$total_pending_orders = $order->count(array('where' => "status = 'pending'",'escape' => false));
		$this->template_data['total_pending_orders'] = $total_pending_orders;
	}
	
	/**
	 * Import an XML product list into the site catalogue
	 */
	function import_product_list()
	{
		//parse xml product list
		
		//import all products checking for duplicates
		
		$this->flash['notice'] = 'xxx Products imported successfully.';
	}
	
	function logout()
	{
		session_regenerate_id();
		if(!version_compare(phpversion(),"4.3.3",">="))
			setcookie(session_name(),session_id(),ini_get("session.cookie_lifetime"),"/",config::get('host','domain'),false);
				
		$_SESSION['account_id'] = null;
		unset($_SESSION['account_id']);
		unset($_SESSION['account_group']);
		setcookie('auto_login','',time()-3600,'/',config::get('host','domain'),false);
		
		$this->flash['notice'][] = 'You have successfully logged out of administration.';
		$this->redirect_to('login');	
	}
}
?>