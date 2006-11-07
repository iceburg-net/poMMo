<?php

/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2006 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/

/* Collection of useful helper functions/"utilities"
 * NOTE: Should be called statically e.g. PommoHelper::helperFunction($arg1,...);
 */

class PommoHelper {
	
	// deeply strips slashes added by magic quotes. Generally used on $_POST & $_GET.
	function slashStrip($input) {
			if (is_array($input)) {
				foreach ($input as $key => $value) {
					$input[$key] =$this->slashStrip($value);
				}
				return $input;
			} else {
				return stripslashes($input);
			}
		}
		
	
	/**
	 * Parse a config file, return an array containing key: value
	 * 
	 * Grammar of config file is;
	 * [key] = "value"
	 *   or
	 * [key] = i am a value
	 * 
	 * If parser comes across a trimmed line not beginning with [, the line will be ignored.
	 *   this flexible grammar allows for commets and user error (non homogenous syntax)
	 */
	function parseConfig($file) {
		$a = array();
		
		$file_content = file($file);
		if (empty($file_content))
			Pommo::kill('Could not read config file ('.$file.')');
		
		foreach ($file_content as $rawLine) {
			$line = trim($rawLine);
			if (substr($line,0,1) == '[') { // line should be traded as a key:value pair
				$matches = array();
				preg_match('/^\[(\w+)\]\s*=\s*\"?(.[^\"]+)\"?.*$/i',$line,$matches);
				
				// check if a key:value was extracted
				if (!empty($matches[2]))
					// merge key:value onto return array
					$a = array_merge($a, array($matches[1] => $matches[2]));
			}
		}
		return $a;
	}

	// OLD....
	
	
	/**********************
	 * TEXT PROCESSING FUNCTIONS
	 **********************/

	// decode_htmlchars: removes any htmlspecialchars. PHP4/5 Compatible.
	function decode_htmlchars($str, $quote_style = ENT_COMPAT) {
		return strtr($str, array_flip(get_html_translation_table(HTML_SPECIALCHARS, $quote_style)));
	}

	//str2db: Formats input for database insertion. Used on POST/GET data before being inserted to DB.
	function str2db(& $string) {
		//if (get_magic_quotes_gpc())
		//	return decode_htmlchars($string);
		return decode_htmlchars(mysql_real_escape_string($string));
	}

	//db2str: Formats text from DB to be displayed in a browser.
	//     Used mainly for printing values from a database or populating form values.
	function db2str(& $string) {
		if (get_magic_quotes_runtime())
			return htmlspecialchars(stripslashes($string));
		return htmlspecialchars($string);
	}

	//db2mail: Formats text from a DB table to be mailed, or viewed in a non browser.
	function db2mail(& $string) {
		if (get_magic_quotes_runtime())
			return stripslashes($string);
		return $string;
	}

	// used to format text being pulled from and reinserted to a database
	function db2db(& $string) {
		if (get_magic_quotes_runtime())
			return $string;
		return mysql_real_escape_string($string);
	}

	// takes an array of input to be sanitized. Type is str (user input), or db (db output) 
	function & dbSanitize(& $entryArray, $type = 'str') {
		switch ($type) {
			case 'db' :
				if (!is_array($entryArray))
					return db2db($entryArray);
				foreach (array_keys($entryArray) as $key)
					$entryArray[$key] = db2db($entryArray[$key]);
				return $entryArray;
			case 'str' :
				if (!is_array($entryArray))
					return str2db($entryArray);
				foreach (array_keys($entryArray) as $key)
					$entryArray[$key] = str2db($entryArray[$key]);
				return $entryArray;
		}
		die('Unknown type sent to dbSanitize');
	}

	// spawns a page in the background, used by mail processor.
	function bmHttpSpawn($page) {

		/* Convert illegal characters in url */
		$page = str_replace(' ', '%20', $page);

		$errno = '';
		$errstr = '';
		$port = (defined('bm_hostport')) ? bm_hostport : $_SERVER['SERVER_PORT'];
		$host = (defined('bm_hostname')) ? bm_hostname : $_SERVER['HTTP_HOST'];

		// strip port information from hostname
		$host = preg_replace('/:\d+$/i', '', $host);

		// NOTE: fsockopen() SSL Support requires PHP 4.3+ with OpenSSL compiled in
		$ssl = (strpos($this->_http, 'https://')) ? 'ssl://' : '';

		$out = "GET $page HTTP/1.1\r\n";
		$out .= "Host: " . $host . "\r\n";

		// to allow for basic .htaccess http authentication, uncomment the following;
		// $out .= "Authorization: Basic " . base64_encode('username:password')."\r\n";

		$out .= "\r\n";

		$socket = fsockopen($ssl . $host, $port, $errno, $errstr, 10);

		if ($socket) {
			fwrite($socket, $out);
		} else {
			global $logger;
			$logger->addErr(Pommo::_T('Error Spawning Page') . ' ** Errno : Errstr: ' . $errno . ' : ' . $errstr);
			return false;
		}

		return true;
	}

}
?>
