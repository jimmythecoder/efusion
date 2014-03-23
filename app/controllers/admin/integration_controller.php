<?php
/**
 * Store product data integration
 * 
 * @package efusion
 * @subpackage controllers
 */
class integration_controller extends admin_controller
{	
	var $valid_file_formats = array('xml','csv');
	
	function integration_controller(&$application)
	{
		parent::admin_controller($application);
		
		$this->breadcrumb[] = array('admin/integration/index' => 'Data integration');	
	}
	
	function index()
	{
	
	}
	
	function import()
	{
		if(isset($this->params['import']))
		{
			$upload_successfull = $this->_is_valid_file_upload($_FILES['import_data']['error']);
			
			$file_format = $this->_get_file_format_from_mime_or_name($_FILES['import_data']['type'],$_FILES['import_data']['name']);
			
			if($upload_successfull !== true)
				$this->flash['error'][] = $upload_successfull;
			else if(!is_uploaded_file($_FILES['import_data']['tmp_name']))
				$this->flash['error'][] = 'Invalid file upload!';
			else if(!in_array($file_format,$this->valid_file_formats))
				$this->flash['error'][] = 'Invalid import file format';
			else
			{
				if($_POST['format'] == 'xml' && $file_format == 'xml')
				{
					$xml_parser =& model::create('xml_parser');
					
					$xml_data = file_get_contents($_FILES['import_data']['tmp_name']);
					
					$obj_node_tree = $xml_parser->parse_xml_string_into_node_tree($xml_data);
					
					$xml_parser->cleanup();
					
					if($_POST['what'] == 'products')
					{		
						$product =& model::create('product');
						$image =& model::create('image');
						
						//If we are importing only 1 product, make sure its an array so we can iterate over it
						if(!is_array($obj_node_tree->products->product))
							$obj_node_tree->products->product = array(0 => $obj_node_tree->products->product);
							
						$number_of_products_to_import = count($obj_node_tree->products->product);	
						$number_of_products_imported = 0;
						
						foreach($obj_node_tree->products->product as $key => $import_product)
						{
							if($product->find($import_product->id))
								$import_action = 'update';
							else
								$import_action = 'insert';
							
							$product->set_field_values_from_object($import_product);
							$product->id = $import_product->id;
							$product->is_active = 1;
		
							if(!empty($import_product->image_url))
							{
								if(!$product->image_id = $image->create_image_from_url($import_product->image_url))
								{
									logger::log_message('Warning: Could not upload image: ' . $import_product->image_url . ' for product ID: ' . $product->id,'import');
									$product->image_id = DEFAULT_IMAGE_ID;
								}
							}
							else if($import_action == 'insert')
								$product->image_id = DEFAULT_IMAGE_ID;
							
							if(!$product->{$import_action}())
							{
								//Rollback transaction
								$image->delete($product->image_id);
								
								logger::log_message('Import error: [' . implode(',',$product->_errors) . '] On product ID: ' . $product->id . '. Record skipped','import'); 
							}
							else
								$number_of_products_imported++;

							$product->clear_field_values();
							$product->clear_model_errors();
						}
						
						if($number_of_products_imported < $number_of_products_to_import)
							$this->flash['error'][] = 'Not all products were imported sucessfully, please check the import log in ' . LOGS_DIR;
						else
							$this->flash['notice'][] = $number_of_products_imported . ' Products imported successfully';
					}
					else if($_POST['what'] == 'categories')
					{
						$category =& model::create('category');
						
						//If we are importing only 1 category, make sure its an array so we can iterate over it
						if(!is_array($obj_node_tree->categories->category))
							$obj_node_tree->categories->category = array(0 => $obj_node_tree->categories->category);
							
						foreach($obj_node_tree->categories->category as $key => $import_category)
						{
							if($category->find($import_category->id))
								$import_action = 'update';
							else
								$import_action = 'insert';
								
							$category->set_field_values_from_object($import_category);
							$category->id = $import_category->id;
							
							if(!$category->{$import_action}())
								$this->flash['error'] = $category->_errors; 
							
							$category->clear_field_values();
						}
						
						$this->flash['notice'][] = 'Categories imported successfully';
					}
				}
				else if($_POST['format'] == 'csv' && $file_format == 'csv')
				{
					$fp = fopen($_FILES['import_data']['tmp_name'], "r");
					
					$csv_header = fgetcsv($fp); 
					$csv_column_names = $this->_get_column_names_from_csv_header($csv_header);
					
					if($_POST['what'] == 'products')
					{			
						$product =& model::create('product');
						$image =& model::create('image');
						
						$number_of_products_to_import = 0;	
						$number_of_products_imported = 0;
						
						while($arr_product_data = fgetcsv($fp))
						{
							$number_of_products_to_import++;
							
							$import_product = array_combine($csv_column_names, $arr_product_data);
							
							if($product->find($import_product['id']))
								$import_action = 'update';
							else
								$import_action = 'insert';		
							
							$product->set_field_values_from_array($import_product);
							$product->id = $import_product['id'];
							$product->is_active = 1;
							
							if(!empty($import_product['image_url']))
							{
								if(!$product->image_id = $image->create_image_from_url($import_product['image_url']))
								{
									logger::log_message('Warning: Could not upload image: ' . $import_product['image_url'] . ' for product ID: ' . $product->id,'import');
									$product->image_id = DEFAULT_IMAGE_ID;
								}
							}
							else if($import_action == 'insert')
								$product->image_id = DEFAULT_IMAGE_ID;
							
							if(!$product->{$import_action}())
							{
								//Rollback transaction
								$image->delete($product->image_id);

								logger::log_message('Import error: [' . implode(',',$product->_errors) . '] On product ID: ' . $product->id . '. Record skipped','import');
							}
							else
								$number_of_products_imported++;
							
							$product->clear_model_errors();
							$product->clear_field_values();
						}

						if($number_of_products_imported < $number_of_products_to_import)
							$this->flash['error'][] = 'Not all products were imported sucessfully, please check the import log in ' . LOGS_DIR;
						else
							$this->flash['notice'][] = $number_of_products_imported . ' Products imported successfully';
					}
					else if($_POST['what'] == 'categories')
					{
						$category =& model::create('category');
						
						while($arr_category_data = fgetcsv($fp))
						{
							$import_category = array_combine($csv_column_names, $arr_category_data);

							if($category->find($import_category['id']))
								$import_action = 'update';
							else
								$import_action = 'insert';
								
							$category->set_field_values_from_array($import_category);
							$category->id = $import_category['id'];
							
							if(!$category->{$import_action}())
								$this->flash['error'] = $category->_errors; 
							
							$category->clear_field_values();
						}	
						
						$this->flash['notice'][] = 'Categories imported successfully';
					}
				}
				else
					$this->flash['error'][] = 'Invalid file format specified';
			}
		}
		
		$this->breadcrumb[] = array('admin/integration/import' => 'Import store data');
	}
		
