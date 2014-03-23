<?php
/**
 * Math helper methods
 * 
 * @package efusion
 * @subpackage helpers
 */
class math
{
	/**
	 * Rounds a currency value to the nearest 5 cents based on the swedish rounding system
	 * @param decimal $value The currency value to round
	 * @return decimal returns the rounded value to the nearest 5 cents
	 * @see http://en.wikipedia.org/wiki/Swedish_rounding#Rounding_with_5.C2.A2_intervals
	 * @requires bcmath extension for precision floating point handling (bcmod function is used)
	 */
	static function swedish_round($value)
	{
		$arr_rounding_table = array(0,0,0,5,5,5,5,5,10,10); //NZ 5c rounding table 0-9
		
		$currency_value 	= round($value,2);
		
		$whole_number 		= ($currency_value * 100);

		$cents_to_round 	= bcmod($whole_number,10);
		
		$swedish_cents 		= $arr_rounding_table[$cents_to_round];
		
		return (floor($currency_value * 10) / 10) + ($swedish_cents / 100);
	}
	
	static function gigabytes_to_megabytes($gigabytes)
	{
		return $gigabytes * 1024;	
	}
	
	static function bytes_to_kilobytes($bytes)
	{
		return ceil($bytes / 1024);	
	}
}

?>