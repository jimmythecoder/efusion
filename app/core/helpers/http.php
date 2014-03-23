<?php
/**
 * HTTP helper methods
 * 
 * @package efusion
 * @subpackage helpers
 */
class HTTP
{
	/**
	 * Send a HTTP header
	 * @param int $http_status_code http code number to send
	 */
	function header($http_status_code) 
	{   
		$http_status_codes = array 
   		(
	       100 => "HTTP/1.1 100 Continue",
	       101 => "HTTP/1.1 101 Switching Protocols",
	       200 => "HTTP/1.1 200 OK",
	       201 => "HTTP/1.1 201 Created",
	       202 => "HTTP/1.1 202 Accepted",
	       203 => "HTTP/1.1 203 Non-Authoritative Information",
	       204 => "HTTP/1.1 204 No Content",
	       205 => "HTTP/1.1 205 Reset Content",
	       206 => "HTTP/1.1 206 Partial Content",
	       300 => "HTTP/1.1 300 Multiple Choices",
	       301 => "HTTP/1.1 301 Moved Permanently",
	       302 => "HTTP/1.1 302 Found",
	       303 => "HTTP/1.1 303 See Other",
	       304 => "HTTP/1.1 304 Not Modified",
	       305 => "HTTP/1.1 305 Use Proxy",
	       307 => "HTTP/1.1 307 Temporary Redirect",
	       400 => "HTTP/1.1 400 Bad Request",
	       401 => "HTTP/1.1 401 Unauthorized",
	       402 => "HTTP/1.1 402 Payment Required",
	       403 => "HTTP/1.1 403 Forbidden",
	       404 => "HTTP/1.1 404 Not Found",
	       405 => "HTTP/1.1 405 Method Not Allowed",
	       406 => "HTTP/1.1 406 Not Acceptable",
	       407 => "HTTP/1.1 407 Proxy Authentication Required",
	       408 => "HTTP/1.1 408 Request Time-out",
	       409 => "HTTP/1.1 409 Conflict",
	       410 => "HTTP/1.1 410 Gone",
	       411 => "HTTP/1.1 411 Length Required",
	       412 => "HTTP/1.1 412 Precondition Failed",
	       413 => "HTTP/1.1 413 Request Entity Too Large",
	       414 => "HTTP/1.1 414 Request-URI Too Large",
	       415 => "HTTP/1.1 415 Unsupported Media Type",
	       416 => "HTTP/1.1 416 Requested range not satisfiable",
	       417 => "HTTP/1.1 417 Expectation Failed",
	       500 => "HTTP/1.1 500 Internal Server Error",
	       501 => "HTTP/1.1 501 Not Implemented",
	       502 => "HTTP/1.1 502 Bad Gateway",
	       503 => "HTTP/1.1 503 Service Unavailable",
	       504 => "HTTP/1.1 504 Gateway Time-out"       
   		);
   		
	   	header($http_status_codes[$http_status_code]);
	}
	
	/**
	 * Identical to header method but exits right after issuing the header
	 * Attempts to display a static status html page if exists in public dir
	 * @see HTTP::header
	 */
	function exit_on_header($http_status_code)
	{
		HTTP::header($http_status_code);
		
		$error_display_page = PUBLIC_DIR . '/'.  $http_status_code . '.html';
		
		if(file_exists($error_display_page))
			echo file_get_contents($error_display_page);
			
		exit;
	}
	
	/**
	 * Retrieves the clients ip address, tries 3 methods and returns false if none found
	 * @return string visitors ip address if found, else false
	 */
	function remote_ip_address()
	{			
		if(!empty($_SERVER['REMOTE_ADDR']))
          	$ip_address = $_SERVER['REMOTE_ADDR'];
		else if(!empty($_SERVER['HTTP_CLIENT_IP']))
          	$ip_address = $_SERVER['HTTP_CLIENT_IP'];
     	else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
			$ip_address = array_shift(explode(',',$_SERVER['HTTP_X_FORWARDED_FOR']));
     	else
     		$ip_address = false;
     	
		return $ip_address;
	}
	
