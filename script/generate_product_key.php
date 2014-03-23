<?php
	//Generates a random 20 character alpha-numeric string
	
	if(!empty($_SERVER['argv'][1]))
		$number_of_keys_to_generate = (int)$_SERVER['argv'][1];
	else
		$number_of_keys_to_generate = 1;

	if(!empty($_SERVER['argv'][2]))
		$filename = $_SERVER['argv'][2];
		
	$key_file = '';
	for($i=0;$i<$number_of_keys_to_generate;$i++)
		$key_file .= generate_key();
	
	if(empty($filename))
		echo $key_file;
	else
	{
		$fh = fopen($filename,'wb');
		fwrite($fh,$key_file);
		fclose($fh);
	}
	
	function generate_key()
	{
		$unique_id = strtoupper(substr(md5(uniqid(rand(),true)),0,16));
		
		$key = 'EF10-';
		for($i=0;$i<4;$i++)
			$key .= (substr($unique_id,$i*3,4).'-');
			
		$key = rtrim($key,'-');
		
		return $key . "\n";
	}
?>