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


// include the pending prototype object 
$GLOBALS['pommo']->requireOnce($GLOBALS['pommo']->_baseDir. 'inc/classes/prototypes.php');

class PommoPending {
	// make a pending template
	// accepts a pending template (assoc array)
	// return a pending object (array)
	function & make($in = array()) {
		$o = PommoType::pending();
		return PommoAPI::getParams($o, $in);
	}
	
	// make a pending template based off a database row (subscriber_pending schema)
	// accepts a pending template (assoc array)  
	// return a pending object (array)
	function & makeDB(&$row) {
		$in = array(
		'id' => $row['pending_id'],
		'subscriber_id' => $row['subscriber_id'],
		'code' => $row['pending_code'],
		'array' => unserialize($row['pending_array']),
		'type' => $row['pending_type']);
		
		$o = PommoType::pending();
		return PommoAPI::getParams($o,$in);
	}
	
	// pending object validation
	// accepts a pending object (array)
	// returns true if pending object ($in) is valid, false if not
	function validate(&$in) {
		global $pommo;
		$logger =& $pommo->_logger;
		
		$invalid = array();

		if (!is_numeric($in['subscriber_id']))
			$invalid[] = 'subscriber_id';
		if (empty($in['code']))
			$invalid[] = 'code';
		if (!is_array($in['array']))
			$invalid[] = 'array';
		
		switch($in['type']) {
			case 'add':
			case 'del':
			case 'change':
			case 'password':
				break;
			default:
				$invalid[] = 'type';
		}
			
		if (!empty($invalid)) {
			foreach ($invalid as $i)
				$str .= " [$i] ";
			$logger->addErr("Pending Object failed validation on; $str",1);
			return false;
		}
		return true;
	}
	
	// get a pending entry from a code
	// accepts a pending code (str)
	// returns pending object (array) or false if not found.
	function & get($code = null){
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$o = array();
		
		$query = "
			SELECT *
			FROM ".$dbo->table['subscriber_pending']."
			WHERE pending_code='%s' LIMIT 1";
		$query = $dbo->prepare($query,array($code));
		while ($row = $dbo->getRows($query)) 
			$o[$row['pending_id']] = PommoPending::MakeDB($row);
		
		return (empty($o)) ? false : current($o);
	}
	
	// get a pending entry from a email address
	// accepts a pending code (str)
	// returns pending object (array) or false if not found.
	function & getByEmail($email = null){
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$o = array();
		
		$query = "
			SELECT 
				p.*
			FROM 
				".$dbo->table['subscriber_pending']." p,
				".$dbo->table['subscribers']." s
			WHERE 
				s.subscriber_id = p.subscriber_id
				AND s.email = '%s'
			LIMIT 1";
		$query = $dbo->prepare($query,array($email));
		while ($row = $dbo->getRows($query)) 
			$o[$row['pending_id']] = PommoPending::MakeDB($row);
		
		return (empty($o)) ? false : current($o);
	}
	
	// checks to see if a subscriber ID has a pending request
	// accepts a subscriber ID (int)
	// returns true if pending exists, false if not (bool)
	function & isPending($id = null){
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$query = "
			SELECT 
			count(pending_id)
			FROM 
				".$dbo->table['subscriber_pending']."
			WHERE 
				subscriber_id = %i
			LIMIT 1";
		$query = $dbo->prepare($query,array($id));
		return ($dbo->query($query,0) > 0) ? true : false;
	}
	
	// checks to see if a email has a pending request
	// accepts a email (str)
	// returns true if pending exists, false if not (bool)
	function & isEmailPending($email = null){
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$query = "
			SELECT 
			count(p.pending_id)
			FROM 
				".$dbo->table['subscriber_pending']." p,
				".$dbo->table['subscribers']." s
			WHERE 
				s.subscriber_id = p.subscriber_id
				AND s.email = '%s'
			LIMIT 1";
		$query = $dbo->prepare($query,array($email));
		return ($dbo->query($query,0) > 0) ? true : false;
	}
	
	// adds a pending entry
	// accepts a subscriber object (array)
	// accepts a pending type (str) ['add','del','change','password']
	// returns the pending code (str) or FALSE if error
	function add(&$subscriber, $type = null) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		$loger =& $pommo->_logger;
		
		switch ($type) {
			case 'add':
			case 'del':
			case 'change':
			case 'password':
				break;
			default:
				$logger->addErr('Unknown type passed to PommoPending::add');
				return false;
		}
		
