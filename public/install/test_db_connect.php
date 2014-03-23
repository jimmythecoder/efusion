<?php
	//Tests db connectivity and returns the result
	//Should be called via ajax
	
	if(@mysql_connect($_POST['host'],$_POST['username'],$_POST['password']))
	{
		if(@mysql_select_db($_POST['database']))
			echo 'Connected successfully !';
		else
			echo 'Could not connect to database, retry?';
	}
	else
		echo 'Could not connect to database server, retry?';
?>