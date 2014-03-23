<?php
/**
 * Brute force hack IP based protection
 * 
 * @package efusion
 * @subpackage models
 */
class ip_bruteforce_ban extends model
{
	/**
	 * Primary key
	 * @var int
	 */
	var $id;
	
	/**
	 * IP Address of the visitor
	 * @var string
	 */
	var $ip;
	
	/**
	 * Number of failed attempts this visitor has made (login, email password)
	 */
	var $failed_attempts;
	
	/**
	 * Unix timestamp of when this user is allowed to try again, 0 or null if not banned
	 * @var int
	 */
	var $banned_until;
	
	/**
	 * Unix timestamp of when the last failed access attempt was made
	 * @var int
	 */
	var $last_attempt_at;
	
	/**
	 * Controller action name where this ban applies to
	 * @var enum (login,email-password)
	 */
	var $action;
	
	
	function validate()
	{
		$this->validates_presence_of('ip');
		$this->validates_numericality_of('failed_attempts');
		
		return parent::validate();
	}
	
	/**
	 * Checks if the ip_address has been banned, resets expired bans
	 * @param string $ip_address ip address of the user to check
	 * @param string $action controller action used to selectively check against
	 * @return boolean true if user is currently banned, else false
	 */
	function is_ip_address_banned($ip_address, $action)
	{
		if($this->find_by_field_array(array('ip' => $ip_address, 'action' => $action)))
		{
			if(((int)$this->banned_until > 0 && (int)$this->banned_until < time()) || ($this->last_attempt_at < (time() - BRUTEFORCE_BAN_DURATION)))
			{
				//Users ban has expired, remove the ban
				$this->delete($this->id);	
				return false;
			}
			else if((int)$this->banned_until >= time())
				return true;
			else
				return false;
		}
		else
			return false;
	}
	
	/**
	 * Logs a failed login or other event to the db to prevent bruteforce attacks
	 * Will set a banned until timestamp if user has exceeded a failed attempts threshold
	 * @param string $ip_address unique ip address of the user to log
	 * @param string $action controller action used to selectively check against
	 */
	function log_failed_attempt($ip_address, $action)
	{
		//Find any previous records
		if($this->find_by_field_array(array('ip' => $ip_address, 'action' => $action)))
		{	
			//Reset the failed attempts counter if a period of in-activity
			if($this->last_attempt_at < (time() - BRUTEFORCE_BAN_DURATION))
				$this->failed_attempts = 1;
			else
				$this->failed_attempts++;
		}
		else
		{
			//Create a new record
			$this->ip = $ip_address;
			$this->failed_attempts = 1;	
			$this->action = $action;
		}
		
		$this->last_attempt_at = time();
		
		if($this->failed_attempts >= MAX_BRUTEFORCE_ATTEMPTS)
			$this->banned_until = time() + BRUTEFORCE_BAN_DURATION;

		$this->save();	
	}
}

?>