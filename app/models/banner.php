<?php
/**
 * Banner image displayed in the header
 * 
 * @package efusion
 * @subpackage models
 */
class banner extends model
{
	/**
	 * Primary key
	 * @var int
	 */
	var $id;
	
	/**
	 * Name of the banner, used to uniquely identify it
	 * @var string
	 */
	var $name;
	
	/**
	 * Foriegn key to image record that stores the banner image
	 * @var string
	 */
	var $image_id;
	
	/**
	 * If the banner is active, only 1 banner can be active at any time
	 * @var boolean
	 */
	var $is_active;
	
	function validate()
	{
		$this->validates_foriegnkey_exists('image_id', 'Image did not get uploaded correctly, please try again.');
		$this->validates_presence_of('name');
		
		return parent::validate();
	}
	
	function get_fields_for_form($edit_mode = false)
	{
		$form_data = parent::get_fields_for_form();
		
		$form_data['image_id'] = array('table' => 'banner', 'type' => 'file', 'label' => 'Banner Image','null' => true);	
		
		if($edit_mode)
			$form_data['image_id']['null'] = true;
		else
			$form_data['image_id']['null'] = false;
			
		$form_data['is_active']['label'] = 'Activate';
		
		return $form_data;
	}
	
	function after_save()
	{
		cache::clear_cache_groups_from_cache_id('banner');	
	}
	
	function delete($id)
	{
		//Remove the banner image first
		$banner =& model::create('banner');
		
		if($banner->find($id))
		{
			//Delete the banner image
			$image =& model::create('image');
			$image->delete($banner->image_id,BANNER_UPLOADS_DIR);
			
			cache::clear_cache_groups_from_cache_id('banner');	
		}
		
		return parent::delete($id);
	}
}

?>