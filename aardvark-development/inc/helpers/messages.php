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

class PommoHelperMessages {

	// send a confirmation message
 	// accepts to address (str) [email]
 	// accepts a confirmation code (str)
 	// accepts a confirmation type (str) either; 'subscribe', 'activate', 'password', 'update'
	function sendConfirmation($to, $confirmation_key, $type) {
	 	global $pommo;
		$logger = & $pommo->_logger;
		
		if (empty($confirmation_key) || empty ($to) || empty($type)) 
			return false;
		
		$dbvalues = PommoAPI::configGet('messages');
		$messages = unserialize($dbvalues['messages']);

		$subject = $messages[$type]['sub'];
	
		$url = ($type == 'activate') ? 
			$pommo->_http.$pommo->_baseUrl.'user/update_activate.php?codeTry=true&Email='.$to.'&code='.$confirmation_key :
			$pommo->_http.$pommo->_baseUrl.'user/confirm.php?code='.$confirmation_key;
			
		$body = preg_replace('@\[\[URL\]\]@i',$url,$messages[$type]['msg']);
		
		if ($type == 'activate') 
			$body = preg_replace('@\[\[CODE\]\]@i',$confirmation_key,$body);
	
		
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
	
	function resetDefault($section = 'all') {
		global $pommo;
		$dbo =& $pommo->_dbo;

		$messages = array();
		if ($section != 'all') {
			$config = PommoAPI::configGet(array('messages'));
			$messages = unserialize($config['messages']);
		}

		if ($section == 'all' || $section == 'subscribe') {
		$messages['subscribe'] = array();
		$messages['subscribe']['msg'] = sprintf(Pommo::_T('You have requested to subscribe to %s. We would like to validate your email address before adding you as a subscriber. Please click the link below to be added ->'), $pommo->_config['list_name'])."\r\n\t[[url]]\r\n\r\n".Pommo::_T('If you have received this message in error, please ignore it.');
		$messages['subscribe']['sub'] = Pommo::_T('Subscription request'); 
		$messages['subscribe']['suc'] = Pommo::_T('Welcome to our mailing list. Enjoy your stay.');
		}
		
		if ($section == 'all' || $section == 'activate') {
		$messages['activate'] = array();
		$messages['activate']['msg'] =  sprintf(Pommo::_T('You have requested to activate your records for %s.'),$pommo->_config['list_name']).' '.sprintf(Pommo::_T('Your activation code is %s'),"[[CODE]]\r\n\r\n").Pommo::_T('You can access your records by visiting the link below ->')."\r\n\t[[url]]\r\n\r\n".Pommo::_T('If you have received this message in error, please ignore it.');
		$messages['activate']['sub'] = Pommo::_T('Verify your address'); 
		}
		
		
		if ($section == 'all' || $section == 'password') {
		$messages['password'] = array();
		$messages['password']['msg'] =  sprintf(Pommo::_T('You have requested to change your password for %s.'),$pommo->_config['list_name']).' '.Pommo::_T('Please validate this request by clicking the link below ->')."\r\n\t[[url]]\r\n\r\n".Pommo::_T('If you have received this message in error, please ignore it.');
		$messages['password']['sub'] = Pommo::_T('Change Password request'); 
		$messages['password']['suc'] = Pommo::_T('Your password has been reset. Enjoy!');
		}
		
		if ($section == 'all' || $section == 'unsubscribe') {
		$messages['unsubscribe'] = array();
		$messages['unsubscribe']['suc'] = Pommo::_T('You have successfully unsubscribed. Enjoy your travels.');
		}
		
		if ($section == 'all' || $section == 'update') {
			$messages['update'] = array();
			$messages['update']['msg'] =  sprintf(Pommo::_T('You have requested to update your records for %s.'),$pommo->_config['list_name']).' '.Pommo::_T('Please validate this request by clicking the link below ->')."\n\n\t[[url]]\n\n".Pommo::_T('If you have received this message in error, please ignore it.');
			$messages['update']['sub'] = Pommo::_T('Update Records request'); 
			$messages['update']['suc'] = Pommo::_T('Your records have been updated. Enjoy!');
		}

		$input = array('messages' => serialize($messages));
		PommoAPI::configUpdate($input, TRUE);

		return $messages;
	}
}