<?php
/**
 * Members account controller
 * 
 * @package efusion
 * @subpackage controllers
 */
class my_account_controller extends application_controller
{
	function my_account_controller(&$application)
	{
		parent::application_controller($application);

		//Filter member only access
		if(!isset($_SESSION['account_id']) || $_SESSION['account_id'] == null)
			$this->redirect_to('login',null,'https');
		else
		{
			$account =& model::create('account',$_SESSION['account_id']);
			$account->find_foreign_key('group_id');
			
			if($account->group->name != 'members')
			{
				$this->flash['error'][] = 'You do not have permission to view this page';
				$this->redirect_to('login',null,'https');
			}
		}
		
		$this->set_layout('my_account');
		
		$this->template_data['account'] 			= $account->fields_as_associative_array();
		$this->template_data['account']['group'] 	= $account->group->fields_as_associative_array();
		
		$this->breadcrumb[] = array('my-account/index' => 'My Account');
	}
	
	/**
	 * Members home page
	 */
	function index()
	{
		
	}
	
	function send_activation_email()
	{
		if(isset($_POST['send_activation_email']))
		{
			$email =& model::create('email',EMAIL_ACTIVATE_EMAIL);
			$account =& model::create('account',$_SESSION['account_id']);
			$address_book =& model::create('address_book');
			$address_book->find_primary_account_address($account->id);
			
			$email_substitutions = array(
				'site_title' 	=> config::get('content','title'),
				'domain_name' 	=> config::get('host','http'),
				'first_name' 	=> $address_book->first_name,
				'activation_key'=> $account->email_activation_key);
				
			$email->to = $account->email;
			$email->parse_message($email_substitutions);
			
			if($email->send())
			{	
				$this->flash['notice'][] = 'A new activation E-Mail has just been sent to ' . $account->email;
				$this->redirect_to('my-account','index','https');
			}
			else
				$this->flash['error'] = $email->_errors;
		}
	}
	
	/**
	 * View account orders
	 */
	function orders()
	{
		$this->redirect_to('my-account/orders','index','https');
	}
	
	/**
	 * Logout from there account
	 */
	function logout()
	{
		session_regenerate_id();
		if(!version_compare(phpversion(),"4.3.3",">="))
			setcookie(session_name(),session_id(),ini_get("session.cookie_lifetime"),"/",config::get('host','domain'),false);
				
		$_SESSION['account_id'] = null;
		unset($_SESSION['account_id']);
		unset($_SESSION['account_group']);
		setcookie('auto_login','',time()-3600,'/',config::get('host','domain'),false);
		
		$this->flash['notice'][] = 'You have successfully logged out of your account.';
		$this->redirect_to('login');	
	}
}
?>