<?php
class search extends model
{
	/**
	 * User input search query
	 * @var string
	 */
	var $search_query;
	
	/**
	 * An array of keywords form the search query to search for
	 * @var array
	 */
	var $keywords_to_search_for;
	
	/**
	 * Total number of search results produced from the keywords
	 * @var int
	 */
	var $total_number_of_search_results;
	
	/**
	 * How many results to fetch in the limit clause
	 * @var int
	 */
	var $results_per_page;
	
	/**
	 * Current page number for paginated search results
	 * @var int
	 */
	var $page_number;
	
	/**
	 * Search only products in this category or its children
	 * @var int
	 */
	var $in_category_id;
	
	/**
	 * Search results found as an associative array with the following keys
	 * @var array
	 */
	var $search_results;
	
	/**
	 * Associative array of the weighting or rank given to any particular content
	 * @var array
	 */
	var $_search_weights;
	
	/**
	 * An array of fields to index
	 */
	var $_indexed_fields;
	
	function search()
	{
		parent::model();
			
		$this->set_foreign_key('in_category_id','category');	
		
		$this->_search_weights = array('product' =>
									array('code' 		=> 6,
									  	  'name' 		=> 3,
									  	  'description' => 2),
								  	  'content' =>
									array('title' 		=> 3,
									  	  'content' 	=> 2,
									  	  'description' => 1));
									  	  
		$this->_indexed_fields = array('product' => 
									array('code','name','description'),
									   'content' =>
									array('title','content','description'));
	}
	
	function validate()
	{
		$this->validates_presence_of('search_query');
		
		if($this->in_category_id)
			$this->validates_foriegnkey_exists('in_category_id','Selected category does not exist');
		
		$this->validates_presence_of('results_per_page');
		$this->validates_presence_of('page_number');
		
		return parent::validate();
	}
	
	/**
	 * Returns an array of unique keywords extracted from the search string
	 */
	function set_keywords_from_search_query()
	{
		$santizied_search_query = $this->get_sanitized_search_query($this->search_query);
	
		$keywords_to_search_for = explode(' ',$santizied_search_query);
		$unique_keywords = array_unique($keywords_to_search_for);
		
		$this->keywords_to_search_for = $this->remove_ignored_keywords_from_array($unique_keywords);
	}

	/**
	 * Returns a sanitized search query by
	 *  1. Making all search input lowercase
	 *  2. Trimming off whitespace and control chars
	 *  3. Removing all non alphanumeric characters from the search query
	 */
	function get_sanitized_search_query($search_query)
	{
		$lowercase_search_query = strtolower(substr($search_query,0,20000));	
		$trimed_search_query = trim($lowercase_search_query);	
		$santizied_search_query = preg_replace('/[^[:alnum:]\- ]/i', '', $trimed_search_query);
		
		return $santizied_search_query;
	}
	
	/**
	 * Removes ignored search terms like profanity from a keywords array
	 */	
	function remove_ignored_keywords_from_array($keywords_array)
	{
		//Load all short search terms we wish to ignore
		$path_and_filename = SITE_ROOT_DIR . '/config/ignored_search_terms.txt';
		if(!file_exists($path_and_filename))
		{
			trigger_error('ignored search terms config file is missing',E_USER_WARNING);
			return $keywords_array;
		}
			
		$ignored_search_terms = file_get_contents($path_and_filename);
		$ignored_search_terms_as_array = explode(',',$ignored_search_terms);

		//Remove ignored search terms
		$clean_keywords = array_diff($keywords_array,$ignored_search_terms_as_array);
	
		//Remove short keywords < 2 chars
		$long_keywords = array_filter($clean_keywords,create_function('$keyword','return strlen($keyword) > 2;'));
		$nice_keywords = array_filter($long_keywords,create_function('$keyword','return strlen($keyword) < 15;'));
		
		return $nice_keywords;			
	}
	
	function build_keyword_sql_filter($keywords_to_search_for)
	{
    	$keyword_filter = '';
    	
    	if(empty($keywords_to_search_for))
    		return $keyword_filter;
    	
    	foreach($keywords_to_search_for as $keyword)
    		$keyword_filter .= "keyword.keyword = '" . $this->_db->escape_string($keyword) . "' OR ";	
    		
    	$keyword_filter = substr($keyword_filter, 0, strlen($keyword_filter) - 3);
    	
    	return $keyword_filter;
	}
	
	function build_category_sql_filter($in_category_id)
	{
		if(empty($in_category_id))
			return '';
		
		$category =& model::create('category');
		$child_categories = $category->get_all_child_categories($in_category_id);
		$category_filter = "AND (product.category_id IN (" . implode(',',$child_categories) . '))';	
	
		return $category_filter;
	}
	
	/**
	 * Counts the number of products found for a search query
	 * @return int total number of products found
	 */
	function count_products()
	{	
		if(!$this->validate())
			return false;

		if(empty($this->keywords_to_search_for))
			$this->set_keywords_from_search_query();
					
		$keyword_filter 	= $this->build_keyword_sql_filter($this->keywords_to_search_for);
		
		$category_filter 	= $this->build_category_sql_filter($this->in_category_id);
		
		if(empty($keyword_filter))	
		{
			$this->total_number_of_search_results = 0;
			
			return $this->total_number_of_search_results;
		}
			
		//Find the total number of search results
		$count_search_results_sql = '
				SELECT COUNT(*) ' .
				'FROM (SELECT DISTINCT product.id FROM product ' .
				'	INNER JOIN product_keyword ON product_keyword.product_id = product.id ' .
				'	INNER JOIN keyword ON product_keyword.keyword_id = keyword.id ' .
				'WHERE ('.$keyword_filter.') '.$category_filter . ' AND product.is_active = 1 ' . 
				'GROUP BY product.id) AS product_count';
		
		$this->total_number_of_search_results = $this->_db->get_first_cell($count_search_results_sql);
	
		return $this->total_number_of_search_results;
	}

