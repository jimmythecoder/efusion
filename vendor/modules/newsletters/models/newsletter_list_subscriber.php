<?php
/**
 * Class that defines which newsletter lists a user is subscribed to
 */
class newsletter_list_subscriber extends model
{	
	/**
	 * Primary key
	 */
	var $id;
	
	/**
	 * Foreign key to the newsletter list
	 * @var int
	 */
	var $newsletter_list_id;
	
	/**
	 * Foreign key to the subscriber
	 * @var int
	 */
	var $subscriber_id;
	
	
	function validate()
	{
		$this->validates_foriegnkey_exists('subscriber_id','Subscriber id missing', realpath(dirname(__FILE__)));
		$this->validates_foriegnkey_exists('newsletter_list_id','Please select a list to subscribe to', realpath(dirname(__FILE__)));
		
		$this->validates_uniqueness_of(array('newsletter_list_id','subscriber_id'),'You are already subscribed to this list.');
	
		return parent::validate();
	}
	
	function delete_by_subscriber_id($subscriber_id)
	{
		$sql = 'DELETE FROM ' . $this->get_table_name() . ' WHERE subscriber_id = ' . (int)$subscriber_id;
		
		$this->_db->query($sql);
	}
}
?>