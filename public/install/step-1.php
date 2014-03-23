<?php
	include 'functions.php';
	
	$site_root_dir = realpath(dirname(__FILE__).'/../../');
	
	session_start();

	$required_versions = array('PHP' => '4.3.0','GD' => '2.0','MySQL' => '4.1.0');
	$writable_dirs  = array('/tmp/cache',
							'/tmp/cache/catalog',
							'/tmp/cache/home',
							'/tmp/cache/core',
							'/tmp/cache/product',
							'/tmp/templates_c',
							'/logs/production.log',
							'/config/database.ini',
							'/config/domains.ini',
							'/config/environments/production.ini',
							'/public/images/banners',
							'/public/images/products',
							'/public/images/charts',
							'/public/install');
	$output = array();
	$errors = array();
	
	//Check PHP version
	if(version_compare(PHP_VERSION, $required_versions['PHP'], ">="))
		$output['PHP'] = 'Yes ! <small>Installed version: ('.PHP_VERSION.')</small>';
	else
	{
		$output['PHP'] = 'NO, <small>Installed version: ('.PHP_VERSION.')</small>';
		$errors['PHP'] = 'Your PHP version may not be compatable, please contact your web host to confirm and have it upgraded';
	}
	
	//Check GD version
	$gd_info = gd_info();
	$gd_version = ereg_replace('[[:alpha:][:space:]()]+', '', $gd_info['GD Version']);

	if(version_compare($gd_version, $required_versions['GD'], ">="))
		$output['GD'] = 'Yes ! <small>Installed version: ('.$gd_version.')</small>';
	else
	{
		$output['GD'] = 'NO, <small>Installed version: ('.$gd_version.')</small>';
		$errors['GD'] = 'Your PHP GD (Graphics Library) version may not be compatable, please contact your web host to confirm and have it upgraded';
	}	
	
	//Check MySQL version
	$mysql_version_string = shell_exec('mysql -V');
   	preg_match('@[0-9]+\.[0-9]+\.[0-9]+@', $mysql_version_string, $version);
   	$mysql_version = $version[0]; 
   	
	if(version_compare($mysql_version, $required_versions['MySQL'], ">="))
		$output['MySQL'] = 'Yes ! <small>Installed version: ('.$mysql_version.')</small>';
	else
	{
		$output['MySQL'] = 'NO, <small>Installed version: ('.$mysql_version.')</small>';
		$errors['MySQL'] = 'Your MySQL version may not be compatable, please contact your web host to confirm and have it upgraded';
	}	
		
	//Check for correct write permissions 
	$write_errors = array();
	
	foreach($writable_dirs as $dir)
	{
		if(!is_writable($site_root_dir . $dir))
			$write_errors[] = $site_root_dir . $dir;
	}

	if(!count($write_errors))
		$output['WritableDirs'] = 'Yes !';
	else
	{
		$output['WritableDirs'] = 'NO, <small>The following directories and or files are not writable by the web server: ('.implode(',',$write_errors).')</small>';
		$errors['WritableDirs'] = 'The above directories and or files need to have write permissions, please make sure they are writable by the web server or contact your web host to do so.';
	}
			
	//On form submit	
	if(isset($_POST['next_x']))
	{		
		$_SESSION['step_1_complete'] = true;
			
		header('Location: /install/step-2.php');
		exit;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Step 1 of 4 : Checking system requirements - eFusion eCommerce installation</title>

<link rel="stylesheet" type="text/css" href="install.css" media="all" />
<script type="text/javascript" src="../javascripts/lib/jquery.js"></script>

</head>
<body id="step-1">
	<div id="container">
		<p>Step 1 of 4: <strong>Checking system requirements</strong></p>
		
		<p>First we need to check if your web host has the required software to run this store. The requirements
		are listed at the start of your installation manual under Server Requirements.</p>
		
		<ol id="server-requirements">
		<li>PHP 4.3.0 or higher ... <?php echo $output['PHP']; ?></li>
		<li>GD 2.0 or higher ... <?php echo $output['GD']; ?></li>
		<li>MySQL 4.1 or higher ... <?php echo $output['MySQL']; ?></li>
		<li>Write permissions on folders and files ... <?php echo $output['WritableDirs']; ?></li>
		</ol>
		
		<?php if(count($errors)): ?>
			Your current web host setup may not be compatable with eFusion ecommerce, the following problems were found. 
			<ul id="errors">
			<?php foreach($errors as $application => $error): ?>
			<li><?php echo $error; ?></li>
			<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<table class="form" cellpadding="0" cellspacing="0" summary="Server requirements">
		<tr class="last">
			<td>&nbsp;</td>
			<td style="text-align: right;"><input type="image" name="next" src="next.jpg" alt="Proceed to step 2" onmouseover="this.src='next_hover.jpg'" onmouseout="this.src='next.jpg'" /></td>
		</tr>
		</table>
		</form>
	</div>
</body>
</html>