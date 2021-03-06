<?php
define('SQL_DATETIME_FORMAT','Y-m-d H:i:s');
define('SQL_DATE_FORMAT','Y-m-d');
 /**
  * Database driver for sqlite
  * Usage 
  * 	$db = new db();
  * 	$db->connect($db_connection_data);
  * 	$arr_names = $db->query_as_array("SELECT name FROM test");
  * 	$user_id = $db->get_first_cell("SELECT id FROM test WHERE id = 1");
  * 
  * @package efusion
  * @subpackage core
  */
  class db
  {  	
  	/**
  	 * @var resource MySQL link identifier
  	 */
  	var $connection;
  	
  	/**
  	 * @var resource Result resource from a query
  	 */
  	var $result;
  	
  	
  	function db()
  	{
  		$this->result 		= null;
		$this->connection 	= null;
  	}

  	/**
  	 * Creates a connection to the database
  	 * @param array $db_connection_data database config array from /config/database.ini
  	 */  	
  	function connect($db_connection_data)
  	{
  		$connection_error = null;
  		
  		if($db_connection_data['pconnect'])
	    	$this->connection = @sqlite_popen($db_connection_data['filename'],0666,$connection_error);
		else
			$this->connection = @sqlite_open($db_connection_data['filename'],0666,$connection_error);
		
  		if($this->connection)
  		{  		
  			//Setup UTF-8 Support
  			$this->query('SET NAMES utf8');
  		}
  		else
  			throw new Exception("DB Connection failed ".$connection_error);	  		
  	}
  	
  	/**
  	 * Checks if there is a currently active connection to the database
  	 * @return boolean true if connected, else false
  	 */
  	function is_connected()
  	{
  		return !empty($this->connection);
  	}
  	
  	/**
  	 * Closes sqlite connection
  	 */
  	function destroy()
  	{
		sqlite_close($this->connection);
		$this->connection = null;
  	}
  	
  	
  	/**
  	 * Performs a database query
  	 * @param string $query The full query string to perform
  	 * @return resource The query result resource
  	 */
  	function query($query)
  	{  
   		if(config::get('core','enable_query_logging'))
  			logger::log_debug($query,'query');
  					
  		$query_error = null;
  		
  		$this->result = @sqlite_query($query,$this->connection,SQLITE_ASSOC,$query_error);	
  		
  		//If there was an error
  		if(!$this->result)
  			throw new Exception('DB Query error on query: '.$query.' <br /> description: '.$query_error);
  		
  		return $this->result;
  	}
  	
  	/**
  	 * Gets the next row from the given query result resource. If not given
  	 * then defaults to using the current objects property result resource
  	 * @param resource $query_result query result
  	 * @return array Single row from the query, null if reached the end
  	 */
  	function get_next_row($query_result = null)
  	{
  		if($query_result)
  			$result = sqlite_fetch_array($query_result,MYSQL_ASSOC);
  		else
  			$result = sqlite_fetch_array($this->result,MYSQL_ASSOC);
  		
  		return $result;
  	}
  
  	/**
  	 * Retrieves the number of rows the query result resource contains
  	 * @param resource $query_result Query result resource to check (optional)
  	 * @return integer Number of rows the query returned
  	 */
  	function query_result_rows($query_result = null)
  	{
  		if($query_result)
  			$result = sqlite_num_rows($query_result);
  		else
  			$result = sqlite_num_rows($this->result);
  		
  		return $result;
  	}
  	
  	
   	/**
  	 * Performs a database query and retrieves its result rows
  	 * @param string $query The full query string to perform
  	 * @return Associative array of the result set
  	 */
  	function query_as_array($query)
  	{
  		$result = array();
  		
  		//Fetch the rows silently (do not display any failure notices)
  		$query_result = $this->query($query);
  		
  		if($query_result)
  		{
  			//Assign rows to associative array
	  		while($row = $this->get_next_row($query_result))
	  			$result[] = $row;
  		}
  		
  		return $result;
  	}
  	
  	/**
  	 * Retrieves the primary key (auto increment value) for the last query performed
  	 * @param string $table the name of the table to get the autoincrement value for (PostgreSQL Only)
  	 * @return integer Last inserted Id, 0 if none
  	 */
  	function get_last_insert_id($table)
  	{
  		return sqlite_last_insert_rowid($this->connection);
  	}
  
  	/**
  	 * Retrieves the number of rows affected by the previous query operation
  	 * @return integer Affected rows for the previous query
  	 */
  	function get_affected_rows()
  	{
  		return sqlite_changes($this->connection);
  	}
  
   	/**
  	 * Performs a database query and retrieves the first row/column from the result set
  	 * @param string $query The full query string to perform
  	 * @return Mixed data The data within the first row/col of the result set if found, else null
  	 */
  	function get_first_cell($query)
  	{
  		$result = array();
  		 
  		//Limit query to only fetching the first row
  		$query = str_replace("LIMIT 1","",$query);
  		$query .= " LIMIT 1"; 
  		 
  		//Fetch the first row silently (do not display any failure notices)
  		$query_result = $this->query($query);
  		
  		if($query_result)
  		{
  			if($this->query_result_rows($query_result) > 0)
		  		$result = mysql_result($query_result,0);	
  			else
  				$result = null;
  		}
  		
  		return $result;
  	}
  
     /**
  	 * Performs a database query and retrieves the first row from the result set
  	 * @param string $query The full query string to perform
  	 * @return Mixed data The data within the first row of the result set
  	 */
  	function get_first_row($query)
  	{
  		$result = array();
  		 
  		//Limit query to only fetching the first row
  		$query = str_replace("LIMIT 1","",$query);
  		$query .= " LIMIT 1"; 
  		
  		//Fetch the first row silently (do not display any failure notices)
  		$query_result = $this->query($query);
  		
  		if($query_result)
  		{
  			if($this->query_result_rows($query_result) > 0)
		  		$result = $this->get_next_row($query_result);
  			else
  				$result = null;
  		}
  		
  		return $result;
  	}
  	
  	/**
  	 * Gets the MySQL datatypes for each column in a given table
  	 * @param string $table The name of the table to fetch the column types from
  	 * @return array associative array of the field => typename
  	 */
  	function get_table_column_types($table)
  	{
  		$column_types = array();
		$table = $this->escape_string($table);
		$this->query('SHOW COLUMNS FROM `'.$table.'`');
		while($row = $this->get_next_row())
		{
			preg_match('/^([a-z]+)/',$row['Type'],$type);
			preg_match('/\(([0-9]+)\)/',$row['Type'],$size);

			//MySQL 5/4 compatibility, version 5 uses YES/NO, version 4 uses 1 or null
			if($row['Null'] == 'NO' || empty($row['Null']))
				$is_not_null = 1;
			else
				$is_not_null = 0;
						
			$column_types[$row['Field']] = array('type' => $type[1], 'size' => isset($size[1]) ? $size[1] : null, 'null' => !$is_not_null, 'default' => $row['Default']);
		}		
		
		return $column_types;
  	}
  	
  	/**
  	 * Escapes a database query string to prevent sql injection and allow quotes
  	 * and special character input, as to not affect the query
  	 * @param string $query SQL query string to escape
  	 * @return string Escaped query string, uses slashes \
  	 */
  	function escape_string($query)
  	{
  	   	//If magic quotes is on, strip the slashes first 
		if(get_magic_quotes_gpc())
			$query = stripslashes($query);

  		return sqlite_escape_string($query);
  	}

  	/**
  	 * Starts a database transaction by calling BEGIN
  	 */
  	function start_transaction()
  	{
  		$this->query('BEGIN');
  	}
  	
  	/**
  	 * Commits/ends a transaction by calling COMMIT
  	 */
  	function commit_transaction()
  	{
  		$this->query('COMMIT');
  	}
  	
  	/**
  	 * Rollback/revert all queries performed after the last start transaction call by calling ROLLBACK
  	 */
  	function rollback_transaction()
  	{
  		$this->query('ROLLBACK');
  	}
};
?>