<?php
/**
 * Admin category management
 * 
 * @package efusion
 * @subpackage controllers
 */
class categories_controller extends admin_controller
{
	function categories_controller(&$application)
	{
		parent::admin_controller($application);
		
		$this->parent_category_id = isset($this->params['url_params']) ? (int)$this->params['url_params'] : 0;
		
		$this->breadcrumb[] = array('admin/categories/index' => 'Categories');
		
		$parent_category =& model::create('category',$this->parent_category_id);
		$this->template_data['category_named_path'] = $parent_category->get_category_named_path($this->parent_category_id);
		
		array_shift($this->template_data['category_named_path']);	//Pop off root element, as we dont want this in the breadcrumb
		foreach($this->template_data['category_named_path'] as $index => $category)
			$this->breadcrumb[] = array('admin/categories/index/'.$category['id'] => $category['name']);
		
		$this->template_data['parent_category'] = $parent_category->fields_as_associative_array();	
	}
	
	function index()
	{
		//Display a list of categories in hierachy
			
		$category =& model::create('category');
		
		$find_conditions = array(
							'select' => 'category.*, COUNT(product.id) AS product_count', 
							'join' => 'LEFT JOIN product ON product.category_id = category.id', 
							'where' => 'category.category_id = '.$this->parent_category_id, 
							'group' => 'category.id', 
							'order' => 'sort_order,name');
		
		$this->template_data['categories'] = $category->find_all($find_conditions);
	}
	
	function edit()
	{
		$category =& model::create('category');
		if(!$category->find($this->params['url_params']))
		{
			$this->flash['error'][] = 'The category you tried to edit does not exist';
			$this->redirect_to('admin/categories','index','https');
		}
		
		$this->parent_category_id = $category->category_id;
		
		if(isset($this->params['save']))
		{
			$category->set_field_values_from_array($this->params['category']);
			$category->category_id = $this->parent_category_id;
			if($category->save())
			{
				$this->flash['notice'][] = 'Category updated successfully.';
				$this->redirect_to('admin/categories','index/'.$this->parent_category_id,'https');
			}
			else
				$this->flash['error'] = $category->_errors;
		}
		else if(isset($this->params['delete']))
		{
			$category->delete($category->id);
			$this->flash['notice'][] = 'Category deleted successfully';
			$this->redirect_to('admin/categories','index/'.$this->parent_category_id,'https');
		}
		else if(isset($this->params['cancel']))
			$this->redirect_to('admin/categories','index','https');
		
		$this->template_data['category_form'] = $category->get_fields_for_form();
			
		//If no products nor categories have linked to this category, it is considered deletable
		$product =& model::create('product');
		if($product->count(array('where' => 'category_id = '.$category->id)) <= 0)
		{
			$sub_category =& model::create('category');
			if($sub_category->find_by_field('category_id',$category->id))
				$this->template_data['allow_delete'] = false;
			else
				$this->template_data['allow_delete'] = true;
		}
		else
			$this->template_data['allow_delete'] = false;
			
		$this->breadcrumb[] = array('admin/categories/edit/'.$this->parent_category_id => 'Edit category');	
	}
	
	function create()
	{
		$category =& model::create('category');
		
		if(isset($this->params['save']))
		{
			$category->set_field_values_from_array($this->params['category']);
			
			//Set the categories parent
			$category->category_id = $this->parent_category_id;
			
			if($category->save())
			{
				$this->flash['notice'][] = 'Category created successfully.';
				$this->redirect_to('admin/categories','index/'.$this->parent_category_id,'https');
			}
			else
				$this->flash['error'] = $category->_errors;
		}
		else if(isset($this->params['cancel']))
			$this->redirect_to('admin/categories','index','https');
		
		$this->template_data['category_form'] = $category->get_fields_for_form();	
		
		$this->breadcrumb[] = array('admin/categories/create/'.$this->parent_category_id => 'Create new category');	
	}
	
	/**
	 * Set a new category sort order via ajax post request
	 */
	function save_category_order()
	{
		$this->set_layout('services');
		$this->application->content_type = 'text/xml';
	
		$category =& model::create('category');
		$category_sort_order = $_POST['category-list'];

		foreach($category_sort_order as $sort_order => $category_id_string)
		{
			$category_id = preg_replace('/[^0-9+]/','',$category_id_string);
			
			if($category->find($category_id))
			{
				$category->sort_order = $sort_order;
				$category->save();
			}
		}
		
		$this->template_data['categories'] = implode(',',$category_sort_order);
	}
}

?>