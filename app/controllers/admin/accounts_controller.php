<?php
/**
 * Store Administrator management
 * 
 * @package efusion
 * @subpackage controllers
 */
class accounts_controller extends admin_controller
{	
	function accounts_controller(&$application)
	{
		parent::admin_controller($application);
		
		$this->breadcrumb[] = array('admin/accounts/index' => 'Administrator account management');	
	}
	
	function index()
	{
		if(!empty($_GET['sort']))
			$order_by = '"'.$this->application->db->escape_string($_GET['sort']).'"';
		else
			$order_by = '"id" DESC';
			
		if(!empty($_GET['filter_by']))
			$filter_by = strtolower($_GET['filter_by']);
		else
			$filter_by = null;
					
		if(!empty($_GET['page']))
			$current_page_index = (int)$_GET['page'];
		else
			$current_page_index = 1;
		
		$account =& model::create('account');
				
		$pager_options = array('select' => 'account.id, account.email, account.phone', 
								'where' => "\"group\".name = 'administrators'" .
											(($filter_by) ? " AND LOWER(account.email) LIKE '%".$this->application->db->escape_string($filter_by)."%'" : null), 
								'join' => 'INNER JOIN "group" ON "group".id = account.group_id',
								'escape' => false, 
								'order' => $order_by);
								
		$this->template_data['accounts_paged'] = $account->find_all_paged($pager_options,config::get('admin','results_per_page'),$current_page_index);		
	}
	
	function create()
	{
		$account =& model::create('account');
		$admin_group =& model::create('group');
		
		$admin_group->find_by_field('name','administrators');
		
		if(isset($this->params['save']))
		{
			$account->set_field_values_from_array($this->params['account']);
			$account->group_id = $admin_group->id;

			if($account->save())
			{			
				$this->flash['notice'][] = 'Administrator account created successfully.';
				$this->redirect_to('admin/accounts','index','https');	
			}
			else
				$this->flash['error'] = $account->_errors;
		}
		else if(isset($this->params['cancel']))
			$this->redirect_to('admin/accounts','index','https');
		
		$this->template_data['account_form'] = $account->get_fields_for_form(true, false);
		$this->template_data['account_form']['new_password']['label'] = 'Password';
		
		$this->breadcrumb[] = array('admin/accounts/create' => 'Create new account');
	}
	
	function edit()
	{
		$account =& model::create('account',$this->params['url_params']);
				
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
			
			$account->set_field_values_from_array($this->params['account']);
			
			if($account->save())
			{
				$this->flash['notice'][] = 'Account updated successfully.';
				$this->redirect_to('admin/accounts','index','https');		
			}	
			else
				$this->flash['error'] = $account->_errors;
		}
		else if(isset($this->params['delete']))
		{
			$account->delete($account->id);
			
			$this->flash['notice'][] = 'Account deleted successfully';
			$this->redirect_to('admin/accounts','index','https');
		}
		else if(isset($this->params['cancel']))
			$this->redirect_to('admin/accounts','index','https');
		
		$this->template_data['account_form'] = $account->get_fields_for_form(true, true);
		
		//Cannot delete own user
		$this->template_data['allow_delete'] = ($account->id != $_SESSION['account_id']);
		
		$this->breadcrumb[] = array('admin/accounts/edit/'.$account->id => 'Modify administrator account');	
	}
}

?>