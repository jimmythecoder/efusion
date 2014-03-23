<?php
/**
 * Newsletter subscriber, someone who has entered their email address verifying they wish to receive newsletters.
 */
class subscriber extends model
{
	/**
	 * Primary key
	 * @var int
	 */
	var $id;
	
	/**
	 * First name of the user
	 * @var string
	 */
	var $first_name;
	
	/**
	 * Last name of the user
	 * @var string
	 */
	var $last_name;
	 
	/**
	 * Email address of the user (unique)
	 * @var string
	 */
	var $email;
	
	/**
	 * Date and time this user first subscribed
	 * @var date time
	 */
	var $created_at;
		
	
	function subscriber($id = null)
	{
		parent::model($id);
		
		$this->set_protected_fields(array('created_at'));
	}	
	
	function validate()
	{
		$this->validates_presence_of('first_name','Please enter your name');
		
		if($this->validates_presence_of('email','Please enter your E-Mail address'))
			$this->validates_uniqueness_of('email','This E-Mail address has already been taken');
	
		$this->validates_regular_expression_of('email', EMAIL_REGEX, 'Please enter a valid email address');
		
		$this->validates_presence_of('created_at','User created_at date is not set');
		
		return parent::validate();
	}
	
	function before_insert()
	{
		$this->created_at 	= date(SQL_DATETIME_FORMAT);
	}
	
	/**
	 * Overwrite this so we can split the users name up into first/last
	 */
	function set_field_values_from_array($arr_field_values)
	{
		if(empty($arr_field_values['last_name']) && strpos($arr_field_values['first_name'],' '))
		{
			list($this->first_name, $this->last_name) = explode(' ',trim($arr_field_values['first_name']));
			
			unset($arr_field_values['first_name'],$arr_field_values['last_name']);
		}
		
		return parent::set_field_values_from_array($arr_field_values);
	}
	
	function subscribe_to_lists($arr_lists)
	{
		$obj_newsletter_list_subscriber =& model::create('newsletter_list_subscriber', null, realpath(dirname(__FILE__)));
		
		foreach($arr_lists as $key => $newsletter_list_id)
		{
			$obj_newsletter_list_subscriber->clear_field_values();
			$obj_newsletter_list_subscriber->subscriber_id 		= $this->id;
			$obj_newsletter_list_subscriber->newsletter_list_id = $newsletter_list_id;
			$obj_newsletter_list_subscriber->insert();
		}
	}
	
	function subscribe_to_all_lists()
	{
		$obj_newsletter_list 			=& model::create('newsletter_list', null, realpath(dirname(__FILE__)));
		$obj_newsletter_list_subscriber =& model::create('newsletter_list_subscriber', null, realpath(dirname(__FILE__)));
		$arr_active_lists 				= $obj_newsletter_list->find_all(array('where' => 'is_active = 1'));
		
		foreach($arr_active_lists as $key => $arr_newsletter_list)
		{
			$obj_newsletter_list_subscriber->clear_field_values();
			$obj_newsletter_list_subscriber->subscriber_id 		= $this->id;
			$obj_newsletter_list_subscriber->newsletter_list_id = $arr_newsletter_list['id'];
			$obj_newsletter_list_subscriber->insert();
		}
	}
}
?>