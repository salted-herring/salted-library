<?php
/**
 * @file DataLib
 *
 * All about utilising remote resources
 * */
namespace SaltedHerring;
class RPC {
	
	/**
	 * @param string $endpoint: an url on the remote host
	 * @param array | null $basic_auth: structure: array('username' => 'admin', 'password' => '123')
	 * @param int $connection_timeout: in second. If the remote host does not reponde within the timeout limit, it kills the connection and return false. Default 1 second
	 * @param int $timeout: in second. If the remote resource does not finished transferring within the timeout limit, it kills the connection and return false. Default 5 seconds
         *
	 * @return string | boolean
	 */	
	public static function fetch($endpoint, $basic_auth = null, $connection_timeout = 1, $timeout = 5) {
		$curl	= curl_init();
		curl_setopt($curl, CURLOPT_URL, $endpoint);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($curl, CURLOPT_TIMEOUT, 5);
		
		if (!empty($basic_auth)) {
			curl_setopt($curl, CURLOPT_USERPWD, $basic_auth['username'] . ':' . $basic_auth['password']);
			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		}
		
		$data = curl_exec($curl);
		$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		
		return ($httpcode>=200 && $httpcode<300) ? $data : false;
	}
	
	/**
	 * @param string $endpoint: an url on the remote host
	 * @param array | null $basic_auth: structure: array('username' => 'admin', 'password' => '123')
	 * @paran string $fn: filename. It needs to be the FULL path of the file that are going to be written on the disk. e.g. /var/www/vhosts/foo/httpdocs/assets/foobar.tgz
	 * @param int $connection_timeout: in second. If the remote host does not reponde within the timeout limit, it kills the connection and return false. Default 10 seconds
	 * @param int $timeout: in second. If the remote resource does not finished transferring within the timeout limit, it kills the connection and return false. Default 10 minutes
         *
	 * @return boolean
	 */		
	public static function download($url, $basic_auth = null, $fn, $connection_timeout = 10, $timeout = 600) {
		set_time_limit(0);
		$fp		=	fopen ($fn, 'w');
		$curl	=	curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $connection_timeout);
		curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
		
		if (!empty($basic_auth)) {
			curl_setopt($curl, CURLOPT_USERPWD, $basic_auth['username'] . ':' . $basic_auth['password']);
			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		}
		
		curl_setopt($curl, CURLOPT_FILE, $fp); 
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		
		curl_exec($curl);
		$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		fclose($fp);
		return ($httpcode>=200 && $httpcode<300) ? true : false;
	}
}