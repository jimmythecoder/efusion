<?php
/**
 * Customer Address book for contact information
 * 
 * @package efusion
 * @subpackage models
 */
class address_book extends model
{
	/**
	 * Primary Key
	 * @var int
	 */
	var $id;
	
	/**
	 * Foriegn key to account table
	 * @var int
	 */
	var $account_id;
	
	/**
	 * Customers first name
	 * @var string
	 */
	var $first_name;
	
	/**
	 * Customers last name
	 * @var string
	 */
	var $last_name;
	
	/**
	 * Customers company name (optional)
	 * @var string
	 */
	var $company;
	
	/**
	 * Customers street address
	 * @var int
	 */
	var $street;
	
	/**
	 * Suburb name
	 * @var string
	 */
	var $suburb;
	
	/**
	 * Address postal code
	 * @var string
	 */
	var $post_code;

	/**
	 * Name of the city
	 * @var string
	 */
	var $city;
	
	/**
	 * Foreign key to country table
	 * @var int
	 */
	var $country_id;
		
	/**
	 * Primary address flag, true if this is there main address to use by default
	 * @var boolean
	 */
    var $is_primary;
    
    /**
     * Address is locked if it is used in an order and cannot be changed
     * @var boolean
     */
    var $is_locked;
    
    /**
     * Longitude geocode of the addres
     * @var float
     */
    var $longitude;
    
    /**
     * Latitude geocode of the address
     * @var float
     */
    var $latitude;
	
	function address_book($id = null)
	{
		parent::model($id);
		
		$this->set_protected_fields(array('is_primary','is_locked','longitude','latitude'));	
	}
	
	function validate()
	{
		$this->validates_foriegnkey_exists('account_id');
		$this->validates_foriegnkey_exists('country_id');
		$this->validates_presence_of('first_name','Your first name is required');
		$this->validates_presence_of('last_name','Your last name is required');
		$this->validates_presence_of('street','Your street address is required');
		$this->validates_presence_of('suburb','Your suburb is required');
		$this->validates_presence_of('city','Your city is required');
		$this->validates_presence_of('is_locked','Is locked system setting failed to initialize');
		$this->validates_presence_of('is_primary','Is primary system setting failed to initialize');
		
		//Check user has not exceeded the maximum number of addresses allowed
		//If we are creating a new record and the is_locked (address used for orders) is not set
		if(is_null($this->id) && !$this->is_locked)
		{
			$address_book_count = $this->count(array('where' => 'account_id = '.$this->account_id.' AND is_locked = 0'));		
			if($address_book_count >= MAX_ADDRESS_BOOKS_ALLOWED)
				$this->_errors[] = 'You have reached the maximum allowed address book entries';
		}
		
		return parent::validate();
	}
	
	/**
	 * @deprecated moved into shipping_zone model
	 */
	function get_shipping_zone($tmp)
	{
		trigger_error('Method get_shipping_zone in address book has been depreciated, please use shipping_zone model instead.',E_USER_ERROR);
		
		return false;
	}
	
	/**
	 * @deprecated moved into shipping_tier model
	 */
	function get_shipping_cost($shipping_zone_id, $weight)
	{
		trigger_error('Method get_shipping_cost in address book has been depreciated, please use shipping_tier model instead.',E_USER_ERROR);
		
		return false;
	}
	
	function after_save()
	{
		$primary_address =& model::create('address_book');
		$primary_address->find_by_sql('SELECT * FROM "'.$this->get_table_name().'" WHERE account_id = '.$this->account_id.' AND is_locked = 0 AND is_primary = 1 AND id != '.$this->id);	

		//If we are making this address the primary, unset the old primary address
		if($this->is_primary && $primary_address->id)
		{
			$primary_address->is_primary = 0;
			$primary_address->save();
		}
		else if(!$this->is_primary && !$primary_address->id)
		{
			//Check that we have atleast 1 primary address
			$primary_address->find_by_field_array(array('account_id' => $this->account_id, 'is_locked' => 0, 'is_primary' => 0));
			$primary_address->is_primary = 1;
			$primary_address->save();
		}
	}
	
	function delete($id)
	{
		$address_to_delete =& model::create('address_book',$id);
		
		//We cant delete a primary address
		if($address_to_delete->is_primary)
			$this->_errors[] = 'You can not delete your primary address';

		if($address_to_delete->is_locked)
			$this->_errors[] = 'You can not delete an address used for an order';
					
		if(!count($this->_errors))
			return parent::delete($id);
		else
			return false;
	}
	
	/**
	 * Finds an address book record by id and account id (used to verify an address book record is associated with a specific account)
	 * @param int $id address book id
	 * @param int $account_id account id
	 */
	function find_by_id_and_account_id($id, $account_id)
	{
		return $this->find_by_field_array(array('id' => $id, 'account_id' => $account_id));
	}
	
	/**
	 * Finds an accounts primary address, used for contact details
	 */
	function find_primary_account_address($account_id)
	{
		return $this->find_by_field_array(array('account_id' => $account_id, 'is_primary' => 1, 'is_locked' => 0));
	}
}

?>