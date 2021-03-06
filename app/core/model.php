<?php
/**
 * PHP model class, simulates rails ActiveRecord
 * 
 * @package efusion
 * @subpackage core
 * @abstract This class must be inherited by a table model class
 */
class model
{
	/**
	 * Reference to database abstraction layer
	 * @var object
	 */
	var $_db;
	
	/**
	 * Errors generated by the model from validation method
	 * @var array 
	 */
	var $_errors;
	
	/**
	 * Foriegn key references (key=>value = $model field name => foriegn table name)
	 * @var array 
	 */
	var $_foriegn_keys;
	
	/**
	 * Prefix string for this table name
	 * @var string
	 */
	var $_table_prefix;
	
	/**
	 * A singular array of protected fields
	 * @var array
	 */
	var $_protected_fields;
	
	
	/**
	 * Model constructor, Sets up the table fields
	 * Auto loads record if an id is parsed in
	 * @param int $id record id to load (optional)
	 */
	function model($id = null)
	{
		global $db;
		$this->_db = &$db;
		$this->_errors = array();
		$this->_foriegn_keys = array();
		$this->_protected_fields = array('id');
		$this->_table_prefix = '';

		//Set all fields initially as null
		$this->clear_field_values();
		
		//Sets all the foriegn keys for the table from *_id fields
		$this->set_foreign_keys();
		
		//If an id was passed in, load the record
		if(!is_null($id))
			$this->find($id); 
	}
	
	
	/**
	 * Finds a single record by id
	 * @param int $id primary key id for the row to find
	 * @return boolean true if record found, else false
	 */
	function find($id)
	{
		$id = (int)$id;
		$sql = 'SELECT * FROM "'.$this->get_table_name().'" WHERE id = '.$id.' LIMIT 1';
		$record = $this->_db->get_first_row($sql);
		
		return $this->set_attributes_from_record($record);
	}

	
	/**
	 * Finds a single record by a unique field
	 * @param string $field_name name of the column to search through
	 * @param mixed $field_value the value of the field to search for
	 * @return boolean true if record found, else false
	 */
	function find_by_field($field_name,$field_value)
	{
		$field_name = $this->_db->escape_string($field_name);
		$field_value = $this->_db->escape_string($field_value);
		
		$sql = 'SELECT * FROM "'.$this->get_table_name().'" WHERE '.$field_name." = '".$field_value."' LIMIT 1";
		$record = $this->_db->get_first_row($sql);
		
		return $this->set_attributes_from_record($record);
	}


	/**
	 * Finds a single record by an array of fields and values
	 * @param array $fields_and_values associative array of the column => value to match against
	 * @return boolean true if record found, else false
	 */
	function find_by_field_array($fields_and_values)
	{
		$where = '';
		foreach($fields_and_values as $field => $value)
		{
			$field = $this->_db->escape_string($field);
			$value = $this->_db->escape_string($value);
			
			$where .= (($where != '') ? ' AND "' : '"').$field.'"'." = '".$value."'";
		}
		
		$sql = 'SELECT * FROM "'.$this->get_table_name().'" WHERE '.$where.' LIMIT 1';
		$record = $this->_db->get_first_row($sql);
		
		return $this->set_attributes_from_record($record);
	}	

	/**
	 * Finds all records by an array of fields and values
	 * @param array $fields_and_values associative array of the column => value to match against
	 * @return array of records found
	 */
	function find_all_by_field_array($fields_and_values)
	{
		$where = '';
		foreach($fields_and_values as $field => $value)
		{
			$field = $this->_db->escape_string($field);
			$value = $this->_db->escape_string($value);
			
			$where .= (($where != '') ? ' AND "' : '"').$field.'"'." = '".$value."'";
		}
		
		$sql = 'SELECT * FROM "'.$this->get_table_name().'" WHERE '.$where;
		return $this->_db->query_as_array($sql);
	}	
		
