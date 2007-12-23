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
$dbo = & $pommo->_dbo;
$logger = & $pommo->_logger;


/**********************************
	JSON OUTPUT INITIALIZATION
*********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/lib/class.json.php');
$pommo->logErrors(); // PHP Errors are logged, turns display_errors off.
$pommo->toggleEscaping(); // Wraps _T and logger responses with htmlspecialchars()


$json = array(
	'success' => FALSE,
	'message' => null
);

if(!is_numeric($_REQUEST['id']) || $_REQUEST['id'] == 0)
	$json['message'] = 'ERROR; Bad Subscriber ID Received';
else {
	
	$subscriber = array(
		'id' => $_REQUEST['id'],
		'email' => $_REQUEST['email'],
		'data' => $_REQUEST['d']
	);
	
	$validateOptions = array(
		'skipReq' => TRUE,
		'active' => FALSE
	);
	
	// check for dupe
	if (PommoHelper::isDupe($subscriber['email']))
		$json['message'] = Pommo::_T('Email address already exists. Duplicates are not allowed.');
	else {
	
		if (!PommoValidate::subscriberData($subscriber['data'],$validateOptions) && 
			!isset($_REQUEST['force']))
				$json['message'] = Pommo::_T('Error updating subscriber.').' '.Pommo::_T('Fields failed validation')." >>> ".implode($logger->getAll(), " : ");
		else {
			if (!PommoSubscriber::update($subscriber,'REPLACE_ALL'))
				$json['message'] =  Pommo::_T('Error updating subscriber.');
			else {
				$json['success'] = TRUE;
				$json['message'] = Pommo::_T('Subscriber Updated');
				
				// return jqGrid compatible JSON
				$json['key'] = $subscriber['id'];
				$json['subscriber'] = array('email' => $subscriber['email']);
				
				
				// return human readable date formatting
				Pommo::requireOnce($pommo->_baseDir.'inc/helpers/fields.php');
				$dateFields = PommoField::getByType('date');
				
				foreach($subscriber['data'] as $k => $val) {
					$json['subscriber']['d'.$k] = in_array($k,$dateFields) ?
						PommoHelper::timeToStr($val) :
						htmlspecialchars($val);
					}	
				}		
		}
	}
}

$encoder = new json;
die($encoder->encode($json));
?>