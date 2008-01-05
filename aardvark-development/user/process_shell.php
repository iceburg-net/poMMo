#!/usr/bin/php -q
<?php
/**
 * Copyright (C) 2005, 2006, 2007, 2008  Brice Burgess <bhb@iceburg.net>
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
require (dirname(__FILE__) . '/../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/validate.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/subscribers.php');

$pommo->init(array('authLevel' => 0,'noSession' => true));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	VALIDATE INPUT
 *********************************/

// first arguments should be the email, followed by ip_addres (defaults to 127.0.0.1).
// everything after will be appended to the data array and should be passed in key=value pairs
// please note that you must escape the variables first (in php can use escapeshellarg())
// below is an example which is equal to $_POST = array(
// 'email' => 'user@domain.com',
// 'd[1]' => 'from_website'
// )
// $_SERVER['REMOTE_ADDR'] = '192.168.0.1'
// 
// php process_shell.php user@domain.com 192.168.0.1 1=from_website

$_argv_len = count($argv);
$_email = @$argv[1];
if (empty($_email)) {
	die("Please pass an email address\n");
}
$_ip = isset($argv[2]) ? $argv[2] : '127.0.0.1';

$_data = array();
if ($_argv_len > 3) { // data is being passed
	for ($i = 3, $len = count($argv); $i < $argv; $i++) {
		list($key, $value) = explode('=', $argv[$i]);
		$_data[$key] = $value;
	}
}


$subscriber = array(
	'email' => $_email,
	'registered' => time(),
	'ip' => $_ip,
	'status' => 1,
	'data' => $_data,
);

// ** check for correct email syntax
if (!PommoHelper::isEmail($subscriber['email']))
	die(Pommo::_T('Invalid Email Address') . "\n");
		
// ** check if email already exists in DB ("duplicates are bad..")
if (PommoHelper::isDupe($subscriber['email'])) {
	die(Pommo::_T('Email address already exists. Duplicates are not allowed.') . "\n");
}

// check if errors exist with data, if so print results and die.
if ($logger->isErr() || !PommoValidate::subscriberData($subscriber['data'], array(
	'active' => FALSE))) {
	Pommo::kill();
	die("Errors with passed data\n");
}

$comments = (isset($_POST['comments'])) ? substr($_POST['comments'],0,255) : false;

/**********************************
	ADD SUBSCRIBER
 *********************************/
 
$config = PommoAPI::configGet(array (
	'site_success', // URL to redirect to on success, null is us (default)
	'site_confirm', // URL users will see upon subscription attempt, null is us (default)
	'list_confirm', // Requires email confirmation
	'notices'
));
$notices = unserialize($config['notices']);
Pommo::requireOnce($pommo->_baseDir . 'inc/helpers/messages.php');

if ($config['list_confirm'] == 'on') { // email confirmation required. 
	// add user as "pending"
	
	$subscriber['pending_code'] = PommoHelper::makeCode();
	$subscriber['pending_type'] = 'add';
	$subscriber['status'] = 2;
	
	$id = PommoSubscriber::add($subscriber);
	if (!$id) {
		die("Error adding subscriber! Please contact the administrator.\n");
	}
	else {
		
		if (PommoHelperMessages::sendConfirmation($subscriber['email'], $subscriber['pending_code'], 'subscribe')) {
			$subscriber['registered'] = date("F j, Y, g:i a",$subscriber['registered']);
			if ($comments || isset($notices['pending']) && $notices['pending'] == 'on')
				PommoHelperMessages::notify($notices, $subscriber, 'pending', $comments);
			
			exit(Pommo::_T('Subscription request received.').' '.Pommo::_T('A confirmation email has been sent. You should receive this letter within the next few minutes. Please follow its instructions.'));
		}
		else {
			die(Pommo::_T('Problem sending mail! Please contact the administrator.') . "\n");
			
			// delete the subscriber
			PommoSubscriber::delete($id);
		}
	}
}
else { // no email confirmation required
	if (!PommoSubscriber::add($subscriber)) {
		die("'Error adding subscriber! Please contact the administrator.'\n");
	}
	else {
		$subscriber['registered'] = date("F j, Y, g:i a",$subscriber['registered']);
		if ($comments || isset($notices['subscribe']) && $notices['subscribe'] == 'on')
			PommoHelperMessages::notify($notices, $subscriber, 'subscribe',$comments);
				
		
		$dbvalues = PommoAPI::configGet('messages');
		$messages = unserialize($dbvalues['messages']);
		exit($messages['subscribe']['suc']);
	}
	
}
Pommo::kill();

?>