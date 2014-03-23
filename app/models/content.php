<?php
/**
 * Site CMS content
 * 
 * @package efusion
 * @subpackage models
 */
class content extends model
{
	/**
	 * Primary key
	 * @var int
	 */
	var $id;
	
	/**
	 * Content title
	 * @var string
	 */
	var $title;
	
	/**
	 * Unique name which will be used as the URL for this content
	 * @var string
	 */
	var $url_name;
	
	/**
	 * The content to go in the title tag of the html
	 * @var string
	 */
	var $page_title;
	
	/**
	 * Comma seperated meta keywords for this content (optional)
	 * @var string
	 */
	var $keywords;
	
	/**
	 * Meta description of this content (optional)
	 * @var string
	 */
	var $description;
	
	/**
	 * Raw content data
	 * @var string
	 */
	var $content;
	
	/**
	 * Flag if the content is required by system
	 * @var boolean
	 */
	var $is_system_content;
	
	
	function content($id = null)
	{
		parent::model($id);
		
		$this->set_protected_fields(array('is_system_content'));	
	}
	
	function validate()
	{
		$this->validates_presence_of('title');
		$this->validates_presence_of('content');
		$this->validates_presence_of('url_name');
		
		if($this->url_name)
			$this->validates_uniqueness_of('url_name');
			
		return parent::validate();
	}
	
	function before_save()
	{
		if(empty($this->url_name))
			$this->url_name = str_replace(' ','-', preg_replace('/[^A-Za-z0-9 ]/','', strtolower($this->title)));	
		else
			$this->url_name = preg_replace('/[^a-z0-9\-]/','-', strtolower($this->url_name));	
	}
	
	function after_save()
	{
		cache::clear_cache_groups_from_cache_id('content');	
	}
	
	function get_fields_for_form()
	{
		$form_data = parent::get_fields_for_form();
	
		$form_data['content']['type'] = 'wysiwyg';
		$form_data['url_name']['null'] = true;
		unset($form_data['is_system_content']);
		
		return $form_data;
	}
	
	function delete($id)
	{
		//Enforce business rule of not deleting system content
		$content =& model::create('content');
		$content->find($id);
		
		if(!$content->is_system_content)
		{
			cache::clear_cache_groups_from_cache_id('content');	
			
			return parent::delete($id);
		}
		else
			return false;
	}
}

?>