<?php
/**
 * Product reviews
 * 
 * @package efusion
 * @subpackage models
 */
class product_review extends model
{
	/**
	 * Primary key
	 * @var int
	 */
	var $id;
	
	/**
	 * Foriegn key to product table
	 * @var int
	 */
	var $product_id;

	/**
	 * Foriegn key to account table (user who posted the comment)
	 * @var int
	 */
	var $account_id;
		
	/**
	 * Review rating as a percentage /100
	 * @var int
	 */
	var $rating;
	
	/**
	 * User review
	 * @var string
	 */
	var $comment;
	
	/**
	 * Date and time the review was posted at
	 * @var datetime
	 */
	var $reviewed_at;


	function product_review($id = null)
	{
		parent::model($id);
		
		$this->set_protected_fields(array('reviewed_at'));
	}
		
	function validate()
	{
		$this->validates_foriegnkey_exists('product_id');
		
		if($this->validates_foriegnkey_exists('account_id','You must be logged in to post a review'))
		{
			//Verfies the account has been activated
			$account =& model::create('account',$this->account_id);
			if(!$account->is_email_activated)
				$this->_errors[] = 'User account email has not yet been activated';	
		}
		
		$this->validates_uniqueness_of(array('product_id','account_id'),'You are only allowed to post 1 review per product');
		$this->validates_presence_of('rating','Please rate this product before posting');
		$this->validates_presence_of('comment','You must add a comment to post a review');
		$this->validates_presence_of('reviewed_at');
		
		return parent::validate();
	}
	
	/**
	 * Sets the reviewed_at date and time just before a record insert
	 */
	function before_insert()
	{
		$this->reviewed_at = date(SQL_DATETIME_FORMAT);
	}
	
	/**
	 * Finds all reviews for a single product
	 * @param int $product_id id of the product to find reviews for
	 * @return array associative array of product review records
	 */
	function find_all_by_product_id($product_id)
	{
		$product_id_as_int = (int)$product_id;
		
		return $this->find_all(array('select' => 'product_review.*, address_book.first_name, address_book.last_name','join' => 'INNER JOIN address_book ON address_book.account_id = product_review.account_id', 'where' => 'address_book.is_primary = 1 AND address_book.is_locked = 0 AND product_review.product_id = ' . $product_id_as_int,'order' => 'reviewed_at DESC'));	
	}
	
	/**
	 * Finds a single record based upon a user account id and a product id
	 * @param int $account_id id from account table
	 * @param int $product_id id from product table
	 * @return boolean true if record exists, else false
	 */
	function find_by_account_id_and_product_id($account_id, $product_id)
	{
		return $this->find_by_field_array(array('account_id' => $account_id, 'product_id' => $product_id));
	}
	
	/**
	 * Returns the average rating for a particular product
	 * @param int $product_id id of the product to find the average rating for
	 * @return int average rating
	 */
	function get_average_rating($product_id)
	{
		$product_id_as_int = (int)$product_id;
		
		return ceil($this->_db->get_first_cell('SELECT AVG(rating) FROM product_review WHERE product_id = ' . $product_id_as_int));
	}
	
	/**
	 * Deletes all the reviews for a given product id
	 * @param int $product_id primary key for product table
	 */
	function delete_all_reviews_for_product_id($product_id)
	{
		$this->execute_sql_query('DELETE FROM ' . $this->get_table_name() . ' WHERE product_id = ?',array($product_id));
		
		cache::clear_cache_groups_from_cache_id('product_review');	
	}
	
	function after_save()
	{
		cache::clear_cache_groups_from_cache_id('product_review');	
	}

	function delete($id)
	{
		cache::clear_cache_groups_from_cache_id('product_review');	
		
		return parent::delete($id);
	}
}

?>