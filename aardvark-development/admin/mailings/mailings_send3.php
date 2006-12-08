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
require ('../../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/classes/mailing.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/groups.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/mailings.php');

$pommo->init(array('keep' => TRUE));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();

if (PommoMailing::isCurrent())
	Pommo::kill(sprintf(Pommo::_T('A Mailing is currently processing. Visit the %s Status %s page to check its progress.'),'<a href="mailing_status.php">','</a>'));

$input = $pommo->get('mailingData');


// redirect (restart) if body or group id are null...
if (empty($input['mailgroup']) || empty($input['body'])) {
	Pommo::redirect('mailings_send.php');
}

if ($pommo->_config['demo_mode'] == 'on')
	$logger->addMsg(Pommo::_T('Demonstration Mode is on. No Emails will be sent.'));


$group = new PommoGroup($input['mailgroup'], 1);

$input['tally'] = $group->_tally;
$input['group'] = $group->_name;

// if sendaway variable is set (user confirmed mailing parameters), send mailing & redirect.
if (!empty ($_GET['sendaway'])) {
	if ($input['tally'] > 0) {
		$mailing = PommoMailing::make(array(), TRUE);
		$input['status'] = 1;
		$input['current_status'] = 'stopped';
		$input['command'] = 'restart';
		$mailing = PommoHelper::arrayIntersect($input, $mailing);
		
		$code = PommoMailing::add($mailing);
		if(!PommoMailing::queueMake($group->_memberIDs))
			Pommo::kill('Unable to populate queue');
			
		if (!PommoHelperMailings::spawn($pommo->_baseUrl.'admin/mailings/mailings_send4.php?securityCode='.$code))
			Pommo::kill('Unable to spawn background mailer');
		
		sleep(1);
		Pommo::redirect('mailing_status.php');
		
		//die ($pommo->_baseUrl.'admin/mailings/mailings_send4.php?securityCode='.$securityCode);
	}
	else {
		$logger->addMsg(Pommo::_T('Cannot send a mailing to 0 subscribers!'));
	}
}

$smarty->assign($input);
$smarty->display('admin/mailings/mailings_send3.tpl');
?>
