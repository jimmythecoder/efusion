<?php
/**
 * Customers address book management controller
 * 
 * @package efusion
 * @subpackage controllers
 */
class address_book_controller extends my_account_controller
{
	function address_book_controller(&$application)
	{
		parent::my_account_controller($application);
		
		$this->breadcrumb[] = array('my-account/address-book/index' => 'Address Book');
	}
	
	/**
	 * Display all customers address book entries
	 */
	function index()
	{
		$address_book 	=& model::create('address_book');
		$country 		=& model::create('country');
		
		$this->template_data['address_books'] = $address_book->find_all(array('where' => 'account_id = '.$_SESSION['account_id'].' AND is_locked = 0'));
		
		foreach($this->template_data['address_books'] as $key => $address_book)
		{
			$country->find($address_book['country_id']);
			$this->template_data['address_books'][$key]['country'] = $country->fields_as_associative_array();
		}
		
		$this->template_data['max_address_books_allowed'] = MAX_ADDRESS_BOOKS_ALLOWED;
	}
	
	/**
	 * Add a new address book entry
	 */
	function add()
	{
		if(isset($this->params['save']))
		{
			$address_book =& model::create('address_book');
			$address_book->set_field_values_from_array($this->params['address_book']);
			$address_book->account_id 	= $_SESSION['account_id'];
			$address_book->is_locked 	= 0;
			$address_book->is_primary 	= !empty($this->params['address_book']['is_primary']);
			
			if($address_book->save())
			{
				$this->flash['notice'][] = 'Address has been added to your address book.';
				if(isset($_GET['redirect']) && $_GET['redirect'] == 'checkout')
					$this->redirect_to('checkout',null,'https');
				else
					$this->redirect_to('my-account/address-book','index','https');
			}
			else
			{
				$this->flash['error'] = $address_book->_errors;
				$this->template_data['address_book'] = $this->params['address_book'];	
			}
		}
		else if(isset($this->params['cancel']))
			$this->redirect_to('my-account/address-book','index','https');
			
	
		$obj_country =& model::create('country');
		$this->template_data['countries'] = $obj_country->get_countries_as_list();
		$this->template_data['address_book']['country_id'] = DEFAULT_COUNTRY_ID;
			
		$this->breadcrumb[] = array('my-account/address-book/add' => 'Add a new address');
	}
	
	function edit()
	{
		$address_book =& model::create('address_book',$this->params['url_params']);
		
		//Check logged in user is the actual owner of this address
		if($address_book->account_id == $_SESSION['account_id'])
		{
			if(isset($this->params['save']))
			{
				$address_book->set_field_values_from_array($this->params['address_book']);
				
				//Do not let user overwrite these settings
				$address_book->account_id 	= $_SESSION['account_id'];
				$address_book->is_locked 	= 0;
				$address_book->is_primary 	= !empty($this->params['address_book']['is_primary']);
				
				if($address_book->save())
				{
					$this->flash['notice'][] = 'Address has been updated successfully.';
					if(isset($_GET['redirect']) && $_GET['redirect'] == 'checkout')
						$this->redirect_to('checkout',null,'https');
					else
						$this->redirect_to('my-account/address-book','index','https');
				}
				else
					$this->flash['error'] = $address_book->_errors;
			}
		}
		else
			$this->redirect_to('my-account/address-book','index','https');

		$this->template_data['address_book'] = $address_book->fields_as_associative_array();
		
		$obj_country =& model::create('country');
		$this->template_data['countries'] = $obj_country->get_countries_as_list();
		
		$this->breadcrumb[] = array('my-account/address-book/edit/'.$this->params['url_params'] => 'Edit address entry');
	}
	
	function delete()
	{
		$address_book =& model::create('address_book',$this->params['url_params']);
		
		//Check logged in user is the actual owner of this address
		if($address_book->account_id == $_SESSION['account_id'])
		{
			if(isset($this->params['delete']))
			{
				if($address_book->delete($address_book->id))
				{
					$this->flash['notice'][] = 'Address deleted successfully.';
					if(isset($_GET['redirect']) && $_GET['redirect'] == 'checkout')
						$this->redirect_to('checkout',null,'https');
					else
						$this->redirect_to('my-account/address-book','index','https');
				}
				else
					$this->flash['error'] = $address_book->_errors;
			}
		}
		else
			$this->redirect_to('my-account/address-book','index','https');

		$address_book->find_foreign_key('country_id');
		
		$this->template_data['address'] 			= $address_book->fields_as_associative_array();
		$this->template_data['address']['country'] 	= $address_book->country->fields_as_associative_array();
		
		$this->breadcrumb[] = array('my-account/address-book/delete/'.$this->params['url_params'] => 'Delete address entry');	
	}
}
?>