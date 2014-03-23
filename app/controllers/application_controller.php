<?php
/**
 * Application Controller, put any global filters etc here
 * 
 * @package efusion
 * @subpackage controllers
 */
class application_controller extends controller
{   
	function application_controller(&$application)
	{
		parent::controller($application);
		
		//Load the site configuration
		$this->template_data['config'] = config::get_all();
		
		//Shortcut urls
		$this->template_data['current_location'] = $this->template_data['config']['current_location'];
		$this->template_data['http_location'] = $this->template_data['config']['http_location'];
		$this->template_data['https_location'] = $this->template_data['config']['https_location'];
		
		//Site banner
		if(!($site_banner = cache::get('banner','core')))
		{
			$banner =& model::create('banner');
			$banner->find_by_field('is_active',1);
			$banner->find_foreign_key('image_id');
			$site_banner 			= $banner->fields_as_associative_array();
			$site_banner['image'] 	= $banner->image->fields_as_associative_array();
			
			cache::save($site_banner,'banner','core');
		}
		
		$this->template_data['config']['content']['banner'] = $site_banner;

		//If the user is not logged in and has a keep me logged in cookie set
		if(empty($_SESSION['account_id']) && isset($_COOKIE['auto_login']))
		{
			$ip_bruteforce_ban =& model::create('ip_bruteforce_ban');
				
			//Check if this IP is banned
			if(!$ip_bruteforce_ban->is_ip_address_banned(HTTP::remote_ip_address(),'login'))
			{
				$serialized_credentials = base64_decode($_COOKIE['auto_login']);
				$login_credentials = unserialize($serialized_credentials);	
				
				$account =& model::create('account');
				if(!$account->login($login_credentials['email'],$login_credentials['hashed_password'],false))
				{
					setcookie('auto_login','',time()-3600,'/',config::get('host','domain'),false);
					$ip_bruteforce_ban->log_failed_attempt(HTTP::remote_ip_address(),'login');
				}
				else
					$this->flash['notice'][] = 'You have automatically been logged in as '.$account->email.
												'. If this it not your account, please logout from My Account and then login again.';
			}
		}
	}
}

?>