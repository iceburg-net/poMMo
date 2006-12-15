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
Pommo::requireOnce($pommo->_baseDir.'inc/classes/mailctl.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/mailings.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/subscribers.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/validate.php');


$pommo->init(array('noDebug' => TRUE, 'keep' => TRUE));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

$pommo->toggleEscaping(); // _T and logger responses will be wrapped in htmlspecialchars

function jsonKill($msg,$key = 0) {
	PommoSubscriber::delete($key);
	$json = "{success: false, msg: \"".$msg."\"}";
	die($json);
}

$input = $pommo->get('mailingData');

$subscriber = array(
	'email' => $_POST['Email'],
	'registered' => time(),
	'ip' => $_SERVER['REMOTE_ADDR'],
	'status' => 0,
	'data' => $_POST['d']);
	

if(!PommoHelper::isEmail($_POST['Email']))
	jsonKill(Pommo::_T('Invalid Email Address'));


PommoValidate::subscriberData($subscriber['data'],array('active' => FALSE, 'ignore' => TRUE));
$key = PommoSubscriber::add($subscriber);
if (!$key)
	jsonKill('Unable to add test subscriber',$key);


$input['tally'] = 1;
$input['group'] = Pommo::_T("Test Mailing");

$mailing = PommoMailing::make(array(), TRUE);
$input['status'] = 1;
$input['current_status'] = 'stopped';
$input['command'] = 'restart';
$mailing = PommoHelper::arrayIntersect($input, $mailing);
		
$code = PommoMailing::add($mailing);
if (!$code)
	jsonKill('Unable to add mailing',$key);
	
$queue = array($key);
if(!PommoMailCtl::queueMake($queue))
	jsonKill('Unable to populate queue',$key);
			
if (!PommoMailCtl::spawn($pommo->_baseUrl.'admin/mailings/mailings_send4.php?testMailing=TRUE&securityCode='.$code))
	jsonKill('Unable to spawn background mailer',$key);

$json = "{success: true, msg: \"".Pommo::_T('Test Mailing Sent.')."\"}";
die($json);
?>