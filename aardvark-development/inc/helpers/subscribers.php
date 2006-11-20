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

// include the subscriber prototype object 
$GLOBALS['pommo']->requireOnce($GLOBALS['pommo']->_baseDir. 'inc/classes/prototypes.php');

/**
 * Subscriber: A Subscriber
 * ==SQL Schema==
 *	subscriber_id	(int)			Database ID/Key
 *	email			(str)			Email Address
 *	time_touched	(timestamp)		Date last modified (records changed)
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
 *	pending_array	(str)			Serialized Subscriber object (for update)
 *
 * == Additional Data Columns ==
 *	data_id			(int)			Database ID/Key
 *	field_id		(int)			Field ID in fields table
 *	subscriber_id	(int)			Subscriber ID in subscribers table
 *	value			(str)			Subscriber's field value
 */
	
class PommoSubscriber {
	
	// make a subscriber template
	// accepts a subscriber template (assoc array)
	// accepts a flag (bool) to designate return of a pending subscriber type
	// return a subscriber object (array)
	function & make($in = array(), $pending = FALSE) {
		$o = ($pending) ?
			PommoType::subscriberPending() :
			PommoType::subscriber();
		return PommoAPI::getParams($o, $in);
	}
	
	// make a subscriber template based off a database row (field schema)
	// accepts a subscriber template (assoc array)  
	// accepts a flag (bool) to designate return of a pending subscriber type
	// return a subscriber object (array)
	function & makeDB(&$row, $pending = FALSE) {
		$in = array(
		'id' => $row['subscriber_id'],
		'email' => $row['email'],
		'touched' => $row['touched'],
		'registered' => $row['time_registered'],
		'flag' => $row['flag'],
		'ip' => $row['ip'],
		'status' => $row['status']);
			
		if ($pending) {
			$o = array(
				'pending_code' => $row['pending_code'],
				'pending_array' => $row['pending_array'],
				'pending_type' => $row['pending_type']);
			$in = array_merge($o,$in);
		}

		$o = ($pending) ?
			PommoType::subscriberPending() :
			PommoType::subscriber();
		return PommoAPI::getParams($o,$in);
	}
	
	// subscriber validation
	// accepts a subscriber object (array)
	// returns true if subscriber ($in) is valid, false if not
	function validate(&$in) {
		global $pommo;
		$logger =& $pommo->_logger;
		
		$invalid = array();

		if (!PommoHelper::isEmail($in['email']))
			$invalid[] = 'email';
		if (!is_numeric($in['registered']))
			$invalid[] = 'registered';
		if (!empty($in['flag']) && $in['flag'] != 'update')
			$invalid[] = 'flag';
		if (!is_array($in['data']))
			$invalid[] = 'data';
		
		switch($in['status']) {
			case 'active':
			case 'inactive':
			case 'pending':
				break;
			default:
				$invalid[] = 'status';
		}
		
		if ($in['status'] == 'pending') {
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
		}
			
		if (!empty($invalid)) {
			foreach ($invalid as $i)
				$str .= " [$i] ";
			$logger->addErr("Subscriber failed validation on; $str",1);
			return false;
		}
		return true;
	}
	
