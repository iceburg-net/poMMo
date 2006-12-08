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

class PommoMailCtl {
 	
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
			PommoMailCtl::kill('Failed to poll mailing.');
			
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
				PommoMailCtl::kill('Failed to restart mailing');
			$logger->addMsg(sprintf(Pommo::_T('Mailing resumed under serial %s'),$serial), 3);
			
			$query = "UPDATE ".$dbo->table['queue']." SET smtp=0";
			if(!$dbo->query($query))
				PommoMailCtl::kill('Could not clear relay allocations');
			
			break;
	
		case 'stop':
			$query = "
				UPDATE ". $dbo->table['mailing_current']."
				SET
					command='none',
					current_status='stopped'";
			if (!$dbo->query($query))
				PommoMailCtl::kill('Failed to stop mailing');
			PommoMailCtl::kill('Mailing stopped',TRUE);
			break;
			
		default :
			if (!$skipSecurity && $row['serial'] != $serial) 
				PommoMailCtl::kill(Pommo::_T('Serials do not match. Another background script is probably processing the mailing.'),TRUE);
			if ($row['current_status'] == 'stopped')
				PommoMailCtl::kill(Pommo::_T('Mailing halted. You must restart the mailing.'), TRUE);			
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
				PommoMailCtl::kill('Unable to update queue sent');
				
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
				PommoMailCtl::kill('Unable to update queue failed');
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
	function finish($id = 0, $cancel = false, $test = false) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$status = ($cancel) ? 2 : 0;
		
		$query = "
			DELETE FROM ". $dbo->table['mailing_current']."
			WHERE current_id=%i";
		$query = $dbo->prepare($query, array($id));
		if ($dbo->affected($query) < 1)
			return false;
			
		if ($test) { // remove if this was a test mailing
			$query = "
				DELETE FROM ". $dbo->table['mailings']."
				WHERE mailing_id=%i";
			$query = $dbo->prepare($query, array($id));
		}
		else {
			$query = "
				UPDATE ". $dbo->table['mailings']."
				SET 
				finished=FROM_UNIXTIME(%i),
				status=%i,
				sent=(SELECT count(subscriber_id) FROM ". $dbo->table['queue']." WHERE status > 0)
				WHERE mailing_id=%i";
			$query = $dbo->prepare($query, array(time(), $status, $id));
		}
		
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
		PommoMailCtl::queueRelease($relayID);
		
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
		
		if(!PommoMailCtl::queueRelease($relay))
			PommoMailCtl::kill('Unable to release queue.');
		
		// mark our working queue
		$query = "
			UPDATE ".$dbo->table['queue']."
			SET smtp=%i
			WHERE smtp=0 AND status=0
			LIMIT %i";
		$query = $dbo->prepare($query,array($relay,$limit));
		if (!$dbo->query($query))
			PommoMailCtl::kill('Unable to mark queue.');
		
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
		
		PommoMailCtl::spawn($pommo->_baseUrl.'admin/mailings/mailings_send4.php?securityCode='.$p['code'].'&relayID='.$p['relayID'].'&serial='.$p['serial'].'&spawn='.$p['spawn']);
	}
	
	// gets the number of mailings
	// returns mailing tally (int)
	function tally() {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$query = "
			SELECT count(mailing_id)
			FROM " . $dbo->table['mailings'];
		return ($dbo->query($query,0));
	}
	
	// spawns a page in the background, used by mail processor.
	function spawn($page) {
		global $pommo;
		$logger =& $pommo->_logger;

		/* Convert illegal characters in url */
		$page = str_replace(' ', '%20', $page);

		$errno = '';
		$errstr = '';
		$port = $pommo->_hostport;
		$host = $pommo->_hostname;

		// strip port information from hostname
		$host = preg_replace('/:\d+$/i', '', $host);

		// NOTE: fsockopen() SSL Support requires PHP 4.3+ with OpenSSL compiled in
		$ssl = (strpos($pommo->_http, 'https://')) ? 'ssl://' : '';

		$out = "GET $page HTTP/1.1\r\n";
		$out .= "Host: " . $host . "\r\n";

		// to allow for basic .htaccess http authentication, 
		//   uncomment and fill in the following;
		// $out .= "Authorization: Basic " . base64_encode('username:password')."\r\n";

		$out .= "\r\n";

		$socket = fsockopen($ssl . $host, $port, $errno, $errstr, 10);

		if ($socket) {
			fwrite($socket, $out);
		} else {
			$logger->addErr(Pommo::_T('Error Spawning Page') . ' ** Errno : Errstr: ' . $errno . ' : ' . $errstr);
			return false;
		}

		return true;
	}
	
}
?>