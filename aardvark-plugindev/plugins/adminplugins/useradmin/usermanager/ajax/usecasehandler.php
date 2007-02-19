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
require ('../../../../../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/subscribers.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/validate.php');

$pommo->init(array('noDebug' => TRUE));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

$pommo->toggleEscaping(); // _T and logger responses will be wrapped in htmlspecialchars

function jsonKill($msg) {
	$json = "{success: false, msg: \"".$msg."\"}";
	die($json);
}

/*
if (!PommoHelper::isEmail($_POST['Email']))
	jsonKill(Pommo::_T('Error adding subscriber.').'<br />'.Pommo::_T('Invalid email.'));

if(PommoHelper::isDupe($_POST['Email']))
	jsonKill(Pommo::_T('Error adding subscriber.').'<br />'.Pommo::_T('Email address already exists. Duplicates are not allowed.'));
*/

$user = array(
	'username' => $_POST['username'],
	'userpass' => $_REQUEST['userpass'],
	'userpasscheck' => $_REQUEST['userpasscheck'],
	'usergroup' => $_POST['usergroup']);

$flag = false;
if ($user['userpass'] == $user['userpasscheck']) {
	$flag = TRUE;
}
/*if (!PommoValidate::subscriberData($subscriber['data'],array('active' => FALSE, 'ignore' => TRUE))) {
	if(!isset($_GET['force']))
		jsonKill(Pommo::_T('Error adding subscriber.').'<br />'.Pommo::_T('Invalid or missing information.').'<br />'.implode("<br />", $logger->getAll()));

	$flag = true;
	$subscriber['flag'] = 9; // 9 for "update"
}*/


//$key = PommoSubscriber::add($subscriber);

/*
Pommo::requireOnce($pommo->_baseDir.'plugins/adminplugins/useradmin/usermanager/class.userplugin.php');
Pommo::requireOnce($pommo->_baseDir.'plugins/adminplugins/useradmin/usermanager/class.db_userhandler.php');
$userplugin = new UserPlugin($pommo);
$key = $userplugin->addUser($_REQUEST['username'], $_REQUEST['userpass'], $_REQUEST['userpasscheck'], $_REQUEST['usergroup']);
*/

if (!$key)
	jsonKill(Pommo::_T('Error adding user.'));

// some homebrew json.. ;(
$msg = ($flag) ? 
	sprintf(Pommo::_T('User %s added!'),$_POST['username']).' '.Pommo::_T('User has been flagged for update due to invalid or missing information.') :
	sprintf(Pommo::_T('User %s added!'),$_POST['username']);

$json = 'user: "'.$user['username'].'",pass: "'.$user['userpass'].'",usergroup: "'.$user['usergroup'].'",passcheck: "'.$user['userpasscheck'].'"';
/*foreach($user['data'] as $key => $val) 
	$json .= ",$key: \"".htmlspecialchars($val)."\"";
*/

$json = "{success: true, key: $key, msg: \"".$msg."\", data: {".$json."} }";
die($json);
?>