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
 
// include the mailing prototype object 
$GLOBALS['pommo']->requireOnce($GLOBALS['pommo']->_baseDir. 'inc/classes/prototypes.php');

/**
 * Mailing: A poMMo Mailing
 * ==SQL Schema==
 *	mailing_id		(int)		Database ID/Key
 *	fromname		(str)		Header: FROM name<>
 *  fromemail		(str)		Header: FROM <email>
 *  subject			(str)		Header: SUBJECT
 *  body			(str)		Message Body
 *  altbody			(str)		Alternative Text Body
 *  ishtml			(enum)		'on','off' toggle of HTML mailing
 *  mailgroup		(str)		Name of poMMo group mailed
 *  subscriberCount	(int)		Number of subscribers in group
 *  started			(datetime)	Time mailing started
 *  finished		(datetime)	Time mailing ended
 *  sent			(int)		Number of mails sent
 *  charset			(str)		Encoding of Message
 *  status			(bool)		0: finished, 1: processing, 2: cancelled
 * 	
 * ==Additional Columns for Current Mailing==
 * 
 *  current_id		(int)		ID of current mailing (from mailing_id)
 *  command			(enum)		'none' (default), 'restart', 'stop'
 *  serial			(int)		Serial of this mailing
 *  securityCode	(char[32])	Security Code of Mailing
 *	notices			(str)		Mailing Messages
 *  current_status	(enum)		'started', 'stopped' (default)
 */
 
class PommoMailing {
 	
 	// make a mailing template
	// accepts a mailing template (assoc array)
	// accepts a flag (bool) to designate return of current mailing type
	// return a mailing object (array)
	function & make($in = array(), $current = FALSE) {
		$o = ($current) ?
			PommoType::mailingCurrent() :
			PommoType::mailing();
		return PommoAPI::getParams($o, $in);
	}
	
	// make a mailing template based off a database row (mailing* schema)
	// accepts a mailing template (assoc array)  
	// accepts a flag (bool) to designate return of current mailing type
	// return a mailing object (array)	
	function & makeDB(&$row) {
		$in = array(
		'id' => $row['mailing_id'],
		'fromname' => $row['fromname'],
		'fromemail' => $row['fromemail'],
		'frombounce' => $row['frombounce'],
		'subject' => $row['subject'],
		'body' => $row['body'],
		'altbody' => $row['altbody'],
		'ishtml' => $row['ishtml'],
		'group' => $row['mailgroup'],
		'tally' => $row['subscriberCount'],
		'start' => $row['started'],
		'end' => $row['finished'],
		'sent' => $row['sent'],
		'charset' => $row['charset'],
		'status' => $row['status']);
			
		if ($row['status'] == 1) {
			$o = array(
				'command' => $row['command'],
				'serial' => $row['serial'],
				'code' => $row['securityCode'],
				'notices' => $row['notices'],
				'touched' => $row['touched'], // TIMESTAMP
				'current_status' => $row['current_status']);
			$in = array_merge($o,$in);
		}

		$o = ($row['status'] == 1) ?
			PommoType::mailingCurrent() :
			PommoType::mailing();
		return PommoAPI::getParams($o,$in);
	}
	
	// mailing validation
	// accepts a mailing object (array)
	// returns true if mailing ($in) is valid, false if not
	function validate(&$in) {
		global $pommo;
		$logger =& $pommo->_logger;
		
		$invalid = array();

		if (empty($in['fromemail']) || !PommoHelper::isEmail($in['fromemail']))
			$invalid[] = 'fromemail';
		if (empty($in['frombounce']) || !PommoHelper::isEmail($in['frombounce']))
			$invalid[] = 'frombounce';
		if (empty($in['subject']))
			$invalid[] = 'subject';
		if (empty($in['body']))
			$invalid[] = 'body';
		if (!is_numeric($in['tally']) || $in['tally'] < 1)
			$invalid[] = 'subscriberCount';
		if (!empty($in['start']) && !is_numeric($in['start']))
			$invalid[] = 'started';
		if (!empty($in['end']) && !is_numeric($in['end']))
			$invalid[] = 'finished';
		if (!empty($in['sent']) && !is_numeric($in['sent']))
			$invalid[] = 'sent';
			
		switch($in['status']) {
			case 0:
			case 1:
			case 2:
				break;
			default:
				$invalid[] = 'status';
		}
		
		if ($in['status'] == 1) {
			switch ($in['command']) {
				case 'none':
				case 'restart':
				case 'stop':
					break;
				default:
					$invalid[] = 'command'; 
			}
			if (!empty($in['serial']) && !is_numeric($in['serial']))
			$invalid[] = 'serial';
			switch ($in['current_status']) {
				case 'started':
				case 'stopped':
					break;
				default:
					$invalid[] = 'current_status'; 
			}
		}
			
		if (!empty($invalid)) {
			$logger->addErr("Mailing failed validation on; ".implode(',',$invalid),1);
			return false;
		}
		
		return true;
	}
	
