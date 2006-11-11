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
 *	subscriber_id	(int)			Database ID/Key
 *	email			(str)			Email Address
 *	time_touched	(date)			Date last modified (records changed)
 *	time_registered	(date)			Date registered (signed up)
 *	flag			(enum)			('update',NULL) Subscribers flag (def: null)
 *	ip				(str)			IP (tcp/ip) used to register
 *	status			(enum)			'active','inactive','pending' (def: pending)
 *
 * == Additional columns for Pending ==
 *	pending_id		(int)			Database ID/Key
 *	subscriber_id	(int)			Subscriber ID in subscribers table
 *	pending_code	(str)			Code to complete pending request
 *	pending_type	(enum)			'add','del','change','password',NULL (def: null)
 *	pending_email	(str)			Pending email change (optional)
 *
 * == Additional Data Columns ==
 *	data_id			(int)			Database ID/Key
 *	field_id		(int)			Field ID in fields table
 *	subscriber_id	(int)			Subscriber ID in subscribers table
 *	value			(str)			Subscriber's field value
 */
	
class PommoSubscriber {
	
	// make a subscriber template
	// accepts a field template (assoc array)
	// accepts a flag (bool) to designate return of a pending subscriber type
	// return a subscriber (array)
	function & make($in = array(), $pending = FALSE) {
		$o = ($pending) ?
			PommoType::subscriberPending() :
			PommoType::subscriber();
		return PommoAPI::getParams($o, $in);
	}
	
	// make a subscriber template based off a database row (field schema)
	// accepts a subscriber template (assoc array)  
	// accepts a flag (bool) to designate return of a pending subscriber type
	// return a subscriber (array)
	function & makeDB(&$row, $pending = FALSE) {
		$in = array(
		'id' => $row['subscriber_id'],
		'email' => $row['email'],
		'touched' => $row['time_touched'],
		'registered' => $row['time_registered'],
		'flag' => $row['flag'],
		'ip' => $row['ip'],
		'status' => $row['status']);
			
		if ($pending) {
			$o = array(
				'pending_code' => $row['pending_code'],
				'pending_email' => $row['pending_email'],
				'pending_type' => $row['pending_type']);
			$in = array_merge($o,$in);
		}

		$o = ($pending) ?
			PommoType::subscriberPending() :
			PommoType::subscriber();
		return PommoAPI::getParams($o,$in);
	}
	
	// subscriber validation
	// accepts a subscriber (array)
	// accepts a flag (bool) to designate return of a pending subscriber type
	// returns true if field ($in) is valid, false if not
	function validate(&$in, $pending = FALSE) {
		global $pommo;
		$logger =& $pommo->_logger;
		
		$invalid = array();

		if (!PommoHelper::isEmail($in['email']))
			$invalid[] = 'email';
		if (!is_numeric($in['touched']))
			$invalid[] = 'touched';
		if (!is_numeric($in['registered']))
			$invalid[] = 'registered';
		if (!empty($in['flag']) && $in['flag'] != 'update')
			$invalid[] = 'flag';
		if (!is_array($in['data']))
			$invalid[] = 'data';
		if (empty($in['ip']))
			$invalid[] = 'ip';
			
		switch($in['status']) {
			case 'active':
			case 'inactive':
			case 'pending':
				break;
			default:
				$invalid[] = 'status';
		}
		
		if ($pending) {
			if(empty($in['pending_code']))
				$invalid[] = 'pending_code';
			switch ($in['pending_type']) {
				case 'add':
				case 'del':
				case 'change':
				case 'password':
					break;
				default:
					$invalid[] = 'pending_type'; 
			}
			if (!empty($in['pending_email']) && !PommoHelper::isEmail($in['pending_email']))
				$invalid[] = 'pending_email'; 
		}
			
		if (!empty($invalid)) {
			foreach ($invalid as $i)
				$str .= " [$i] ";
			$logger->addErr("Field failed validation on; $str",1);
			return false;
		}
		return true;
	}
	
