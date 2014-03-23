<?php
/**
 * Variation management
 * 
 * @package efusion
 * @subpackage controllers
 */
class variations_controller extends admin_controller
{
	function variations_controller(&$application)
	{
		parent::admin_controller($application);
		
		$this->breadcrumb[] = array('admin/variations/index' => 'Product Variations');	
	}
	
	/**
	 * Displays a paginated list of all products
	 */
	function index()
	{
		if(!empty($_GET['sort']))
			$order_by = '"'.$this->application->db->escape_string($_GET['sort']).'"';
		else
			$order_by = 'name DESC';
			
		if(!empty($_GET['filter_by']))
			$filter_by = strtolower($_GET['filter_by']);
		else
			$filter_by = null;
					
		if(!empty($_GET['page']))
			$current_page_index = (int)$_GET['page'];
		else
			$current_page_index = 1;
		
		$variant_group =& model::create('variant_group');
				
		$pager_options = array('select' => 'variant_group.id,variant_group.name', 
								'where' => ($filter_by) ? "LOWER(variant_group.name) LIKE '%".$this->application->db->escape_string($filter_by)."%'" : null, 
								'escape' => false, 
								'order' => $order_by);
								
		$this->template_data['product_variants_paged'] = $variant_group->find_all_paged($pager_options,config::get('admin','results_per_page'),$current_page_index);
	}
	
	/**
	 * Modify an existing product variant
	 */
	function edit()
	{
		$variant_group =& model::create('variant_group');
		
		if(!$variant_group->find($this->params['url_params']))
			$this->redirect_to('admin/variations','index','https');
		
		if(isset($this->params['save']))
		{
			$variant_group->set_field_values_from_array($this->params['variant_group']);
			
			if($variant_group->save())
			{
				$this->flash['notice'][] = 'Variation group updated successfully';
				$this->redirect_to('admin/variations','index','https');
			}
			else
				$this->flash['error'] = $variant_group->_errors;
		}
		else if(isset($this->params['delete']))
		{
			$variant_group->delete($variant_group->id);
			$this->flash['notice'][] = 'Variation group deleted successfully';
			$this->redirect_to('admin/variations','index','https');
		}
		else if(isset($this->params['cancel']))
			$this->redirect_to('admin/variations','index','https');
		
		$this->template_data['variant_group_form'] = $variant_group->get_fields_for_form();

		$this->breadcrumb[] = array('admin/variations/edit/'.$variant_group->id => 'Modify '.$variant_group->name);	
	}
	
	/**
	 * Create a new variant group
	 */
	function create()
	{
		$variant_group =& model::create('variant_group');
		
		if(isset($this->params['save']))
		{
			$variant_group->set_field_values_from_array($this->params['variant_group']);
			$variant_group->before_save();
			
			if($variant_group->save())
			{
				$this->flash['notice'][] = 'Product Variation created successfully';
				$this->redirect_to('admin/variations','index','https');
			}
			else
				$this->flash['error'] = $variant_group->_errors;
		}
		
		$this->template_data['variant_group_form'] = $variant_group->get_fields_for_form();

		$this->breadcrumb[] = array('admin/variations/create' => 'Add a new product variation');
	}
	
	function variants()
	{
		$pager_params = $this->_get_pager_url_paramters();
		
		if(empty($pager_params['order_by']))
			$pager_params['order_by'] = 'name';	
			
		$product_variant =& model::create('product_variant');
		$variant_group =& model::create('variant_group',$this->params['url_params']);
		
		$pager_options = array('select' => 'product_variant.id,product_variant.name', 
								'where' => ('product_variant.variant_group_id = ' . $variant_group->id) . (($pager_params['filter_by']) ? " AND product_variant.name LIKE '%".$this->application->db->escape_string($pager_params['filter_by'])."%'" : null), 
								'escape' => false, 
								'order' => $pager_params['order_by']);
								
		$this->template_data['product_variants_paged'] = $product_variant->find_all_paged($pager_options,config::get('admin','results_per_page'),$pager_params['page']);
		$this->template_data['variant_group'] = $variant_group->fields_as_associative_array();
	
		$this->breadcrumb[] = array('admin/variations/variants/'.$variant_group->id => $variant_group->name.' variations');
	}
	
	function create_variant()
	{
		$product_variant =& model::create('product_variant');
		$variant_group =& model::create('variant_group',$this->params['url_params']);
		$product_variant->variant_group_id = $variant_group->id;
		
		if(isset($this->params['save']))
		{
			$product_variant->set_field_values_from_array($this->params['product_variant']);
			$product_variant->before_save();
			
			if($product_variant->save())
			{
				$this->flash['notice'][] = 'Product Variation created successfully';
				$this->redirect_to('admin/variations','variants/'.$variant_group->id,'https');
			}
			else
				$this->flash['error'] = $product_variant->_errors;
		}
		
		$this->template_data['product_variant_form'] = $product_variant->get_fields_for_form();

		$this->breadcrumb[] = array('admin/variations/variants/'.$variant_group->id => $variant_group->name);
		$this->breadcrumb[] = array('admin/variations/create-variant' => 'Create variation');		
	}
	
	function edit_variant()
	{
		$product_variant =& model::create('product_variant');
		
		if(!$product_variant->find($this->params['url_params']))
			$this->redirect_to('admin/variations','index','https');
		
		$variant_group =& model::create('variant_group',$product_variant->variant_group_id);
		
		if(isset($this->params['save']))
		{
			$product_variant->set_field_values_from_array($this->params['product_variant']);
			
			if($product_variant->save())
			{
				$this->flash['notice'][] = 'Variation updated successfully';
				$this->redirect_to('admin/variations','variants/'.$product_variant->variant_group_id,'https');
			}
			else
				$this->flash['error'] = $product_variant->_errors;
		}
		else if(isset($this->params['delete']))
		{
			$variant_group_id = $product_variant->variant_group_id;
			$product_variant->delete($product_variant->id);
			$this->flash['notice'][] = 'Variation deleted successfully';
			$this->redirect_to('admin/variations','variants/'.$variant_group_id,'https');
		}
		else if(isset($this->params['cancel']))
			$this->redirect_to('admin/variations','variants/'.$product_variant->variant_group_id,'https');
		
		$this->template_data['product_variant_form'] = $product_variant->get_fields_for_form();

		$this->breadcrumb[] = array('admin/variations/variants/'.$variant_group->id => $variant_group->name);
		$this->breadcrumb[] = array('admin/variations/edit-variant/'.$product_variant->id => 'Edit variant');	
	}
	
	function _get_pager_url_paramters()
	{
		$params = array();
		
		if(!empty($_GET['sort']))
			$params['order_by'] = '"'.$this->application->db->escape_string($_GET['sort']).'"';
			
		if(!empty($_GET['filter_by']))
			$params['filter_by'] = $_GET['filter_by'];
		else
			$params['filter_by'] = null;
					
		if(!empty($_GET['page']))
			$params['page'] = (int)$_GET['page'];
		else
			$params['page'] = 1;
			
		return $params;
	}
}
?>