	/**
	 * Performs and HTTP post request to the specified URL, requires CURL or openSSL to be installed
	 * @param string $data data to post to the remote server
	 * @param string $url absolute URL of the remote server e.g. ssl://paymentexpress.com/pxaccess.aspx
	 * @return string response from the server if successfull else false on failure
	 */
	function post_request($data, $url)
	{
		//First we check if curl is installed, as thats the preferred method of doing http post requests
		if(function_exists('curl_init'))
		{
			if(!$ch = curl_init($url))
				return false;
			
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, IS_PRODUCTION_ENV);
			curl_setopt($ch, CURLOPT_SSLVERSION, 3);
			
			if(!$http_response = curl_exec($ch))
				exit(curl_error($ch)); 		
				
			curl_close($ch);
		
			return $http_response;
		}
		else if(defined('X509_PURPOSE_SSL_CLIENT')) //Is openSSL installed?
		{
			//If CURL is not installed, we make a standard SSL fopen request (requires openSSL)
			$url_parts_as_array = parse_url($url);
			
			if(empty($url_parts_as_array['port']))
				$url_parts_as_array['port'] = 443;
			
			$http_request = 
				"POST " . $url_parts_as_array['path'] . " HTTP/1.1\r\n" .
				"Host: " . $url_parts_as_array['host'] . "\r\n" .
				"Content-type: application/x-www-form-urlencoded\r\n" .
				"Content-length: " . strlen($data) . "\r\n" .
				"Accept: */*\r\n" .
				"Connection: close\r\n\r\n" .
				$data . "\r\n\r\n";
			
			$error_string = null;
			$error_number = null;
			
			//Attempt to create an SSL connection to payment gateway
			if(!$fp = fsockopen('ssl://' . $url_parts_as_array["host"], $url_parts_as_array["port"], $error_string, $error_number, 30))
			{
				throw new Exception('Failed to connect to remote server! Details: ' . $error_number . $error_string);
				return false;
			}
			
			fwrite($fp,$http_request);
			fflush($fp);
			
			$http_response = '';
			
			//Read back the transaction response
			//NOTE: IIS servers have a bug in which they do not properly send the close connection header which
			//triggers a PHP warning.
			while(!feof($fp))
				$http_response .= @fread($fp, 128);
			
			//Remove headers and just take the XML
			$http_response = trim(strstr($http_response, "\r\n\r\n"));
			
			return $http_response;
		}
		
		return false;			
	}
	
	/**
	 * Fetches data from a given URL which requires HTTP Auth Basic authentication
	 *  - Requires SSL (https URL), and throws an exception on error
	 * @param string $url URL to get the data from
	 * @param string $username username for http auth
	 * @param string $password password for http auth
	 * @return string data returned from the URL
	 */
	function get_data_from_url_with_http_auth($url, $username, $password)
	{
		if(!function_exists('curl_init'))
			throw new Exception('PHP Curl library is not installed, please install it');
			
		if(!$curl = curl_init($url))
			throw new Exception('Could not initiate curl instance');
		
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC ) ; 
		curl_setopt($curl, CURLOPT_USERPWD, "$username:$password"); 
		curl_setopt($curl, CURLOPT_SSLVERSION,3); 
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, IS_PRODUCTION_ENV);
		//curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); 
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
		
		if(($data = curl_exec($curl)) === false)
			throw new Exception("Failed to get service data from $url detail: " . curl_error($curl)); 
	
		return $data;
	}	
	
	/**
	 * Posts data to a given URL which requires HTTP Auth Basic authentication
	 *  - Requires SSL (https URL), and throws an exception on error
	 * @param string $url URL to get the data from
	 * @param array $arr_post_data Data to post to the URL
	 * @param string $username username for http auth
	 * @param string $password password for http auth
	 * @return string data returned from the request
	 */
	function post_data_to_url_with_http_auth($url, $arr_post_data, $username, $password)
	{
		if(!function_exists('curl_init'))
			throw new Exception('PHP Curl library is not installed, please install it');
		
		if(!$curl = curl_init($url))
			throw new Exception('Could not initiate curl instance');
		
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC ) ; 
		curl_setopt($curl, CURLOPT_USERPWD, "$username:$password"); 
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS,$arr_post_data);
		curl_setopt($curl, CURLOPT_SSLVERSION,3); 
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, IS_DEVELOPMENT_ENV ? false : true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); 
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
		
		if(($data = curl_exec($curl)) === false)
			throw new Exception("Failed to get service data from $url detail: " . curl_error($curl)); 
	
		if(curl_getinfo($curl, CURLINFO_HTTP_CODE) != 200)
			throw new Exception('Service Error: ' . $data);
	
		return $data;
	}		
}
?>