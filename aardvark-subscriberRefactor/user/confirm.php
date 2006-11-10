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


require ('../bootstrap.php');
require_once ($pommo->_baseDir . '/inc/db_subscribers.php');

$pommo = & fireup('keep');
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();

if (empty ($_GET['code'])) {
	$logger->addMsg(Pommo::_T('No code given.'));
	$smarty->display('user/confirm.tpl');
	Pommo::kill();
}

// lookup code
$sql = "SELECT type,code,email FROM {$dbo->table['pending']} WHERE code='" . $_GET['code'] . "'";
$row = $row = mysql_fetch_assoc($dbo->query($sql));

if (empty ($row)) {
	$logger->addMsg(Pommo::_T('Invalid code! Make sure you copied it correctly from the email.'));
	$smarty->display('user/confirm.tpl');
	Pommo::kill();
}

// Load success messages and redirection URL from config
$config = PommoAPI::configGet(array (
	'site_success',
	'messages',
	'admin_username',
	'admin_password',
	'admin_email'
));
$messages = unserialize($config['messages']);

switch ($row['type']) {
	case "add" :

		if (!empty ($config['site_success']))
			$redirectURL = $config['site_success'];

		dbSubscriberAdd($dbo, $row['code']);
		$logger->addMsg($messages['subscribe']['suc']);

		if (isset ($redirectURL))
			Pommo::redirect($redirectURL, Pommo::_T('Subscription Successful. Redirecting...'));

		break;
	case "change" :
		$logger->addMsg($messages['update']['suc']);
		dbSubscriberUpdate($dbo, $row['code']);
		break;
	case "del" :

		dbSubscriberRemove($dbo, $row['code']);
		$logger->addMsg($messages['unsubscribe']['suc']);
		break;
	case "password" :

		// TODO -> create dbPasswordReset() fo dis
		$newPassword = substr(md5(rand()), 0, 5);

		// see if we're updating the administrator's password.				
		if ($row['email'] == $config['admin_email']) {
			$sql = "UPDATE {$dbo->table['config']} SET config_value='" . md5($newPassword) . "' WHERE config_name='admin_password'";
			if ($dbo->query($sql)) {
				$logger->addMsg($messages['password']['suc']);
				$logger->addErr(sprintf(Pommo::_T('You may now login with username: %1$s and password: %2$s '), '<span style="font-size: 130%">' . $config['admin_username'] . '</span>', '<span style="font-size: 130%">' . $newPassword . '</span>'));
				dbPendingDel($dbo, $row['code']);
			} else
				$logger->addMsg(Pommo::_T('Could not reset password. Contact Administrator.'));
		} else
			$logger->addMsg(Pommo::_T('Can only reset the administrator password'));
		break;
	default :
		$logger->addMsg(Pommo::_T('Unknown type. Contact Administrator.'));
		break;
}

$smarty->display('user/confirm.tpl');
Pommo::kill();
?>