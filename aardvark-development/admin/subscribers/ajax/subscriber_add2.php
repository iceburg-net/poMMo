<?php
/**
 * Copyright (C) 2005, 2006, 2007  Brice Burgess <bhb@iceburg.net>
 * 
 * This file is part of poMMo (http://www.pommo.org)
 * 
 * poMMo is free software; you can redistribute it and/or modify 
 * it under the terms of the GNU General Public License as published 
 * by the Free Software Foundation; either version 2, or any later version.
 * 
 * poMMo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See
 * the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with program; see the file docs/LICENSE. If not, write to the
 * Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 */

/**********************************
	INITIALIZATION METHODS
*********************************/
require ('../../../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/subscribers.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/validate.php');

$pommo->init();
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;


/**********************************
	JSON OUTPUT INITIALIZATION
 *********************************/
 Pommo::requireOnce($pommo->_baseDir.'inc/lib/class.json.php');
$pommo->logErrors(); // PHP Errors are logged, turns display_errors off.
$pommo->toggleEscaping(); // Wraps _T and logger responses with htmlspecialchars()

// TODO page needs rewrite to utilize the json class for output. e.g. admin/mailings/ajax/status_poll.php



function jsonKill($msg) {
	$json = "{success: false, msg: \"".$msg."\"}";
	die($json);
}

if (!PommoHelper::isEmail($_POST['Email']))
	jsonKill(Pommo::_T('Error adding subscriber.').'<br />'.Pommo::_T('Invalid email.'));
	
// check if email is unsubscribed
if(!isset($_REQUEST['force'])) {
	$unsubscribed = PommoSubscriber::GetIDByEmail($_POST['Email'],0);
	if(!empty($unsubscribed))
		jsonKill(Pommo::_T('Error adding subscriber.').'<br />'.sprintf(Pommo::_T('%s has already unsubscribed. To add the subscriber anyway, check the box to force the addition.'),'<strong>'.$_POST['Email'].'</strong>'));
}

if(PommoHelper::isDupe($_POST['Email']))
	jsonKill(Pommo::_T('Error adding subscriber.').'<br />'.Pommo::_T('Email address already exists. Duplicates are not allowed.'));

$subscriber = array(
	'email' => $_POST['Email'],
	'registered' => time(),
	'ip' => $_SERVER['REMOTE_ADDR'],
	'status' => 1,
	'data' => $_POST['d']);

$flag = false;
if (!PommoValidate::subscriberData($subscriber['data'],array('active' => FALSE, 'ignore' => TRUE))) {
	if(!isset($_REQUEST['force']))
		jsonKill(Pommo::_T('Error adding subscriber.').'<br />'.Pommo::_T('Invalid or missing information.').'<br />'.implode("<br />", $logger->getAll()));

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
foreach($subscriber['data'] as $k => $val) 
	$json .= ",d{$k}: \"".htmlspecialchars($val)."\"";

$json = "{success: true, key: $key, msg: \"".$msg."\", data: {".$json."} }";
die($json);
?>