	/**
	 * Finds the last record in the table
	 * @return boolean true if a record was found, else false
	 */
	function find_last()
	{
		$sql = 'SELECT * FROM "'.$this->get_table_name().'" ORDER BY id DESC LIMIT 1';
		$record = $this->_db->get_first_row($sql);
		
		return $this->set_attributes_from_record($record);		
	}
	
	
	/**
	 * Finds the first record in the table
	 * @return boolean true if a record was found, else false
	 */
	function find_first()
	{
		$sql = 'SELECT * FROM "'.$this->get_table_name().'" ORDER BY id LIMIT 1';
		$record = $this->_db->get_first_row($sql);
		
		return $this->set_attributes_from_record($record);	
	}


	/**
	 * Finds a single record by a given sql query
	 * @param string $sql raw SQL query to execute which should select all fields to populate model object
	 * @return boolean true if record found, else false
	 */
	function find_by_sql($sql)
	{
		$record = $this->_db->get_first_row($sql);
		
		return $this->set_attributes_from_record($record);
	}

	/**
	 * Executes a raw SQL query replacing and escaping query parameters
	 * @param string $sql The full query string to perform with ? character as an identifier for each variable replacement
	 * @param array $query_replacements singular array of query replacement variables (optional)
  	 * @return resource The query result resource
	 */
	function execute_sql_query($sql, $query_replacements = array())
	{
		$sql_parts = explode('?',$sql);
			
		$parsed_sql = '';
		
		foreach($sql_parts as $replacement_index => $query_chunk)
		{
			if(isset($query_replacements[$replacement_index]))
				$replacement = $this->_db->escape_string($query_replacements[$replacement_index]);
			else
				$replacement = null;
			
			$quotable = (ctype_digit($replacement) || is_null($replacement) || $replacement == 'NULL') ? false : true;
			
			$parsed_sql .= $query_chunk . (($quotable) ? "'" : '') .$replacement . (($quotable) ? "'" : '');
		}
		
		return $this->_db->query($parsed_sql);
	}

	/**
	 * Check if a field exists and returns id of record if exists, else null
	 * @param string $field_name name of the column to search through
	 * @param mixed $field_value the value of the field to search for
	 * @return int record id if column with value actually exists, else null
	 */
	function field_exists($field_name,$field_value)
	{
		$field_name = $this->_db->escape_string($field_name);
		$field_value = $this->_db->escape_string($field_value);
		
		$sql = 'SELECT id FROM "'.$this->get_table_name().'" WHERE "'.$field_name.'"'." = '".$field_value."' LIMIT 1";
		$id = $this->_db->get_first_cell($sql);
		
		return $id;
	}
		
		
	/**
	 * Finds all records filtered by the given conditions
	 * @param array $conditions key => value pair of (where,order,limit)
	 * @return array ORM rows
	 */
	function find_all($conditions = array())
	{
		if(!empty($conditions['escape']))
		{
			foreach($conditions as $key => $value)
				$conditions[$key] = $this->_db->escape_string($value);
		}
		
		$sql = 'SELECT '.(!empty($conditions['select']) ? $conditions['select'] : '*').' FROM "'.$this->get_table_name().'" '.(!empty($conditions['join']) ? ' '.$conditions['join'] : '').(!empty($conditions['where']) ? ' WHERE '.$conditions['where'] : '').(!empty($conditions['group']) ? ' GROUP BY '.$conditions['group'] : '').(!empty($conditions['order']) ? ' ORDER BY '.$conditions['order'] : '').(!empty($conditions['limit']) ? ' LIMIT '.$conditions['limit'] : '');
		$result_resource = $this->_db->query($sql);
		
		if($this->_db->query_result_rows($result_resource) > 0)
		{
			$result = array();
			while($row = $this->_db->get_next_row($result_resource))
				$result[$row['id']] = $row;
		}
		else
			$result = null;
		
		return $result;
	}

	/**
	 * Finds all records based on an SQL query which should select all columns of the model in question. No query escaping will take place
	 * @param string $sql The query to run
	 * @return array ORM rows
	 */
	public function find_all_by_sql($sql)
	{
		return $this->_db->query_as_array($sql);
	}

