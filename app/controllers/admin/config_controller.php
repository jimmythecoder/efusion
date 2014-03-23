<?php
/**
 * Admin system generated email management
 * 
 * @package efusion
 * @subpackage controllers
 */
class config_controller extends admin_controller
{
	function index()
	{
		$this->breadcrumb[] = array('admin/config/index' => 'Store Configuration');

		$config =& model::create('configuration');
		$config->environment = ENVIRONMENT;
		
		if(isset($this->params['save']))
		{
			$errors = array();
			
			//Process boolean checkbox values first
			if(empty($this->params['config']['host']['enable_ssl']))
				$this->params['config']['host']['enable_ssl'] = '0';

			if(empty($this->params['config']['payment_method']['credit_card']))
				$this->params['config']['payment_method']['credit_card'] = '0';
	
			if(empty($this->params['config']['payment_method']['bank_deposit']))
				$this->params['config']['payment_method']['bank_deposit'] = '0';

			if(empty($this->params['config']['core']['gzipcompress']))
				$this->params['config']['core']['gzipcompress'] = '0';
				
			if(empty($this->params['config']['adsense']['enabled']))
				$this->params['config']['adsense']['enabled'] = '0';

			if(empty($this->params['config']['mail']['pop_before_smtp']))
				$this->params['config']['mail']['pop_before_smtp'] = '0';
														
			//Save domain info to domains.ini
			$config->write_array_to_ini_file(CONFIG_DIR . '/domains.ini',array(ENVIRONMENT => $this->params['config']['host']));
			unset($this->params['config']['host']);
			
			//Process each text entry value
			foreach($this->params['config'] as $section => $config_value)
			{
				foreach($config_value as $key => $value)
				{
					$config->section 	= $section;
					$config->key 		= $key;
					$config->value 		= $value;
						
					if(!$config->save())
						$errors = array_merge($errors,$config->get_errors());
				}
			}
			
			if(!count($errors))
			{
				$this->flash['notice'][] = 'Configuration updated successfully.';
				$this->redirect_to('admin/config','index','https');
			}
			else
				$this->flash['error'] = $errors;
		}
		else if(isset($this->params['cancel']))
			$this->redirect_to('admin/config','index','https');
			
		$obj_country 						=& model::create('country');
		$this->template_data['countries'] 	= $obj_country->get_countries_as_list();
	}
}

?>