<?php
require VENDOR_DIR . '/phpmailer/phpmailer.lang-en.php';
require VENDOR_DIR . '/phpmailer/phpmailer.php';
require VENDOR_DIR . '/phpmailer/smtp.php';
require VENDOR_DIR . '/phpmailer/pop3.php';

/**
 * Email class, similar to ActionMailer, can save, send, parse dynamic emails
 * 
 * @package efusion
 * @subpackage models
 */
class email extends model
{
	/**
	 * Primary key
	 * @var int
	 */
	public $id;

	/**
	 * Email subject field
	 * @var string
	 */
	public $subject;
	
	/**
	 * Message content data
	 * @var string
	 */
	public $message;
	
	/**
	 * A comma seperated list of variables provided by the system
	 * @var string
	 */
	public $system_variables;
			
	/**
	 * Receipiants email addresses
	 * @var string
	 */
	public $to;

	/**
	 * Email carbon copy comma seperated email address list
	 * @var string
	 */
	public $cc;
		
	/**
	 * A comma seperated list of email addresses for the Blind Carbon Copy recipiants
	 * @var string
	 */
	public $bcc;
	
	/**
	 * Email address where this email was sent from
	 * @var string
	 */
	public $from;
		
	/**
	 * Email format to send it out as (html or plain)
	 * @var enum
	 */
	public $format;
	
	/**
	 * Reply to email address
	 * @var string
	 */
	public $reply_to;
	
	/**
	 * Reference to PHPMailer application object
	 * Used to handle low level email handling
	 */
	private $_phpmailer;
	
	
	public function __construct($id = null)
	{
		parent::model($id);
		
		$this->set_protected_fields(array('system_variables'));
		
		$this->_phpmailer =& new PHPMailer();
	}
	
	function validate()
	{
		$this->validates_presence_of('from');
		$this->validates_presence_of('subject');
		$this->validates_presence_of('message');
		
		if(!empty($this->to))
			$this->validates_regular_expression_of('to',EMAIL_REGEX,'Your To E-Mail address is not correct');
		
		if(!empty($this->from))
			$this->validates_regular_expression_of('from',EMAIL_REGEX,'Your From E-Mail address is not correct');
		
		$this->clean_all_fields();
		
		return parent::validate();
	}
	
	/**
	 * Prevents mail injection by removing \r\n and headers from input fields (spam protection)
	 */
	function clean_all_fields()
	{
		$spam_headers = array('To:','Subject:','Bcc:','Cc:','Reply-To:','Content-Type:');
		$this->to = str_replace($spam_headers,'',$this->sanitize_string($this->to));
		$this->from = str_replace($spam_headers,'',$this->sanitize_string($this->from));
		$this->subject = str_replace($spam_headers,'',$this->sanitize_string($this->subject));
		$this->message = $this->sanitize_string($this->message);
	}
	
	/**
	 * Removes invalid character data from a string
	 */
	function sanitize_string($string)
	{
		return str_replace("\r",'',urldecode($string));
	}
	
	/**
	 * Sends off the email
	 * @return boolean true if sent successfully, else false
	 * @link http://php.net/mail official php documentation on the mail command used to send the email
	 */
	function send()
	{
		if($this->validate())
		{
			//Send all emails to developer on development, dont want real clients getting test emails
			if(ENVIRONMENT != 'production')
				$this->to = config::get('emails','webmaster');
				
			//Send off the email using phpmailer
			$this->_phpmailer->From     = $this->from;
			$this->_phpmailer->Host     = config::get('mail','host');
			$this->_phpmailer->Subject 	= $this->subject;
			$this->_phpmailer->Body 	= $this->message;
			$this->_phpmailer->AddAddress($this->to);
			$this->_phpmailer->AddReplyTo($this->reply_to);
			$this->_phpmailer->IsHTML(false);
			  
			if(!empty($this->cc))
				$this->_phpmailer->AddCC($this->cc);
							
			if(!empty($this->bcc))
				$this->_phpmailer->AddBCC($this->bcc);

			$this->_phpmailer->Mailer   = config::get('mail','method');
			
			if(config::get('mail','pop_before_smtp'))
			{
				$pop =& new POP3();
  				$pop->Authorise(config::get('mail','host'), 110, 30, config::get('mail','username'), config::get('mail','password'), 1);
			}
			else if(config::get('mail','method') == 'smtp')
			{
				$this->_phpmailer->SMTPAuth = true;     					// turn on SMTP authentication
				
				$this->_phpmailer->Username = config::get('mail','username');  	// SMTP username
				$this->_phpmailer->Password = config::get('mail','password'); 	// SMTP password
			}
			
			if($this->_phpmailer->Send())
				return true;
			
			$this->_errors[] = 'Failed to send E-Mail because: ' . $this->_phpmailer->ErrorInfo;
			
			return false;
		}
		else
			return false;
	}
	
	public function add_attachement($path_and_filename_to_attachment, $attachment_name = null)
	{
		$this->_phpmailer->AddAttachment($path_and_filename_to_attachment,$attachment_name);
	}
	
	/**
	 * Substitutes all dynamic variables found in an email subject and message fields within {var} tags
	 * @param array $variables_to_parse Associative array of key => values to substitute in the subject and message
	 */
	function parse_message($variables_to_parse = array())
	{
		foreach($variables_to_parse as $key => $value)
		{
			$this->subject = str_replace('{'.$key.'}', $value, $this->subject);
			$this->message = str_replace('{'.$key.'}', $value, $this->message);
		}
	}
	
	function get_fields_for_form()
	{
		$form_data 								= parent::get_fields_for_form();
	
		$form_data['format']['type'] 			= 'select';
		$form_data['format']['options'] 		= array('plain' => 'Plain Text','html' => 'HTML');
	
		$form_data['system_variables']['type'] 	= 'comment';
		
		unset($form_data['to']);
		
		return $form_data;
	}
}
?>