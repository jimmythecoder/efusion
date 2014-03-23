<?php
/**
 * Product category model, contains a tree hierachy of categories
 * 
 * @package efusion
 * @subpackage models
 */
class category extends model
{
	/**
	 * Primary key
	 * @var int
	 */
	var $id;
	
	/**
	 * Foriegn key to this categories parent category, 0 if this is a root category
	 * @var int
	 */
	var $category_id;
	
	/**
	 * Name of the category
	 * @var string
	 */
	var $name;
	
	/**
	 * Description of the category
	 * @var string
	 */
	var $description;
	
	/**
	 * Unique SEO URL name, should be lower-cased, hyphened category name
	 * @var string
	 */
	var $url_name;
	
	/**
	 * Category sort order to display them in
	 * @var int
	 */
	var $sort_order;

	/**
	 * Date and time the category was created at
	 * @var datetime
	 */
	var $created_at;
	
	function category($id = null)
	{
		parent::model($id);	
		
		$this->set_protected_fields(array('created_at'));
	}
		
	function validate()
	{
		$this->validates_presence_of('category_id');
		$this->validates_presence_of('name');
		$this->validates_presence_of('url_name');
		
		if($this->url_name)
			$this->validates_uniqueness_of('url_name');

		if(!is_null($this->id))
			$this->validates_presence_of('created_at');
					
		return parent::validate();
	}

	/**
	 * Sets the url_name to a sane value if none entered for this category
	 */
	function before_save()
	{
		if(empty($this->url_name))
			$this->set_url_name_from_name();	
	}

	function before_insert()
	{
		$this->created_at = date(SQL_DATETIME_FORMAT);	
	}

	function after_save()
	{
		cache::clear_cache_groups_from_cache_id('category');
	}
		
	/**
	 * Cleans the category name and makes a safe unique URL for the category
	 */
	function set_url_name_from_name()
	{
		$this->url_name = str_replace(' ','-', preg_replace('/[^a-z0-9 ]/','', strtolower($this->name)));	
		
		//Make sure this URL name is unique
		$counter = 2;
		while(!$this->validates_uniqueness_of('url_name'))
			$this->url_name = $this->url_name.$counter++;
			
		//Flush any errors out that we may have generated in creating a unique URL
		$this->_errors = array();	
	}
	
	/**
     * Gets a recursive array tree of product categories (id, name) expanded from a child node
     * @param string $selected_category id
     * @return array a category path containing category id and name
     */
    function get_category_named_path($selected_category)
    {	
    	//Create full category array (category_id => parent_id)
		$categorys = $this->find_all(array('order' => 'sort_order,name'));
		
    	//Get category path within categories (6>3>1>0)
		$current_node = $selected_category;
		$category_named_path = array();
		
		while($current_node > 0)
		{
			//Recursively find parent nodes until we are at root node
			$category_named_path[] = array('id' => $current_node, 'name' => $categorys[$current_node]['name'], 'url' => 'catalog/'.$categorys[$current_node]['url_name']);
			$current_node = $categorys[$current_node]['category_id'];
		}
		$category_named_path[] = array('id' => ROOT_CATEGORY_NODE);
		
		$category_named_path = array_reverse($category_named_path);
		
		//Recursively build category tree
		return $category_named_path;
    }
    
    
    /**
     * Extracts a full tree of sub categories from a parent node recursively
     * @param array $category_path category list from path id => name in reverse (0>1>3>6)
     * @return array Array of recursive categories (id => name)
     */
    function load_category_path($category_path)
    {
    	//Extract this categorys branch data
    	$categorys = $this->find_all(array('where' => 'category_id = '.$category_path[0]['id'],'order' => 'sort_order,name'));
    	
    	//Remove parent category (so we move onto next branch)
    	array_shift($category_path);
    	
    	//If we have another branch to step onto, get subcategory data
    	if(count($category_path) > 0)
    		$categorys[$category_path[0]['id']]["children"] = $this->load_category_path($category_path);

		return $categorys;
    }
    
    /**
     * Gets the child categories of a parent
     * @param integer $category_id The parent category id to fetch children from
     * @return array Array of integer sub category id's, or false if none
     */
    function get_child_categories($category_id)
    {
    	$child_categories = $this->find_all(array('where' => 'category_id = '.(int)$category_id));
    	
    	//If there were any subcategories
    	if(count($child_categories) > 0)
    		return $child_categories;
    	else
    		return false;	
    }
    
    /**
     * Recursively gets all child and subchild categories stemming from a root category
     * @param int $category_root_id Parent category to recurse from
     * @return array of category id's which are children of a parent category
     */
    function get_all_child_categories($category_root_id)
    {
    	$result = array();
    	array_push($result,$category_root_id);
    	
    	if($child_categories = $this->get_child_categories($category_root_id))
    	{
    		//Go through all child nodes and get there children
    		foreach($child_categories as $category_id => $category)
    			$result = array_merge($result,$this->get_all_child_categories($category_id));
    	}
		
		return $result;
    }
    
    /**
     * Returns an associative array of all categories as a recursive list
     * @param int $parent_category_id parent category id to start from
     * @return array recursive array of categorys
     */
    function get_categories_as_list($parent_category_id = ROOT_CATEGORY_NODE)
    {
    	$category_list = array();
    	
    	if(!$categories = $this->find_all(array('where' => 'category_id = ' . (int)$parent_category_id, 'order' => 'category_id, sort_order, id')))
    		return null;
    	
    	foreach($categories as $key => $category)
    	{
    		$category_list[$category['id']] = $category;
    		$category_list[$category['id']]['children'] = $this->get_categories_as_list($category['id']);
    	}
    		
    	return $category_list;
    }
    
    function get_fields_for_form()
    {
    	$form_fields = parent::get_fields_for_form();
    	
    	$form_fields['url_name']['null'] = true;
    	unset($form_fields['created_at']);
    	
    	return $form_fields;
    }
    
    function delete($id)
    {
    	$category =& model::create('category',$id);
    	
    	//Validate no product is using this category
    	$product =& model::create('product');
    	if($product->find_by_field('category_id',$id))
    	{
    		$this->_errors[] = 'Can not delete ' . $category->name . ' category as the product ' . $product->name . ' has been assigned to it';
    		return false;
    	}
    	
		//Maintain referential integrity by removing all subcategories
		while($category->find_by_field('category_id',$id))
			$category->delete($category->id);

		cache::clear_cache_groups_from_cache_id('category');	
		
		return parent::delete($id);
    }
}

?>