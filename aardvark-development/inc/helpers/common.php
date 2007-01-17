<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2006 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/licenses/gpl.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. Notify the above author of modifications to contents within.
 * 
 *  WHY? Because this is a community project -- purposely released under the GPL.
 *    We'd love to have the possiblity to include your derivative works! 
 *    We'd love to coordinate around your development efforts!
 *    We'd love to assist you with your changes!
 *    DON'T BE A STRANGER!
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
					$input[$key] = PommoHelper::slashStrip($value);
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
				preg_match('/^\[(\w+)\]\s*=\s*\"?([^\"]*)\"?.*$/i',$line,$matches);

				// check if a key:value was extracted
				if (!empty($matches[2]))
					// merge key:value onto return array
					$a = array_merge($a, array($matches[1] => $matches[2]));
			}
		}
		return $a;
	}
	
	// check an email. Function lifted from Monte's SmartyValidate class for consistency.
	// accepts an email address (str)
	// returns email legitimacy (bool)
	function isEmail($_address) {
		return (!(preg_match('!@.*@|\.\.|\,|\;!', $_address) || !preg_match('!^.+\@(\[?)[a-zA-Z0-9\.\-]+\.([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$!', $_address))) ? true : false;
	}
	
	// generates a unique code to be used as a confirmation key.
	// returns code (str)
	function makeCode($length = false) {
		if (!$length)
			return md5(rand(0, 5000).time());
		return substr(md5(rand(0, 5000).time()),0,$length-1).rand(0,9);
	}
	
	function makePassword() {
		return substr(md5(rand()), 0, 5);
	}
	
	// checks to see if an email address exists in the system
	//  only includes active && pending subscribers
	// accepts a single email (str) or array of emails
	// returns an array of duplicate found emails. FALSE if no dupes were found. 
	function & isDupe(&$in) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		if(empty($in))
			return false;

		$query = "
			SELECT email
			FROM " . $dbo->table['subscribers'] ."
			WHERE email IN (%q)
			AND status IN(1,2)";
		$query = $dbo->prepare($query,array($in));
		$o = $dbo->getAll($query, 'assoc', 'email');
		if (empty($o))
			$o = false;
		return $o;
	}
	
	// array_intersect_key requires PHP 5.1 +, here's a compat function --> (limited to 2 arrs)
	// returns an array containing all the values of array1  which have matching keys that are present in a2
	function & arrayIntersect(&$a1, &$a2) {		
		$o = array();
		if (!is_array($a1) || !is_array($a2))
			return $o;
			
		foreach(array_keys($a2) as $key) {
			if (isset($a1[$key]))
				$o[$key] = $a1[$key];
		}
		return $o;
	}
}
?>
