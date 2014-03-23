<?php
/**
 * Customer account management
 * 
 * @package efusion
 * @subpackage controllers
 */
class customers_controller extends admin_controller
{	
	function customers_controller(&$application)
	{
		parent::admin_controller($application);
		
		$this->breadcrumb[] = array('admin/customers/index' => 'Customer account management');	
	}
	
	function index()
	{
		if(!empty($_GET['sort']))
			$order_by = '"'.$this->application->db->escape_string($_GET['sort']).'"';
		else
			$order_by = 'account.id DESC';
			
		if(!empty($_GET['filter_by']))
			$filter_by = strtolower($_GET['filter_by']);
		else
			$filter_by = null;
					
		if(!empty($_GET['page']))
			$current_page_index = (int)$_GET['page'];
		else
			$current_page_index = 1;
		
		$account =& model::create('account');
				
		$pager_options = array(	'select' => 'account.*, address_book.first_name, address_book.last_name',
								'where' => '"group".name = \'members\' AND account.is_active = 1 AND address_book.is_primary = 1 AND address_book.is_locked = 0' .
								(($filter_by) ? " AND (LOWER(account.email) LIKE '%".$this->application->db->escape_string($filter_by)."%' OR LOWER(address_book.first_name) LIKE '%".$this->application->db->escape_string($filter_by)."%' OR LOWER(address_book.last_name) LIKE '%".$this->application->db->escape_string($filter_by)."%')" : null), 
								'join' => 'INNER JOIN "group" ON "group".id = account.group_id ' .
										  'INNER JOIN address_book ON address_book.account_id = account.id',
								'escape' => false, 
								'order' => $order_by);
								
		$this->template_data['accounts_paged'] = $account->find_all_paged($pager_options,config::get('admin','results_per_page'),$current_page_index);		
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
					
				if(strlen($this->params['account']['new_password']) < MIN_PASSWORD_LENGTH)
					$account->_errors[] = 'Please enter a password with at least '.MIN_PASSWORD_LENGTH.' characters';
			}
			
			$account->set_field_values_from_array($this->params['account']);
			
			if($account->save())
			{
				$this->flash['notice'][] = 'Customer account updated successfully.';
				$this->redirect_to('admin/customers','index','https');		
			}	
			else
				$this->flash['error'] = $account->_errors;
		}
		else if(isset($this->params['delete']))
		{
			//Perform a soft delete
			$account->is_active = 0;
			$account->save();
			
			$this->flash['notice'][] = 'Customer account deleted successfully';
			$this->redirect_to('admin/customers','index','https');
		}
		else if(isset($this->params['cancel']))
			$this->redirect_to('admin/customers','index','https');
		
		$this->template_data['account_form'] = $account->get_fields_for_form(false, true);
		unset($this->template_data['account_form']['current_password']);
		
		//Allow admins to delete any customer
		$this->template_data['allow_delete'] = true;
		
		$this->breadcrumb[] = array('admin/customers/edit/'.$account->id => 'Modify customer account');	
	}
}

?>