	function export()
	{
		if(isset($_POST['export']))
		{
			switch($_POST['what'])
			{
				case 'products':
					$this->_export_products($_POST['format']);
				  break;
				case 'categories':
					$this->_export_categories($_POST['format']);
				  break;
				case 'orders':
					$this->_export_orders($_POST['format']);
				  break;
				case 'ordered-products':
					$this->_export_ordered_products($_POST['format']);
				  break;
				case 'customers':
					$this->_export_customers($_POST['format']);
				  break;
			}
		}	

		$start_date = date('l, j M Y', time() - 604800);
		$end_date = date('l, j M Y');
			
		$this->template_data['start_date'] = $start_date;
		$this->template_data['end_date'] = $end_date;
				
		$this->breadcrumb[] = array('admin/integration/export' => 'Export data');	
	}
	
	
	function _export_products($file_format)
	{
		if(!in_array($file_format,$this->valid_file_formats))
			return false;

		if(empty($this->params['export_all']))
		{
			$start_date = date(SQL_DATE_FORMAT, strtotime($_POST['start_date']));
			$end_date = date(SQL_DATE_FORMAT, strtotime($_POST['end_date']));
		
			$date_filter = " AND product.created_at >= '" . $start_date . "' AND  product.created_at <= '" . $end_date ."'";
		}
		else
			$date_filter = '';
			
		$product = model::create('product');
		$product_data = $product->find_all(array('select' 	=> 'product.*,image.filename',
												 'join' 	=> 'LEFT JOIN image ON image.id = product.image_id',
												 'where' 	=> "product.is_active = 1 $date_filter",
												 'escape' 	=> false));
		
		$this->application->smarty->assign('products',$product_data);
		$this->application->smarty->assign('http_location',config::get('http_location'));
		
		$file_contents = $this->fetch_template('_export_products_as_'.$file_format);
		
		header('Content-type: text/'.$file_format);
		header('Content-Disposition: attachment; filename="products.'.$file_format.'"');
		
		echo $file_contents;
		exit;
	}
	
