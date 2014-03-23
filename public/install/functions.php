<?php 
@ini_set('magic_quotes_runtime', 0);
/**
 * Writes an associative array out to an .ini file
 */
function write_ini_file($path, $assoc_array)
{
   $content = '';
   $sections = '';

   foreach ($assoc_array as $key => $item)
   {
       if (is_array($item))
       {
           $sections .= "[{$key}]\n";
           foreach ($item as $key2 => $item2)
           {
               if (is_numeric($item2) || is_bool($item2))
                   $sections .= $key2 . ' = '.(string)$item2."\n";
               else
                   $sections .= $key2.' = "'.$item2.'"'."\n";
           }     
       }
       else
       {
           if(is_numeric($item) || is_bool($item))
               $content .= $key.' = '.(string)$item ."\n";
           else
               $content .= "{$key} = \"{$item}\"\n";
       }
   }     

   $content .= $sections;

   if (!$handle = fopen($path, 'w'))
   {
       return false;
   }
  
   if (!fwrite($handle, $content))
   {
       return false;
   }
  
   fclose($handle);
   return true;
} 

/**
 * Trys to create a new database connection
 * @params hostname, username, password, databasename
 */
function get_database_connection($db_host, $db_username, $db_password, $db_name)
{
	//Try to connect to database
	$db_conn = @mysql_connect($db_host, $db_username, $db_password) or die('Could not connect to database, please go back to <a href="/install/step-3.php">step 3</a> and update your database information');
	@mysql_select_db($db_name) or die('Could not connect to database, please go back to <a href="/install/step-3.php">step 3</a> and update your database information');	
	
	return $db_conn;
}

/**
 * Performs a raw mysql query
 * @param string $sql sql query string
 * @param resource $db_conn db connection resource
 */
function sql_query($sql, $db_conn)
{
	$result = mysql_query($sql,$db_conn);
	
	return $result;
}

/**
 * Returns a mysql safe argument to use within an sql query
 */
function escape_query_arg($argument,$db_conn)
{
   	//If magic quotes is on, strip the slashes first 
	if(get_magic_quotes_gpc())
		$query = stripslashes($argument);

	if(function_exists('mysql_real_escape_string'))
		return mysql_real_escape_string($argument,$db_conn);
	else
		return mysql_escape_string($argument);	
}

function query_as_array($sql, $db_conn)
{
	$result_rows = array();
	
	$query_result = sql_query($sql, $db_conn);

	while($row = mysql_fetch_array($query_result,MYSQL_ASSOC))
		$result_rows[] = $row;
		
	return $result_rows;
}

function close_db_conn($db_conn)
{
	mysql_close($db_conn);
}
?>