	/**
	 * Finds all records filtered by the given conditions paginated
	 * @param array $conditions key => value pair of (select,join,where,order,limit)
	 * @param int $records_per_page how many records to retrieve per page
	 * @param int $current_page_index active page number
	 * @return array ORM rows and paginated information (total records found, total number of pages available)
	 */
	function find_all_paged($conditions = array(), $records_per_page, $current_page_index)
	{
		if(!isset($conditions['escape']) || $conditions['escape'] == true)
		{
			foreach($conditions as $key => $value)
				$conditions[$key] = $this->_db->escape_string($value);
		}
		
		//Perform a record count
		$sql = 'SELECT count("'.$this->get_table_name().'"."id") FROM "'.$this->get_table_name().'" '.(!empty($conditions['join']) ? ' '.$conditions['join'] : '').(!empty($conditions['where']) ? ' WHERE '.$conditions['where'] : '').(!empty($conditions['group']) ? ' GROUP BY '.$conditions['group'] : '');
		$total_records_found = $this->_db->get_first_cell($sql);
		
		//Calculate SQL limits and pagination information
		$total_pages_available = ceil($total_records_found / $records_per_page);
		$low_record_limit = ($current_page_index - 1) * $records_per_page;
		
		$sql = 'SELECT '.(!empty($conditions['select']) ? $conditions['select'] : '*').' FROM "'.$this->get_table_name().'" '.(!empty($conditions['join']) ? $conditions['join'] : '').(!empty($conditions['where']) ? ' WHERE '.$conditions['where'] : '').(!empty($conditions['group']) ? ' GROUP BY '.$conditions['group'] : '').(!empty($conditions['order']) ? ' ORDER BY '.$conditions['order'] : '').' LIMIT ' . $records_per_page . ' OFFSET ' . $low_record_limit;
		$result_resource = $this->_db->query($sql);
		
		if($this->_db->query_result_rows($result_resource) > 0)
		{
			$result = array();
			while($row = $this->_db->get_next_row($result_resource))
				$result['records'][] = $row;
				
			$result['total_pages_available'] = $total_pages_available;
			$result['total_records_found'] = $total_records_found;
			$result['records_per_page'] = $records_per_page;
			$result['current_page_index'] = $current_page_index;
		}
		else
			$result = null;
		
		return $result;
	}
	
	
	/**
	 * Returns an array of class field names
	 * @return array field names in the table
	 */
	function field_names_as_array()
	{
		$fields_and_values 	= get_object_vars($this);
		$field_names 		= array();
		
		foreach($fields_and_values as $field => $value)
		{
			if($field[0] != '_' && !isset($this->_foriegn_keys[$field.'_id']))
				$field_names[] = $field;
		}	
		
		return $field_names;
	}
	
	
	/**
	 * Returns query safe table values as an array
	 * @return array associative array of field => value pairs for the table  
	 * generated from the current model object fields and values
	 */
	function field_values_as_array()
	{
		$fields = $this->field_names_as_array();
		$values = array();
		
		foreach($fields as $field)
		{
			if(is_null($this->$field))
				$value = 'DEFAULT';
			else if(isset($this->_foriegn_keys[$field.'_id']))
				$value = $this->$field;
			else
				$value = "'".$this->_db->escape_string($this->$field)."'";
					
			$values[$field] = $value;				
		}	

		return $values;		
	}


	/**
	 * Sets the object fields from an associative array
	 * @param array $field_values associative array of column => value pairs
	 */
	function set_field_values_from_array($field_values)
	{
		$field_names = $this->field_names_as_array();
		$field_types = $this->field_types_as_array();
		
		foreach($field_names as $field)
		{	
			//Not allowed to set the id field 
			if(!in_array($field,$this->_protected_fields))
			{
				//Checkbox hack
				if($field_types[$field]['type'] == 'tinyint' || $field_types[$field]['type'] == 'boolean')
					$this->$field = !empty($field_values[$field]) ? 1 : 0;
				else if(isset($field_values[$field]) && ($field_types[$field]['type'] == 'decimal' || $field_types[$field]['type'] == 'integer'))
					$this->$field = preg_replace('/[^0-9\.\-]/','',trim($field_values[$field]));
				else if(isset($field_values[$field]))
					$this->$field = trim($field_values[$field]);	
			}	
		}		
	}
	
	/**
	 * Sets the object fields from an object which has the same properties as the model
	 * @param object $object object map of the model
	 */
	function set_field_values_from_object($object)
	{
		$field_names = $this->field_names_as_array();
		
		foreach($field_names as $field)
		{	
			if(!in_array($field,$this->_protected_fields) && isset($object->$field))
				$this->$field = trim($object->$field);	
		}		
	}
		
