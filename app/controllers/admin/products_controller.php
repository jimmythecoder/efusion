<?php
/**
 * Product management
 * 
 * @package efusion
 * @subpackage controllers
 */
class products_controller extends admin_controller
{
	function products_controller(&$application)
	{
		parent::admin_controller($application);
		
		$this->breadcrumb[] = array('admin/products/index' => 'Products');	
	}
	
	/**
	 * Displays a paginated list of all products
	 */
	function index()
	{
		if(!empty($_GET['sort']))
			$order_by = '"'.$this->application->db->escape_string($_GET['sort']).'"';
		else
			$order_by = 'is_active DESC,created_at DESC';
			
		if(!empty($_GET['filter_by']))
			$filter_by = strtolower($_GET['filter_by']);
		else
			$filter_by = null;
					
		if(!empty($_GET['page']))
			$current_page_index = (int)$_GET['page'];
		else
			$current_page_index = 1;
		
		$product =& model::create('product');
		$category =& model::create('category');
		
		$conditional_filters = array();
		
		if(!empty($_GET['c']))
		{
			$child_categories = $category->get_all_child_categories((int)$_GET['c']);
			$conditional_filters[] = "(product.category_id IN (".implode(',',$child_categories).'))';
		}
		
		if($filter_by)
			$conditional_filters[] = "LOWER(product.name) LIKE '%".$this->application->db->escape_string($filter_by)."%'";
		
		$pager_options = array('select' => 'product.id,product.url_name,product.name,product.is_active, image.filename AS image_filename', 
								'join' => 'JOIN image ON product.image_id = image.id',
								'where' => implode(' AND ',$conditional_filters), 
								'escape' => false, 
								'order' => $order_by);
		
		if(!$product_categories = cache::get('product_categories','categories'))
		{
			$category =& model::create('category');
			
			$product_categories = $category->get_categories_as_list();
			
			cache::save($product_categories,'product_categories','categories');
		}
								
		$this->template_data['products_paged'] = $product->find_all_paged($pager_options,config::get('admin','results_per_page'),$current_page_index);
		$this->template_data['product_categories'] = $product_categories;
		$this->template_data['search_in_category'] = isset($_GET['c']) ? $_GET['c'] : ROOT_CATEGORY_NODE;
	}
	
	/**
	 * Modify an existing product
	 */
	function edit()
	{
		$product =& model::create('product',$this->params['url_params']);
		
		if(isset($this->params['save']))
		{
			$product->set_field_values_from_array($this->params['product']);
			
			//Upload a new product image if one given
			if(!empty($_FILES['image_id']) && $_FILES['image_id']['error'] != UPLOAD_ERR_NO_FILE)
			{
				$image =& model::create('image');
				if($uploaded_image_id = $image->upload_image('image_id',IMAGE_UPLOADS_DIR,str_replace('-','_',$product->url_name)))
				{						
					//Delete old product images
					if($product->has_image_been_uploaded())
						$image->delete($product->image_id);
						
					$product->image_id = $uploaded_image_id;
				}
				else
					$this->flash['error'] = $image->_errors;
			}
			
			if(empty($this->flash['error']) && $product->save())
			{
				//Save the product variants
				$product->set_variants(isset($this->params['product']['variant']) ? $this->params['product']['variant'] : array());
				
				$this->flash['notice'][] = 'Product updated successfully';
				$this->redirect_to('admin/products','index','https');
			}
		}
		else if(isset($this->params['delete']))
		{
			$product->delete($product->id);
			$this->flash['notice'][] = 'Product deleted successfully';
			$this->redirect_to('admin/products','index','https');
		}
		else if(isset($this->params['cancel']))
			$this->redirect_to('admin/products','index','https');
		
		$this->template_data['product_form'] = $product->get_fields_for_form();
	
		//If no orders have linked to this product, it is considered deletable
		$order_product =& model::create('order_product');
		if($order_product->count(array('where' => 'product_id = '.$product->id)) <= 0)
			$this->template_data['allow_delete'] = true;
		else
			$this->template_data['allow_delete'] = false;
	
		$this->breadcrumb[] = array('admin/products/edit/'.$product->id => 'Modify '.$product->name);	
	}
	
	/**
	 * Create a new product
	 */
	function create()
	{
		$product =& model::create('product');
		
		if(isset($this->params['save']))
		{
			$product->set_field_values_from_array($this->params['product']);
			
			//Upload product image
			if(!empty($_FILES['image_id']) && $_FILES['image_id']['error'] != UPLOAD_ERR_NO_FILE)
			{
				$image 						=& model::create('image');
				
				$product_url_name 			= $product->sanitize_string_for_url_name($product->name);
				
				$preferred_image_filename 	= str_replace('-','_',$product_url_name);
				
				$product->image_id 			= $image->upload_image('image_id',IMAGE_UPLOADS_DIR,$preferred_image_filename);
			}
			else
				$product->image_id = DEFAULT_IMAGE_ID;
				
			if($product->image_id)
			{
				if($product->save())
				{
					//Save the product variants
					if(isset($this->params['product']['variant']))
						$product->set_variants($this->params['product']['variant']);
						
					$this->flash['notice'][] = 'Product created successfully';
					$this->redirect_to('admin/products','index','https');
				}
				else	
					$this->flash['error'] = $product->_errors;
			}
			else	
				$this->flash['error'] = $image->_errors;
		}
		
		$this->template_data['product_form'] = $product->get_fields_for_form();
		
		if(!empty($_GET['category']))
			$this->template_data['product_form']['category_id']['value'] = (int)$_GET['category'];

		$this->breadcrumb[] = array('admin/products/create' => 'Add a new product');
	}
}
?>