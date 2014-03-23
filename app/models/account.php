<?php
/**
 * User accounts, both admin and customer
 * 
 * @package efusion
 * @subpackage models
 */
class account extends model
{
	/**
	 * Primary Key
	 * @var int
	 */
	var $id;
	
	/**
	 * Foriegn key to account group table
	 * @var int
	 */
	var $group_id;
	
	/**
	 * Account email address with unique constraint
	 * @var string
	 */
	var $email;
	
	/**
	 * Hashed value of account password, defaults to md5
	 * @var string
	 */
	var $password_hash;
	
	/**
	 * The date/time the account was created at
	 * @var datetime
	 */
	var $created_at;
	
	/**
	 * Primary contact phone number of the user
	 * @var string
	 */
	var $phone;
	
	/**
	 * Secondary phone contact, cellular phone number
	 * @var string
	 */
	var $cellphone;
	
	/**
	 * Fax number (optional)
	 * @var string
	 */
	var $fax;
	
	/**
	 * A serialized array of the cart items
	 * @var string
	 */
	var $serialized_cart;

	/**
	 * Is the account currently active/enabled
	 * @var boolean
	 */
	var $is_active;
	
	/**
	 * Boolean flag to state if the account email has been activated by the user
	 * @var boolean
	 */
	var $is_email_activated;
	
	/**
	 * Unique activation key 32 char string, used to verify an email address
	 * @var string
	 */
	var $email_activation_key;
	
		
	function account($id = null)
	{
		parent::model($id);
		
		$this->set_protected_fields(array('created_at','serialized_cart', 'is_email_activated', 'email_activation_key', 'password_hash', 'is_active'));
	}
	
	function validate()
	{
		$this->validates_foriegnkey_exists('group_id','User must belong to a group');
		
		$this->validates_presence_of('email','Your E-Mail address is required');
		$this->validates_presence_of('phone','Primary contact phone number is required');
		
		if($this->email)
		{
			$this->validates_uniqueness_of('email','This E-Mail address has already been taken');
			$this->validates_regular_expression_of('email',EMAIL_REGEX,'Your E-Mail address is not correct');
		}
		
		$this->validates_presence_of('group_id');
		$this->validates_numericality_of('group_id');
		
		$this->validates_presence_of('password_hash','Please enter a password with at least '.MIN_PASSWORD_LENGTH.' characters');

		$this->validates_presence_of('email_activation_key');
		if($this->email_activation_key)
			$this->validates_uniqueness_of('email_activation_key');
		
		if(!is_null($this->id))
			$this->validates_presence_of('created_at');
			
		return parent::validate();
	}

	/**
	 * Authorize an E-Mail address and password combination and load account if successfull
	 * @param string $email_address users E-Mail address (account username)
	 * @param string $password users un-encrypted password
	 * @param boolean $hash_password if we should hash the password before authenticating (optional)
	 * @return mixed Account id if login successfull, else false
	 */
	function login($email_address, $password, $hash_password = true)
	{
		$email_address 	= strtolower(trim($email_address));
		$password 		= trim($password);
		
		if($hash_password)
			$password_hash = $this->hash_password($password);
		else
			$password_hash = $password;
			
		if($this->find_by_field_array(array('email' => $email_address,'password_hash' => $password_hash,'is_active' => 1)))
		{
			//Account authorized, regenerate id to prevent session fixation and log them in
			session_regenerate_id();
			
			//PHP bug fix, does not send new cookie after session regenerate id
			if(!version_compare(phpversion(),"4.3.3",">="))
				setcookie(session_name(),session_id(),ini_get("session.cookie_lifetime"),"/",config::get('host','domain'),false);
				
			$this->find_foreign_key('group_id');
			$_SESSION['account_id'] = $this->id;
			$_SESSION['account_group'] = $this->group->name;
			
			return $this->id;
		}
		else
			return false;
	}
	
