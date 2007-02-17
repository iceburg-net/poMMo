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
$pommo->init(array('noDebug' => TRUE));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();
$smarty->prepareForForm();

// Check if user requested to restore defaults
if (isset($_POST['restore'])) {
	Pommo::requireOnce($pommo->_baseDir.'inc/helpers/messages.php');
	switch (key($_POST['restore'])) {
		case 'subscribe' : $messages = PommoHelperMessages::ResetDefault('subscribe'); break;
		case 'activate' : $messages = PommoHelperMessages::resetDefault('activate'); break;
		case 'unsubscribe' : $messages = PommoHelperMessages::resetDefault('unsubscribe'); break;
		var_dump($_POST['restore']); die();
	}
	// reset _POST.
	$_POST = array(); 
}

SmartyValidate :: connect($smarty);
if (!SmartyValidate :: is_registered_form('messages') || empty ($_POST)) {
	// ___ USER HAS NOT SENT FORM ___
	SmartyValidate::register_form('messages', true);
	

	SmartyValidate :: register_validator('subscribe_sub', 'subscribe_sub', 'notEmpty', false, false, 'trim', 'messages');
	SmartyValidate :: register_validator('subscribe_msg', 'subscribe_msg:!\[\[URL\]\]!i', 'isRegExp', false, false, 'trim', 'messages');
	SmartyValidate :: register_validator('subscribe_suc', 'subscribe_suc', 'notEmpty', false, false, 'trim', 'messages');
	
	SmartyValidate :: register_validator('activate_sub', 'activate_sub', 'notEmpty', false, false, 'trim', 'messages');
	SmartyValidate :: register_validator('activate_msg', 'activate_msg:!\[\[URL\]\]!i', 'isRegExp', false, false, 'trim', 'messages');
	
	SmartyValidate :: register_validator('unsubscribe_suc', 'unsubscribe_suc', 'notEmpty', false, false, 'trim', 'messages');
	
	
	$vMsg = array();
	$vMsg['unsubscribe_suc'] = 
	$vMsg['subscribe_sub'] = 
	$vMsg['activate_sub'] = 
	$vMsg['subscribe_suc'] = Pommo::_T('Cannot be empty.');
	
	$vMsg['subscribe_msg'] =
	$vMsg['activate_msg'] = Pommo::_T('You must include "[[URL]]" for the confirm link');
	
	$smarty->assign('vMsg', $vMsg);

	// populate _POST with info from database (fills in form values...)
	if (empty($messages)) {
		$dbvalues = PommoAPI::configGet(array('messages'));
		
		if (empty($dbvalues['messages'])) 
			$messages = PommoHelperMessages::resetDefault('all'); 
		else
			$messages = unserialize($dbvalues['messages']);
	}

	if (isset($messages['subscribe'])) {
		$_POST['subscribe_msg'] = $messages['subscribe']['msg'];
		$_POST['subscribe_sub'] = $messages['subscribe']['sub'];
		$_POST['subscribe_suc'] = $messages['subscribe']['suc'];
	}
	
	if (isset($messages['activate'])) {
		$_POST['activate_msg'] = $messages['activate']['msg'];
		$_POST['activate_sub'] = $messages['activate']['sub'];
	}
	
	if (isset($messages['unsubscribe'])) {
		$_POST['unsubscribe_suc'] = $messages['unsubscribe']['suc'];
	}
	
} 
else {
	// ___ USER HAS SENT FORM ___
	if (SmartyValidate :: is_valid($_POST,'messages')) {
	// __ FORM IS VALID
		$messages = array();
		
		$messages['subscribe'] = array();
		$messages['subscribe']['msg'] = $_POST['subscribe_msg'];
		$messages['subscribe']['sub'] = $_POST['subscribe_sub'];
		$messages['subscribe']['suc'] = $_POST['subscribe_suc']; 
		
		$messages['activate'] = array();
		$messages['activate']['msg'] = $_POST['activate_msg']; 
		$messages['activate']['sub'] = $_POST['activate_sub']; 
		
		$messages['unsubscribe'] = array();
		$messages['unsubscribe']['suc'] = $_POST['unsubscribe_suc']; 
		
		$input = array('messages' => serialize($messages));
		PommoAPI::configUpdate( $input, TRUE);
		
		$smarty->assign('output',Pommo::_T('Settings updated.'));
	} 
	else {
		// __ FORM NOT VALID
		$smarty->assign('output',Pommo::_T('Please review and correct errors with your submission.'));
	}
}
$smarty->assign($_POST);
$smarty->display('admin/setup/ajax/config_messages.tpl');
Pommo::kill();
			