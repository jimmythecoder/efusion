<?php
/**
 * Configuration helper class, contains static methods to load config variables
 * 
 * @package efusion
 * @subpackage helpers
 */
class config extends singleton
{
	var $config_entries;
	
	function config()
	{
		parent::singleton();

		if(!count($this->config_entries))
			$this->load_config();	
	}
	
	/**
	 * Loads all configuration values from the configuration table
	 * And sets timezone, protocol, current, http and https locations
	 * @return int number of configuration entries loaded
	 */
	function load_config()
	{
		if(!($this->config_entries = cache::get('configuration','core')))
		{
			$obj_configuration = model::create('configuration');
			
			if(!$this->config_entries = $obj_configuration->load_configuration_file(ENVIRONMENT))
				throw new Exception($obj_configuration->get_errors());

			//Load domains.ini file
			$domain_settings_config_filename 			= CONFIG_DIR . '/domains.ini';
			$domain_config 								= parse_ini_file($domain_settings_config_filename,true);
			$environment_domain_config 					= $domain_config[ENVIRONMENT];
			
			$this->config_entries['host'] 				= $environment_domain_config;
			$this->config_entries['host']['http'] 		= $environment_domain_config['subdomain'] . '.' . $environment_domain_config['domain'];
			$this->config_entries['host']['https'] 		= $environment_domain_config['ssl_subdomain'] . '.' . $environment_domain_config['domain'];

			$this->config_entries['host']['environment'] 	= ENVIRONMENT;
			$this->config_entries['core']['site_root_dir'] 	= SITE_ROOT_DIR;
			
			//Load modules
			if(!empty($this->config_entries['modules']))
			{
				foreach($this->config_entries['modules'] as $module => $is_active)
				{
					$modules_path = MODULES_DIR . '/' . $module;
					
					if($is_active && file_exists($modules_path))
						$this->config_entries['modules'][$module] = parse_ini_file($modules_path . '/descriptor.ini');
				}
			}
		
			cache::save($this->config_entries,'configuration','core');
		}

		//Set timezone
		if(function_exists('date_default_timezone_set'))	
			date_default_timezone_set($this->config_entries['core']['timezone']);
			
		//Get protocol and host
		if(!IS_SCRIPT && ($_SERVER['SERVER_PORT'] == 443) || (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'))
			$this->config_entries['host']['protocol'] = 'https';
		else
			$this->config_entries['host']['protocol'] = 'http';
			
		//Configure URL's
		$this->config_entries['current_location'] 	= $this->config_entries['host']['protocol'] . '://' . $this->config_entries['host'][$this->config_entries['host']['protocol']];
		$this->config_entries['http_location'] 		= 'http://' . $this->config_entries['host']['http'];
		
		//Set https host if SSL is supported
		if(!empty($this->config_entries['host']['enable_ssl']))
			$this->config_entries['https_location'] = 'https://' . $this->config_entries['host']['https'];
		else
			$this->config_entries['https_location'] = $this->config_entries['http_location'];
		
		return count($this->config_entries);
	}
	
	/**
	 * Gets a single or array configuration value(s)
	 * @param string $section section name of the config to retrieve from
	 * @param string $key property name to retrieve (optional)
	 * @return mixed configuration string value if found, else false
	 */
	function get($section, $key = null)
	{
		$obj_config = new config();
		
		if(is_null($key))
		{
			if(isset($obj_config->config_entries[$section]))
				 return $obj_config->config_entries[$section];
			else
			{
				throw new Exception('Config section ['.$section.'] does not exist');
				return false;
			}
		}
		else
		{
			if(isset($obj_config->config_entries[$section][$key]))
				 return $obj_config->config_entries[$section][$key];
			else
			{
				throw new Exception('Config section and key ['.$section.'] ['.$key.'] does not exist');
				return false;
			}
		}
	}
	
	/**
	 * Gets all configuration values
	 * @return array associative array of section/key => values
	 */
	function get_all()
	{
		$obj_config = new config();
		return $obj_config->config_entries;
	}
}
?>