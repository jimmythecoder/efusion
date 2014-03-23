<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty history_date_format modifier plugin
 *
 * Type:     modifier
 * Name:     history_date_format
 * Purpose:  format dates into human readable sentances in history e.g. 2 Days ago
 * @param string $date_to_format the date which should be formatted
 * @return string The human readable date
 */
function smarty_modifier_history_date_format($date_to_format)
{
	$timestamp_difference_in_seconds = (time() - strtotime($date_to_format)) + 1; //Add 1 so we never get a 0 result

    if($timestamp_difference_in_seconds > 31536000) 
    	$date_duration = round($timestamp_difference_in_seconds / 31536000,0) . ' year';
	else if($timestamp_difference_in_seconds > 2419200) 
		$date_duration = round($timestamp_difference_in_seconds / 2419200,0) . ' month';
	else if($timestamp_difference_in_seconds > 604800) 
		$date_duration = round($timestamp_difference_in_seconds / 604800,0) . ' week';
	else if($timestamp_difference_in_seconds > 86400) 
		$date_duration = round($timestamp_difference_in_seconds / 86400,0) . ' day';
	else if($timestamp_difference_in_seconds > 3600) 
		$date_duration = round($timestamp_difference_in_seconds / 3600,0) . ' hour';
	else if($timestamp_difference_in_seconds > 60) 
		$date_duration = round($timestamp_difference_in_seconds / 60,0) . ' minute';
	else 
		$date_duration = $timestamp_difference_in_seconds . ' second';
      
	if($date_duration > 1) 
		$date_duration .= 's';

	return ucwords($date_duration) . ' Ago';
}
?>