		$p = array(
			'subscriber_id' => $subscriber['id'],
			'type' => $type,
			'code' => PommoHelper::makeCode());
		if ($p['type'] == 'change')
			$p['array'] = $subscriber;
		$pending = PommoPending::make($p);
		
		if (!PommoPending::validate($pending)) {
			$logger->addErr('PommoPending::add() failed validation');
			return false;
		}
		
		// check for pre-existing pending request
		if (PommoPending::isPending($pending['subscriber_id']))
			return false;
		
		$pending['array'] = (empty($pending['array'])) ? 
			null : 
			serialize($pending['array']);
			
		$query = "
			INSERT INTO ".$dbo->table['subscriber_pending']."
			SET
				[pending_array='%S',]
				subscriber_id=%i,
				pending_type='%s',
				pending_code='%s'";
		$query = $dbo->prepare($query,array(
			$pending['array'],
			$pending['subscriber_id'],
			$pending['type'],
			$pending['code']));
			
		if (!$dbo->query($query))
			return false;
		
		return $pending['code'];
	}
	
	
	// removes a pending entry
	// accepts a pending object (array)
	// return success (bool)
	function cancel(&$in) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		// if the user is pending to be added, remove entire subscriber.
		if ($in['type'] == 'add') {
			$pommo->requireOnce($pommo->_baseDir.'inc/helpers/subscribers.php');
			return PommoSubscriber::delete($in['subscriber_id']);
		}
		
		// else, only remove pending entry
		$query = "
			DELETE FROM ".$dbo->table['subscriber_pending']."
			WHERE pending_id=%i";
		$query = $dbo->prepare($query,array($in['id']));
		if (!$dbo->query($query)) {
			$logger->addErr('PommoPending::cancel() -> Error removing pending entry.');
			return false;
		}
		return true;
	}
	
	// performs a pending request
	// accepts a pending object (array)
	// returns success (bool)
	function perform(&$in) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		$logger =& $pommo->_logger;
		
		if (!is_numeric($in['id']) || !is_numeric($in['subscriber_id'])) {
			$logger->addErr('PommoPending::perform() -> invalid pending object sent.');
			return false;
		}
		
		switch ($in['type']) {
			case 'add': // subscribe
				$query = "
					UPDATE ".$dbo->table['subscribers']."
					SET status='active'
					WHERE subscriber_id=%i";
				$query = $dbo->prepare($query,array($in['subscriber_id']));
				if (!$dbo->query($query)) {
					$logger->addErr('PommoPending::perform() -> Error updating subscriber.');
					return false;
				}
				break;
				
			case 'del': // unsubscribe
				$query = "
					UPDATE ".$dbo->table['subscribers']."
					SET status='inactive'
					WHERE subscriber_id=%i";
				$query = $dbo->prepare($query,array($in['subscriber_id']));
				if (!$dbo->query($query)) {
					$logger->addErr('PommoPending::delete() -> Error updating subscriber.');
					return false;
				}
				break;
				
			case 'change': // update
				$pommo->requireOnce($pommo->_baseDir. 'inc/helpers/subscribers.php');
				$subscriber =& $in['array'];
				
				if (!PommoSubscriber::update($subscriber)) {
					$logger->addErr('PommoPending::perform() -> Error updating subscriber.');
					return false;
				}
				break;
			case 'password' : // change (admin) password
				$pommo->requireOnce($pommo->_baseDir. 'inc/helpers/subscribers.php');
				$password = PommoHelper::makePassword();
				
				$email = current(PommoSubscriber::getEmail(array('id' => $in['subscriber_id'])));
				
				$config = PommoAPI::configGet(array(
					'admin_username',
					'admin_email'
				));

				// see if we're updating the administrator's password.				
				if ($email == $config['admin_email']) {
					$sql = "UPDATE {$dbo->table['config']} SET config_value='" . md5($password) . "' WHERE config_name='admin_password'";
				if ($dbo->query($sql)) 
					$logger->addErr(sprintf(Pommo::_T('You may now login with username: %1$s and password: %2$s '), '<span style="font-size: 130%">' . $config['admin_username'] . '</span>', '<span style="font-size: 130%">' . $password . '</span>'));
				else {
					$logger->addMsg('Error updating password.');
					return false;
				}
				}
				break;
		}
		
		$query = "
			DELETE FROM ".$dbo->table['subscriber_pending']."
			WHERE pending_id=%i";
		$query = $dbo->prepare($query,array($in['id']));
		if (!$dbo->query($query)) {
			$logger->addErr('PommoPending::perform() -> Error removing pending entry.');
			return false;
		}
		return true;
		
	}
	
}
?>
