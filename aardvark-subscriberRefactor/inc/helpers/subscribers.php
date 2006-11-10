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

// include the field prototype object 
$GLOBALS['pommo']->requireOnce($GLOBALS['pommo']->_baseDir. 'inc/classes/prototypes.php');

/**
 * Subscriber: A Subscriber
 * ==SQL Schema==
 *	id				(int)			Database ID/Key
 *	email			(str)			Email Address
 *	date			(date)			Date last modified (records changed)
 *	registered		(date)			Date registered (signed up)
 *	ip				(str)			IP (tcp/ip) used to register
 *	flagged			('update','')	Flagged status
 *
 * == Additional columns for Pending ==
 *	code			(str)			Code to complete pending request
 *	newEmail		(str)			The email address to change to upon completion
 *	type			(enum)			'add','del','change','password'
 */
	
 
class PommoSubscriber {
	
	// make a subscriber template
	// accepts a field template (assoc array)
	// accepts a flag (bool) to designate return of a pending subscriber type
	// return a subscriber (array)
	function & make($in = array(), $pending = FALSE) {
		$o = ($pending) ?
			PommoType::subscriber() :
			PommoType::subscriberPending;
		return PommoAPI::getParams($o, $in);
	}
	
	// make a subscriber template based off a database row (field schema)
	// accepts a subscriber template (assoc array)  
	// accepts a flag (bool) to designate return of a pending subscriber type
	// return a subscriber (array)
	function & makeDB(&$row, $pending = FALSE) {
		$in = array(
		'id' => $row['id'],
		'email' => $row['email'],
		'date' => $row['date'],
		'registered' => $row['registered'],
		'ip' => $row['ip'],
		'flagged' => $row['flagged']);
		
		if ($pending) {
			$o = array(
				'code' => $row['code'],
				'newEmail' => $row['newEmail'],
				'type' => $row['type']);
			$in = array_merge($o,$in);
		}
		
		$o = ($pending) ?
			PommoType::subscriber() :
			PommoType::subscriberPending;
		return PommoAPI::getParams($o,$in);
	}
	
	// subscriber validation
	// accepts a field (array)
	// accepts a flag (bool) to designate return of a pending subscriber type
	// returns true if field ($in) is valid, false if not
	function validate(&$in, $pending = FALSE) {
		global $pommo;
		$logger =& $pommo->_logger;
		
		$invalid = array();

		if (!PommoHelper::isEmail($in['email']))
			$invalid[] = 'email';
		if (!is_numeric($in['date']))
			$invalid[] = 'date';
		if (!is_numeric($in['registered']))
			$invalid[] = 'registered';
		if (!is_array($in['data']))
			$invalid[] = 'data';
		
		if ($pending) {
			if(empty($in['code']))
				$invalid[] = 'code';
			switch ($in['type']) {
				case 'add':
				case 'del':
				case 'change':
				case 'password':
					break;
				default:
					$invalid[] = 'type'; 
			}
		}
			
		if (!empty($invalid)) {
			foreach ($invalid as $i)
				$str .= " [$i] ";
			$logger->addErr("Field failed validation on; $str",1);
			return false;
		}
		return true;
	}
	
	// fetches subscribers from the databse
	// accepts group (int) as ID of group
	// accepts sort (int) as ID of subscriber_field
	// accepts order (str) "ASC" or "DESC"
	// accepts limit (int) of # subscribers returned
	// returns an array of subscribers. Array key(s) correlates to subscriber id.
	function & get() {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$o = array();
		
		$query = "
			SELECT *
			FROM " . $dbo->table['fields']."
			ORDER BY field_ordering";
		$query = $dbo->prepare($query);
		
		while ($row = $dbo->getRows($query)) {
			$o[$row['field_id']] = PommoField::makeDB($row);
		}
		
		return $o;
	}
	
	// fetches subscriber's emails (and ID) from the database
	// accepts group (int) as ID of group
	// accepts sort (int) as ID of subscriber_field
	// accepts order (str) "ASC" or "DESC"
	// accepts limit (int) of # subscribers returned
	// returns an array of subscriber's emails. Array key(s) correlates to subscriber id.
	function & getEmail() {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$o = array();
		
		$query = "
			SELECT *
			FROM " . $dbo->table['fields']."
			ORDER BY field_ordering";
		$query = $dbo->prepare($query);
		
		while ($row = $dbo->getRows($query)) {
			$o[$row['field_id']] = PommoField::makeDB($row);
		}
		
		return $o;
	}
	
	// fetches fields from the database based off of their ID
	// accepts a single ID (int) or array of IDs 
	// returns an array of fields. Array key(s) correlates to field key.
	function & getByID($id = array()) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$o = array();
		
		$query = "
			SELECT *
			FROM " . $dbo->table['fields'] . "
			WHERE field_id IN(%c)
			ORDER BY field_ordering";
		$query = $dbo->prepare($query,array($id));
		
		while ($row = $dbo->getRows($query)) {
			$o[$row['field_id']] = PommoField::makeDB($row);
		}
		
		return $o;
	}
	
	// adds a field to the database
	// accepts a field (array)
	// returns the database ID of the added field or FALSE if failed
	function add(&$in) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		// set the ordering of field if not provided
		if (!is_numeric($in['ordering'])) {
			$query = "
				SELECT field_ordering
				FROM " . $dbo->table['fields'] . "
				ORDER BY field_ordering DESC";
			$query = $dbo->prepare($query);
			$in['ordering'] = $dbo->query($query, 0) + 1;
		}
		
		if (!PommoField::validate($in))
			return false;
			
		$query = "
		INSERT INTO " . $dbo->table['fields'] . "
		SET
		field_active='%s',
		field_ordering=%i,
		field_name='%s',
		field_prompt='%s',
		field_normally='%s',
		field_array='%s',
		field_required='%s',
		field_type='%s'";
		$query = $dbo->prepare($query,array(
			$in['active'],
			$in['ordering'],
			$in['name'],
			$in['prompt'],
			$in['normally'],
			serialize($in['array']),
			$in['required'],
			$in['type']
		));
		
		if (!$dbo->query($query))
			return false;
		
		return $dbo->lastId();
	}
	
	// removes a field from the database
	// accepts a single ID (int) or array of IDs 
	// returns the # of deleted fields (int). 0 (false) if none.
	function delete(&$id) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$query = "
			DELETE
			FROM " . $dbo->table['fields'] . "
			WHERE field_id IN(%c)";
		$query = $dbo->prepare($query,array($id));
		
		return $dbo->affected($query);
	}
	
	// flags subscribers to update their records
	// accepts a single ID (int) or array of IDs 
	// returns the # of subscribers successfully flagged (int). 0 (false) if none.
	function flagByID($id = array(), $table = 'active') {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$o = array();
		
		$query = "
			UPDATE " . $dbo->table['subscribers_'.$table] . "
			SET flagged='update'
			WHERE id IN (%q)";
		$query = $dbo->prepare($query,array($id));
		
		return $dbo->affected($query);
	}

}
?>
