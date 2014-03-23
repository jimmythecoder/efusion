<?php
/**
 * Logger class, maintains the log files
 * 
 * @package efusion
 * @subpackage helpers
 */
class logger
{		
	/**
	 * Appends a message to a log file, only logs debug messages outside of production mode
	 * @param string $message message to write to the log file
	 * @param string $file log file name without the extension or path, e.g. query defaults to current environment
	 * @param boolean $prepend_date set to true if date should automatically be prepended to the log message
	 * @param int $log_level log constant, one of (LOG_DEBUG, LOG_INFO, LOG_WARNING, LOG_ERR)
	 * @return boolean true if logged successfully, else false
	 */
	static function log_message($message, $file = ENVIRONMENT, $prepend_date = true, $log_level = null)
	{
		if($log_level == LOG_DEBUG && IS_PRODUCTION_ENV)
			return true;
			
		$path_and_filename = self::get_log_path_and_filename($file);
		
		if(!$log_level)
			$log_level = LOG_INFO;

		//Strings to print to log file in [brackets] prepended to the message for each log level
		$log_levels = array(
			LOG_DEBUG 		=> 'debug', 
			LOG_INFO 		=> 'info', 
			LOG_WARNING 	=> 'warn', 
			LOG_ERR 		=> 'error',
			LOG_CRIT 		=> 'critical');
				
		$message = '['.$log_levels[$log_level].'] ' . $message . "\n";
				
		if($prepend_date)
			$message = (date('Y-m-d H:i:s') . ' ' . $message);
		
		return file_put_contents($path_and_filename,$message,FILE_APPEND);
	}
	
	static function log_debug($message, $file = ENVIRONMENT, $prepend_date = true)
	{
		return self::log_message($message, $file, $prepend_date, LOG_DEBUG);
	}
	
	static function log_error($message, $file = ENVIRONMENT, $prepend_date = true)
	{
		return self::log_message($message, $file, $prepend_date, LOG_ERR);
	}	
	
	static function log_warning($message, $file = ENVIRONMENT, $prepend_date = true)
	{
		return self::log_message($message, $file, $prepend_date, LOG_WARNING);
	}	

	static function log_info($message, $file = ENVIRONMENT, $prepend_date = true)
	{
		return self::log_message($message, $file, $prepend_date, LOG_INFO);
	}	
	
	/**
	 * Logs a critical website error and emails the webmaster
	 */
	static function log_critical($message, $file = 'critical', $prepend_date = true)
	{
		$path_and_filename 	= self::get_log_path_and_filename($file);
		
		$is_log_empty = !file_exists($path_and_filename) || filesize($path_and_filename) < 10;
	
		if(self::log_message($message, $file, $prepend_date, LOG_CRIT))
		{
			if($is_log_empty)
				self::email_log_to_webmaster($file);
		}
	}		
	
	/**
	 * Returns the absolute path and filename to the log file e.g. converts 'debug' to '/var/log/debug.log'
	 * @param string $file log file to email e.g. 'debug'
	 */
	static function get_log_path_and_filename($file)
	{
		return LOGS_DIR . '/' . $file . '.log';
	}
	
	/**
	 * Emails the first 20k of the given log file to the webmaster
	 * @param string $file log file to email e.g. 'debug'
	 */
	static function email_log_to_webmaster($file)
	{
		$webmaster_email_address 	= config::get('emails','webmaster');	
		$site_domain_name 			= config::get('host','http');
		$log_path_and_filename 		= self::get_log_path_and_filename($file);
		$email_body 				= substr(file_get_contents($log_path_and_filename),0,20000);

		return mail($webmaster_email_address,'Critical website error from: '.$site_domain_name,$email_body,'From: ' . $webmaster_email_address);
	}
}