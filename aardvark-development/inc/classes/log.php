<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
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

// TODO -> class will eventually extend to act as error handler
// TODO -> implement get limit @ some point.... ;)

// NOTE: messages are cleared upon page load (as inc/classes/pommo.php calls new constructor)
// TODO -> add message "revival" from SESSION (if ever deemed necessary)
class PommoLog {
	
	var $_errors;
	var $_log;
	var $_messages;
	var $_verbosity;
	var $_escape; // htmlspecialchars escaping (disabled by default)
	
	function PommoLog($verbosity = 3) {
		$this->_errors = array();
		$this->_messages = array();
		$this->_log = FALSE;
		$this->_verbosity = $verbosity;
		$this->_escape = false; 
	}
	
	function toggleLogging($toggle = TRUE) {
		$this->_log = $toggle;
		return $toggle;
	}
	
	function toggleEscaping($toggle = TRUE) {
		$this->_escape = $toggle;
		return $toggle;
	}
	
	function Add(& $msgs, $level, $timestamp, &$stack ) {
		if ($this->_verbosity > $level)
			// don't add message if verbosity level is below indicated message level
			return false;
		if (!is_array($msgs))
			$msgs = array($msgs);
		if ($timestamp)
			$timestamp = date('H:i:s').' > ';
		foreach($msgs as $msg)
			$stack[] = $timestamp.$msg;
		return true;
	}
	
	function addMsg($messages, $level = 3, $timestamp = FALSE) {
		return $this->Add($messages, $level, $timestamp, $this->_messages);
	}
	
	function addErr($messages, $level = 3, $timestamp = FALSE) {
		return $this->Add($messages, $level, $timestamp, $this->_errors);
	}
	
	function & Get($limit, $clear, & $stack) {
		$msgs = $stack;
		if ($clear)
			$stack = array();
		
		if ($this->_escape)
			array_walk($msgs,'htmlspecialchars');
		return $msgs;
	}
	
	function getMsg($limit = FALSE, $clear = TRUE) {
		return $this->Get($limit,$clear,$this->_messages);
	}
	
	function getErr($limit = FALSE, $clear = TRUE) {
		return $this->Get($limit,$clear,$this->_errors);
	}
	
	function getAll($limit = FALSE, $clear = TRUE) {
		return array_merge($this->Get($limit,$clear,$this->_errors),$this->Get($limit,$clear,$this->_messages));
	}
	
	function isMsg() {
		return count($this->_messages);	
	}
	
	function isErr() {
		return count($this->_errors);
	}
	
	function clear() {
		$this->_messages = array();
		$this->_errors = array();
		return true;		
	}
}
?>