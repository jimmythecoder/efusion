<?php
/**
 * Newsletter. A newsletter email, in HTML and text format.
 */
class newsletter extends model
{
	/**
	 * Primary key
	 * @var int
	 */
	var $id;
	
	/**
	 * Foreign key to the list type
	 * @var int
	 */
	var $newsletter_list_id;
	
	/**
	 * Name for this newsletter to identify it by
	 * @var string
	 */
	var $name;
	 
	/**
	 * Email subject that this newsletter will be sent out with
	 * @var string
	 */
	var $subject;

	/**
	 * Plain text version of the email newsletter
	 * @var string
	 */
	var $text_content;
	
	/**
	 * HTML version of the email newsletter
	 * @var string
	 */
	var $html_content;
	
	/**
	 * Date and time this newsletter was sent at
	 * @var date time
	 */
	var $sent_at;
		
	/**
	 * Date and time this newsletter was created
	 * @var date time
	 */
	var $created_at;
		
	
	function newsletter($id = null)
	{
		parent::model($id);
		
		$this->set_protected_fields(array('created_at','sent_at'));
	}	
	
	function validate()
	{
		$this->validates_presence_of('name','Please enter a name for this newsletter');
		$this->validates_presence_of('subject','Please enter a subject for this newsletter');
		$this->validates_foriegnkey_exists('newsletter_list_id','Please select a newsletter list');
		
		$this->validates_presence_of('text_content','Please enter a plain text version for this newsletter');
		
		$this->validates_presence_of('created_at','Newsletter created_at date is not set');
	}
	
	function before_insert()
	{
		$this->created_at 	= date(SQL_DATETIME_FORMAT);
	}
}
?>