	// fetches mailings from the database
	// accepts a filtering array -->
	//   active (bool) toggle returning of only active mailings
	//   id (array) -> an array of mailing IDs
	//   code (str) security code of mailing
	// returns an array of mailings. Array key(s) correlates to mailing ID.
	function & get($p = array()) {
		$defaults = array('active' => false, 'id' => null, 'code' => null);
		$p = PommoAPI :: getParams($defaults, $p);
		
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$p['active'] = ($p['active']) ? 1 : null;
		
		$o = array();
		
		$query = "
			SELECT *
			FROM 
				" . $dbo->table['mailings']." m
				LEFT JOIN " . $dbo->table['mailing_current']." c ON (m.mailing_id = c.current_id)
			WHERE
				1
				[AND m.status=%I]
				[AND m.mailing_id IN(%C)]
				[AND c.securityCode='%S']";
		$query = $dbo->prepare($query,array($p['active'],$p['id'],$p['code']));
		
		while ($row = $dbo->getRows($query)) {
			$o[$row['field_id']] = PommoMailing::makeDB($row);
		}
		
		return $o;
	}
	
	// adds a mailing to the database
	// accepts a mailing (array)
	// returns the database ID of the added mailing,
	//  OR if the mailing is a current mailing (status == 1), returns
	//  the security code of the mailing. FALSE if failed
	function add(&$in) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		// set the start time if not provided
		if (empty($in['start']))
			$in['start'] = time();
			
		if (empty($in['sent']))
			$in['sent'] = 0;

		if (!PommoMailing::validate($in))
			return false;
		
		$query = "
			INSERT INTO " . $dbo->table['mailings'] . "
			SET
			[fromname='%S',]
			[fromemail='%S',]
			[frombounce='%S',]
			[subject='%S',]
			[body='%S',]
			[altbody='%S',]
			[ishtml='%S',]
			[mailgroup='%S',]
			[subscriberCount=%I,]
			[finished=FROM_UNIXTIME(%I),]
			[sent=%I,]
			[charset='%S',]
			[status=%I,]
			started=FROM_UNIXTIME(%i)";
		$query = $dbo->prepare($query,array(
			$in['fromname'],
			$in['fromemail'],
			$in['frombounce'],
			$in['subject'],
			$in['body'],
			$in['altbody'],
			$in['ishtml'],
			$in['group'],
			$in['tally'],
			$in['end'],
			$in['sent'],
			$in['charset'],
			$in['status'],
			$in['start']));
		if (!$dbo->query($query))
			return false;
		
		// fetch new subscriber's ID
		$id = $dbo->lastId();
		
		// insert current if applicable
		if (!empty($in['status']) && $in['status'] == 1) {
			if(empty($in['code']))
				$in['code'] = PommoHelper::makeCode();
			
			$query = "
			INSERT INTO " . $dbo->table['mailing_current'] . "
			SET
			[command='%S',]
			[serial=%I,]
			[securityCode='%S',]
			[notices='%S',]
			[current_status='%S',]
			current_id=%i";
			$query = $dbo->prepare($query,array(
				$in['command'],
				$in['serial'],
				$in['code'],
				$in['notices'],
				$in['current_status'],
				$id
			));
			if (!$dbo->query($query))
				return false;
			return $in['code'];
		}
			
