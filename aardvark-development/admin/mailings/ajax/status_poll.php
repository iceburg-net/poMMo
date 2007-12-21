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
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/mailings.php');

$pommo->init();
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;


/** SET PAGE STATE
 * touched - remember last time the mailing was touched
 * timestamp - updated whenever the mailing touched time changes
 */

// Initialize page state with default values overriden by those held in $_REQUEST
$state =& PommoAPI::stateInit('subscribers_manage',array(
	'touched' => null,
	'timestamp' => time(),
	'notices' => array()
	));
	
if(!empty($_GET['resetNotices']))
	$state['notices'] = array();

/**********************************
	JSON OUTPUT INITIALIZATION
 *********************************/
 Pommo::requireOnce($pommo->_baseDir.'inc/lib/class.json.php');
//$pommo->logErrors(); // PHP Errors are logged, turns display_errors off.
//$pommo->toggleEscaping(); // Wraps _T and logger responses with htmlspecialchars()

$json = array(
	'percent' => null,
	'status' => null,
	'statusText' => null,
	'sent' => null,
	'notices' => FALSE
);

$statusText = array(
	1 => Pommo::_T('Processing'),
	2 => Pommo::_T('Stopped'),
	3 => Pommo::_T('Frozen'),
	4 => Pommo::_T('Finished')
);

$mailing = (isset($_GET['id'])) ?
	 current(PommoMailing::get(array('id' => $_GET['id']))) :
	 current(PommoMailing::get(array('active' => TRUE)));
	 
// status >> 1: Processing  2: Stopped  3: Frozen  4: Finished
if ($mailing['status'] != 1)
	$json['status'] = 4;
elseif($mailing['current_status'] == 'stopped')
	$json['status'] = 2;
else
	$json['status'] = 1;


// check for frozen mailing
if($json['status'] != 4) {
	if($state['touched'] != $mailing['touched']) {
		$state['touched'] = $mailing['touched'];
		$state['timestamp'] = time();
	}
	else {
		if((time()-$state['timestamp']) > 25 )
			$json['status'] = 3;
	}
}


$json['statusText'] = $statusText[$json['status']];

// get last 50 unique notices
$mailingNotices = PommoMailing::getNotices($mailing['id'], 50, true);
$newNotices = array();
foreach($mailingNotices as $time => $arr) {
	if(!isset($state['notices'][$time])) {
		$newNotices = array_merge($newNotices, $arr);
		continue;
	}
	foreach($arr as $notice) {
		if (array_search($notice,$arr) === false)
			$newNotices[] = $notice;
	}
}
$state['notices'] = $mailingNotices;
$json['notices'] = array_reverse($newNotices);

 
// calculate sent
if($json['status'] == 4) {
	$json['sent'] = PommoMailing::getSent($mailing['id']);
}
else {
	$query = "SELECT count(subscriber_id) FROM {$dbo->table['queue']} WHERE status > 0";
	$json['sent'] = $dbo->query($query,0);
}

// cleanup session if frozen or finished.
if ($json['status'] > 2)
		PommoAPI::stateInit('subscribers_manage');
		

$json['percent'] = ($json['status'] == 4) ?
	100 :
	round($json['sent'] * (100 / $mailing['tally']));
	
$encoder = new json;
die($encoder->encode($json));
?>