	/**
	 * Returns a key-value pair array of the models fields-values
	 * @return array associative array field => value pairs
	 */
	function fields_as_associative_array()
	{
		$field_names = $this->field_names_as_array();
		$values = array();
		
		foreach($field_names as $field)
			$values[$field] = is_object($this->$field) ? $this->$field->fields_as_associative_array() : $this->$field;
		
		return $values;		
	}
	
	/**
	 * Alias method for fields_as_associative_array
	 */
	public function to_array()
	{
		return $this->fields_as_associative_array();
	}
	
	
	/**
	 * Retrives a field list of type-value for generating a form
	 * @return array field => (value,table,type) 
	 * @see fields_as_associative_array()
	 */
	function get_fields_for_form($arr_config = array())
	{
		$fields = $this->fields_as_associative_array();
		$field_types = $this->field_types_as_array();
		$result = array();
	
		foreach($fields as $field => $value)
		{
			$is_foreign_key 	= isset($this->_foriegn_keys[$field]);
			$is_field_ignored 	= isset($arr_config['ignore_fields']) && in_array($field,$arr_config['ignore_fields']);
			$is_field_shown 	= empty($arr_config['show_fields']) || in_array($field,$arr_config['show_fields']);
			
			if(!$is_foreign_key && !$is_field_ignored && $is_field_shown)
				$result[$field] = array_merge($field_types[$field], array('table' => $this->get_table_name(), 'value' => !is_null($value) ? $value : $field_types[$field]['default']));	
		}
		
		//Hide the ID field for every table
		$result['id']['type'] = 'hidden';
		
		return $result;
	}
	
	
	/**
	 * Sets a field as a foriegn key relationship
	 * @param string $field fieldname to set as a foriegn key field
	 * @param string $foriegn_table name of table this foriegn key links to
	 */
	function set_foreign_key($field,$foriegn_table)
	{
		$this->_foriegn_keys[$field] = $foriegn_table;
	}
	
	function set_foreign_keys()
	{
		$field_names = $this->field_names_as_array();
		$matches = array();
		
		foreach($field_names as $field)
		{
			if(preg_match('/^([a-z_0-9]+)_id$/',$field, $matches))
				$this->set_foreign_key($field,$matches[1]);
		}
	}

	/**
	 * Sets the models protected fields (cannot be set from array or object methods)
	 * @param array $fields singular array of fields to protect
	 */
	function set_protected_fields($fields)
	{
		$this->_protected_fields = array_merge($this->_protected_fields, $fields);
	}

    /**
     * Creates a sub array of a single foriegn key
     * @param string $field column name to find as forign key
     * @return boolean true if found, else false
     */
	function find_foreign_key($field)
    {
    	//Create instance of a foriegn key model
    	if(empty($this->_foriegn_keys[$field]))
    		return false;
    
		//Create instance of the foreign table, e.g. $model = model::create('category',$this->category_id)
		$table_name = $this->_foriegn_keys[$field];
		
		//Check that we have not already loaded the foriegn key
		if(array_key_exists($table_name,get_object_vars($this)))
			return true;
			
		if(!$model = model::create($table_name))
			return false;
		
		if(!$model->find($this->$field))
			return false;
		
		//Strip off the _id suffix from foriegn key field name and use that
		if(!preg_match('/^([a-z_0-9]+)_id$/',$field, $matches))
  			return false;
  		
		$field_name_without_id_suffix = $matches[1];
		
  		$this->$field_name_without_id_suffix = $model;
  		
  		return true;
	}
	
	
    /**
     * Creates intances of all foriegn key tables 
     */
	function find_all_foreign_keys()
    {
		foreach($this->_foriegn_keys as $field => $table)
	  		$this->find_foreign_key($field);   
	}


