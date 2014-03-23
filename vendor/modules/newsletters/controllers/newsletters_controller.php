<?php
/**
 * Newsletters module controller
 * 
 * @package efusion
 * @subpackage controllers
 * @module newsletters
 */
class newsletters_controller extends application_controller
{
	function newsletters_controller(&$application)
	{
		parent::application_controller($application);
		
		$this->breadcrumb[] = array('newsletters/index' => 'Newsletters');
	}
	
	/**
	 * 
	 */
	function index()
	{
		
	}
	
	/**
	 * Subscribe to the sites newsletters page
	 */
	function subscribe()
	{
		$obj_subscriber 		=& model::create('subscriber', null, $this->module_paths['models']);
		$obj_newsletter_list 	=& model::create('newsletter_list', null, $this->module_paths['models']);
		
		if(isset($_POST['subscribe']))
		{
			$obj_subscriber->set_field_values_from_array($this->params['subscriber']);
			
			if($obj_subscriber->save())
			{
				if(!empty($this->params['newsletter_list']))
					$obj_subscriber->subscribe_to_lists($this->params['newsletter_list']);
				else
					$obj_subscriber->subscribe_to_all_lists();
				
				$this->flash['notice'][] = 'You have been subscribed to our newsletters';
				$this->redirect_to('newsletters','subscribed');
			}
			else
				$this->flash['error'] = $obj_subscriber->_errors;
		}
			
		$this->template_data['subscriber'] 	= $obj_subscriber->fields_as_associative_array();
		$this->template_data['lists'] 		= $obj_newsletter_list->find_all(array('where' => 'is_active = 1'));
			
		$this->breadcrumb[] = array('newsletters/subscribe' => 'Subscribe');
	}
	
	/**
	 * User has successufully subscribed
	 */
	function subscribed()
	{
		$this->breadcrumb[] = array('newsletters/subscribed' => 'Subscribed');
	}
	
	/**
	 * Remove oneself from the newsletter mailing list
	 */
	function unsubscribe()
	{
		if(isset($_POST['unsubscribe']))
		{
			$obj_subscriber =& model::create('subscriber', null, $this->module_paths['models']);
			$obj_newsletter_list_subscriber =& model::create('newsletter_list_subscriber', null, $this->module_paths['models']);
			
			if($obj_subscriber->find_by_email($this->params['subscriber']['email']))
			{
				$arr_subscribed_lists = $obj_newsletter_list_subscriber->find_all(array('where' => 'subscriber_id = ' . $obj_subscriber->id));
				
				if(!empty($arr_subscribed_lists))
				{
					$obj_newsletter_list_subscriber->delete_by_subscriber_id($obj_subscriber->id);
					$this->flash['notice'][] = 'You have been unsubscribed from future newsletters';
				}
				else
					$this->flash['error'][] = 'You are not currently subscribed to any of our newsletter lists';
			}
			else
				$this->flash['error'][] = 'Your email address is not subscribed to any of our lists';
		}
		
		$this->breadcrumb[] = array('newsletters/unsubscribe' => 'Unsubscribe');
	}
	
	/**
	 * View a newsletter online in HTML format
	 */
	function view()
	{
		
		$this->breadcrumb[] = array('newsletters/view' => 'View Online');
	}
}
?>