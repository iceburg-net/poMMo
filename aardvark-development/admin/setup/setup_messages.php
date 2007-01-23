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
 
 // TODO -- This page is an optimized sludge... re-write! Too much code repetition, memory use.
 
/**********************************
	INITIALIZATION METHODS
 *********************************/
require('../../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir . 'inc/helpers/messages.php');

$pommo->init();
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();
//$smarty->assign('title', $pommo->_config['site_name'] . ' - ' . Pommo::_T('subscriber logon'));
$smarty->prepareForForm();
$smarty->assign('returnStr',Pommo::_T('Configure'));


$messages = array();

// Check if user requested to restore defaults
if (isset($_POST['restore'])) {
	switch (key($_POST['restore'])) {
		case 'subscribe' : $messages = PommoHelperMessages::messageResetDefault('subscribe'); break;
		case 'activate' : $messages = PommoHelperMessages::resetDefault('activate'); break;
		case 'unsubscribe' : $messages = PommoHelperMessages::resetDefault('unsubscribe'); break;
	}
	// reset _POST.
	$_POST = array(); 
}

if (!SmartyValidate::is_registered_form() || empty($_POST)) {
	// ___ USER HAS NOT SENT FORM ___
	
	SmartyValidate :: connect($smarty, true); 
	
	SmartyValidate :: register_validator('subscribe_sub', 'Subscribe_sub', 'notEmpty', false, false, 'trim');
	SmartyValidate :: register_validator('subscribe_msg', 'Subscribe_msg:!\[\[URL\]\]!i', 'isRegExp', false, false, 'trim');
	SmartyValidate :: register_validator('subscribe_suc', 'Subscribe_suc', 'notEmpty', false, false, 'trim');
	
	SmartyValidate :: register_validator('activate_sub', 'Activate_sub', 'notEmpty', false, false, 'trim');
	SmartyValidate :: register_validator('activate_msg', 'Activate_msg:!\[\[URL\]\]!i', 'isRegExp', false, false, 'trim');
	
	SmartyValidate :: register_validator('unsubscribe_suc', 'Unsubscribe_suc', 'notEmpty', false, false, 'trim');
	
	
	$formError = array();
	$formError['empty'] = Pommo::_T('Cannot be empty.');
	$formError['url'] =	 Pommo::_T('You must include "[[URL]]" for the confirm link');
	
	$smarty->assign('formError',$formError);
	
	// populate _POST with info from database (fills in form values...)
	if (empty($messages)) {
		$dbvalues = PommoAPI::configGet(array('messages'));
		
		if (empty($dbvalues['messages'])) 
			$messages = PommoHelperMessages::resetDefault('all'); 
		else
			$messages = unserialize($dbvalues['messages']);
	}

	if (isset($messages['subscribe'])) {
		$_POST['Subscribe_msg'] = $messages['subscribe']['msg'];
		$_POST['Subscribe_sub'] = $messages['subscribe']['sub'];
		$_POST['Subscribe_suc'] = $messages['subscribe']['suc'];
	}
	
	if (isset($messages['activate'])) {
		$_POST['Activate_msg'] = $messages['activate']['msg'];
		$_POST['Activate_sub'] = $messages['activate']['sub'];
	}
	
	if (isset($messages['unsubscribe'])) {
		$_POST['Unsubscribe_suc'] = $messages['unsubscribe']['suc'];
	}
	
}
else {
	// ___ USER HAS SENT FORM ___
	SmartyValidate :: connect($smarty);

	if (SmartyValidate :: is_valid($_POST)) {
	// __ FORM IS VALID
		$messages = array();
		
		$messages['subscribe'] = array();
		$messages['subscribe']['msg'] = $_POST['Subscribe_msg'];
		$messages['subscribe']['sub'] = $_POST['Subscribe_sub'];
		$messages['subscribe']['suc'] = $_POST['Subscribe_suc']; 
		
		$messages['activate'] = array();
		$messages['activate']['msg'] = $_POST['Activate_msg']; 
		$messages['activate']['sub'] = $_POST['Activate_sub']; 
		
		$messages['unsubscribe'] = array();
		$messages['unsubscribe']['suc'] = $_POST['Unsubscribe_suc']; 
		
		$input = array('messages' => serialize($messages));
		PommoAPI::configUpdate( $input, TRUE);
		
		$logger->addMsg(Pommo::_T('Settings updated.'));
	} 
	else {
		// __ FORM NOT VALID
		$logger->addMsg(Pommo::_T('Please review and correct errors with your submission.'));
	}
}
$smarty->assign($_POST);
$smarty->display('admin/setup/setup_messages.tpl');
Pommo::kill();
?>