	/**
	 * Retrieves the column type names for each field 
	 */
	function field_types_as_array()
	{	
		return $this->_db->get_table_column_types($this->get_table_name());
	}
	
	
	/**
	 * Sets the models attributes from a table record
	 * @param array $record db record array (field => value)
	 * @return boolean true if attributes set, else false
	 */
	function set_attributes_from_record($record)
	{
		$field_names = $this->field_names_as_array();
		
		if($record)
		{
			foreach($field_names as $field)
				$this->$field = isset($record[$field]) ? $record[$field] : null;
			
			return true;
		}
		else
			return null;	
	}
	
	/**
	 * Returns the real (prefixed) version of the table name
	 */
	function get_table_name()
	{
		return $this->_table_prefix . get_class($this);
	}
	
	/**
	 * Performs a row count on the table filtered by conditions
	 * @param array $conditions associative array of conditions to filter the count query by (where, limit)
	 * @return int Number of rows found
	 */
	function count($conditions)
	{
		if(!isset($conditions['escape']) || $conditions['escape'] == true)
		{
			foreach($conditions as $key => $value)
				$conditions[$key] = $this->_db->escape_string($value);
		}
		
		$sql = 'SELECT count("id") FROM "'.$this->get_table_name().'"'.(isset($conditions['where']) ? ' WHERE '.$conditions['where'] : '').(isset($conditions['limit']) ? ' LIMIT '.$conditions['limit'] : '');
		return $this->_db->get_first_cell($sql);	
	}
    
    
	/**
	 * Validates fields in the model, this function should be overwritten
	 * @return boolean true if data in model is valid for an insert/update else false
	 */
	function validate()
	{
		//If model produced any errors
		if(count($this->_errors) > 0)
			return false;
		else
			return true;
	}
	
	/**
	 * Validates the field has been set
	 * @param string $field name of field to valilate against
	 * @param string $message Message to generate on failure (optional)
	 * @return boolean true if field is not null, else false
	 */
	function validates_presence_of($field, $message = null)
	{
		$result = false;
		if(is_null($this->$field) || $this->$field === '')
			$this->_errors[] = is_null($message) ? ($field.' is required') : $message;		
		else
			$result = true;
			
		return $result;
	}
	
	/**
	 * Validates the field is unique
	 * @param string/array $field name(s) of fields to valilate against
	 * @param string $message Message to generate on failure (optional)
	 * @return boolean true if this field is unique, else false
	 */
	function validates_uniqueness_of($field, $message = null)
	{
		$result = false;
		
		if(gettype($field) == 'array')
		{
			$validate_model = model::create($this->get_table_name());
			$validate_array = array();
			
			foreach($field as $property)
				$validate_array[$property] = $this->$property;
			
			if($validate_model->find_by_field_array($validate_array))
				$id = $validate_model->id;
			else
				$id = null;
			
			//Convert back to string for error message display
			$field = implode(' ',$field);
		}
		else
			$id = $this->field_exists($field,$this->$field);
		
		if(!is_null($id) && $id != $this->id)
			$this->_errors[] = is_null($message) ? ($this->get_table_name().' '.$field.' is not unique') : $message;	
		else
			$result = true;
			
		return $result;		
	}
	
	
	/**
	 * Validates a field is of numeric type (e.g. -123, 0.123)
	 * @param string $field name of field to valilate against
	 * @param string $message Message to generate on failure (optional)
	 * @return boolean true if field is of numeric type, else false
	 */
	function validates_numericality_of($field, $message = null)
	{
		$result = false;
		if(!is_numeric($this->$field))
			$this->_errors[] = is_null($message) ? ($field.' is not numeric') : $message;		
		else
			$result = true;
			
		return $result;
	}
	
	/**
	 * Validates a string field is within the given length parameters
	 * @param string $field name of field to valilate against
	 * @param int $min_length Minimum length of the string
	 * @param int $max_length Maximum length of the string (optional)
	 * @param string $message Message to generate on failure (optional)
	 * @return boolean true on success, else false
	 */
	function validates_length_of($field, $min_length, $max_length = null, $message = null)
	{
		$string_length_of_field = strlen($this->$field);
		
		if(($string_length_of_field >= $min_length) && (is_null($max_length) || $string_length_of_field <= $max_length))
			return true;
			
		$this->_errors[] = is_null($message) ? ($field.' is not a valid length') : $message;
		return false;
	}
	
	
	/**
	 * validates a field with a perl regular expression
	 * @param string $field name of field to valilate against
	 * @param string $regex perl regular expression to match field against
	 * @param string $message Message to generate on failure (optional)
	 * @return boolean true if regular expression was matched, else false
	 */
	function validates_regular_expression_of($field,$regex,$message)
	{
		$result = false;
		if(!preg_match($regex,$this->$field))
			$this->_errors[] = $message;		
		else
			$result = true;
			
		return $result;	
	}


