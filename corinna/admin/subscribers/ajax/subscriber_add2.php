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
require ('../../../bootstrap.php');
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

if (!PommoHelper::isEmail($_POST['Email']))
	jsonKill(Pommo::_T('Error adding subscriber.').'<br>'.Pommo::_T('Invalid Email.'));

if(count(PommoHelper::emailExists($_POST['Email'])) > 0)
		jsonKill(Pommo::_T('Error adding subscriber.').'<br>'.Pommo::_T('Email address already exists. Duplicates are not allowed.'));

$subscriber = array(
	'email' => $_POST['Email'],
	'registered' => time(),
	'ip' => $_SERVER['REMOTE_ADDR'],
	'status' => 1,
	'data' => $_POST['d']);

$flag = false;
if (!PommoValidate::subscriberData($subscriber['data'],array('active' => FALSE, 'ignore' => TRUE))) {
	if(!isset($_GET['force']))
		jsonKill(Pommo::_T('Error adding subscriber.').'<br>'.Pommo::_T('Invalid or missing information.').'<br>'.implode("<br>", $logger->getAll()));
		
	$flag = true;
	$subscriber['flag'] = 9; // 9 for "update"
}

$key = PommoSubscriber::add($subscriber);
if (!$key)
	jsonKill(Pommo::_T('Error adding subscriber.'));
	

// some homebrew json.. ;(
$msg = ($flag) ? 
	sprintf(Pommo::_T('Subscriber %s added!'),$_POST['Email']).' '.Pommo::_T('Subscriber has been flagged for update due to invalid or missing information.') :
	sprintf(Pommo::_T('Subscriber %s added!'),$_POST['Email']);

$json = 'email: "'.$subscriber['email'].'",registered: "'.$subscriber['registered'].'",touched: "'.$subscriber['registered'].'",ip: "'.$subscriber['ip'].'"';
foreach($subscriber['data'] as $key => $val) 
	$json .= ",$key: \"".htmlspecialchars($val)."\"";

$json = "{success: true, key: $key, msg: \"".$msg."\", data: {".$json."} }";
die($json);
?>