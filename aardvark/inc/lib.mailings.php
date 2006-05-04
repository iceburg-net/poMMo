<?php /** [BEGIN HEADER] **
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
 
 // TODO: Combine these mailing confirmation functions... they repeat.
 
 /** 
 * Don't allow direct access to this file. Must be called from
elsewhere
*/
defined('_IS_VALID') or die('Move along...');

// send a confirmation email
function bmSendConfirmation($to, $confirmation_key, $type) {
	if (empty($confirmation_key) || empty ($to) || empty($type)) 
		return false;
	
	global $poMMo;
	$logger = & $poMMo->logger;
		
	$dbvalues = $poMMo->getConfig(array('messages'));
	$messages = unserialize($dbvalues['messages']);
	
	$subject = $messages[$type]['sub'];
	
	$url = bm_http.bm_baseUrl.'/user/confirm.php?code='.$confirmation_key;
	$body = preg_replace('@\[\[URL\]\]@i',$url,$messages[$type]['msg']);  
	
	if (empty($subject) || empty($body))
		return false;
	
	require_once(bm_baseDir.'/inc/class.pommoer.php');
	$message = new poMMoer;
	
	// allow mail to be sent, even if demo mode is on
	$message->toggleDemoMode("off");
	
	// send the confirmation mail
	$message->prepareMail($subject, $body);
	if ($message->bmSendmail($to)) {
		$message->toggleDemoMode();
		return true; // mailing was a sucess...
	}
	// reset demo mode to default
	$message->toggleDemoMode();
	
	// PHASE OUT .. use logger!
	$msg = "<b>Error</b>: Confirmation Mailing Not Sent.";
	$_SESSION["poMMo"]->addMessage($msg);
	
	$logger->addErr(_T('Unable to send confirmation mailing. Contact Administrator.'));
	return false;	
}

// Sends a "test" mailing to an address, returns <string> status.
function bmSendTestMailing(&$to, &$input) {
	require_once (bm_baseDir.'/inc/class.pommoer.php');
	require_once (bm_baseDir.'/inc/lib.txt.php');
		$Mail = new poMMoer($input['fromname'], $input['fromemail'], $input['frombounce']);
		$altbody = NULL;
		$html = FALSE;
		if ($input['mailtype'] == 'html')
			$html = TRUE;
		if (!empty($input['altbody']) && $input['altInclude'] == 'yes')
			$altbody = str2str($input['altbody']);
		if (!$Mail->prepareMail(str2str($input['subject']), str2str($input['body']), $html, $altbody)) 
			return '(Errors Preparing Test)';
		
		if (!$Mail->bmSendmail($to))
			return _T('Error Sending: ').$_SESSION['poMMo']->getMessages();
		return sprintf(_T('Test sent to %s'), $to);
}
	
?>