	/**
	 * Validates that a foriegn key field points to a valid record in another table
	 * @param string $field name of field to valilate against
	 * @param string $message Message to generate on failure (optional)
	 * @return boolean true if foriegn key is valid, else false
	 */
	function validates_foriegnkey_exists($field, $message = null, $path = null)
	{
		if(isset($this->_foriegn_keys[$field]))
		{
			$foriegn_table 			= $this->_foriegn_keys[$field];
			$foriegn_table_model 	= model::create($foriegn_table, null, $path);
			
			if($foriegn_table_model->find($this->$field))
				return true;
		}
		
		$this->_errors[] = is_null($message) ? ($field.' foriegn key is not valid') : $message;		
			
		return false;
	}	
	
	/**
	 * Clears and resets the models errors array
	 */
	function clear_model_errors()
	{
		$this->_errors = array();
	}
	
	/**
	 * Callback before save method executes
	 */
	function before_save(){}
	
	/**
	 * Callback after validation is successfull
	 */
	function before_save_after_validate(){}
	
	/**
	 * Callback after a successfull save
	 */
	function after_save(){}
	
	function after_update(){}
	
	function after_insert(){}

	/**
	 * Callback before an update is performed
	 */
	function before_update(){}
	
	/**
	 * Callback before an insert is performed
	 */
	function before_insert(){}
		
	/**
	 * Callback before a delete
	 */
	function before_delete(){}
	
			
	/**
	 * Validates and creates or updates a record if already exists
	 * @return boolean true if save successfull, else false
	 */
	function save()
	{
		if($this->is_new())
			return $this->insert();
		else
			return $this->update();
	}
	
	public function is_new()
	{
		return is_null($this->id);
	}
	
	/**
	 * Validates the data and updates a current record in the database
	 * @return boolean true if successfull else false
	 * @see validate method
	 */	
	function update()
	{
		$this->before_update();
		$this->before_save();
		
		if($this->validate())
		{
			$this->before_save_after_validate();
			
			$values = $this->field_values_as_array();
				
			//Update table
			$set_query = '';
			foreach($values as $field => $value)
				$set_query .= (($set_query != '') ? ', "' : '"').$field.'" = '.$value;
					
			$sql = 'UPDATE "'.$this->get_table_name().'" SET '.$set_query .' WHERE id = '.$this->id;
			$this->_db->query($sql);
			
			$this->after_save();
			$this->after_update();	
			
			return true;
		}
		else
			return false;
	}
	
	/**
	 * Validates the data and inserts a new record into the database
	 * @return boolean true if successfull else false
	 * @see validate method
	 */
	function insert()
	{
		$this->before_insert();
		$this->before_save();
		
		if($this->validate())
		{
			$this->before_save_after_validate();
			
			$fields = $this->field_names_as_array();
			$values = $this->field_values_as_array();
				
			//Create a new record
			$sql = 'INSERT INTO "'.$this->get_table_name().'" ("'.implode('","',$fields).'") VALUES('.implode(',',$values).')';
			$this->_db->query($sql);
			$this->id = $this->_db->get_last_insert_id($this->get_table_name());	
			
			$this->after_save();	
			$this->after_insert();
			
			return true;
		}
		else
			return false;
	}
	
	/**
	 * Deletes a single record, resets the models properties back to null
	 * @param int $id primary key row id to delete
	 */
	function delete($id)
	{
		$this->before_delete();
		
		$id = (int)$id;
		$sql = 'DELETE FROM "'.$this->get_table_name().'" WHERE "id" = '.$id;
		$this->_db->query($sql);
		
		//Set all fields back to null if we deleted this model
		if($this->id == $id)
			$this->clear_field_values();
		
		return true;
	}
	
	function start_transaction()
	{
		$this->_db->start_transaction();
	}

