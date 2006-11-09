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
 
 class PommoHelperConfig {
 	
 	function messageResetDefault($section = 'all') {
		global $pommo;
		$dbo =& $pommo->_dbo;
	
		$messages = array();
		if ($section != 'all') {
			$config = PommoAPI::configGet(array('messages'));
			$messages = unserialize($config['messages']);
		}
		
		if ($section == 'all' || $section == 'subscribe') {
		$messages['subscribe'] = array();
		$messages['subscribe']['msg'] = sprintf(Pommo::_T('You have requested to subscribe to %s. We would like to validate your email address before adding you as a subscriber. Please click the link below to be added ->'), $pommo->_config['list_name'])."\n\t[[url]]\n\n".Pommo::_T('If you have received this message in error, please ignore it.');
		$messages['subscribe']['sub'] = Pommo::_T('Subscription request'); 
		$messages['subscribe']['suc'] = Pommo::_T('Welcome to our mailing list. Enjoy your stay.');
		}
		
		if ($section == 'all' || $section == 'unsubscribe') {
		$messages['unsubscribe'] = array();
		$messages['unsubscribe']['msg'] = sprintf(Pommo::_T('You have requested to unsubscribe from %s.'),$pommo->_config['list_name']).Pommo::_T('Please validate this request by clicking the link below ->')."\n\t[[url]]\n\n".Pommo::_T('If you have received this message in error, please ignore it.');
		$messages['unsubscribe']['sub'] = Pommo::_T('Unsubscription request'); 
		$messages['unsubscribe']['suc'] = Pommo::_T('You have successfully unsubscribed. Enjoy your travels.');
		}
		
		if ($section == 'all' || $section == 'password') {
		$messages['password'] = array();
		$messages['password']['msg'] =  sprintf(Pommo::_T('You have requested to change your password for %s.'),$pommo->_config['list_name']).Pommo::_T('Please validate this request by clicking the link below ->')."\n\t[[url]]\n\n".Pommo::_T('If you have received this message in error, please ignore it.');
		$messages['password']['sub'] = Pommo::_T('Change Password request'); 
		$messages['password']['suc'] = Pommo::_T('Your password has been reset. Enjoy!');
		}
		
		if ($section == 'all' || $section == 'update') {
		$messages['update'] = array();
		$messages['update']['msg'] =  sprintf(Pommo::_T('You have requested to change your password for %s.'),$pommo->_config['list_name']).Pommo::_T('Please validate this request by clicking the link below ->')."\n\t[[url]]\n\n".Pommo::_T('If you have received this message in error, please ignore it.');
		$messages['update']['sub'] = Pommo::_T('Update Records request'); 
		$messages['update']['suc'] = Pommo::_T('Your records have been updated. Enjoy!');
		}
		
		$input = array('messages' => serialize($messages));
		PommoAPI::configUpdate($input, TRUE);
		
		return $messages;
}

 }
?>
