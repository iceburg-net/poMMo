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
require ('../../../bootstrap.php');
require_once($pommo->_baseDir.'inc/helpers/fields.php');

$pommo->init(array('keep' => TRUE));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;


/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
require_once($pommo->_baseDir.'inc/classes/template.php');
$template = new PommoTheme();
$template->prepareForForm();

// validate field ID
$field = current(PommoField::get(array('id' => $_REQUEST['field_id'])));
if ($field['id'] != $_REQUEST['field_id'])
	die('bad field ID');
	

if (!SmartyValidate :: is_registered_form() || empty ($_POST)) {
	// ___ USER HAS NOT SENT FORM ___
	SmartyValidate :: connect($template, true);
	
	SmartyValidate :: register_validator('field_name', 'field_name', 'notEmpty', false, false, 'trim');
	SmartyValidate :: register_validator('field_prompt', 'field_prompt', 'notEmpty', false, false, 'trim');
	SmartyValidate :: register_validator('field_required','field_required:!^(on|off)$!','isRegExp');   
	SmartyValidate :: register_validator('field_active','field_active:!^(on|off)$!','isRegExp'); 
	
	$vMsg = array ();
	$vMsg['field_name'] = $vMsg['field_prompt'] = Pommo::_T('Cannot be empty.');
	$template->assign('vMsg', $vMsg);

} else {
	// ___ USER HAS SENT FORM ___
	
	
	/**********************************
		JSON OUTPUT INITIALIZATION
	 *********************************/
	require_once($pommo->_baseDir.'inc/classes/json.php');
	$json = new PommoJSON();
	
	SmartyValidate :: connect($template);

	if (SmartyValidate :: is_valid($_POST)) {
		// __ FORM IS VALID

		// TODO -> Which below logic is better? the computed diff, or send all fields for update?
		
		/*
		// make a difference between updated & original field
		$update = array_diff_assoc(PommoField::makeDB($_POST),$field);
		// restore the ID
		$update['id'] = $field['id'];
		*/
		
		// let MySQL do the difference processing
		$update = PommoField::makeDB($_POST);
		if (!PommoField::update($update))
			$json->fail('error updating field');
			
		$json->add('callbackFunction','updateField');
		$json->add('callbackParams',$update);
		$json->success(Pommo::_T('Settings updated.'));

	} else {
		// __ FORM NOT VALID
		
		$json->add('fieldErrors',$template->getInvalidFields());
		$json->fail(Pommo::_T('Please review and correct errors with your submission.'));
	}
}

$f_text = sprintf(Pommo::_T('%s - Any value will be accepted for text fields. They are useful for collecting names, addresses, etc.'),'<strong>'.$field['name'].' ('.Pommo::_T('Text').')</strong>');
$f_check = sprintf(Pommo::_T('%s - Checkboxes can be toggled ON or OFF. They are useful for opt-ins and agreements.'),'<strong>'.$field['name'].' ('.Pommo::_T('Checkbox').')</strong>');
$f_num = sprintf(Pommo::_T('%s - Only Numeric values will be accepted for number fields.'),'<strong>'.$field['name'].' ('.Pommo::_T('Number').')</strong>');
$f_date = sprintf(Pommo::_T('%s - Only calendar values will be accepted for this field. A date selector (calendar popup) will appear next to the field to aid the subscriber in selecting a date.'),'<strong>'.$field['name'].' ('.Pommo::_T('Date').')</strong>');
$f_mult = sprintf(Pommo::_T('%s - Subscribers will be able to select a value from the options you provide below. Multiple choice fields have reliable values for organizing, and are useful for collecting Country, Interests, etc.'),'<strong>'.$field['name'].' ('.Pommo::_T('Multiple Choice').')</strong>');
$f_comm = sprintf(Pommo::_T('%s -. If a subscriber enters a value for a comment field, it will be mailed to the admin notification email.'),'<strong>'.$field['name'].' ('.Pommo::_T('Comment').')</strong>');

switch ($field['type']) {
		case 'text' :
			$template->assign('intro', $f_text);
			break;
		case 'checkbox' :
			$template->assign('intro', $f_check);
			break;
		case 'number' :
			$template->assign('intro', $f_num);
			break;
		case 'date' :
			$template->assign('intro', $f_date);
			break;
		case 'multiple' :
			$template->assign('intro', $f_mult);
			break;
		case 'comment' :
			$template->assign('intro', $f_comm);
			break;
	}

$template->assign('field', $field);
$template->display('admin/setup/ajax/field_edit.tpl');
Pommo::kill();