	function commit_transaction()
	{
		$this->_db->commit_transaction();
	}
	
	function rollback_transaction()
	{
		$this->_db->rollback_transaction();
	}
	
	/**
	 * Resets all attributes to null
	 */
	function clear_field_values()
	{
		$fields = $this->field_names_as_array();
		foreach($fields as $key)
			$this->$key = null;		
			
		$this->clear_model_errors();
	} 	
	
	/**
	 * Static method which includes, creates an instance of and returns a reference to a model
	 * @param string $model_name the name of the model to create
	 * @param int $id The id to find in the model (optional)
	 * @param string $path absolute file path to the model dir (optional)
	 * @return object reference to the model object
	 */
	function &create($model_name, $id = null, $path = null)
	{
		if(class_exists($model_name) || model::include_model($model_name, $path))
		{
			if(class_exists($model_name))
			{
				$obj_model = new $model_name($id);	
				return $obj_model;
			}
			else
				throw new Exception('Model class "'.$model_name.'" does not exist.');
		}
		else
			throw new Exception('Model file ' . $model_name . ' does not exist.');
			
		return false;
	}
	
	/**
	 * Alias method for create, interopability with ActiveRecord
	 */
	public function &load($model, $path = null)
	{
		return model::create($model, null, $path);
	}
	
	/**
	 * Includes a model file from the models directory
	 * @param string $model_name model filename only
	 * @param string $path absolute file path to the model dir (optional)
	 * @return boolean true if model included successfully, else false
	 */
	function include_model($model_name, $path = null)
	{
		if($path)
			$model_path_and_filename = $path . $model_name . '.php';
		else
			$model_path_and_filename = MODELS_DIR . '/' . $model_name . '.php';
		
		if(file_exists($model_path_and_filename))
			include_once($model_path_and_filename);
		else
		{
			//Scan module directories
			$arr_module_config = config::get('modules');
			foreach($arr_module_config as $module_name => $config)
			{
				$model_path_and_filename = MODULES_DIR . '/' . $module_name . '/' . $config['models'] . $model_name . '.php';

				if(file_exists($model_path_and_filename))
				{
					include_once($model_path_and_filename);
					return true;
				}
			}
			
			return false;
		}

		return true;	
	}
	
	/**
	 * Overload find_by_* methods so we can call any method like
	 * find_by_email_and_password('abc@example.com','123') which will call find_by_field_array
	 * @param string $method method name which was called on the model
	 * @param string $arguments array of arguments passed to the function
	 * @return boolean true if record found, else false
	 */
	function __call($method, $arguments)
	{
		//We overload any find_by_* method
		if(strpos($method,'find_by_') === 0)
		{
			//Get the parameters to find by e.g. find_by_email_and_password we extract email, password
			$find_by_fields = explode('_and_',substr($method,strlen('find_by_')));
			if(count($arguments) == count($find_by_fields))
				return $this->find_by_field_array(array_combine($find_by_fields, $arguments));
			else
				return false;
		}
		else if(strpos($method,'find_all_by_') === 0)
		{
			//Get the parameters to find by e.g. find_by_email_and_password we extract email, password
			$find_by_fields = explode('_and_',substr($method,strlen('find_all_by_')));
			if(count($arguments) == count($find_by_fields))
				return $this->find_all_by_field_array(array_combine($find_by_fields, $arguments));
			else
				return false;
		}
				
		throw new Exception('Method: '.$method.' does not exist');
	}
	
	/**
	 * Overload get so we can have easy access to foreign keys e.g.
	 *   $customer->group->name will look up group on group_id key and return the object
	 * @param mixed $property the missing object property you are trying to access
	 * @return mixed the property value
	 */
	function __get($property)
	{
		//Autoload foriegn keys
		$foriegn_key_column = $property . '_id';
		
		if(!$this->find_foreign_key($foriegn_key_column))
		{
			throw new Exception('Property [' . $property . '] does not exist in model [' . $this->get_table_name() . ']');
			return false;
		}
		
		return $this->$property;
	}
	
	public function get_errors()
	{
		return $this->_errors;
	}
	
	public function get_errors_as_string($seperator = "\n")
	{
		return implode($seperator,$this->_errors);
	}
}
?>