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

class PommoHelperMailings {
 
 	// send a confirmation message
 	// accepts to address (str) [email]
 	// accepts a confirmation code (str)
 	// accepts a confirmation type (str) either; 'subscribe', 'unsubscribe', 'update'
	function sendConfirmation($to, $confirmation_key, $type) {
	 	global $pommo;
		$logger = & $pommo->_logger;
	
		if (empty($confirmation_key) || empty ($to) || empty($type)) 
			return false;
		
		$dbvalues = PommoAPI::configGet('messages');
		$messages = unserialize($dbvalues['messages']);

		$subject = $messages[$type]['sub'];
	
		$url = $pommo->_http.$pommo->_baseUrl.'user/confirm.php?code='.$confirmation_key;
		$body = preg_replace('@\[\[URL\]\]@i',$url,$messages[$type]['msg']);  
	
		if (empty($subject) || empty($body)) 
			return false;
	
		Pommo::requireOnce($pommo->_baseDir.'inc/classes/mailer.php');
		$mail = new PommoMailer();
	
		// allow mail to be sent, even if demo mode is on
		$mail->toggleDemoMode("off");
	
		// send the confirmation mail
		$mail->prepareMail($subject, $body);
		
		$ret = true;
		if (!$mail->bmSendmail($to)) {
			$logger->addErr(Pommo::_T('Error Sending Mail'));
			$ret = false;
		}
		// reset demo mode to default
		$mail->toggleDemoMode();
		return $ret;
	}

	// Sends a "test" mailing to an address, returns <string> status.
	function bmSendTestMailing(&$to, &$input) {
		require_once (bm_baseDir.'inc/class.bmailer.php');
		require_once (bm_baseDir.'inc/lib.txt.php');
			$Mail = new bMailer($input['fromname'], $input['fromemail'], $input['frombounce'],NULL,NULL,$input['charset']);
			$altbody = NULL;
			$html = FALSE;
			if ($input['ishtml'] == 'html')
				$html = TRUE;
			if (!empty($input['altbody']) && $input['altInclude'] == 'yes')
				$altbody = str2str($input['altbody']);
			if (!$Mail->prepareMail(str2str($input['subject']), str2str($input['body']), $html, $altbody)) 
				return '(Errors Preparing Test)';
			
			if (!$Mail->bmSendmail($to))
				return _T('Error Sending Mail');
			return sprintf(_T('Test sent to %s'), $to);
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
