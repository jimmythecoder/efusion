<?php
/**
 * Admin system generated email management
 * 
 * @package efusion
 * @subpackage controllers
 */
class emails_controller extends admin_controller
{
	function index()
	{
		$this->breadcrumb[] = array('admin/emails/index' => 'Emails');
	}
	
	function edit()
	{
		$email =& model::create('email',$this->params['url_params']);
		
		if(isset($this->params['save']))
		{
			$email->set_field_values_from_array($this->params['email']);
			
			if($email->save())
			{
				$this->flash['notice'][] = 'Email updated successfully.';
				$this->redirect_to('admin/emails','index','https');
			}
			else
				$this->flash['error'] = $email->_errors;
		}
		else if(isset($this->params['cancel']))
			$this->redirect_to('admin/emails','index','https');
		
		$this->template_data['email_form'] = $email->get_fields_for_form();
			
		$this->breadcrumb[] = array('admin/emails/edit/'.$email->id => 'Edit email');	
	}
}

?>