	/**
	 * Calculates the record offset to use in the limit clause of the search query by
	 *  - results per page * current page number
	 * @return int record offset
	 */
	function calculate_record_offset()
	{
		return $this->results_per_page * ($this->page_number - 1);
	}
		
	function calculate_number_of_pages()
	{
		return ceil($this->total_number_of_search_results / $this->results_per_page);	
	}
	
	function is_last_page()
	{
		$number_of_pages = $this->calculate_number_of_pages();
		
		return $this->page_number >= $number_of_pages; 
	}

	/**
	 * Finds all products for a search query
	 * 
	 * @return array associative array with the following keys
	 * 	- product_id
	 *  - product_name
	 *  - product_description
	 *  - product_url_name
	 *  - frequency
	 */
	function find_all_products()
	{
		if(!$this->validate())
			return false;

		if(empty($this->keywords_to_search_for))
			$this->set_keywords_from_search_query();
						
		$keyword_filter 	= $this->build_keyword_sql_filter($this->keywords_to_search_for);
		
		$category_filter 	= $this->build_category_sql_filter($this->in_category_id);

		$limit 				= (int)$this->results_per_page.' OFFSET '. (int)$this->calculate_record_offset();

		if(empty($keyword_filter))	
		{
			$this->search_results = array();
			
			return $this->search_results;
		}
				
		//Perform a keyword weighted search query to find the most relavent results
		$sql = 'SELECT product.id, product.name, product.description, product.url_name, image.filename AS image_filename, sum(product_keyword.frequency) AS frequency ' .
				'FROM product ' .
				'	INNER JOIN product_keyword ON product_keyword.product_id = product.id ' .
				'	INNER JOIN keyword ON product_keyword.keyword_id = keyword.id ' .
				'	INNER JOIN image ON product.image_id = image.id ' .
				'WHERE ('.$keyword_filter.') '.$category_filter . ' AND product.is_active = 1 ' . 
				'GROUP BY 
					product.id,
					product.name,
  					product.description,
  					product.url_name ' . 
				'ORDER BY frequency DESC, product.name, product.id ' .
				'LIMIT ' . $limit;
		
		$this->search_results = $this->_db->query_as_array($sql);
					
		return $this->search_results;		
	}
	
	/**
	 * Adds a new product to the search index
	 * @param int $product_id product id for the product to add
	 */
	function add_product_to_search_index($product_id)
	{
		$product =& model::create('product');
				
		if(!$product->find($product_id))
			return false;
		
		foreach($this->_indexed_fields['product'] as $field)
		{
			$string_to_index 	= $product->$field;
			$string_weight 		= $this->_search_weights['product'][$field];
			
			//Sanitize input
			$sanitized_string 	= $this->get_sanitized_search_query($string_to_index);	
		
			//Split string into keywords
			$keywords 			= explode(" ",$sanitized_string);
			
			//Remove unneccessary keywords
			$clean_keywords 	= $this->remove_ignored_keywords_from_array($keywords);
			
			//Group keywords and count frequency of occurance
			$keywords_with_frequencies = array_count_values($clean_keywords);
			
			foreach($keywords_with_frequencies as $keyword => $frequency)
				$this->_index_keyword($keyword, $product->id, $frequency * $string_weight);
		}
	}
	
	/**
	 * Adds or updates a keyword to the product keyword index
	 * @param string $keyword single keyword to index
	 * @param int $product_id product id that we are indexing
	 * @param int $frequency number of times the keyword appears in this context
	 * @return boolean true if added successfully, else false
	 */
	function _index_keyword($keyword, $product_id, $frequency)
	{
		$model_keyword 		=& model::create('keyword');
		$product_keyword 	=& model::create('product_keyword');
		
		//Insert unique keyword
		if(!$model_keyword->find_by_field('keyword',$keyword))
		{
			$model_keyword->keyword = $keyword;
			$model_keyword->save();
		}
		
		//Add keyword into product frequency table
		if($product_keyword->find_by_field_array(array('keyword_id' => $model_keyword->id,'product_id' => $product_id)))
			$product_keyword->frequency += $frequency;	//If keyword was found in say title and description		
		else
		{
			$product_keyword->keyword_id 	= $model_keyword->id;
			$product_keyword->product_id 	= $product_id;
			$product_keyword->frequency 	= $frequency;			
		}
		
		return $product_keyword->save();		
	}
	
	/**
	 * Updates a products index data when a product has changed
	 * @param int $product_id product id for the product to update
	 */
	function update_product_in_search_index($product_id)
	{
		$this->remove_product_from_search_index($product_id);
		
		$this->add_product_to_search_index($product_id);
	}
	
	/**
	 * Removes a product from the search index
	 * @param int $product_id product id for the product to remove
	 */
	function remove_product_from_search_index($product_id)
	{
		$this->execute_sql_query('DELETE FROM product_keyword WHERE product_id = ?',array((int)$product_id));	
	}
}
?>