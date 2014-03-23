<?php
/**
 * Customers address book management controller
 * 
 * @package efusion
 * @subpackage controllers
 */
class details_controller extends my_account_controller
{	
	/**
	 * Edit customers account details
	 */
	function edit()
	{
		$account =& model::create('account',$_SESSION['account_id']);

		if(isset($this->params['save']))
		{
			//Check passwords
			if(!empty($this->params['account']['new_password']) || !empty($this->params['account']['confirm_new_password']))
			{
				if($this->params['account']['new_password'] != $this->params['account']['confirm_new_password'])
					$account->_errors[] = 'Your new password does not match the confirm password.';
					
				if($account->hash_password($this->params['account']['current_password']) != $account->password_hash)
					$account->_errors[] = 'Your current password is not correct.';
					
				if(strlen($this->params['account']['new_password']) < MIN_PASSWORD_LENGTH)
					$account->_errors[] = 'Please enter a password with at least '.MIN_PASSWORD_LENGTH.' characters';
			}
			
			//Prevent user overriding email activation flag
			$is_email_activated = $account->is_email_activated;
			
			$account->set_field_values_from_array($this->params['account']);
			$account->is_email_activated = $is_email_activated;
			$account->is_active = 1;
					
			if($account->save())
			{
				$this->flash['notice'][] = 'Your account has been updated successfully.';
				$this->redirect_to('my-account','index','https');
			}
			else
				$this->flash['error'] = $account->_errors;
		}

		$this->template_data['account_form'] = $account->get_fields_for_form(false, true);
		
		$this->breadcrumb[] = array('my-account/details/edit' => 'Update Account Details');
	}
}
?>