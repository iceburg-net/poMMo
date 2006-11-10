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
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/fields.php');

$pommo->init();
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();
$smarty->prepareForForm();

$smarty->assign('intro', Pommo::_T('Subscriber fields are used to gather and sort information on your list members. Any number of fields can be assigned to the subscription form.  Each field is categorized as either <em>TEXT</em>, <em>NUMBER</em>, <em>MULTIPLE CHOICE</em>, <em>CHECK BOX</em>, or <em>DATE</em> depending on kind of information it collects.'));

// add field if requested, redirect to its edit page on success
if (!empty ($_POST['field_name'])) {
	$field = PommoField::make(array(
		'name' => $_POST['field_name'],
		'type' => $_POST['field_type'],
		'prompt' => 'Field Prompt',
		'required' => 'off',
		'active' => 'off'
	));
	$id = PommoField::add($field);
	($id) ?
		Pommo::redirect("fields_edit.php?field_id=$id") :
		$logger->addMsg(Pommo::_T('Error with addition.'));
}

// check for a deletion request
if (!empty ($_GET['delete'])) {

	$field = PommoField::getByID($_GET['field_id']);
	$field =& current($field);
	
	if (count($field) === 0) {
		$logger->addMsg(Pommo::_T('Error with deletion.'));
	}
	else {
		$affected = PommoField::subscribersAffected($field['id']);
		if(count($affected) > 0 && empty($_GET['dVal-force'])) {
			$smarty->assign('confirm', array (
				'title' => Pommo::_T('Confirm Action'),
				'nourl' => $_SERVER['PHP_SELF'] . '?field_id=' . $_GET['field_id'],
				'yesurl' => $_SERVER['PHP_SELF'] . '?field_id=' . $_GET['field_id'] . '&delete=TRUE&dVal-force=TRUE',
				'msg' => sprintf(Pommo::_T('Currently, %1$s subscribers have a non empty value for %2$s. All Subscriber data relating to this field will be lost.'), '<b>' . count($affected) . '</b>','<b>' . $field['name'] . '</b>')));
			$smarty->display('admin/confirm.tpl');
			Pommo::kill();
		}
		else {
			(PommoField::delete($field['id'])) ?
				Pommo::redirect($_SERVER['PHP_SELF']) :
				$logger->addMsg(Pommo::_T('Error with delete.'));
		}
	}
}

// Get array of fields. Key is ID, value is an array of the demo's info
$fields = PommoField::get();
if (!empty($fields))
	$smarty->assign('fields', $fields);
	
$smarty->display('admin/setup/setup_fields.tpl');
Pommo::kill();
?>