	function _export_orders($file_format)
	{
		if(!in_array($file_format,$this->valid_file_formats))
			return false;

		if(empty($this->params['export_all']))
		{
			$start_date = date(SQL_DATE_FORMAT, strtotime($_POST['start_date']));
			$end_date = date(SQL_DATE_FORMAT, strtotime($_POST['end_date']));
		
			$where_filter = '"order".created_at >= ' . "'" . $start_date . "'" . ' AND  "order".created_at <= ' . "'" . $end_date . "'";
		}
		else
			$where_filter = null;
		
		//TODO: MySQL doesnt seem to support mass table alias prefixes!! Query sux ass, worst query eva!
		
		$order = model::create('order');
		$order_data = $order->find_all(array('select' 	=> '"order".*, 
															account.phone,
															billing_address.company AS billing_address_company, 
															billing_address.first_name AS billing_address_first_name, 
															billing_address.last_name AS billing_address_last_name, 
															billing_address.street AS billing_address_street, 
															billing_address.suburb AS billing_address_suburb, 
															billing_address.city AS billing_address_city,
															billing_address_country.name AS billing_address_country, 
															billing_address.post_code AS billing_address_postcode, 
															delivery_address.company AS delivery_address_company, 
															delivery_address.first_name AS delivery_address_first_name, 
															delivery_address.last_name AS delivery_address_last_name, 
															delivery_address.street AS delivery_address_street, 
															delivery_address.suburb AS delivery_address_suburb, 
															delivery_address.city AS delivery_address_city, 
															delivery_address_country.name AS delivery_address_country, 
															delivery_address.post_code AS delivery_address_postcode',
															
											 'join' 	=> 'INNER JOIN address_book AS billing_address ON billing_address.id = "order".billing_address_id ' .
											 			   'INNER JOIN address_book AS delivery_address ON delivery_address.id = "order".delivery_address_id ' .
											 			   'INNER JOIN "country" AS billing_address_country ON billing_address_country.id = billing_address.country_id ' .
											 			   'INNER JOIN "country" AS delivery_address_country ON delivery_address_country.id = delivery_address.country_id ' .
											 			   'INNER JOIN account ON account.id = "order".account_id',
											 
											 'where' 	=> $where_filter,
											 'escape' 	=> false));
		
		$this->application->smarty->assign('orders',$order_data);
				
		$file_contents = $this->fetch_template('_export_orders_as_'.$file_format);
	
		header('Content-type: text/'.$file_format);
		header('Content-Disposition: attachment; filename="orders.'.$file_format.'"');	
		
		echo $file_contents;
		exit;
	}