		return $id;
	}
	
	// populates the queue with subscribers
	// accepts an array of subscriber IDs
	// returns (bool) - true if success
	function queueMake(&$in) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		// clearQueue
		$query = "DELETE FROM ".$dbo->table['queue'];
		if (!$dbo->query($query))
				return false;
				
		if (empty($in)) { // all subscribers
			$query = "
				INSERT IGNORE INTO ".$dbo->table['queue']."
				(subscriber_id)
				SELECT subscriber_id FROM ".$dbo->table['subscribers'];
			if (!$dbo->query($query))
				return false;
				
			return true;
		}
				
		$values = array();
		foreach ($in as $id)
			$values[] = $dbo->prepare("(%i)",array($id));
			
		$query = "
			INSERT IGNORE INTO ".$dbo->table['queue']."
			(subscriber_id)
			VALUES ".implode(',',$values);
		if (!$dbo->query($query))
				return false;	
		return true;
	}
	
	// checks if a mailing is processing
	// returns (bool) - true if current mailing
	function isCurrent() {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$query = "
			SELECT count(mailing_id)
			FROM ".$dbo->table['mailings']."
			WHERE status=1";
		return ($dbo->query($query,0) > 0) ? true : false;
	}
	
	
	// Polls a mailings commands, performs any necessary actions
	function poll() {
		global $pommo;
		global $skipSecurity;
		global $relayID;
		global $serial;
		global $mailingID;
		
		$dbo =& $pommo->_dbo;
		$logger =& $pommo->_logger;
		
		$query = "
			SELECT command, current_status, serial
			FROM ". $dbo->table['mailing_current']."
			WHERE current_id=%i";
		$query = $dbo->prepare($query,array($mailingID));
		
		$row = mysql_fetch_assoc($dbo->query($query));
		if (empty($row))
			PommoMailing::kill('Failed to poll mailing.');
			
		switch ($row['command']) {
		case 'restart':
			$query = "
				UPDATE ". $dbo->table['mailing_current']."
				SET
					serial=%i,
					command='none',
					current_status='started'
					WHERE current_id=%i";
			$query = $dbo->prepare($query,array($serial,$mailingID));
			if (!$dbo->query($query))
				PommoMailing::kill('Failed to restart mailing');
			$logger->addMsg(sprintf(Pommo::_T('Mailing resumed under serial %s'),$serial), 3);
			
			$query = "UPDATE ".$dbo->table['queue']." SET smtp=0";
			if(!$dbo->query($query))
				PommoMailing::kill('Could not clear relay allocations');
			
			break;
	
		case 'stop':
			$query = "
				UPDATE ". $dbo->table['mailing_current']."
				SET
					command='none',
					current_status='stopped'";
			if (!$dbo->query($query))
				PommoMailing::kill('Failed to stop mailing');
			PommoMailing::kill('Mailing stopped',TRUE);
			break;
			
		default :
			if (!$skipSecurity && $row['serial'] != $serial) 
				PommoMailing::kill(Pommo::_T('Serials do not match. Another background script is probably processing the mailing.'),TRUE);
			if ($row['current_status'] == 'stopped')
				PommoMailing::kill(Pommo::_T('Mailing halted. You must restart the mailing.'), TRUE);			
			break;
		}
		
		return true;
	}
	
	// updates the queue and notices
	// accepts a array of failed emails
	// accepts a array of sent emails
	// accepts an hash array (key == email, value == subsriber ID)
	function update(&$sent, &$failed, &$emailHash) {
		global $mailingID;
		global $pommo;
		$dbo =& $pommo->_dbo;
		$logger =& $pommo->_logger;
		
		if (!empty($sent)) {
			$a = array();
			foreach($sent as $e)
				$a[] = $emailHash[$e];
			
			$query = "
				UPDATE ".$dbo->table['queue']."
				SET status=1
				WHERE subscriber_id IN(%q)";
			$query = $dbo->prepare($query,array($a));
			
			if (!$dbo->query($query))
				PommoMailing::kill('Unable to update queue sent');
				
		}
		
		if (!empty($failed)) {
			$a = array();
			foreach($failed as $e)
				$a[] = $emailHash[$e];
			
			$query = "
				UPDATE ".$dbo->table['queue']."
				SET status=2
				WHERE subscriber_id IN(%q)";
			$query = $dbo->prepare($query,array($a));
			
			if (!$dbo->query($query))
				PommoMailing::kill('Unable to update queue failed');
		}
			
		
		// update DB notices
		$notices = $dbo->prepare('[%Q]', array($logger->getAll()));
		if (!empty($notices)) {
			$query = "
				UPDATE ".$dbo->table['mailing_current']."
				SET notices=CONCAT_WS('||',notices,".$notices.") 
				WHERE current_id=".$mailingID;
			$dbo->query($query);
		}
		
	}
	
	
	// end a mailing
	function finish($id = 0, $cancel = false) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$status = ($cancel) ? 2 : 0;
		
		$query = "
			DELETE FROM ". $dbo->table['mailing_current']."
			WHERE current_id=%i";
		$query = $dbo->prepare($query, array($id));
		if ($dbo->affected($query) < 1)
			return false;
			
			
		$query = "
			UPDATE ". $dbo->table['mailings']."
			SET 
			finished=FROM_UNIXTIME(%i),
			status=%i,
			sent=(SELECT count(subscriber_id) FROM ". $dbo->table['queue']." WHERE status > 0)
			WHERE mailing_id=%i";
		$query = $dbo->prepare($query, array(time(), $status, $id));
		
		if (!$dbo->query($query))
			return false;
		return true;	
	}
	
	// cleanup function called just before script termination
	function kill($reason = null, $killSession = FALSE) {
		global $pommo;
		global $relayID;
		global $mailingID;
		
		$logger =& $pommo->_logger;
		$dbo =& $pommo->_dbo;
	
		if(!empty($reason))
			$logger->addMsg('Script Ending: ' . $reason, 2);
			
		// release queue items allocated to this relayID
		PommoMailing::queueRelease($relayID);
		
		// update DB notices
		$notices = $dbo->prepare('[%Q]', array($logger->getAll()));
		if (!empty($notices)) {
			$query = "
				UPDATE ".$dbo->table['mailing_current']."
				SET notices=CONCAT_WS('||',notices,".$notices.") 
				WHERE current_id=".$mailingID;
			$dbo->query($query);
		}
			
		
		if ($killSession)
			session_destroy();
			
		Pommo::kill();
	}
	
	// allocates part of the queue to a relay
	// returns an array of subscriber_ids
	function queueGet($relay, $limit) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		if(!is_numeric($relay) || $relay == 0)
			return array();
		
		if(!PommoMailing::queueRelease($relay))
			PommoMailing::kill('Unable to release queue.');
		
		// mark our working queue
		$query = "
			UPDATE ".$dbo->table['queue']."
			SET smtp=%i
			WHERE smtp=0 AND status=0
			LIMIT %i";
		$query = $dbo->prepare($query,array($relay,$limit));
		if (!$dbo->query($query))
			PommoMailing::kill('Unable to mark queue.');
		
		// return our queue
		$query = "
			SELECT subscriber_id
			FROM ".$dbo->table['queue']."
			WHERE smtp=%i";
		$query = $dbo->prepare($query,array($relay));
		
		return $dbo->getAll($query, 'assoc', 'subscriber_id');
	}
	
	// release queue items allocated to a relay ID
	// returns success (bool)
	function queueRelease($relay) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$query = "
			UPDATE ".$dbo->table['queue']."
			SET smtp=0 
			WHERE smtp=%i";
		$query = $dbo->prepare($query, array($relay));
		return (!$dbo->query($query)) ? false : true;
	}
	
	// returns the # of unsent emails in a queue
	function queueUnsentCount() {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$query = "
			SELECT COUNT(subscriber_id) 
			FROM ".$dbo->table['queue']."
			WHERE status=0";
		return $dbo->query($query,0);
	}
	
	// mark (serialize) a mailing
	// returns success (bool)
	function mark($serial, $id) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		if(!is_numeric($serial))
			return false;
			
		$query = "
			UPDATE ".$dbo->table['mailing_current']."
			SET serial=%i
			WHERE current_id=%i";
		$query = $dbo->prepare($query,array($serial, $id));
		return ($dbo->affected($query) > 0) ? true : false;
	}
	
	
	function respawn($p = array()) {
		global $pommo;
		Pommo::requireOnce($pommo->_baseDir.'inc/helpers/mailings.php');
		
		global $relayID;
		global $serial;
		global $code;
		$defaults = array('relayID' => $relayID, 'serial' => $serial, 'spawn' => 'TRUE', 'code' => $code);
		$p = PommoAPI :: getParams($defaults, $p);
		
		PommoHelperMailings::spawn($pommo->_baseUrl.'admin/mailings/mailings_send4.php?securityCode='.$p['code'].'&relayID='.$p['relayID'].'&serial='.$p['serial'].'&spawn='.$p['spawn']);
	}
}
?>