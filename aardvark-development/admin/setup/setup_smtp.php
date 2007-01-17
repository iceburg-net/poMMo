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

/**********************************
	INITIALIZATION METHODS
 *********************************/
require ('../../bootstrap.php');
$pommo->init();
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

Pommo::requireOnce($pommo->_baseDir . 'inc/lib/phpmailer/class.phpmailer.php');
Pommo::requireOnce($pommo->_baseDir . 'inc/lib/phpmailer/class.smtp.php');


/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();
$smarty->prepareForForm();
$smarty->assign('returnStr', Pommo::_T('Configure'));

// Read user requested changes	
if (!empty ($_POST['addSmtpServer'])) {
	$server = array (
		'host' => 'mail.localhost',
		'port' => '25',
		'auth' => 'off',
		'user' => '',
		'pass' => ''
	);
	$input['smtp_' . key($_POST['addSmtpServer'])] = serialize($server);
	PommoAPI::configUpdate($input, TRUE);
}
elseif (!empty ($_POST['updateSmtpServer'])) {
	$key = key($_POST['updateSmtpServer']);
	$server = array (
		'host' => $_POST['host'][$key], 'port' => $_POST['port'][$key], 'auth' => $_POST['auth'][$key], 'user' => $_POST['user'][$key], 'pass' => $_POST['pass'][$key]);
	$input['smtp_' . $key] = serialize($server);
	PommoAPI::configUpdate( $input, TRUE);
}
elseif (!empty ($_POST['deleteSmtpServer'])) {
	$input['smtp_' . key($_POST['deleteSmtpServer'])] = '';
	PommoAPI::configUpdate( $input, TRUE);
}
elseif (!empty ($_POST['throttle_SMTP'])) {
	$input['throttle_SMTP'] = $_POST['throttle_SMTP'];
	PommoAPI::configUpdate( $input);
}

// Get the SMTP settings from DB
$smtpConfig = PommoAPI::configGet(array (
	'smtp_1',
	'smtp_2',
	'smtp_3',
	'smtp_4',
	'throttle_SMTP'
));

$smtp[1] = unserialize($smtpConfig['smtp_1']);
$smtp[2] = unserialize($smtpConfig['smtp_2']);
$smtp[3] = unserialize($smtpConfig['smtp_3']);
$smtp[4] = unserialize($smtpConfig['smtp_4']);

if (empty ($smtp[1]))
	$smtp[1] = array (
		'host' => 'mail.localhost',
		'port' => '25',
		'auth' => 'off',
		'user' => '',
		'pass' => ''
	);

// Test the servers
$addServer = FALSE;
$smtpStatus = array ();
for ($i = 1; $i < 5; $i++) {

	if (empty ($smtp[$i])) {
		if (!$addServer)
			$addServer = $i;
		continue;
	}

	$test[$i] = new PHPMailer();

	$test[$i]->Host = (empty ($smtp[$i]['host'])) ? null : $smtp[$i]['host'];
	$test[$i]->Port = (empty ($smtp[$i]['port'])) ? null : $smtp[$i]['port'];
	if (!empty ($smtp[$i]['auth']) && $smtp[$i]['auth'] == 'on') {
		$test[$i]->SMTPAuth = TRUE;
		$test[$i]->Username = (empty ($smtp[$i]['user'])) ? null : $smtp[$i]['user'];
		$test[$i]->Password = (empty ($smtp[$i]['pass'])) ? null : $smtp[$i]['pass'];
	}
	if (@ $test[$i]->SmtpConnect()) {
		$smtpStatus[$i] = TRUE;
		$test[$i]->SmtpClose();
	} else {
		$smtpStatus[$i] = FALSE;
	}
}

$smarty->assign('addServer',$addServer);
$smarty->assign('smtpStatus',$smtpStatus);
$smarty->assign('smtp', $smtp);
$smarty->assign('throttle_SMTP', $smtpConfig['throttle_SMTP']);

$smarty->display('admin/setup/setup_smtp.tpl');
Pommo::kill();
?>