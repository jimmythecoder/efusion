<?php
/**
 * Dynamic MVC source generation scripts
 * Usage: php script/generate.php (model|scaffold|controller) name
 */
 
	$site_root_dir = realpath(dirname(__FILE__).'/../');
	
	switch($_SERVER['argv'][1])
	{
		case 'model':	
			echo 'Generating model '.$_SERVER['argv'][2]."... \n";
			generate_model($_SERVER['argv'][2]);
			echo "Done.\n";
			break;
		case 'scaffold':
			echo 'Generating scaffolding '.$_SERVER['argv'][2]."... \n";
			
			break;
		case 'controller':
			if(!isset($_SERVER['argv'][2]))
				exit('Usage: script/generate.php controller $controller_name [module_name]');
			
			echo 'Generating controller '.$_SERVER['argv'][2]."... \n";
			generate_controller($_SERVER['argv'][2], isset($_SERVER['argv'][3]) ? $_SERVER['argv'][3] : null);
			echo "Done.\n";
			break;
		default:
			echo "Usage: php script/generate.php (model|scaffold|controller) name \n";
	}
	
	/**
	 * Generates an empty model file
	 */
	function generate_model($model_name)
	{
		global $site_root_dir;

		$model_template = <<<EOF
<?php
class $model_name extends model
{
	var \$id;
	
	function validate()
	{
		//Add validation rules here
		
		parent::validate();
	}
}
?>
EOF;
		
		$model_file = fopen($site_root_dir.'/app/models/'.$model_name.'.php','w');
		fwrite($model_file,$model_template);
		fclose($model_file);
	}
	

	/**
	 * Generates an empty controller file
	 */
	function generate_controller($controller_name, $module_name = null)
	{
		global $site_root_dir;

		$controller_template = <<<EOF
<?php
class $controller_name extends application_controller
{

}
?>
EOF;
		
		if($module_name)
		{
			mkdir($site_root_dir.'/app/controllers/'.$module_name);
			echo 'Created dir: '.$site_root_dir.'/app/controllers/'.$module_name." \n";
			
			$controller_file = fopen($site_root_dir.'/app/controllers/'.$module_name.'/'.$controller_name.'_controller.php','w');
			fwrite($controller_file,$controller_template);
			fclose($controller_file);
			
			echo 'Created file: '.$site_root_dir.'/app/controllers/'.$module_name.'/'.$controller_name."_controller.php \n";
			
			mkdir($site_root_dir.'/app/views/'.$module_name.'/'.$controller_name);
			echo 'Created dir: '.$site_root_dir.'/app/views/'.$module_name.'/'.$controller_name." \n";
			
			mkdir($site_root_dir.'/tmp/'.$module_name.'/'.$controller_name);
			echo 'Created dir: '.$site_root_dir.'/tmp/'.$module_name.'/'.$controller_name." \n";
			
			mkdir($site_root_dir.'/tmp/'.$module_name.'/'.$controller_name.'/templates_c');
			echo 'Created dir: '.$site_root_dir.'/tmp/'.$module_name.'/'.$controller_name."/templates_c \n";
			
			mkdir($site_root_dir.'/tmp/'.$module_name.'/'.$controller_name.'/cache');
			echo 'Created dir: '.$site_root_dir.'/tmp/'.$module_name.'/'.$controller_name."/cache \n";
		}
		else
		{
			mkdir($site_root_dir.'/app/controllers/'.$module_name);
			
			$controller_file = fopen($site_root_dir.'/app/controllers/'.$controller_name.'_controller.php','w');
			fwrite($controller_file,$controller_template);
			fclose($controller_file);
			
			echo 'Created file: '.$site_root_dir.'/app/controllers/'.$controller_name."_controller.php \n";
			
			mkdir($site_root_dir.'/app/views/'.$controller_name);
			echo 'Created dir: '.$site_root_dir.'/app/views/'.$controller_name." \n";
			
			mkdir($site_root_dir.'/tmp/'.$controller_name);
			echo 'Created dir: '.$site_root_dir.'/tmp/'.$controller_name." \n";
			
			mkdir($site_root_dir.'/tmp/'.$controller_name.'/templates_c');
			echo 'Created dir: '.$site_root_dir.'/tmp/'.$controller_name."/templates_c \n";
			
			mkdir($site_root_dir.'/tmp/'.$controller_name.'/cache');
			echo 'Created dir: '.$site_root_dir.'/tmp/'.$controller_name."/cache \n";	
		}
	}
?>