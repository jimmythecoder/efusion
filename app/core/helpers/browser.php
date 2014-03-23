<?php
/**
 * Browser detection methods
 * 
 * @package efusion
 * @subpackage helpers
 */
class browser
{
	const BROWSERS = 'msie,firefox,safari,webkit,opera,netscape,konqueror,gecko,aol,chrome';
	
	static function get($agent = null) 
	{
		$arr_browsers = explode(',',self::BROWSERS);
		
		// Clean up agent and build regex that matches phrases for known browsers
		// (e.g. "Firefox/2.0" or "MSIE 6.0" (This only matches the major and minor
		// version numbers.  E.g. "2.0.0.6" is parsed as simply "2.0"
		$agent 		= strtolower($agent ? $agent : isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null);
		$pattern 	= '#(?<browser>' . join('|', $arr_browsers) . ')[/ ]+(?<version>[0-9]+(?:\.[0-9]+)?)#';
		
		// Find all phrases (or return empty array if none found)
		if (!preg_match_all($pattern, $agent, $matches)) 
			return false;
		
		// Since some UAs have more than one phrase (e.g Firefox has a Gecko phrase,
		// Opera 7,8 have a MSIE phrase), use the last one found (the right-most one
		// in the UA).  That's usually the most correct.
		$i = count($matches['browser']) - 1;
		
		$browser_match = array(
			'name' 		=> $matches['browser'][$i],
			'version' 	=> $matches['version'][$i],
			'ua' 		=> $agent,
			'is_crawler'=> !in_array($matches['browser'][$i], $arr_browsers)
		);
		
		return $browser_match;
	}
}
?>