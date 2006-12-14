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

// TODO -> Add auto firewalling [DOS protection] scripts here.. ie. if Bad/no code received by same IP 3 times, temp/perm ban. 
//  If page is being bombed/DOSed... temp shutdown. should all be handled inside @ _IS_VALID or fireup(); ..

/**********************************
	INITIALIZATION METHODS
*********************************/
require('../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/pending.php');

$pommo->init(array('authLevel' => 0, 'noSession' => true));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();

if (empty($_GET['code'])) {
	$logger->addMsg(Pommo::_T('No code given.'));
	$smarty->display('user/confirm.tpl');
	Pommo::kill();
}

// lookup code
$pending = PommoPending::get($_GET['code']);

if (!$pending) {
	$logger->addMsg(Pommo::_T('Invalid code! Make sure you copied it correctly from the email.'));
	$smarty->display('user/confirm.tpl');
	Pommo::kill();
}

// Load success messages and redirection URL from config
$config = PommoAPI::configGet(array (
	'site_success',
	'messages',
));
$messages = unserialize($config['messages']);

if(PommoPending::perform($pending)) {
	switch ($pending['type']) {
		case "add" :
			$logger->addMsg($messages['subscribe']['suc']);
			if (!empty($config['site_success']))
				Pommo::redirect($config['site_success']);
			break;
		case "change" :
			$logger->addMsg($messages['update']['suc']);
			break;
		case "del" :
			$logger->addMsg($messages['unsubscribe']['suc']);
			break;
		case "password" :
			break;
		default :
			$logger->addMsg('Unknown Pending Type.');
			break;
	}
}
$smarty->display('user/confirm.tpl');
Pommo::kill();
?>