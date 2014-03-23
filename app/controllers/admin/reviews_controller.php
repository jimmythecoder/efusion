<?php
/**
 * Product review management
 * 
 * @package efusion
 * @subpackage controllers
 */
class reviews_controller extends admin_controller
{	
	function product()
	{
		$product =& model::create('product');
		
		if($product->find($this->params['url_params']))
		{
			$this->template_data['product'] = $product->field_values_as_array();
			
			$product_review =& model::create('product_review');
			
			$this->template_data['product']['reviews'] = $product_review->find_all_by_product_id($product->id);
		}
		else
			$this->flash['error'][] = 'Product does not exist';

		$this->breadcrumb[] = array('admin/reviews/product/'.$this->params['url_params'] => 'Product reviews');	
	}
	
	function edit()
	{
		$product_review =& model::create('product_review',$this->params['url_params']);
				
		if(isset($this->params['save']))
		{
			$product_review->comment = $this->params['comment'];
			
			if($product_review->save())
			{
				if(isset($this->params['save_by_ajax']))
					exit($product_review->comment);
					
				$this->flash['notice'][] = 'Review updated successfully.';
				$this->redirect_to('admin/reviews/product/' . $product_review->product_id,'index','https');		
			}	
			else
			{
				if(isset($this->params['save_by_ajax']))
					exit('<strong>' . implode('<br />',$product_review->_errors) . '</strong>');
					
				$this->flash['error'] = $product_review->_errors;
			}
		}
		else if(isset($this->params['delete']))
		{
			$product_id = $product_review->product_id;
			$product_review->delete($product_review->id);
			
			$this->flash['notice'][] = 'Review deleted successfully';
			$this->redirect_to('admin/reviews','/product/' . $product_id,'https');
		}
		
		$this->breadcrumb[] = array('admin/reviews/edit/'.$product_review->id => 'Modify product review');	
	}
	
	function delete()
	{
		$product_review =& model::create('product_review',$this->params['url_params']);

		if(isset($this->params['delete']))
		{
			$product_id = $product_review->product_id;
			$product_review->delete($product_review->id);
			
			$this->flash['notice'][] = 'Review deleted successfully';
			$this->redirect_to('admin/reviews','product/' . $product_id,'https');
		}
		else if(isset($this->params['cancel']))
			$this->redirect_to('admin/reviews','product/'.$product_review->product_id,'https');
			
		$this->template_data['review'] = $product_review->fields_as_associative_array();
	}
}

?>