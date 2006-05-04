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
define('_IS_VALID', TRUE);

require ('../../bootstrap.php');
require_once (bm_baseDir . '/inc/db_demographics.php');
require_once (bm_baseDir.'/inc/lib.txt.php');

$poMMo = & fireup('secure','keep');
$logger = & $poMMo->logger;
$dbo = & $poMMo->openDB();

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
$smarty = & bmSmartyInit();
$smarty->prepareForForm();

if (isset ($_REQUEST['demographic_id']) && dbDemographicCheck($dbo, $_REQUEST['demographic_id']))
	$demographic_id = str2db($_REQUEST['demographic_id']);
else {
	bmRedirect('setup_demographics.php');
}

// check if user submitted options to add
if (!empty ($_POST['dVal-add']) && !empty ($_POST['addOption'])) {
	dbDemographicOptionAdd($dbo, $demographic_id, $_POST['addOption']);
	$_POST = array();
}

// check if user requesteddemographic_id='.$demographic_id to remove an option
if (!empty ($_REQUEST['dVal-del']) && !empty ($_REQUEST['delOption'])) {
	
	// See if this change will affect any subscribers, if so, confirm the change.
	$sql = 'SELECT COUNT(data_id) FROM ' . $dbo->table['subscribers_data'] . ' WHERE demographic_id=\'' . $demographic_id . '\' AND value=\'' . str2db($_POST['delOption']) . '\'';
	$affected = $dbo->query($sql, 0);
	
	if ($affected && empty($_GET['dVal-force'])) {
		$smarty->assign('confirm',array(
		 	'title' => _T('Remove Option'),
		 	'nourl' =>  $_SERVER['PHP_SELF'].'?demographic_id='.$demographic_id,
		 	'yesurl' => $_SERVER['PHP_SELF'].'?demographic_id='.$demographic_id.'&dVal-del=TRUE&dVal-force=TRUE&delOption='.$_POST['delOption'],
		 	'msg' => sprintf(_T('Deleting option %1$s will affect %2$s subscribers who have selected this choice. They will be flagged as needing to update their records.'), '<b>'.$_POST['delOption'].'</b>', '<em>'.$affected.'</em>')
		 	));
		 
		 $smarty->display('admin/confirm.tpl');
		 bmKill();
	}
	else {
		// delete option, no subscriber is affected || force given.
		dbDemographicOptionDelete($dbo, $demographic_id, $_REQUEST['delOption']);
		bmRedirect($_SERVER['PHP_SELF'].'?demographic_id='.$demographic_id);
	}
}


if (!SmartyValidate :: is_registered_form() || empty ($_POST)) {
	// ___ USER HAS NOT SENT FORM ___

	SmartyValidate :: connect($smarty, true);
	SmartyValidate :: register_validator('demographic_name', 'demographic_name', 'notEmpty', false, false, 'trim');
	SmartyValidate :: register_validator('demographic_prompt', 'demographic_prompt', 'notEmpty', false, false, 'trim');

	$formError = array ();
	$formError['demographic_name'] = $formError['demographic_prompt'] = _T('Cannot be empty.');
	$smarty->assign('formError', $formError);
	
	// fetch demographic info
	$demographics = & dbGetDemographics($dbo, $demographic_id);
	$demographic = & $demographics[$demographic_id];
	$demographic['id'] = $demographic_id;
	$smarty->assign('demographic', $demographic);
	$poMMo->set($demographic);

	// populate _POST with info from database (fills in form values...)
	@ $_POST['demographic_name'] = $demographic['name'];
	@ $_POST['demographic_prompt'] = $demographic['prompt'];
	@ $_POST['demographic_active'] = $demographic['active'];
	@ $_POST['demographic_required'] = $demographic['required'];
	@ $_POST['demographic_normally'] = $demographic['normally'];

} else {
	
	$demographic =& $poMMo->get();
	$smarty->assign('demographic', $demographic);
	
	// ___ USER HAS SENT FORM ___
	SmartyValidate :: connect($smarty);

	if (SmartyValidate :: is_valid($_POST)) {
		// __ FORM IS VALID

		dbDemographicUpdate($dbo, $_POST);
		$logger->addMsg(_T('Settings updated.'));

	} else {
		// __ FORM NOT VALID
		$logger->addMsg(_T('Please review and correct errors with your submission.'));
	}
}

switch ($demographic['type']) {
		case 'text' :
			$smarty->assign('intro', _T('This is a <b>TEXT</b> based demographic. Subscribers will be allowed to type in any value in for this demographic. Text demographics are useful for collecting names, cities, and such.'));
			break;
		case 'checkbox' :
			$smarty->assign('intro', _T('This is a <b>CHECKBOX</b> based demographic. Subscribers will be allowed to toggle this demographic ON and OFF. Checkboxes are useful for asking a user if they\'d like to be included or excluded in something.'));
			break;
		case 'number' :
			$smarty->assign('intro', _T('This is a <b>NUMBER</b> based demographic -- <b>UNSUPPORTED</b>. Support for this type will be added later.'));
			break;
		case 'date' :
			$smarty->assign('intro', _T('This is a <b>DATE</b> based demographic -- <b>UNSUPPORTED</b>. Support for this type will be added later.'));
			break;
		case 'multiple' :
			$smarty->assign('intro', _T('This is a <b>MULTIPLE CHOICE</b> based demographic. Subscribers will be able to select a value from the options you provide below. Multiple choice demographics have reliable values, and are useful for collecting subsscriber Country, income range, and such.'));
			break;
	}
	
$smarty->assign($_POST);
$smarty->display('admin/setup/demographics_edit.tpl');
bmKill();