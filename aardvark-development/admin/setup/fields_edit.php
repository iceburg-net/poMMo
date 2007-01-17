<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/licenses/gpl.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. Notify the above author of modifications to contents within.
 * 
 *  WHY? Because this is a community project -- purposely released under the GPL.
 *    We'd love to have the possiblity to include your derivative works! 
 *    We'd love to coordinate around your development efforts!
 *    We'd love to assist you with your changes!
 *    DON'T BE A STRANGER!
 * 
 ** [END HEADER]**/

/**********************************
	INITIALIZATION METHODS
*********************************/
require ('../../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/fields.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/subscribers.php');

$pommo->init(array('keep' => TRUE));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();
$smarty->prepareForForm();

$field = PommoField::get(array('id' => $_REQUEST['field_id']));
if (count($field) < 1)
	Pommo::redirect('setup_fields.php');
$field =& current($field); // reference the first field returned by PommoField::getById


// check if user submitted options to add
if (!empty ($_POST['dVal-add'])) {
	if (!empty ($_POST['addOption']))
		if(!PommoField::optionAdd($field,$_POST['addOption']))
			$logger->addMsg(Pommo::_T('Error with addition.'));
	$_POST = array();
}

// check if user requestedfield_id='.$field['id'] to remove an option
if (!empty ($_REQUEST['dVal-del'])) {
	if(empty ($_REQUEST['delOption']))
		Pommo::redirect($_SERVER['PHP_SELF'].'?field_id='.$field['id']);
		
	$affected = PommoField::subscribersAffected($field['id'],$_REQUEST['delOption']);
	if(count($affected) > 0 && empty($_GET['dVal-force'])) {
		$smarty->assign('confirm',array(
		 	'title' => Pommo::_T('Confirm Action'),
		 	'nourl' =>  $_SERVER['PHP_SELF'].'?field_id='.$field['id'],
		 	'yesurl' => $_SERVER['PHP_SELF'].'?field_id='.$field['id'].'&dVal-del=TRUE&dVal-force=TRUE&delOption='.$_POST['delOption'],
		 	'msg' => sprintf(Pommo::_T('Deleting option %1$s will affect %2$s subscribers who have selected this choice. They will be flagged as needing to update their records.'), '<b>'.$_POST['delOption'].'</b>', '<em>'.$affected.'</em>')
		 	));
		 
		 $smarty->display('admin/confirm.tpl');
		 Pommo::kill();
	}
	else {
		// delete option, no subscriber is affected || force given.
		if (!PommoField::optionDel($field,$_POST['delOption']))
			Pommo::kill(Pommo::_T('Error with deletion.'));
			
		// flag subscribers for update
		if(count($affected) > 0)
			PommoSubscribers::flagByID($affected);
		Pommo::redirect($_SERVER['PHP_SELF'].'?field_id='.$field['id']);
	}
}

$smarty->assign('field', $field);

if (!SmartyValidate :: is_registered_form() || empty ($_POST)) {
	// ___ USER HAS NOT SENT FORM ___

	SmartyValidate :: connect($smarty, true);
	SmartyValidate :: register_validator('field_name', 'field_name', 'notEmpty', false, false, 'trim');
	SmartyValidate :: register_validator('field_prompt', 'field_prompt', 'notEmpty', false, false, 'trim');

	$formError = array ();
	$formError['field_name'] = $formError['field_prompt'] = Pommo::_T('Cannot be empty.');
	$smarty->assign('formError', $formError);

	// populate _POST with info from database (fills in form values...)
	@ $_POST['field_name'] = $field['name'];
	@ $_POST['field_prompt'] = $field['prompt'];
	@ $_POST['field_active'] = $field['active'];
	@ $_POST['field_required'] = $field['required'];
	@ $_POST['field_normally'] = $field['normally'];

} else {
	// ___ USER HAS SENT FORM ___
	SmartyValidate :: connect($smarty);

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
			Pommo::kill(Pommo::_T('Error with deletion.'));
		$logger->addMsg(Pommo::_T('Settings updated.'));

	} else {
		// __ FORM NOT VALID
		$logger->addMsg(Pommo::_T('Please review and correct errors with your submission.'));
	}
}

switch ($field['type']) {
		case 'text' :
			$smarty->assign('intro', Pommo::_T('This is a <b>TEXT</b> field. Subscribers will be allowed to type any value for this field. Text fields are useful for collecting names, cities, and such.'));
			break;
		case 'checkbox' :
			$smarty->assign('intro', Pommo::_T('This is a <b>CHECKBOX</b> field. Subscribers will be allowed to toggle this field ON and OFF. Checkboxes are useful for subscriber acceptance and opt-ins.'));
			break;
		case 'number' :
			$smarty->assign('intro', Pommo::_T('This is a <b>NUMBER</b> field. Only numeric values will be accepted for this field. Number fields are useful for collecting ages, quantities, and such.'));
			break;
		case 'date' :
			$smarty->assign('intro', Pommo::_T('This is a <b>DATE</b> field. Only calendar values will be accepted for this field. A date selector (calendar popup) will appear next to the field to aid the subscriber in selecting a date.'));
			break;
		case 'multiple' :
			$smarty->assign('intro', Pommo::_T('This is a <b>MULTIPLE CHOICE</b> field. Subscribers will be able to select a value from the options you provide below. Multiple choice fields have reliable values, and are useful for collecting Country, income range, pre-defined sizes and such.'));
			break;
	}
	
$smarty->assign($_POST);
$smarty->display('admin/setup/fields_edit.tpl');
Pommo::kill();