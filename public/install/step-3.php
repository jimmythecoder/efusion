<?php
	include 'functions.php';
	
	$site_root_dir = realpath(dirname(__FILE__).'/../../');
	
	session_start();

	if(empty($_SESSION['step_2_complete']))
	{
		header('Location: /install/step-2.php');
		exit;
	}

	$required_fields = array('database_name','database_username','database_password','database_host');

	if(isset($_POST['database_username']))
		$database_username = $_POST['database_username'];
	else if(isset($_SESSION['database_username']))
		$database_username = $_SESSION['database_username'];
	else
		$database_username = '';
			
	if(isset($_POST['database_password']))
		$database_password = $_POST['database_password'];
	else if(isset($_SESSION['database_password']))
		$database_password = $_SESSION['database_password'];
	else
		$database_password = '';
		
	if(isset($_POST['database_name']))
		$database_name = $_POST['database_name'];
	else if(isset($_SESSION['database_name']))
		$database_name = $_SESSION['database_name'];
	else
		$database_name = '';
		
	if(isset($_POST['database_host']))
		$database_host = $_POST['database_host'];
	else if(isset($_SESSION['database_host']))
		$database_host = $_SESSION['database_host'];
	else
		$database_host = 'localhost';
	
	$errors = array();
		
	if(isset($_POST['next_x']))
	{
		foreach($required_fields as $field)
		{
			if(empty($_POST[$field]))
				$errors[$field] = ucfirst(str_replace('_',' ',$field)).' is required';
		}
			
		//Test db connection
		$db_conn = get_database_connection($database_host,$database_username,$database_password,$database_name);	
			
		if(!count($errors))
		{
			//Write database details to ini file
			$database_ini = array();
			$database_ini['production']['adapter'] = 'mysql';
			$database_ini['production']['host'] = $database_host;
			$database_ini['production']['database'] = $database_name;
			$database_ini['production']['username'] = $database_username;
			$database_ini['production']['password'] = $database_password;
			$database_ini['production']['port'] = 3306;
			$database_ini['production']['pconnect'] = false;
			
			$config_dir = $site_root_dir.'/config';
			$database_ini_filename = $config_dir.'/database.ini';
			if(!write_ini_file($database_ini_filename,$database_ini))
				exit('Could not write database configuration file to ' . $database_ini_filename . ' please ensure this file has write permissions or contact your webhost.');
			
			$_SESSION['database_username'] = $database_username;
			$_SESSION['database_password'] = $database_password;
			$_SESSION['database_name'] = $database_name;
			$_SESSION['database_host'] = $database_host;

			//Create database tables and setup data
			if(file_exists('install.sql'))
			{
				$install_sql_file = file_get_contents('install.sql');
				$arr_install_queries = explode(';',$install_sql_file);
				foreach($arr_install_queries as $key => $query)
					($result = sql_query($query, $db_conn)) ? '' : exit('Database install error: ' . mysql_error($db_conn) . 'for : '.$query);
			}
			else
				exit('The file install.sql does not exist in ' . $site_root_dir . '/public/install/ directory, please re-install this application.');
		
			$_SESSION['step_3_complete'] = true;
			
			header('Location: /install/step-4.php');
			exit;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Step 3 of 4 : Database setup - eFusion eCommerce installation</title>

<link rel="stylesheet" type="text/css" href="install.css" media="all" />
<script type="text/javascript" src="../javascripts/lib/jquery.js"></script>

<?php if(count($errors)): ?>
<style type="text/css">
 <?php $css_fields = '';
 
 foreach($errors as $field => $error)
 {
 	$css_fields .=  '#'.$field . ',';
 } 
 
 $css_fields = substr($css_fields,0,strlen($css_fields) - 1);
 ?> 
 <?php echo $css_fields; ?>{
	border: 1px solid red;
}
</style>
<?php endif; ?>

<script type="text/javascript">
<!--
$(document).ready(function(){
	$('#database_name').get(0).focus();
	
	$('#test-db-connection').bind('click',function(){
		$(this).html('Connecting...');
		
		var database_credentials = new Object();
		database_credentials.database = $('#database_name').val();
		database_credentials.username = $('#database_username').val();
		database_credentials.password = $('#database_password').val();
		database_credentials.host = $('#database_host').val();
		
		$.post('/install/test_db_connect.php',database_credentials,function(response){
			
			$('#test-db-connection').html(response);
		});
	});
});
-->
</script>
</head>
<body id="step-1">
	<div id="container">
		<p>Step 3 of 4: <strong>Database Setup</strong></p>
		
		<p>Next we need to know your web hosts database details.</p>
		
		<?php if(count($errors)): ?>
			<ul id="flash-errors">
			<?php foreach($errors as $error): ?>
				<li><?php echo $error; ?></li>
			<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<fieldset><legend>Database information</legend>
		<p class="small">The following database connection details should have been provided to you by your web host. If not, please contact them and
		ask for the following information.</p>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<table class="form" cellpadding="0" cellspacing="0" summary="Database information">
		<tr class="required">
			<td class="label"><label for="database_name">Database name</label></td>
			<td><input class="text" type="text" name="database_name" id="database_name" value="<?php echo htmlentities($database_name); ?>" /><em>*</em>
				<small>Enter the name of the database to use for this store</small></td>
		</tr>
		<tr class="required">
			<td class="label"><label for="database_username">Database username</label></td>
			<td><input class="text" type="text" name="database_username" id="database_username" value="<?php echo htmlentities($database_username); ?>" /><em>*</em>
				<small>Enter the username to connect to the above database</small></td>
		</tr>
		<tr>
			<td class="label"><label for="database_password">Database password</label></td>
			<td><input class="text" type="password" name="database_password" id="database_password" value="<?php echo htmlentities($database_password); ?>" /><em>*</em>
				<small>Enter the password to connect to the above database</small></td>
		</tr>
		<tr>
			<td class="label"><label for="database_host">Database host</label></td>
			<td><input class="text" type="text" name="database_host" id="database_host" value="<?php echo htmlentities($database_host); ?>" />&nbsp;
				<small>* Only change if your database is hosted on an external server, leave as localhost if you don't know</small></td>
		</tr>
		<tr>
			<td class="label">&nbsp;</td>
			<td><button type="button" id="test-db-connection" style="line-height: 130%;">Test database connection</button>&nbsp;
				<small>Click to check for a successfull database connection.</small></td>
		</tr>
		<tr class="last">
			<td style="font-size: 12px;"><a href="/install/step-2.php"><< Back to step 2</a></td>
			<td style="text-align: right;"><input type="image" name="next" src="next.jpg" alt="Proceed to step 4" onmouseover="this.src='next_hover.jpg'" onmouseout="this.src='next.jpg'" /></td>
		</tr>
		</table>
		</form>
		</fieldset>
	</div>
</body>
</html>