	/**
	 * Sends an email confirmation for a new account created
	 * @return boolean true on success, else false
	 */
	function send_new_account_created_email()
	{
		//Validate we are working with an active account
		if(is_null($this->id))
			return false;
			
		$email_substitutions = array(
			'site_title' => config::get('content','title'),
			'site_email' => config::get('contact','email'),
			'domain_name' => config::get('host','http'),
			'activation_key' => $this->email_activation_key);
			
		$email =& model::create('email',EMAIL_NEW_ACCOUNT);
		$email->to = $this->email;
		$email->parse_message($email_substitutions);
		
		return $email->send();
	}
	
	/**
	 * Perform account creation tasks 
	 */
	function before_insert()
	{
		$this->created_at = date(SQL_DATETIME_FORMAT);
		
		$this->generate_email_activation_key();
		
		$this->is_active = 1;
		
		$this->email = strtolower(trim($this->email));
	}
	
	/**
	 * Generate a new unique email activation key and saves it
	 */
	function generate_email_activation_key()
	{
		do
		{
			$this->email_activation_key = strtolower($this->generate_random_password(32));
		}
		while(!$this->validates_uniqueness_of('email_activation_key'));	
		
		return $this->email_activation_key;
	}
	
	/**
	 * Return an md5 hash of the input string
	 * @param string $password un-encrypted password value
	 * @return string a hash of the password
	 */
	function hash_password($password)
	{
		return md5(CORE_PASSWORD_SALT . $password);
	}
	
	function generate_random_password($length = 8)
	{
		return substr(md5(uniqid(rand(), true)),0,$length);
	}
	
	function set_field_values_from_array($field_values)
	{
		if(!empty($field_values['email']))
			$field_values['email'] = strtolower(trim($field_values['email']));
		
		parent::set_field_values_from_array($field_values);
		
		if(!empty($field_values['new_password']))
			$this->password_hash = $this->hash_password($field_values['new_password']);
	}
	
	/**
	 * Builds a custom form for this account
	 * @param boolean $administrator_form if this form is to be used for an admin account (not a customer)
	 * @param boolean $edit if we are editing an account
	 */
	function get_fields_for_form($administrator_form = false, $edit = true)
	{
		$form_fields = parent::get_fields_for_form();
		
		unset($form_fields['created_at']);
		unset($form_fields['serialized_cart']);
		unset($form_fields['email_activation_key']);
		unset($form_fields['is_email_activated']);
		unset($form_fields['is_active']);
		unset($form_fields['password_hash']);
		
		if($edit)
			$form_fields['current_password'] = array('table' => 'account','type' => 'password','size' => 255,'null' => true,'label' => 'Current password');
		
		$form_fields['new_password'] = array('table' => 'account','type' => 'password','size' => 255,'null' => true,'label' => 'New password');
		unset($form_fields['password_hash']);
		
		if($edit)
		{
			unset($form_fields['id']);
			$form_fields['confirm_new_password'] = array('table' => 'account','type' => 'password','size' => 255,'null' => true,'label' => 'Confirm new password');
		}	
		
		return $form_fields;
	}
	
	/**
	 * Activates an accounts email address from a key sent in the account creation email
	 * @param string $email_activation_key unique activation key sent by email
	 * @return boolean true if activation successfull else false
	 */
	function activate_email_by_key($email_activation_key)
	{
		if($this->find_by_field_array(array('email_activation_key' => $email_activation_key)))
		{
			$this->is_email_activated = 1;
			$this->save();
			
			return true;
		}
		else
			return false;
	}
	
    /**
     * Checks if this account is a member of the given group name
     * @param string $group_name name of the group to check for
     * @return boolean true if this account is a member of the given group name, else false
     */
    function is_a_member_of_group($group_name)
    { 
        $this->find_foreign_key('group_id'); 
            
        if($this->group->name == $group_name)
            return true;
        else
            return false;
    }
}

?>