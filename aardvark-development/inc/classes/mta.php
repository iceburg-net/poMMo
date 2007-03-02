<?php
/**
 * Copyright (C) 2005, 2006, 2007  Brice Burgess <bhb@iceburg.net>
 * 
 * This file is part of poMMo (http://www.pommo.org)
 * 
 * poMMo is free software; you can redistribute it and/or modify 
 * it under the terms of the GNU General Public License as published 
 * by the Free Software Foundation; either version 2, or any later version.
 * 
 * poMMo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See
 * the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with program; see the file docs/LICENSE. If not, write to the
 * Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 */
 
 // poMMo MTA - poMMo's background mailer
 
 // includes
$GLOBALS['pommo']->requireOnce($GLOBALS['pommo']->_baseDir. 'inc/classes/mailctl.php');
$GLOBALS['pommo']->requireOnce($GLOBALS['pommo']->_baseDir. 'inc/classes/mailer.php');
$GLOBALS['pommo']->requireOnce($GLOBALS['pommo']->_baseDir. 'inc/classes/throttler.php');
$GLOBALS['pommo']->requireOnce($GLOBALS['pommo']->_baseDir. 'inc/helpers/mailings.php');
$GLOBALS['pommo']->requireOnce($GLOBALS['pommo']->_baseDir. 'inc/helpers/subscribers.php');

 class PommoMTA {

	// Attempted number of mails to process per queue batch.
	var $_queueSize;

	// Number of seconds the MTA process is allowed to run for.
	var $_maxRunTime;

	// Time the MTA process began 
	var $_start;

	// (bool) Skip Security checks
	var $_skipSecurity = false;
	
	// The ID of the current mailing
	var $_id;
	
	// serial of mailing, prevents 2 scripts from working on the same mailing
	var $_serial;
	
	// security code - prevent oustide interferrence
	var $_code;
	
	// (bool) True if this is a test mailing
	var $_test;
	
	// the current mailing (object) body, serial, code, etc!
	var $_mailing;
	
	// the poMMo mailer
	var $_mailer;
	
	// the current queue, holds an array of subscriber objects
	var $_queue;
	var $_sent;
	var $_failed;
	
	// the email hash array('email' => 'subscriber_id')
	var $_hash;
	
	// the throttle object
	var $_throttler;	
	
	function PommoMTA($args = array()) {
		
		$defaults = array (
			'queueSize' => 100,
			'maxRunTime' => 80,
			'skipSecurity' => false,
			'start' => time(),
			'serial' => false,
			'spawn' => 1
		);
		$p = PommoAPI :: getParams($defaults, $args);
		
		foreach($p as $k => $v)
			$this->{'_'.$k} = $v;
			
		// protect against safe mode timeouts
		if (ini_get('safe_mode'))
			$this->_maxRunTime = ini_get('max_execution_time') - 10;
		else
			set_time_limit(0);
			
		// protect against user (client) abort
		ignore_user_abort(true);
		
		// register shutdown method
   		register_shutdown_function(array(&$this, "shutdown"));
   		
   		// register error handler
   		set_error_handler(array(&$this, "error"));
   		
   		// set parameters from URL
		$this->_code = (empty($_GET['code'])) ? 'invalid' : $_GET['code'];
		$this->_test = (empty($_GET['test'])) ? false : true;
		$this->_id = (isset($_GET['id']) && is_numeric($_GET['id'])) ? $_GET['id'] : false;
		
		// verify and initialize the current mailing
		$p = array(
			'active' => true,
			'code' => (($this->_skipSecurity) ? null : $this->_code),
			'id' => (($this->_id) ? $this->_id : null));
			
		$this->_mailing = current(PommoMailing::get($p));
		if(!is_numeric($this->_mailing['id']))
			$this->shutdown('Unable to initialize mailing.');
		$this->_id = $this->_mailing['id'];
		
		// security routines
		if($this->_mailing['end'] > 0)
			$this->shutdown(Pommo::_T('Mailing Complete.'));
			
		if(empty($this->_mailing['serial']))
			if (!PommoMailCtl::mark($this->_serial,$this->_id))
				$this->shutdown('Unable to serialize mailing (ID: '.$this->_id.' SERIAL: '.$this->_serial.')');
			
		if($this->_maxRunTime < 15)
			$this->shutdown('Max Runtime must be at least 15 seconds!');
			
		$this->_queue = $this->_sent = $this->_failed = array();
			
		return;
	}
	
	// polls the current mailing
	function poll() {
		global $pommo;
		$dbo =& $pommo->_dbo;
		$logger =& $pommo->_logger;
		
		$query = "
			SELECT command, current_status, serial
			FROM ". $dbo->table['mailing_current']."
			WHERE current_id=%i";
		$query = $dbo->prepare($query,array($this->_id));
		
		$row = mysql_fetch_assoc($dbo->query($query));
		if (empty($row))
			$this->shutdown('Unable to poll mailing.');
			

		switch ($row['command']) {
		case 'restart': // terminate if this is not a "fresh"/"new" process
			if (is_object($this->_mailer)) {
				$this->_mailer->SmtpClose();
				$this->shutdown(sprintf(Pommo::_T('Restarting Mailing #%s'),$this->_id));
			}
			
			$query = "
				UPDATE ". $dbo->table['mailing_current']."
				SET
					serial=%i,
					command='none',
					current_status='started'
					WHERE current_id=%i";
			$query = $dbo->prepare($query,array($this->_serial,$this->_id));
			if (!$dbo->query($query))
				$this->shutdown('Database Query failed: '.$query);

			$logger->addMsg(sprintf(Pommo::_T('Started Mailing #%s'),$this->_id), 3);
			
			break;
	
		case 'stop':
			if (is_object($this->_mailer))
				$this->_mailer->SmtpClose();
				
			$query = "
				UPDATE ". $dbo->table['mailing_current']."
				SET
					command='none',
					current_status='stopped' WHERE current_id=%i";
			$query = $dbo->prepare($query,array($this->_id));
			if (!$dbo->query($query))
				$this->shutdown('Database Query failed: '.$query);
				
			$logger->addMsg(sprintf(Pommo::_T('Stopped Mailing #%s'),$this->_id), 3, TRUE);
			break;
			
		case 'cancel':
			PommoMailCtl::finish($this->_id, true);
			$this->shutdown(Pommo::_T('Mailing Cancelled.'), true);
			break;
				
		default :
			if (!$this->_skipSecurity && $row['serial'] != $this->_serial) 
				$this->shutdown('Terminating due to Serial Mismatch!');
			if ($row['current_status'] == 'stopped')
				$this->shutdown(Pommo::_T('You must restart the mailing.'));	
			
			// upate the timestamp
			$query = "UPDATE ". $dbo->table['mailing_current']." SET touched=NULL WHERE current_id=%i";
			$query = $dbo->prepare($query,array($this->_id));
			if (!$dbo->query($query))
				$this->shutdown('Database Query failed: '.$query);
			break;
		}
		
		// update the notices, queue
		$this->update();
		
		return true;
	}
	
	
	// pulls from the queue
	function pullQueue() {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$relay = 1; // switched to static relay in PR15, will utilize swiftmailer's multi-SMTP support.
		
		// check mailing status + update queue, notices
		$this->poll();
		
		// ensure queue is active
		$query = "
			SELECT COUNT(subscriber_id) 
			FROM ".$dbo->table['queue']."
			WHERE status=0";
		if($dbo->query($query,0) < 1) { // no unsent mails left in queue
			$this->_mailer->SmtpClose();
			PommoMailCtl::finish($this->_id);
			$this->shutdown(Pommo::_T('Mailing Complete.'));
		}
		
		// release lock on queue
		$query = "
			UPDATE ".$dbo->table['queue']."
			SET smtp=0 
			WHERE smtp=%i";
		$query = $dbo->prepare($query, array($relay));
		if(!$dbo->query($query))
			$this->shutdown('Database Query failed: '.$query);
		
		// mark our working queue
		$query = "
			UPDATE ".$dbo->table['queue']."
			SET smtp=%i
			WHERE smtp=0 AND status=0
			LIMIT %i";
		$query = $dbo->prepare($query,array($relay,$this->_queueSize));
		if(!$dbo->query($query))
			$this->shutdown('Database Query failed: '.$query);
		
		// pull our queue
		$query = "
			SELECT subscriber_id
			FROM ".$dbo->table['queue']."
			WHERE smtp=%i";
		$query = $dbo->prepare($query,array($relay));
		
		if(!$dbo->query($query))
			$this->shutdown('Database Query failed: '.$query);

		$this->_queue =& PommoSubscriber::get(array(
			'id' => $dbo->getAll(false, 'assoc', 'subscriber_id')));
		
		if (empty($this->_queue))
			$this->shutdown('Unable to pull queue.');
			
		return;
	}
	
	// pushes queue into throttler
	function pushThrottler() {
		$this->_throttler->clearQueue();
		
		// seperate emails into an array ([email],[domain]) to feed to throttler
		$emails = array();
		$emailHash = array(); // used to quickly lookup subscriberID based off email
		foreach($this->_queue as $s) {
			array_push($emails, array(
				$s['email'],
				substr($s['email'],strpos($s['email'],'@')+1)
				)
			);
			$emailHash[$s['email']] = $s['id'];
		}
		
		$this->_hash = & $emailHash;
		$this->_throttler->pushQueue($emails);
	}
	
	// continually sends mails from the queue until mailing completes or max runtime reached
	function processQueue() {
		global $pommo;
		$logger =& $pommo->_logger;
		
		$die = false;
		$timer = time();
		while(!$die) {
			
			// repopulate throttler's queue if empty
			if (!$this->_throttler->mailsInQueue()) {
				$this->pullQueue(); // get unsent
				$this->pushThrottler(); // push unsent
			}
			
			// attempt to pull email from throttler's queue
			$mail = $this->_throttler->pullQueue();
			
			// if an email was returned, send it.
			if (!empty($mail)) {
				
				// set $personal as subscriber if personalization is enabled 
				$personal = FALSE;
				if ($pommo->_session['personalization'])
					$personal =& $this->_queue[$this->_hash[$mail[0]]];
				
				if (!$this->_mailer->bmSendmail($mail[0], $personal)) // sending failed, write to log  
					$this->_failed[] = $mail[0];
				else
					$this->_sent[] = $mail[0];
				
		
				// If throttling by bytes (bandwith) is enabled, add the size of the message to the throttler
				if ($this->_byteMask > 1) {
					$bytes = $this->_mailer->GetMessageSize();
					if ($this->_byteMask > 2)
						$this->_throttler->updateBytes($bytes, $mail[1]);
					else
						$this->_throttler->updateBytes($bytes);
					$logger->addMsg('Added ' . $bytes . ' bytes to throttler.', 1);
				}
			}
			
			
			 // check to see if we have exceeded max runtime
			 if ((time() - $this->_start) > $this->_maxRunTime)
				$die = TRUE;
				
			// update & poll every 10 seconds || if logger is large
			if (!$die && ((time() - $timer) > 9) || count($logger->_messages) > 40) {
				$this->poll();
				$timer = time();
			}
		}
		
		// don't respawn if this is a test mailing
		if ($this->_test) {
			PommoMailCtl::finish($this->_id,TRUE,TRUE);
			PommoSubscriber::delete($this->_queue[0]['id']);
			$this->_mailer->SmtpClose();
			session_destroy();
			die();
		}
		
		$this->_mailer->SmtpClose();
		if(!PommoMailCtl::respawn(array('code' => $this->_code, 'serial' => $this->_serial, 'id' => $this->_id)))
			$this->shutdown('*** RESPAWN FAILED! ***');
		$this->shutdown(sprintf(Pommo::_T('Runtime (%s seconds) reached, respawning.'),$this->_maxRunTime), false);
	}
	
	// updates the queue and notices
	// accepts a array of failed emails
	// accepts a array of sent emails
	function update() {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		if (!empty($this->_sent)) {
			$a = array();
			foreach($this->_sent as $e)
				$a[] = $this->_hash[$e];
			
			$query = "
				UPDATE ".$dbo->table['queue']."
				SET status=1
				WHERE subscriber_id IN(%q)";
			$query = $dbo->prepare($query,array($a));
			
			if (!$dbo->query($query))
				$this->shutdown('Database Query failed: '.$query);
				
		}
		
		if (!empty($this->_failed)) {
			$a = array();
			foreach($this->_failed as $e)
				$a[] = $this->_hash[$e];
			
			$query = "
				UPDATE ".$dbo->table['queue']."
				SET status=2
				WHERE subscriber_id IN(%q)";
			$query = $dbo->prepare($query,array($a));
			
			if (!$dbo->query($query))
				$this->shutdown('Database Query failed: '.$query);
		}
			
		// add notices
		PommoMailCtl::addNotices($this->_id);
		
		// reset sent/failed
		$this->_sent = $this->_failed = array();
		return;
	}
	
	
	function attach($name, &$obj) {
		$this->{$name} =& $obj;
		return;
	}
	
	
	function shutdown($msg = false, $destroy = true, $error = false) {
		
		// prevent recursion
		static $static = false;
		if($static) exit();
		$static = true;
		
		global $pommo;
		$logger =& $pommo->_logger;
		
		// DATA DUMP *temp*
		if(!$msg || $error) {
			$output = "--- poMMo MTA DEBUG --- \n";
			
	
			$output .= "[[ ERROR ]] \n".$error;
			
			
			$backtrace = (function_exists('debug_backtrace')) ? debug_backtrace() : 'not supported';
			
			if(is_array($backtrace)) {
			$bto = '';
			foreach ($backtrace as $bt) {
				$args = '';
				foreach ($bt['args'] as $a) {
					if (!empty ($args)) {
						$args .= ', ';
					}
					switch (gettype($a)) {
						case 'integer' :
						case 'double' :
							$args .= $a;
							break;
						case 'string' :
							$a = htmlspecialchars(substr($a, 0, 64)) . ((strlen($a) > 64) ? '...' : '');
							$args .= "\"$a\"";
							break;
						case 'array' :
							$args .= 'Array(' . count($a) . ')';
							break;
						case 'object' :
							$args .= 'Object(' . get_class($a) . ')';
							break;
						case 'resource' :
							$args .= 'Resource(' . strstr($a, '#') . ')';
							break;
						case 'boolean' :
							$args .= $a ? 'True' : 'False';
							break;
						case 'NULL' :
							$args .= 'Null';
							break;
						default :
							$args .= 'Unknown';
					}
				}
				@ $bto .= "<b>file:</b> {$bt['line']} - {$bt['file']}<br />\n";
				@ $bto .= "<b>call:</b> {$bt['class']}{$bt['type']}{$bt['function']}($args)<br />\n";
			}
			$backtrace = $bto;
			}
		
			$output .= "[[BACKTRACE]]:".$backtrace."\n\n[[VARIABLES]]\n\n";
			
			$output .= "Connection Aborted: ".((connection_aborted())?'true' : 'false')."\n\n";
			$output .= "MAX EXECUTION: ".ini_get('max_execution_time')."\n\n";
			
			$x = print_r($this,true);
			$output .= "MTA:: \n".$x;
			
			$x = print_r($pommo,true);
			$output .= "\n\nPOMMO:: \n".$x;
			
			if (!$handle = fopen($pommo->_workDir.'/DEBUG'.time(), 'w'))
				$msg = '**** DEBUG FILE COULD NOT BE WRITTEN TO WORK DIRECTORY ****';
			else {
				if (fwrite($handle, $output) === FALSE)
					$msg = '**** DEBUG FILE COULD NOT BE WRITTEN TO WORK DIRECTORY ****';
				elseif(!$error)
					$msg = '**** DEBUG FILE WRITTEN TO WORK DIRECTORY ****';
					
				fclose($handle);
			}
			
		}
		
		$msg = ($msg) ? $msg : '*** ERROR *** PHP Invoked Shutdown Function. Runtime: '.(time() - $this->_start).' seconds.';
		
		$logger->addMsg($msg,3,TRUE);
		echo $msg;
		
		// update queue sent/failed and notices
		$this->update();
		
		if($destroy)
			session_destroy();
			
		exit($msg);
	}
	
	// the error handler
	function error($errno, $errstr, $errfile, $errline, $errcontext) {
		$error = "*** PHP ERROR NO. $errno ***\n";
		$error .= "\tERROR: $errstr \n";
		$error .= "\tFILE: $errfile \n";
		$error .= "\tLINE: $errline \n";
		
		// $this->shutdown('*** ERROR THROWN IN MTA! SEE DEBUG FILE IN WORKDIR ***',true);
		
		switch ($errno) {
			case E_NOTICE:
			case E_USER_NOTICE:
				global $pommo;
				$logger =& $pommo->_logger;
				$logger->addMsg($error,1);	
				break;
			case E_USER_WARNING:
			case E_WARNING:
				global $pommo;
				$logger =& $pommo->_logger;
				$logger->addMsg($error,3);	
				break;
			case E_USER_ERROR:
			case E_ERROR:
				$this->shutdown('*** FATAL ERROR THROWN IN MTA! SEE DEBUG FILE IN WORKDIR ***',true,$error);
				break;
		}
		return;
	}
	
 }
 ?>	