	// fetches subscribers (and their data) from the databse
	// accepts filtering array -->
	//   status (str) ['active','inactive','pending','all'(def)]
	//   email (str||array) Email address(es)
	//   sort (str) [email, ip, time_registered, time_touched, status, etc.]
	//   order (str) "ASC" or "DESC"
	//   limit (int) limits # subscribers returned
	//   id (array||str) A single or an array of subscriber IDs
	// returns an array of subscribers. Array key(s) correlates to subscriber id.
	function & get($p = array('status' => 'all', 'email' => null, 'sort' => null, 'order' => null, 'limit' => null, 'offset' => null, 'id' => null)) {
		global $pommo;
		$dbo =& $pommo->_dbo;

		if ($p['status'] == 'all')
			$p['status'] = null;
			
		if (is_numeric($p['limit']) && !is_numeric($p['offset']))
			$p['offset'] = 0;
	
		$o = array();
		
		$query = "
			SELECT
				s.*,
				p.pending_code,
				p.pending_array,
				p.pending_type
			FROM 
				" . $dbo->table['subscribers']." s
				LEFT JOIN " . $dbo->table['subscriber_pending']." p ON (s.subscriber_id = p.subscriber_id)
			WHERE
				1
				[AND s.subscriber_id IN(%C)]
				[AND s.status='%S']
				[AND s.email IN (%Q)]
				[ORDER BY %S] [%S]
				[LIMIT %I, %I]";
		$query = $dbo->prepare($query,array($p['id'],$p['status'], $p['email'], $p['sort'], $p['order'], $p['offset'], $p['limit']));
		
		while ($row = $dbo->getRows($query)) 
			$o[$row['subscriber_id']] = (empty($row['pending_code'])) ?
				PommoSubscriber::MakeDB($row) :
				PommoSubscriber::MakeDB($row, TRUE);
		
		// fetch data
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
			$query = $dbo->prepare($query,array(array_keys($o)));	
			while ($row = $dbo->getRows($query)) 
				$o[$row['subscriber_id']]['data'][$row['field_id']] = $row['value'];
		}
		return $o;
	}
	
	// fetches subscriber emails from the databse
	// accepts filtering array -->
	//   status (str) ['active','inactive','pending','all'(def)]
	//   id (array||str) A single or an array of subscriber IDs
	// accepts ordering array -->
	//   status (str) ['active','inactive','pending','all'(def)]
	//   sort (str) [email, ip, time_registered, time_touched, status, etc.]
	//   order (str) "ASC" or "DESC"
	//   limit (int) limits # subscribers returned
	// returns an array of emails. Array key(s) correlates to subscriber id.
	function & getEmail($p = array('status' => 'all', 'id' => null)) {
		global $pommo;
		$dbo =& $pommo->_dbo;

		if ($p['status'] == 'all')
			$p['status'] = null;

		$o = array();
		
		$query = "
			SELECT
				subscriber_id,
				email
			FROM 
				" . $dbo->table['subscribers']."
			WHERE
				1
				[AND subscriber_id IN(%C)]
				[AND status='%S']";
		$query = $dbo->prepare($query,array($p['id'],$p['status']));
		
		while ($row = $dbo->getRows($query)) 
			$o[$row['subscriber_id']] = $row['email'];
		
		return $o;
	}
	
	// fetches a subscriber ID from an email
	// accepts a email address (str)
	// returns a subscriber ID (int) or false
	function & getIDByEmail($email) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$query = "
			SELECT subscriber_id
			FROM " . $dbo->table['subscribers'] . "
			WHERE email='%s'
			LIMIT 1";
		$query = $dbo->prepare($query,array($email));
		return $dbo->query($query,0);
	}
	
	// fetches subscribers from the database based off their attributes
	// accepts a attribute filtering array. 
	//   array_key == filter table (subscriber_pending, subscriber_data, subscribers)
	//   array_value == array column
	// accepts filter by status (str) either 'active', 'inactive', 'pending' or NULL (any/default)
	// Returns an array of subscriber IDs
	/** EXAMPLE
	array(
		'subscriber_pending' => array(
			'pending_code' => array("not: 'abc1234'", "is: 'def123'", "is: '2234'"),
			'pending_email' => array('not: NULL')),
		'subscriber_data' => array(
			12 => array("not: 'Milwaukee'"), // 12 is alias for field_id=12 ...
			15 => array("greater: 15")),
		'subscribers' => array(
			'email' => "not: 'bhb@iceburg.net'")
		);
	*/
	function & getIDByAttr($f = array('subscriber_pending' => array(), 'subscriber_data' => array(), 'subscribers' => array()), $status = null) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$o = array();
		
		// get the column(s) logic + value(s)
		function getLogic(&$col, &$val, &$filters) {
			if (is_array($val)) {
				foreach($val as $v)
					getLogic($col,$v,$filters);
			}
			else {
				// extract logic ($matches[1]) + value ($matches[2]) 
				preg_match('/^(?:(not|is|less|greater|true|false):)?(.*)$/i',$val,$matches);
				if (!empty($matches[1])) { 
					if (empty($filters[$col]))
						$filters[$col] = array();
					if (empty($filters[$col][$matches[1]]))
						$filters[$col][$matches[1]] = array();
					array_push($filters[$col][$matches[1]],$matches[2]);
				}
			}
		}
		
		function getWhere($col, &$in, &$where) { 
			global $dbo;
			
			if (is_numeric($col)) { // "likely" encountered a field_id in subscriber_data... 
				$field_id = $col;
				$col = 'value';
				$where .= " AND (field_id=$field_id ";
			}
			
			foreach($in as $logic => $vals) {
				switch ($logic) {
					case "is" :
						$where .= $dbo->prepare("[ AND $col IN (%Q) ]",array($vals)); 
						break;
					case "not":
						$where .= $dbo->prepare("[ AND $col NOT IN (%Q) ]",array($vals)); break;
					case "less":
						$where .= $dbo->prepare("[ AND $col < %I ]",array($vals)); break;
					case "greater":
						$where .= $dbo->prepare("[ AND $col > %I ]",array($vals)); break;
					case "true":
						$where .= " AND $col = 'on' "; break;
					case "false":
						$where .= " AND $col != 'on' "; break;
				}
			}
			
			if (isset($field_id))
				$where .= ")";
		}
		
		function & getIDs($table, &$f) {
			global $dbo;
			$filters = array();
			$where = null;
			
			foreach ($f[$table] as $col => $val) 
				getLogic($col,$val,$filters);
			
			foreach($filters as $col => $logic) 
				getWhere($col, $logic, $where);	
				
			if (!empty($where)) {
				$query = "
					SELECT DISTINCT subscriber_id
					FROM ". $dbo->table[$table]."
					WHERE 1 ".$where;
				$o = $dbo->getAll($query, 'assoc', 'subscriber_id');
			}
			return (empty($o)) ? array() : $o;
		}
		
		if (!empty($f['subscriber_pending'])) 
			$o = array_merge($o,getIDs('subscriber_pending',$f));
		
		if (!empty($f['subscriber_data']))
			$o = array_merge($o,getIDs('subscriber_data',$f));
			
		if (!empty($f['subscribers']))
			$o = array_merge($o,getIDs('subscribers',$f));
		
		$o = array_unique($o);
		
		// filter by status if given
		if ($status) {
			$query = "
				SELECT subscriber_id
				FROM ". $dbo->table['subscribers']."
				WHERE 
					subscriber_id IN(%c)
					AND status='%s'";
			$query = $dbo->prepare($query,array($o,$status));
			return $dbo->getAll($query, 'assoc', 'subscriber_id');
		}
		
		return $o;
	}
	
	// adds a subscriber to the database
	// accepts a subscriber (array)
	// returns the database ID of the added field or FALSE if failed
	function add(&$in) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		// set the registration date if not provided
		if (empty($in['registered']))
			$in['registered'] = time();

		if (!PommoSubscriber::validate($in))
			return false;
			
		$query = "
		INSERT INTO " . $dbo->table['subscribers'] . "
		SET
		email='%s',
		time_registered=FROM_UNIXTIME(%i),
		flag='%s',
		ip='%s',
		status='%s'";
		$query = $dbo->prepare($query,array(
			$in['email'],
			$in['registered'],
			$in['flag'],
			$in['ip'],
			$in['status']
		));
		if (!$dbo->query($query))
			return false;
		
		// fetch new subscriber's ID
		$id = $dbo->lastId();
		
		// insert pending (if exists)
		if ($in['status'] == 'pending') {
			$query = "
			INSERT INTO " . $dbo->table['subscriber_pending'] . "
			SET
			[pending_array='%S',]
			subscriber_id=%i,
			pending_code='%s',
			pending_type='%s'";
			$query = $dbo->prepare($query,array(
				$in['pending_array'],
				$id,
				$in['pending_code'],
				$in['pending_type']
			));
			if (!$dbo->query($query))
				return false;
		}
		
		// insert data
		foreach ($in['data'] as $field_id => $value)
			$values[] = $dbo->prepare("(%i,%i,'%s')",array($field_id,$id,$value));
			
		if (!empty($values)) {
			$query = "
			INSERT INTO " . $dbo->table['subscriber_data'] . "
			(field_id, subscriber_id, value)
			VALUES ".implode(',', $values);
			if (!$dbo->query($query))
				return false;
		}
			
		return $id;
	}
	
	// removes a subscriber from the database
	// accepts a single ID (int) or array of IDs 
	// returns the # of deleted subscribers (int). 0 (false) if none.
	function delete(&$id) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$query = "
			DELETE
			FROM " . $dbo->table['subscribers'] . "
			WHERE subscriber_id IN(%c)";
		$query = $dbo->prepare($query,array($id));
		
		$deleted = $dbo->affected($query);
		
		$query = "
			DELETE
			FROM " . $dbo->table['subscriber_pending'] . "
			WHERE subscriber_id IN(%c)";
		$query = $dbo->prepare($query,array($id));
		$dbo->query($query);
		
		$query = "
			DELETE
			FROM " . $dbo->table['subscriber_data'] . "
			WHERE subscriber_id IN(%c)";
		$query = $dbo->prepare($query,array($id));
		$dbo->query($query);
		
		return $deleted;
	}
	
	// updates a subscriber in the database
	// accepts a subscriber (array)
	// returns success (bool)
	// NOTE: The passed subscriber field will overwrites all subscriber info 
	//   (including values in subscriber_pending/subscriber_data). Make sure to pass
	//   the entire subscriber!
	// Does not change the subscriber_id -->  paves the path to add manually assign subs to a group?
	function update(&$in) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$query = "
			UPDATE " . $dbo->table['subscribers'] . "
			SET
			[email='%S',]
			[time_registered='%S',]
			[ip='%S',]
			[status='%S',]
			flag='%s'
			WHERE subscriber_id=%i";
		$query = $dbo->prepare($query,array(
			$in['email'],
			$in['registered'],
			$in['ip'],
			$in['status'],
			$in['flag'],
			$in['id']
		));
		if (!$dbo->query($query))
				return false;
		
		// insert data
		$query = "
			DELETE
			FROM " . $dbo->table['subscriber_data'] . "
			WHERE subscriber_id IN(%c)";
		$query = $dbo->prepare($query,array($in['id']));
		if (!$dbo->query($query))
				return false;
		
		foreach ($in['data'] as $field_id => $value)
			$values[] = $dbo->prepare("(%i,%i,'%s')",array($field_id,$in['id'],$value));
			
		if (!empty($values)) {
			$query = "
			INSERT INTO " . $dbo->table['subscriber_data'] . "
			(field_id, subscriber_id, value)
			VALUES ".implode(',', $values);
			if (!$dbo->query($query))
				return false;
		}
		
		return true;
	}
	
	// flags subscribers to update their records
	// accepts a single ID (int) or array of IDs 
	// returns the # of subscribers successfully flagged (int). 0 (false) if none.
	function flagByID(&$id) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$query = "
			UPDATE " . $dbo->table['subscribers'] ."
			SET flag='update'
			WHERE id IN (%q)";
		$query = $dbo->prepare($query,array($id));
		
		return $dbo->affected($query);
	}
}
?>