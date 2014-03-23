<?php
/**
 * Product information that does not change
 * 
 * @package efusion
 * @subpackage models
 */
class product extends model
{
	/**
	 * Primary key
	 * @var int
	 */
	var $id;
	
	/**
	 * Foriegn key to the category table which this product resides under
	 * @var int
	 */
	var $category_id;
	
	/**
	 * Foriegn key to the image table for the primary product image
	 * @var int
	 */
	var $image_id;
	
	/**
	 * Product name
	 * @var string
	 */
	var $name;
	
	/**
	 * Product description
	 * @var string
	 */
	var $description;
	
	/**
	 * Cost of the product to the store
	 */
	var $cost_price;
	
	/**
	 * Price of the product to customers
	 */
	var $sale_price;
	
	/**
	 * Product weight in Kilograms
	 * @var float
	 */
	var $weight;
	
	/**
	 * Product code/reference/external id
	 * @var string
	 */
	var $code;
	
	/**
	 * Total quantity of this product that is currently in stock
	 * @var int
	 */
	var $quantity_in_stock;
	
	/**
	 * Is the product active/enabled
	 * @var boolean
	 */
	var $is_active;
	
	/**
	 * The date and time this product was created at
	 * @var datetime
	 */
	var $created_at;
	
	/**
	 * The unique name to use in the URL to view this product
	 * @var string
	 */
	var $url_name;
	
	/**
	 * Flag to mark the product as a featured product or not
	 * @var boolean
	 */
	var $is_featured;
	
	
	function product($id = null)
	{
		parent::model($id);
		
		$this->set_protected_fields(array('created_at'));
	}
	
	function validate()
	{
		$this->validates_foriegnkey_exists('image_id','An image must be associated with this product');
		$this->validates_foriegnkey_exists('category_id','You must select a category for this product');
		$this->validates_presence_of('name');
		$this->validates_presence_of('cost_price');
		$this->validates_presence_of('sale_price');
		$this->validates_presence_of('url_name');

		if($this->url_name)
			$this->validates_uniqueness_of('url_name');

		if(!is_null($this->id))
			$this->validates_presence_of('created_at');
							
		return parent::validate();
	}
	
	/**
	 * Sets the url_name to a sane value if none entered for this product
	 */
	function before_save()
	{
		if(empty($this->url_name))
			$this->set_url_name_from_name();
		else
			$this->url_name = $this->sanitize_string_for_url_name($this->url_name);
		
		//Strip off non numeric chars from price
		$this->sale_price = preg_replace('/[^0-9\.]+/','',$this->sale_price);
		$this->cost_price = preg_replace('/[^0-9\.]+/','',$this->cost_price);
	}
	
	function before_insert()
	{
		$this->created_at = date(SQL_DATETIME_FORMAT);	
	}
	
	/**
	 * Cleans the product name and makes a safe unique URL for the product
	 */
	function set_url_name_from_name()
	{
		$this->url_name = $this->sanitize_string_for_url_name($this->name);
		
		//Make sure this URL name is unique
		$counter = 2;
		while(!$this->validates_uniqueness_of('url_name'))
			$this->url_name = $this->url_name.$counter++;
			
		//Flush any errors out that we may have generated in creating a unique URL
		$this->_errors = array();	
	}
	
	/**
	 * Sanitizes a given string to make it safe for use in a URL
	 */
	function sanitize_string_for_url_name($string)
	{
		$lowercase_string = strtolower($string);
		$alphanumeric_string = preg_replace('/[^a-z0-9 \-]/', '', $lowercase_string);
		$string_with_hyphens_as_spaces = str_replace(' ', '-', $alphanumeric_string);
		
		return $string_with_hyphens_as_spaces;	
	}
	
	/**
	 * After the record is saved, we clear the product cache 
	 * and add/update the product in the search index
	 */
	function after_save()
	{
		cache::clear_cache_groups_from_cache_id('product');
		
		//Update the search index for this product
		$search =& model::create('search');
		$search->update_product_in_search_index($this->id);
	}
	
