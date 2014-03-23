<?php
	$site_root_dir = realpath(dirname(__FILE__).'/../../');
	define('SITE_ROOT_DIR',$site_root_dir);
	
	include 'functions.php';
	
	require(SITE_ROOT_DIR . '/config/constants.php');
	
	session_start();

	$required_fields = array('domain');

	if(isset($_POST['subdomain']))
		$subdomain = $_POST['subdomain'];
	else if(isset($_SESSION['subdomain']))
		$subdomain = $_SESSION['subdomain'];
	else
		$subdomain = 'www';
			
	if(isset($_POST['domain']))
		$domain = $_POST['domain'];
	else if(isset($_SESSION['domain']))
		$domain = $_SESSION['domain'];
	else
	{
		$domain_name_parts = explode('.',$_SERVER['HTTP_HOST']);
		if(count($domain_name_parts) > 3)
		{
			array_shift($domain_name_parts);
			$domain = implode('.',$domain_name_parts);
		}
		
		$domain = str_replace('www.','',$domain);
	}
		
	if(isset($_POST['ssl_subdomain']))
		$ssl_subdomain = $_POST['ssl_subdomain'];
	else if(isset($_SESSION['ssl_subdomain']))
		$ssl_subdomain = $_SESSION['ssl_subdomain'];
	else
		$ssl_subdomain = 'secure';
		
	if(isset($_POST['enable_ssl']))
		$enable_ssl = $_POST['enable_ssl'];
	else if(isset($_SESSION['enable_ssl']))
		$enable_ssl = $_SESSION['enable_ssl'];
	else
		$enable_ssl = 0;
	
	$errors = array();
		
	if(isset($_POST['next_x']))
	{
		foreach($required_fields as $field)
		{
			if(empty($_POST[$field]))
				$errors[$field] = ucfirst(str_replace('_',' ',$field)).' is required';
		}
		
		if(!empty($_POST['enable_ssl']) && empty($_POST['ssl_subdomain']))
			$errors[] = 'Please enter your SSL Sub Domain';
			
		if(!count($errors))
		{
			//Write database details to ini file
			$domains_ini = array();
			$domains_ini['production']['domain'] 		= $domain;
			$domains_ini['production']['subdomain'] 	= $subdomain;
			$domains_ini['production']['ssl_subdomain']	= $ssl_subdomain;
			$domains_ini['production']['enable_ssl'] 	= $enable_ssl;
			$domains_ini['production']['port'] 			= 80;
			$domains_ini['production']['ssl_port'] 		= 443;
			
			$domains_ini_filename = CONFIG_DIR . '/domains.ini';
			if(!write_ini_file($domains_ini_filename,$domains_ini))
				exit('Could not write domains configuration file to ' . $domains_ini_filename . ' please ensure this file has write permissions or contact your webhost.');
			
			$_SESSION['subdomain'] 		= $domain_name;
			$_SESSION['domain'] 		= $domain;
			$_SESSION['enable_ssl'] 	= $enable_ssl;
			$_SESSION['ssl_subdomain'] 	= $ssl_domain_name;
			$_SESSION['step_2_complete'] = true;
			
			header('Location: /install/step-3.php');
			exit;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Step 2 of 4 : Domain setup - eFusion eCommerce installation</title>

<link rel="stylesheet" type="text/css" href="install.css" media="all" />
<script type="text/javascript" src="../javascripts/lib/jquery.js"></script>

<script type="text/javascript">
$(document).ready(function(){
	<?php if(!$enable_ssl): ?>
	$('#ssl_subdomain').attr('disabled','disabled');	
	$('#ssl_subdomain').addClass('disabled');	
	<?php endif; ?>
	
	$('#enable_ssl').bind('click',function(){
		if(this.checked)
		{
			$('#ssl_subdomain').removeAttr('disabled');
			$('#ssl_subdomain').removeClass('disabled');
		}
		else
		{
			$('#ssl_subdomain').attr('disabled','disabled');	
			$('#ssl_subdomain').addClass('disabled');		
		}
	});
});
</script>
</head>
<body id="step-1">
	<div id="container">
		<p>Step 2 of 4: <strong>Domain Setup</strong></p>
		
		<p>Next we need to know the domain name you are using for this store.</p>
		
		<?php if(count($errors)): ?>
			<ul id="flash-errors">
			<?php foreach($errors as $error): ?>
				<li><?php echo $error; ?></li>
			<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<fieldset><legend>Domain name information</legend>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<table class="form" cellpadding="0" cellspacing="0" summary="Domain name information">
		<tr class="required">
			<td class="label"><label for="domain">Domain name</label></td>
			<td><input class="text" type="text" name="domain" id="domain" value="<?php echo $domain; ?>" /><em>*</em>
				<small>The domain name you are using with no aliases such as www, e.g. efusion.co.nz</small></td>
		</tr>
		<tr class="required">
			<td class="label"><label for="subdomain">Sub Domain</label></td>
			<td><input class="text" type="text" name="subdomain" id="subdomain" value="<?php echo $subdomain; ?>" /><em>&nbsp;</em>
				<small>The Sub Domain name you wish to run the store under, usually www</small></td>
		</tr>
		<tr>
			<td class="label"><label for="ssl_subdomain">SSL Sub Domain</label></td>
			<td><input type="checkbox" name="enable_ssl" value="1" id="enable_ssl" <?php echo $enable_ssl ? 'checked="checked"' : ''; ?> /> <label for="enable_ssl">I have purchased an SSL certificate</label><br />
				<input class="text" type="text" name="ssl_subdomain" id="ssl_subdomain" value="<?php echo $ssl_subdomain; ?>" />
				<small>Enter the Sub Domain which you have purchased your SSL certificate for</small></td>
		</tr>
		<tr class="last">
			<td><small><em>*</em> Denotes a required field</small><br /><span style="font-size: 12px;"><a href="/install/step-1.php"><< Back to step 1</a></span></td>
			<td style="text-align: right;"><input type="image" name="next" src="next.jpg" alt="Proceed to step 3" onmouseover="this.src='next_hover.jpg'" onmouseout="this.src='next.jpg'" /></td>
		</tr>
		</table>
		</form>
		</fieldset>
	</div>
</body>
</html>