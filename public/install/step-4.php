<?php
	include 'functions.php';
	
	$site_root_dir = realpath(dirname(__FILE__).'/../../');
	define('SITE_ROOT_DIR',$site_root_dir);
	
	require(SITE_ROOT_DIR . '/config/constants.php');
	
	session_start();

	if(empty($_SESSION['step_3_complete']))
	{
		header('Location: /install/step-3.php');
		exit;
	}
	
	$required_fields = array('store_title','store_email','store_city','store_country','password','confirm_password');

	if(isset($_POST['store_title']))
		$store_title = $_POST['store_title'];
	else if(isset($_SESSION['store_title']))
		$store_title = $_SESSION['store_title'];
	else
		$store_title = '';
			
	if(isset($_POST['store_email']))
		$store_email = $_POST['store_email'];
	else if(isset($_SESSION['store_email']))
		$store_email = $_SESSION['store_email'];
	else
		$store_email = 'orders@'.$_SESSION['domain'];
		
	if(isset($_POST['store_phone']))
		$store_phone = $_POST['store_phone'];
	else if(isset($_SESSION['store_phone']))
		$store_phone = $_SESSION['store_phone'];
	else
		$store_phone = '';
		
	if(isset($_POST['store_city']))
		$store_city = $_POST['store_city'];
	else if(isset($_SESSION['store_city']))
		$store_city = $_SESSION['store_city'];
	else
		$store_city = '';

	if(isset($_POST['store_country']))
		$store_country = $_POST['store_country'];
	else if(isset($_SESSION['store_country']))
		$store_country = $_SESSION['store_country'];
	else
		$store_country = 'New Zealand';
		
	if(isset($_POST['password']))
		$password = $_POST['password'];
	else if(isset($_SESSION['password']))
		$password = $_SESSION['password'];
	else
		$password = '';

	if(isset($_POST['confirm_password']))
		$confirm_password = $_POST['confirm_password'];
	else if(isset($_SESSION['confirm_password']))
		$confirm_password = $_SESSION['confirm_password'];
	else
		$confirm_password = '';
		
	$administrator_email = 'administrator@' . $_SESSION['domain'];
	
	$errors = array();
	$db_conn = get_database_connection($_SESSION['database_host'],$_SESSION['database_username'],$_SESSION['database_password'],$_SESSION['database_name']);

	if(isset($_POST['next_x']))
	{
		foreach($required_fields as $field)
		{
			if(empty($_POST[$field]))
				$errors[$field] = ucfirst(str_replace('_',' ',$field)).' is required';
		}
		
		if(!empty($password) && strlen($password) < 6)
			$errors['password'] = 'Please make sure your password has at least 6 characters';

		if(!empty($password) && strcmp($password,$confirm_password) != 0)
			$errors['confirm_password'] = 'Your password and confirm passwords do not match, please enter them again.';
				
		if(!count($errors))
		{		
			if(get_magic_quotes_gpc())
			{
				$store_title 	= stripslashes($store_title,$db_conn);
				$store_email 	= stripslashes($store_email,$db_conn);
				$store_phone 	= stripslashes($store_phone,$db_conn);
				$store_city 	= stripslashes($store_city,$db_conn);
				$store_country 	= stripslashes($store_country,$db_conn);
				$administrator_email = stripslashes($administrator_email);
			}
			
			$hashed_password 		= md5($password);
			
			//Set the production environment config file
			$production_environment_file = ENVIRONMENTS_DIR . '/production.ini';
			
			$config_entries = parse_ini_file($production_environment_file,true);
			
			$config_entries['content']['title'] = $store_title;
			$config_entries['contact']['email'] = $store_email;
			$config_entries['contact']['phone'] = $store_phone;
			$config_entries['shipping']['city'] = $store_city;
			$config_entries['shipping']['country'] = $store_country;
			
			write_ini_file($production_environment_file,$config_entries);
			
			//Create the site administrator account
			sql_query("INSERT INTO `account` (`group_id`,`email`,`password_hash`,`is_active`,`created_at`,`phone`,`is_email_activated`) VALUES(1,'$escaped_administrator_email','$hashed_password',1,NOW(),'000',1)",$db_conn);
			
			$_SESSION['store_title'] = $store_title;
			$_SESSION['store_email'] = $store_email;
			$_SESSION['store_phone'] = $store_phone;
			$_SESSION['store_city'] = $store_city;
			$_SESSION['store_country'] = $store_country;
			$_SESSION['step_4_complete'] = true;
			
			close_db_conn($db_conn);
			header('Location: http://' . $_SESSION['subdomain'] . '.' . $_SESSION['domain'] . '/installation-complete');
			exit;
		}
	}
	
	//Load all countries
	$countries = query_as_array('SELECT * FROM country',$db_conn);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Step 4 of 4 : Store details - eFusion eCommerce installation</title>

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
</head>
<body id="step-1">
	<div id="container">
		<p>Step 4 of 4: <strong>Store details</strong></p>
		
		<p>Finally we need to know information about your store.</p>
		
		<?php if(count($errors)): ?>
			<ul id="flash-errors">
			<?php foreach($errors as $error): ?>
				<li><?php echo $error; ?></li>
			<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<fieldset><legend>Store details</legend>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<table class="form" cellpadding="0" cellspacing="0" summary="Store details">
		<tr class="required">
			<td class="label"><label for="store_title">Store title</label></td>
			<td><input class="text" type="text" name="store_title" id="store_title" value="<?php echo htmlentities($store_title); ?>" /><em>*</em>
				<small>Enter the title to use for this store, usually your company name</small></td>
		</tr>
		<tr class="required">
			<td class="label"><label for="store_email">Store email</label></td>
			<td><input class="text" type="text" name="store_email" id="store_email" value="<?php echo htmlentities($store_email); ?>" /><em>*</em>
				<small>Enter your main email address you wish to use for communication with your customers</small></td>
		</tr>
		<tr>
			<td class="label"><label for="store_phone">Store phone</label></td>
			<td><input class="text" type="text" name="store_phone" id="store_phone" value="<?php echo htmlentities($store_phone); ?>" /><em>&nbsp;</em>
				<small>Enter your company phone number to be displayed on the site</small></td>
		</tr>
		<tr>
			<td class="label"><label for="store_city">Store city</label></td>
			<td><input type="text" class="text" name="store_city" id="store_city" value="<?php echo htmlentities($store_city); ?>" /><em>*</em>
				<small>Enter your city where goods will be delivered from</small></td>
		</tr>
		<tr>
			<td class="label"><label for="store_country">Store country</label></td>
			<td><select class="select" name="store_country" id="store_country">
				<?php foreach($countries as $country): ?>
					<option <?php if($country['name'] == $store_country) echo 'selected="selected"'; ?> value="<?php echo $country['id']; ?>"><?php echo $country['name']; ?></option>
				<?php endforeach; ?>
				</select><em>*</em>
				<small>Select the country where goods will be delivered from</small></td>
		</tr>
		<tr>
			<td class="label"><label for="password">Password</label></td>
			<td><input class="text" type="password" name="password" id="password" value="" /><em>*</em>
				<small>Enter a password of atleast 6 letters and numbers to access your store management facilities.</small></td>
		</tr>
		<tr>
			<td class="label"><label for="confirm_password">Confirm your password</label></td>
			<td><input class="text" type="password" name="confirm_password" id="confirm_password" value="" /><em>*</em>
				<small>Enter your password again to validate, please do not forget or loose this password.</small></td>
		</tr>
		<tr class="last">
			<td style="font-size: 12px;"><a href="/install/step-3.php"><< Back to step 3</a></td>
			<td style="text-align: right;"><input type="image" name="next" src="next.jpg" alt="Proceed to step 3" onmouseover="this.src='next_hover.jpg'" onmouseout="this.src='next.jpg'" /></td>
		</tr>
		</table>
		</form>
		</fieldset>
	</div>
</body>
</html>