	/**
	 * Customize the product form
	 */
	function get_fields_for_form()
	{
		$form_data = parent::get_fields_for_form();
		
		//Customize the product form
		$form_data['description']['type'] = 'wysiwyg';
		unset($form_data['created_at']);
		
		$form_data['weight']['label'] = 'Weight (Kg)';
		$form_data['url_name']['null'] = true;
		
		$form_data['image_id'] = array('table' => 'product', 'type' => 'image', 'label' => 'Image','null' => true);	
	
		//Set the category list options
		if(!$product_category_options = cache::get('product_categories','categories'))
		{
			$category =& model::create('category');

			$product_category_options = $category->get_categories_as_list();
			
			cache::save($product_category_options,'product_categories','categories');
		}
		
		$form_data['category_id'] = array('table' => 'product', 'value' => $this->category_id, 'type' => 'recursive_option_select', 'label' => 'Category', 'options' => $product_category_options);
	
		if($this->find_foreign_key('image_id'))
			$form_data['image_id']['image'] = $this->image->fields_as_associative_array();
		
		//Load product variants
		$variant_group =& model::create('variant_group');
		$product_variant_group =& model::create('product_variant_group');
		
		if(!is_null($this->id))
			$selected_variants = $product_variant_group->find_all(array('select' => 'variant_group_id AS id', 'where' => 'product_id = '. $this->id));
		else
			$selected_variants = array();
		
		//Hack: the id's are the array keys for find_all results, so we simply selected variant_group_id as the id as we wanted that and not the actual row ids
		$selected_variants = array_keys((array)$selected_variants);
		$variation_options = $variant_group->find_all();
		
		$form_data['variations'] = array('table' => 'product', 'type' => 'checklist', 'label' => 'Variations', 'options' => $variation_options, 'values' => $selected_variants, 'null' => true);	
		
		return $form_data;
	}
	
	function delete($id)
	{
		//Maintain referential integrity by removing subordinates of the product table
		$product =& model::create('product');
		
		if($product->find($id))
		{
			//Delete images/files
			if($product->has_image_been_uploaded())
			{
				$image =& model::create('image');
				$image->delete($product->image_id);
			}
			
			//Delete product variations
			$product_variant_group =& model::create('product_variant_group');
			$product_variant_group->delete_all_product_variant_groups_for_product_id($product->id);
			
			//Delete product reviews
			$product_review =& model::create('product_review');
			$product_review->delete_all_reviews_for_product_id($product->id);
			
			//Delete from search index
			$search =& model::create('search');
			$search->remove_product_from_search_index($product->id);
			
			//Clear cache
			cache::clear_cache_groups_from_cache_id('product');
		}
		
		return parent::delete($id);
	}
	
	/**
	 * Tests if the user has uploaded an image for this product
	 * @return boolean true if an image exists, else false
	 */
	function has_image_been_uploaded()
	{
		return ($this->image_id != DEFAULT_IMAGE_ID);
	}
	
	/**
	 * Sets the associated product variants after save of a product
	 */
	function set_variants($variants)
	{
		if(is_null($this->id) || !is_array($variants))
			return false;
			
		//Truncate current variants
		$this->execute_sql_query('DELETE FROM product_variant_group WHERE product_id = ?',array($this->id));
		
		$product_variant_group =& model::create('product_variant_group');
		$product_variant_group->product_id = $this->id;
		
		foreach($variants as $selected_variant_group_id => $value)
		{
			$product_variant_group->variant_group_id = $selected_variant_group_id;
			$product_variant_group->save();
			
			//Reset object id so we can do an insert on next loop iteration
			$product_variant_group->id = null;
		}
	}
	
	/**
	 * Calls the find_all method with the given conditions and then joins the image table with each result
	 * @param array $conditions find_all conditions
	 * @return array product array with an array image attribute added
	 * @todo implement a file library and join onto that
	 */
	function find_all_with_images($conditions = null)
	{
		if(!$products = $this->find_all($conditions))
			return array();
		
		$image =& model::create('image');
		
		//Load image information for each product
		foreach($products as $id => $product)
		{
			$image->find($product['image_id']);
			
			$products[$id]['image'] = $image->fields_as_associative_array();
			
			$image->clear_field_values();
		}

		return $products;
	}
}

?>