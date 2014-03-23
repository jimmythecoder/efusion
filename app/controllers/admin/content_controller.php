<?php
/**
 * Admin content management
 * 
 * @package efusion
 * @subpackage controllers
 */
class content_controller extends admin_controller
{
	function content_controller(&$application)
	{
		parent::admin_controller($application);
		
		$this->breadcrumb[] = array('admin/content/index' => 'Content Management');	
	}
	
	function index()
	{
		if(!empty($_GET['sort']))
			$order_by = '"'.$this->application->db->escape_string($_GET['sort']).'"';
		else
			$order_by = '"id" DESC';
			
		if(!empty($_GET['filter_by']))
			$filter_by = strtolower($_GET['filter_by']);
		else
			$filter_by = null;
					
		if(!empty($_GET['page']))
			$current_page_index = (int)$_GET['page'];
		else
			$current_page_index = 1;
		
		$content =& model::create('content');
				
		$pager_options = array('select' => 'content.id,content.url_name,content.title', 
								'where' => ($filter_by) ? "LOWER(content.title) LIKE '%".$this->application->db->escape_string($filter_by)."%'" : null, 
								'escape' => false, 
								'order' => $order_by);
								
		$this->template_data['content_paged'] = $content->find_all_paged($pager_options,config::get('admin','results_per_page'),$current_page_index);		
	}
	
	function create()
	{
		$content =& model::create('content');
		
		if(isset($this->params['save']))
		{
			$content->set_field_values_from_array($this->params['content']);
			
			if($content->save())
			{			
				$this->flash['notice'][] = 'Page created successfully.';
				$this->redirect_to('admin/content','index','https');	
			}
			else
				$this->flash['error'] = $content->_errors;
		}
		else if(isset($this->params['cancel']))
			$this->redirect_to('admin/content','index','https');
		
		$this->template_data['content_form'] = $content->get_fields_for_form();
		
		$this->breadcrumb[] = array('admin/content/create' => 'Create new Page');
	}
	
	function edit()
	{
		$content =& model::create('content',$this->params['url_params']);
				
		if(isset($this->params['save']))
		{
			$content->set_field_values_from_array($this->params['content']);
			
			if($content->save())
			{
				$this->flash['notice'][] = 'Page updated successfully.';
				$this->redirect_to('admin/content','index','https');		
			}	
			else
				$this->flash['error'] = $content->_errors;
		}
		else if(isset($this->params['delete']))
		{
			$content_title = $content->title;
			$content->delete($content->id);
			
			$this->flash['notice'][] = 'Page `'.$content_title.'` deleted successfully';
			$this->redirect_to('admin/content','index','https');
		}
		else if(isset($this->params['cancel']))
			$this->redirect_to('admin/content','index','https');
		
		$this->template_data['content_form'] = $content->get_fields_for_form();
		$this->template_data['allow_delete'] = !(bool)$content->is_system_content;
		
		$this->breadcrumb[] = array('admin/content/edit/'.$content->id => 'Modify '.$content->title);	
	}
}

?>