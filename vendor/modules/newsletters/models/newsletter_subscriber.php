<?php
/**
 * Newsletter Subscriber. A user who has received this particular newsletter sendout
 */
class newsletter_subscriber extends model
{
	/**
	 * Primary key
	 * @var int
	 */
	var $id;
	
	/**
	 * Foreign key to the newsletter
	 * @var int
	 */
	var $newsletter_id;

	/**
	 * Foreign key to the subscriber
	 * @var int
	 */
	var $subscriber_id;
		
	/**
	 * Has this newsletter been emailed out to this user
	 * @var boolean
	 */
	var $is_sent;

	/**
	 * Has this subscriber clicked on a link in the newsletter email
	 * @var boolean
	 */
	var $is_read;	 

	
	function validate()
	{
		$this->validates_foriegnkey_exists('newsletter_id','No newsletter set', realpath(dirname(__FILE__)));
		$this->validates_foriegnkey_exists('subscriber_id','No subscriber set', realpath(dirname(__FILE__)));
		
		return parent::validate();
	}
}
?>