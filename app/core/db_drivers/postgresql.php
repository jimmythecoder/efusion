<?php
define('SQL_DATETIME_FORMAT','Y-m-d H:i:s');
define('SQL_DATE_FORMAT','Y-m-d');
 /**
  * Database driver for PostgreSQL
  * @description Abstracts the database interface for the model classes
  * Usage 
  * 	$db = new db();
  * 	$db->connect($db_connection_data);
  * 	$names = $db->query_as_array("SELECT name FROM test");
  * 	$user_id = $db->get_first_cell("SELECT id FROM test WHERE id = 1");
  * 
  * @package efusion
  * @subpackage core
  */
  class db
  {  	
  	/**
  	 * @var resource PostgreSQL connection link identifier
  	 */
  	var $connection;
  	
  	/**
  	 * @var resource Result resource from a query
  	 */
  	var $result;
  	
  	/**
  	 * Hash of postgresql column type names => simple generic types e.g. int4 => integer
  	 * @var array
  	 */
  	var $_column_types = array(
  							'numeric' 	=> 'decimal',
							'int4' 		=> 'integer',
							'int2' 		=> 'boolean',
							'timestamp' => 'datetime'
						);
  	
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
  		if($db_connection_data['pconnect'])
	    	$this->connection = @pg_pconnect('host='.$db_connection_data['host'].' port='.$db_connection_data['port'].' dbname='.$db_connection_data['database'].' user='.$db_connection_data['username'].' password='.$db_connection_data['password']);
  		else
  			$this->connection = @pg_connect('host='.$db_connection_data['host'].' port='.$db_connection_data['port'].' dbname='.$db_connection_data['database'].' user='.$db_connection_data['username'].' password='.$db_connection_data['password']);
  		
  		if($this->connection)
  			$this->query("SET NAMES 'utf8'");
  		else
  			throw new Exception('DB Connection failed');	  		
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
  	 * Closes PostgreSQL connection
  	 */
  	function destroy()
  	{
		pg_close($this->connection);
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
  					
  		$this->result = @pg_query($this->connection, $query);	
  		
  		//If there was an error
  		if(!$this->result)
  			throw new Exception('PostgreSQL Query error on query: '.$query);
  		
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
  			$result = pg_fetch_array($query_result,null,PGSQL_ASSOC);
  		else
  			$result = pg_fetch_array($this->result,null,PGSQL_ASSOC);
  		
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
  			$result = pg_num_rows($query_result);
  		else
  			$result = pg_num_rows($this->result);
  		
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
	  		
	  		//Free up used memory
	  		pg_free_result($query_result);
  		}
  		else
  			throw new Exception('PostgreSQL Query error on query: '.$query);
  		
  		return $result;
  	}
  	
  	/**
  	 * Retrieves the primary key (auto increment value) for the last query performed
  	 * @param string $table the name of the table to get the autoincrement value for (PostgreSQL Only)
  	 * @return integer Last inserted Id, 0 if none
  	 */
  	function get_last_insert_id($table)
  	{
  		return $this->get_first_cell("select currval('".$table."_id_seq')");
  	}
  
  	/**
  	 * Retrieves the number of rows affected by the previous query operation
  	 * @return integer Affected rows for the previous query
  	 */
  	function get_affected_rows()
  	{
  		return pg_affected_rows($this->connection);
  	}
  
   	/**
  	 * Performs a database query and retrieves the first row/column from the result set
  	 * @param string $query The full query string to perform
  	 * @return Mixed data The data within the first row/col of the result set if found else null
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
		  		$result = pg_fetch_result($query_result,0);	
  			else
  				$result = null;

	  		//Free up used memory
	  		pg_free_result($query_result);
  		}
  		else
  			throw new Exception('PostgreSQL Query error on query: '.$query);
  		
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
  			
	  		//Free up used memory
	  		pg_free_result($query_result);
  		}
  		else
  			throw new Exception('PostgreSQL Query error on query: '.$query);
  		
  		return $result;
  	}
  	
  	/**
  	 * Gets the PostgreSQL datatypes for each column in a given table
  	 * @param string $table The name of the table to fetch the column types from
  	 * @return array associative array of the field => typename
  	 * @todo still need to get size, null and default values and test
  	 */
  	function get_table_column_types($table)
  	{
  		$column_types = array();
  		$escaped_table_name = $this->escape_string($table);
  		
  		$get_table_schema_sql = "SELECT 
									attnum,
									attname, 
									typname, 
									atttypmod-4,
									attnotnull,
									atthasdef,
									adsrc AS def  
								 FROM 
								 	pg_attribute, 
								 	pg_class, 
								 	pg_type, 
 								 	pg_attrdef 		
								 WHERE 
									pg_class.oid = attrelid 
									AND pg_type.oid = atttypid 
									AND attnum > 0 
									AND pg_class.oid = adrelid 
									AND adnum = attnum
   									AND atthasdef = 't' 
								 AND lower(relname) = '".$escaped_table_name."' 
								 UNION
								 SELECT 
									attnum,
									attname, 
									typname, 
									atttypmod-4, 
									attnotnull, 
									atthasdef,
									'' AS def
								 FROM 
									pg_attribute, 
									pg_class, 
									pg_type 
								 WHERE 
									pg_class.oid = attrelid
									AND pg_type.oid = atttypid 
									AND attnum > 0 
									AND atthasdef = 'f' 
									AND lower(relname) = '".$escaped_table_name."'";
						
		$this->query($get_table_schema_sql);
		while($row = $this->get_next_row())
		{
			if($row['attnotnull'] == 'f' || empty($row['attnotnull']))
				$is_not_null = 0;
			else
				$is_not_null = 1;
			
			if($row['atthasdef'] == 't')
				$default = $row['def'];
			else
				$default = null;

			if(isset($this->_column_types[$row['typname']]))
				$column_type = $this->_column_types[$row['typname']];
			else
				$column_type = $row['typname'];
			
			$column_types[$row['attname']] = array('type' => $column_type,'size' => $row['?column?'],'null' => !$is_not_null,'default' => $default);	
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

  		return pg_escape_string($query);
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