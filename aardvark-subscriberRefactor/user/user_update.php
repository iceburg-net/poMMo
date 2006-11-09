<?php

/** [BEGIN HEADER] **
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

/**********************************
	INITIALIZATION METHODS
 *********************************/


require ('../bootstrap.php');
require ($pommo->_baseDir . '/inc/lib.validate_subscriber.php');
require_once ($pommo->_baseDir . '/inc/db_subscribers.php');
require_once ($pommo->_baseDir . '/inc/db_fields.php');
require_once ($pommo->_baseDir . '/inc/lib.mailings.php');
require_once ($pommo->_baseDir . '/inc/lib.txt.php');

$pommo = & fireup('keep');
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();

// Prepare for subscriber form -- load in fields + POST/Saved Subscribe Form
$smarty->prepareForSubscribeForm(); 

$_POST['bm_email'] = $smarty->get_template_vars('bm_email');

if (empty($_POST['bm_email']))
		Pommo::redirect('login.php');

// populates form values with subscribers info from DB (called when POST vals not present)
function bmPopulate() {
	global $dbo;
	global $smarty;
	
	$subscribers = & dbGetSubscriber($dbo, str2db($_POST['bm_email']), 'detailed');
	if (empty($subscribers))
		Pommo::redirect('login.php');
	$subscriber_id = & key($subscribers); // subscriber's ID
	$subscriber = & current($subscribers);

	$smarty->assign('original_email', $_POST['bm_email']);
	$smarty->assign('email2', $_POST['bm_email']);
	$smarty->assign('d', $subscriber['data']); 
}

if (!empty ($_POST['update'])) {
	// validate new subscriber info
	if (validateSubscribeForm(FALSE)) {
		// allow user to change their email address
		if ($_POST['original_email'] != $_POST['bm_email'])
			$_POST['d']['newEmail'] = $_POST['bm_email'];
		$code = dbPendingAdd($dbo, 'change', $_POST['original_email'], $_POST['d']);
		if (empty ($code)) {
			$logger->addMsg(Pommo::_T('The system could not process your request. Perhaps you already have requested a change?') . 
			sprintf(Pommo::_T('%s Click Here %s to try again.'),'<a href="'.$pommo->_baseUrl.'user/login.php">','</a>'));
		} else {
			bmSendConfirmation($_POST['original_email'], $code, "update");
			$logger->addMsg(Pommo::_T('Update request received.') . ' ' . Pommo::_T('A confirmation email has been sent. You should receive this letter within the next few minutes. Please follow its instructions.'));
		}
	}
}
elseif (!empty ($_POST['unsubscribe'])) {
	$code = dbPendingAdd($dbo, "del", $_POST['original_email']);
	if (empty ($code))
		$logger->addMsg(Pommo::_T('The system could not process your request. Perhaps you already have requested a change?') .
		sprintf(Pommo::_T('%s Click Here %s to try again.'),'<a href="'.$pommo->_baseUrl.'user/login.php">','</a>'));
	else {
		bmSendConfirmation($_POST['original_email'], $code, "unsubscribe");
		$logger->addMsg(Pommo::_T('Unsubscribe request received.') . ' ' . Pommo::_T('A confirmation email has been sent. You should receive this letter within the next few minutes. Please follow its instructions.'));
	}
	bmPopulate();
} 
else { // both update + unsubsscribe empty...
	bmPopulate();
}


$smarty->display('user/user_update.tpl');
Pommo::kill();
?>