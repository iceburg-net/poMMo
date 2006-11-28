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
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/subscribers.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/validate.php');

$pommo->init(array('noDebug' => TRUE));
$dbo = & $pommo->_dbo;
$logger = & $pommo->_logger;

if (!is_numeric($_GET['key']))
	die(Pommo::_T('Error updating subscriber').'\n Bad Key');

if (isset($_POST['email'])) {
	if (!PommoHelper::isEmail($_POST['email']))
		die(Pommo::_T('Error updating subscriber').' '.Pommo::_T('Invalid Email.'));
	if(count(PommoHelper::emailExists($_POST['email'])) > 0)
		die(Pommo::_T('Error updating subscriber').' '.Pommo::_T('Email address already exists. Duplicates are not allowed.'));
}

$s = array(
	'id' => $_GET['key'],
	'email' => $_POST['email']
	);
	
$data = array();
foreach($_POST as $key => $val) {
	if (is_numeric($key))
		$data[$key] = $val;
}

if (!PommoValidate::subscriberData($data,array('skipReq' => TRUE)))
	die(Pommo::_T('Error updating subscriber').' '.Pommo::_T('Fields failed validation')."\n".implode($logger->getAll(), "\n"));

$s['data'] = $data;
if (!PommoSubscriber::update($s,FALSE))
	die(Pommo::_T('Error updating subscriber'));
	
die();
?>