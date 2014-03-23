<?php
/**
 * Store Administrator management
 * 
 * @package efusion
 * @subpackage controllers
 */
class banners_controller extends admin_controller
{	
	function banners_controller(&$application)
	{
		parent::admin_controller($application);
		
		$this->breadcrumb[] = array('admin/banners/index' => 'Banners');	
	}
	
	function index()
	{
		$banner =& model::create('banner');					
		$this->template_data['banners'] = $banner->find_all(array('select' => 'banner.*,image.filename','join' => 'INNER JOIN image ON image.id = banner.image_id'));		
	}
	
	function create()
	{
		$banner =& model::create('banner');
		$image =& model::create('image');
		
		if(isset($this->params['save']))
		{
			$banner->set_field_values_from_array($this->params['banner']);
			$banner->image_id = $image->upload_image('image_id',BANNER_UPLOADS_DIR);
			
			if($banner->validate())
			{
				//Make sure we only have 1 banner active at a time
				if($banner->is_active)
					$banner->execute_sql_query('UPDATE banner SET is_active = 0 WHERE is_active = 1');
					
				if(!count($this->flash['error']) && $banner->save())
				{	
					$this->flash['notice'][] = 'Banner uploaded successfully.';
					$this->redirect_to('admin/banners','index','https');	
				}
				else
					$this->flash['error'] = $banner->_errors;
			}
			else
			{
				$image->delete($image->id);
				$this->flash['error'] = $banner->_errors;
			}
		}
		else if(isset($this->params['cancel']))
			$this->redirect_to('admin/banner','index','https');

		$this->template_data['banner_form'] = $banner->get_fields_for_form(false);
		
		$this->breadcrumb[] = array('admin/banners/create' => 'Upload a new banner');
	}
	
	function edit()
	{
		$banner =& model::create('banner',$this->params['url_params']);
				
		if(isset($this->params['save']))
		{	
			$banner->set_field_values_from_array($this->params['banner']);

			//Upload banner image
			model::include_model('image');
			
			if(image::is_valid_file_upload($_FILES['image_id']['error']))
			{
				$image =& model::create('image');
				$image->delete($banner->image_id,BANNER_UPLOADS_DIR);
				
				if(!($banner->image_id = $image->upload_image('image_id',BANNER_UPLOADS_DIR)))
					$this->flash['error'] = $image->_errors;
			}
				
			//Make sure we only have 1 banner active at a time
			if($banner->is_active)
				$banner->execute_sql_query('UPDATE banner SET is_active = 0 WHERE is_active = 1',array());
								
			if($banner->save())
			{
				$this->flash['notice'][] = 'Banner updated successfully.';
				$this->redirect_to('admin/banners','index','https');		
			}	
			else
				$this->flash['error'] = $banner->_errors;
		}
		else if(isset($this->params['delete']))
		{
			$banner->delete($banner->id);
			
			$this->flash['notice'][] = 'Banner deleted successfully';
			$this->redirect_to('admin/banners','index','https');
		}
		else if(isset($this->params['cancel']))
			$this->redirect_to('admin/banners','index','https');
		
		$this->template_data['banner_form'] = $banner->get_fields_for_form(true);
		
		//Cannot delete if currently active
		$this->template_data['allow_delete'] = !$banner->is_active;
		
		$this->breadcrumb[] = array('admin/banners/edit/'.$banner->id => 'Modify banner');	
	}
	
	function activate()
	{
		$banner =& model::create('banner',$this->params['url_params']);
		
		if(isset($this->params['confirm']))
		{					
			//Set all other banners as inactive
			$banner->execute_sql_query('UPDATE banner SET is_active = 0 WHERE is_active = 1',array());
			
			//Set this banner as active
			$banner->is_active = 1;
			$banner->save();
			
			$this->flash['notice'][] = 'Activated banner successfully';
			$this->redirect_to('admin/banners','index','https');
		}
		else if(isset($this->params['cancel']))
			$this->redirect_to('admin/banners','index','https');
			
		$this->template_data['banner'] = $banner->fields_as_associative_array();
	}
}

?>