	function _export_ordered_products($file_format)
	{
		if(!in_array($file_format,$this->valid_file_formats))
			return false;

		if(empty($this->params['export_all']))
		{
			$start_date = date(SQL_DATE_FORMAT, strtotime($_POST['start_date']));
			$end_date = date(SQL_DATE_FORMAT, strtotime($_POST['end_date']));
		
			$where_filter = '"order".created_at >= ' ."'" . $start_date . "'" . ' AND "order".created_at <= ' . "'" . $end_date . "'";
		}
		else
			$where_filter = null;


		$order_product = model::create('order_product');
		$ordered_products = $order_product->find_all(array('select'	=>  'order_product.*, product.name',
												 			'join' 	=>  'INNER JOIN product ON product.id = order_product.product_id ' .
												 				   		'INNER JOIN "order" ON "order".id = order_product.order_id',
												 			'where' =>  $where_filter,
												 			'escape'=>  false));
		
		$this->application->smarty->assign('ordered_products',$ordered_products);
		
		$file_contents = $this->fetch_template('_export_ordered_products_as_'.$file_format);
		
		header('Content-type: text/'.$file_format);
		header('Content-Disposition: attachment; filename="ordered_products.'.$file_format.'"');
		
		echo $file_contents;
		exit;
	}

	function _export_categories($file_format)
	{
		if(!in_array($file_format,$this->valid_file_formats))
			return false;

		$start_date = date(SQL_DATE_FORMAT, strtotime($_POST['start_date']));
		$end_date = date(SQL_DATE_FORMAT, strtotime($_POST['end_date']));

		$category = model::create('category');
		
		if(empty($this->params['export_all']))
			$filters = array('where' =>  "category.created_at >= '" . $start_date . "' AND  category.created_at <= '" . $end_date ."'", 'escape' =>  false);
		else
			$filters = array();
		
		$categories = $category->find_all($filters);
		
		$this->application->smarty->assign('categories',$categories);
		
		$file_contents = $this->fetch_template('_export_categories_as_'.$file_format);
		
		header('Content-type: text/'.$file_format);
		header('Content-Disposition: attachment; filename="categories.'.$file_format.'"');
		
		echo $file_contents;
		exit;
	}
	
	/**
	 * Checks for PHP thrown upload errors, returns true if upload successfull else false 
	 * and assigns and error
	 * @param int $upload_error_code PHP upload error code from $_FILES['xxx']['error']
	 * @return mixed true on success, else the error string on failure
	 * @link http://nz.php.net/manual/en/features.file-upload.errors.php list of error code constants
	 */
	function _is_valid_file_upload($upload_error_code)
	{
		switch($upload_error_code)
		{
		  case UPLOAD_ERR_OK:
			return true;
			break;
		  case UPLOAD_ERR_INI_SIZE:
			return 'The uploaded file is to large, please use a smaller one';
			break; 
		  case UPLOAD_ERR_FORM_SIZE:
			return 'The uploaded file exceeds the maximum allowed file size, please use a smaller one';
			break; 
		  case UPLOAD_ERR_PARTIAL:
			return 'The uploaded file was only partially uploaded, please try again';
			break; 
		  case UPLOAD_ERR_NO_FILE:
			return 'No file was uploaded';
			break; 
		  case UPLOAD_ERR_NO_TMP_DIR:
			return 'System error: Missing a temporary folder! Please contact server administrator';
			break; 
		  case UPLOAD_ERR_CANT_WRITE:
			return 'System error: Failed to write file to disk';
			break; 
		}

		return 'Unknown upload error!';
	}
	
	/**
	 * Attempts to find what format the file is first by the mime type, else by the file extension
	 * @param string $file_mime mime type associated with the file
	 * @param string $file_name name of the file
	 */
	function _get_file_format_from_mime_or_name($file_mime, $file_name)
	{
		$mime_types = array('text/csv' 					=> 'csv',
							'text/xml' 					=> 'xml',
							'application/vnd.ms-excel' 	=> 'csv');
		
		//If it matches a mime type return the shorthand type (csv/xml)
		if(isset($mime_types[$file_mime]))
			return $mime_types[$file_mime];
			
		$file_extension = array_pop(explode('.',$file_name));
		
		return $file_extension;
	}
	
	/**
	 * Transforms a CSV header into a column name e.g. URL Name to url_name
	 * lowercases the name and replaces spaces with underscores
	 * @param array $csv_header first line of the CSV file
	 * @return array transformed header names as column names
	 */
	function _get_column_names_from_csv_header($csv_header)
	{
		$column_names = array();
		
		foreach($csv_header as $column)
			$column_names[] = str_replace(' ','_',strtolower($column));
			
		return $column_names;
	}
}
?>