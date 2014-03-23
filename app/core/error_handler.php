<?php
/**
 * Custom error handling class for PHP errors
 * 
 * @package efusion
 * @subpackage core
 * @link http://nz.php.net/manual/en/ref.errorfunc.php Error Handling and Logging Functions
 */
 class error_handler
 {
 	var $errors;
 	
 	function error_handler()
 	{	
 		$errors = array();
    	
    	if(IS_DEVELOPMENT_ENV)
    	{
	    	error_reporting(E_ALL & ~E_STRICT);
	    	ini_set('display_errors',1);
    	}
    	else
    	{
    		error_reporting(0);
	    	ini_set('display_errors',0);
    	}
    	
    	set_error_handler(array($this, 'report_error'),E_ALL & ~E_STRICT);
 	}
 	
 	/**
 	 * Callback function for the PHP trigger error function
 	 * @param int $error_level PHP constant for error levels
 	 * @param string $message the message thrown
 	 * @param string $filename the file name the error was thrown in
 	 * @param int $linenum the line number the error was thrown at
 	 */
 	function report_error($error_level, $message, $filename, $linenum)
 	{	
		if($error_level != E_NOTICE)
			throw new Exception($message);
 	}
 
  	/**
 	 * Logs a single error to the filesystem
 	 * @param int $error_level PHP constant error level section
 	 * @param int $error_number array index of the error to send
 	 */
 	function log_error_to_file($error_level, $error_number)
 	{ 		
 		$error_level_name = $this->get_error_level_natural_name($error_level);
 		$message = $this->errors[$error_level_name][$error_number];
 		
 		logger::log_message($message, ENVIRONMENT, true);
 	}
 	
 	/**
 	 * Emails a single error to the webmaster
 	 * @param int $error_level PHP constant error level section
 	 * @param int $error_number array index of the error to send
 	 * @return int return value for the PHP mail function
 	 * @link http://php.net/mail	
 	 */
 	function email_error($error_level, $error_number)
 	{
 		$site_domain_name = config::get('host','http');
 		$webmaster_email  = config::get('emails','webmaster');
 		$error_level_name = $this->get_error_level_natural_name($error_level);
 		
 		return mail($webmaster_email,'Critical website error from: '.$site_domain_name,$this->errors[$error_level_name][$error_number],'From: ' . $webmaster_email);
 	}
 	
 	/**
 	 * Returns an array of errors triggered 
 	 * @param int $error_level PHP Constant for the error level array to retrieve
 	 * @return array all errors within a given error level if given, else all errors triggered
 	 */
 	function get_errors($error_level = null)
 	{
 		if($error_level)
			return $this->errors[$this->get_error_level_natural_name($error_level)];
		else
			return $this->errors;
 	}
 	
 	/**
 	 * Returns a human readable translation name for an error level constant
 	 * @param int $error_level PHP error level constant, e.g. E_USER_ERROR
 	 * @return string English translated constant
 	 */
 	function get_error_level_natural_name($error_level)
 	{
 		switch($error_level)
 		{
 		  case E_USER_ERROR:
 		  	return 'Critical Error';
 		  	break;
 		  case E_USER_WARNING:
 		  	return 'Warning';
 		  	break;	
 		  case E_USER_NOTICE:
 		  	return 'Information';
 		  	break;	
 		  case E_STRICT:
 		  	return 'PHP 5 Warning';
 		  	break;	
 		  case E_WARNING:
 		  	return 'PHP Warning';
 		  	break;	
 		  case E_NOTICE:
 		  	return 'PHP Notice';
 		  	break;
 		  case E_ERROR:
 		  	return 'PHP Error';
 		  	break;
 		  default:
 		  	return 'Unknown error';
 		}
 	}
 }
?>