#!/usr/bin/php
<?php
	ini_set('display_errors',1);
 	ini_set('magic_quotes_runtime', 0);	
 	
	//Load environment configuration
	if(!($ENVIRONMENT = getenv('ENVIRONMENT')))
		$ENVIRONMENT = 'production';
		
	define('SITE_ROOT_DIR',realpath(dirname(__FILE__).'/../'));
	
	require(SITE_ROOT_DIR . '/config/constants.php');
	require(SITE_ROOT_DIR . '/app/core/db_drivers/mysql.php');
	
	$database_config_file = parse_ini_file(SITE_ROOT_DIR . '/config/database.ini',true);
	$database_config = $database_config_file[$ENVIRONMENT];
	
	$schema_version_file = parse_ini_file(SITE_ROOT_DIR . '/db/schema_version.txt',true);
	$current_schema_version = (int)$schema_version_file[$ENVIRONMENT]['schema_version'];

	$db =& new db();
	$db->connect($database_config);

	$migration_files = get_migrations_as_array();
	
	echo "Current schema_version: " . $current_schema_version . "\n";
	
	$new_schema_version = null;
	
	foreach($migration_files as $schema_version => $migration_file)
	{
		if($schema_version > $current_schema_version)
		{
			$migration_file_contents = file_get_contents($migration_file);
			
			$sql_queries = explode(';',$migration_file_contents);
			
			foreach($sql_queries as $query)
			{
				$escaped_query = $db->escape_string(trim($query));
				//echo $escaped_query . "\n";
				if(!empty($escaped_query))
					$db->query($escaped_query);
			}
			
			$new_schema_version = $schema_version;
		}
	}
	
	echo "Latest schema version: " . $schema_version . "\n";
	
	if($new_schema_version)
	{
		//Update schema_version.txt file
		$schema_version_file[$ENVIRONMENT]['schema_version'] = $new_schema_version;
		
		write_ini_file(SITE_ROOT_DIR . '/db/schema_version.txt',$schema_version_file);
		
		echo "Migrated to schema version:  " . $new_schema_version . "\n";
	}
	else
		echo "No migration required, nothing done...\n";
	
	/**
	 * Finds all the migration files in db/migrations dir and puts the migration id's into an array
	 */
	function get_migrations_as_array()
	{
		foreach (glob(SITE_ROOT_DIR . '/db/migrations/*.sql') as $migration_filename)
		{
			$index = (int)basename($migration_filename,'.sql');
			
			$migration_files[$index] = $migration_filename;
		}
		
		ksort($migration_files);
		
		return $migration_files;
	}
	
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
?>