	// fetches subscribers (and their data) from the databse
	// accepts subscriber status (str) ['active','inactive','pending','all'(def)]
	// accepts sorting column (str) of subscriber table [email, ip, time_registered, etc] TODO: expand this to field_id/val
	// accepts order (str) "ASC" or "DESC"
	// accepts limit (int) of # subscribers returned
	// returns an array of subscribers. Array key(s) correlates to subscriber id.
	function & get($status = null, $sort = null, $order = null, $limit = null, $offset = null) {
		global $pommo;
		$dbo =& $pommo->_dbo;

		if ($status == 'all')
			$status = null;
			
		if (is_numeric($limit) && !is_numeric($offset))
			$offset = 0;
	
		$o = array();
		
		$query = "
			SELECT DISTINCT 
				s.*, 
				p.pending_code, 
				p.pending_type, 
				p.pending_email,
				d.field_id,
				d.value
			FROM 
				" . $dbo->table['subscribers']." s
				LEFT JOIN " . $dbo->table['subscriber_data']." d ON (s.subscriber_id = d.subscriber_id)
				LEFT JOIN " . $dbo->table['subscriber_pending']." p ON (s.subscriber_id = p.subscriber_id)
			WHERE
				1
				[AND s.status='%S']
				[ORDER BY %S %s]
				[LIMIT %I, %I]";
		$query = $dbo->prepare($query,array($status, $sort, $order, $offset, $limit));
		
		
		while ($row = $dbo->getRows($query)) {
			
			$mysqlResult[] = $row; // for demonstration purposes, will be removed
			if (!isset($o[$row['subscriber_id']])) {
				$o[$row['subscriber_id']] = ($row['status'] == 'pending') ?
					PommoSubscriber::MakeDB($row, TRUE) :
					PommoSubscriber::MakeDB($row);
			}
			if (!empty($row['value'])) 
				$o[$row['subscriber_id']]['data'][$row['field_id']] = $row['value'];
		}
		
		echo "<--- SINGLE QUERY FUNCTION RETURNS --->\n\n";
		echo "Normal (processed) Return:\n";
		var_dump($o);
		
		echo "\n\nRows of MySQL Result:\n";
		var_dump($mysqlResult);
		
		return $o;
	}
	
	
	// fetches subscribers (and their data) from the databse
	// accepts subscriber status (str) ['active','inactive','pending','all'(def)]
	// accepts sort as ID (id) of field_id or subscriber column name [email, ip, etc] (str)
	// accepts order (str) "ASC" or "DESC"
	// accepts limit (int) of # subscribers returned
	// returns an array of subscribers. Array key(s) correlates to subscriber id.
	function & getAlt($status = null, $sort = null, $order = null, $limit = null, $offset = null) {
		global $pommo;
		$dbo =& $pommo->_dbo;

		if ($status == 'all')
			$status = null;
			
		if (is_numeric($limit) && !is_numeric($offset))
			$offset = 0;
	
		$o = array();
		
		$query = "
			SELECT *
			FROM " . $dbo->table['subscribers']."
			WHERE
				1
				[AND s.status='%S']
				[ORDER BY %S] [%S]
				[LIMIT %I, %I]";
		$query = $dbo->prepare($query,array($status, $sort, $order, $offset, $limit));
		
		
		$pending = array();
		$data = array();
		
		$mysqlResult[] = "First Query\n\n\n"; // for demonstration purposes, will be removed
		
		while ($row = $dbo->getRows($query)) {
			$mysqlResult[] = $row; // for demonstration purposes, will be removed
			if ($row['status'] == 'pending') {
				$o[$row['subscriber_id']] = PommoSubscriber::MakeDB($row, TRUE);
				// array of pending IDs
				$pending[] = $o[$row['subscriber_id']];
			}
			else {
				$o[$row['subscriber_id']] = PommoSubscriber::MakeDB($row);
			}
		}
		
		// fetch pending
		if (!empty($pending)) {
			$query = "
				SELECT
					pending_code, 
					pending_type, 
					pending_email,
					subscriber_id
				FROM 
					" . $dbo->table['subscriber_pending']."
				WHERE 
					subscriber_id IN(%c)";
			$query = $dbo->prepare($query,array($pending));
			
			$mysqlResult[] = "\n\n\nSECOND QUERY\n\n\n"; // for demonstration purposes, will be removed
			
			while ($row = $dbo->getRows($query)) {
				$mysqlResult[] = $row; // for demonstration purposes, will be removed
				$o[$row['subscriber_id']]['pending_code'] = $row['pending_code'];
				$o[$row['subscriber_id']]['pending_type'] = $row['pending_type'];
				$o[$row['subscriber_id']]['pending_email'] = $row['pending_email'];
			}
		}
		
		// fetch data
		$data = array_keys($o);
		if (!empty($o)) {
			$query = "
				SELECT
					field_id,
					value,
					subscriber_id
				FROM
					" . $dbo->table['subscriber_data']."
				WHERE
					subscriber_id IN(%c)";
			$query = $dbo->prepare($query,array($data));
			
			$mysqlResult[] = "\n\n\nTHIRD QUERY\n\n\n"; // for demonstration purposes, will be removed
			
			while ($row = $dbo->getRows($query)) {
				$mysqlResult[] = $row; // for demonstration purposes, will be removed
				$o[$row['subscriber_id']]['data'][$row['field_id']] = $row['value'];
			}
		}
		
		echo "<--- SINGLE QUERY FUNCTION RETURNS --->\n\n";
		echo "Normal (processed) Return:\n";
		var_dump($o);
		
		echo "\n\nRows of MySQL Result:\n";
		var_dump($mysqlResult);
		
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
	
	// fetches subscribers from the databse based off their email
	// accepts a single email (str) or array of emails
	// accepts a s
	// returns an array of subscribers. Array key(s) correlates to subscriber id.
	
	function getByEmail($email = array(), $table = 'active') {
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
