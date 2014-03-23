<?php
/**
 * Referrer URL model, used for site statistics
 * 
 * @package efusion
 * @subpackage models
 */
class referer extends model
{
	/**
	 * Primary key
	 * @var int
	 */
	var $id;
	
	/**
	 * Domain name URL of the website referrer
	 * @var string
	 */
	var $url;

	/**
	 * Number of times the site has been hit this referer
	 * @var int
	 */
	var $hits;
		
	function validate()
	{
		$this->validates_presence_of('url');
		$this->validates_uniqueness_of('url');
		
		return parent::validate();
	}
	
	/**
	 * Logs a referer URL, increments the hit count by 1 if referer already exists
	 * @param string $referer_url domain name portion only of the referer url e.g. www.google.com
	 * @return int referer id for this URL
	 */
	function log_url($referer_url)
	{
		//Find the url and increment its hit count
		if($this->find_by_field('url',$referer_url))
		{
			$this->hits = $this->hits + 1;
			$this->save();
		}
		else
		{
			//Or create a new referer url record
			$this->url = $referer_url;
			$this->hits = 1;
			$this->save();
		}
		
		return $this->id;
	}
}

?>