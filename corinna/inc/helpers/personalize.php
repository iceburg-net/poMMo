<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/

$GLOBALS['pommo']->requireOnce($GLOBALS['pommo']->_baseDir. 'inc/helpers/fields.php');

class PommoHelperPersonalize {
	
	// scans a message body and returns an array of applicable personaliztions
	// accepts a message body (str)
	// returns a personalization array (array of 4 arrays) 
	//  array[0] == fulltext replace, array[1] == field_name, array[2] == default value, array[3] == field_id

	/* e.g.
	 array(4) {
	  [0]=> -- FULLTEXT REPLACE(s)
	  array(2) {
	    [0]=>
	    string(9) "[[smell]]"
	    [1]=>
	    string(17) "[[xyz|defaultZZ]]"
	  }
	  [1]=> -- FIELD NAME(s)
	  array(2) {
	    [0]=>
	    string(5) "smell"
	    [1]=>
	    string(3) "xyz"
	  }
	  [2]=> -- DEFAULT(s)
	  array(2) {
	    [0]=>
	    string(0) ""
	    [1]=>
	    string(9) "defaultZZ"
	  }
	  [3] => -- FIELD_ID(s)
	  	array(2) {
	  		[0] =>
	  		string(1) "1"
	  		[2] =>
	  		string(1) "7"
	  	}
	} */
	function & get(&$body) {
		$fields = PommoField::get();
		
		$matches = array();
		$pattern = '/\[\[([^\]|]+)(?:\|([^\]]+))?]]/';
		
		if (preg_match_all($pattern, $body, $matches) < 1)
			return array();
		
		// add field_id to name
		
		$matches[3] = array();
		foreach($matches[1] as $field) {
			foreach($fields as $f) {
				if ($f['name'] == $field)
					$matches[3][] = $f['id'];
			}
		}
		return $matches;
	}
	
	
	// personalizes a message body
	// accepts message
	// accepts subscriber object (single subscriber)
	// accepts personalization array
	// returns a personalized body
	function body(&$msg, &$s, &$p) {
		
		foreach($p[0] as $key => $search) {
		
			// lookup replace string (or if it is Email, replace with email address)
			$replace = ($p[1][$key] == 'Email') ? $s['email'] : 
				$s['data'][ ($p[3][$key]) ];
			
			// attempt to add default if replacement is empty
			if (empty($replace))
				$replace = $p[2][$key];
				
			$body = str_replace($search, $replace,$body);
		}
	
	return $body;
	}
}
?>