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
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/groups.php');

$pommo->init(array('noDebug' => TRUE));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;


/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();

// current group
$group = current(PommoGroup::get(array('id' => $_POST['group'])));


if ($_POST['add'] == 'group') {
	$match = PommoGroup::getName($_POST['ID']);
	$key = key($match);
	
	$smarty->assign('group_id',$group['id']);
	$smarty->assign('match_name',$match[$key]);
	$smarty->assign('match_id',$key);
	
	$smarty->display('admin/subscribers/ajax_group.tpl');
	Pommo::kill();
}
elseif ($_POST['add'] == 'field') {
	Pommo::requireOnce($pommo->_baseDir.'inc/helpers/fields.php');
	Pommo::requireOnce($pommo->_baseDir.'inc/helpers/filters.php');
	
	// check to see if we're editing
	
	$values = array();
	if (isset($_POST['logic'])) { // logic is passed only when edit button is clicked..
		foreach($group['criteria'] as $filter) {
			if($filter['logic'] == $_POST['logic'] && $filter['field_id'] == $_POST['ID'])
				$values[] = $filter['value'];
		}
	}
	$firstVal = (empty($values)) ? false : array_shift($values);
	$smarty->assign('values',$values);
	$smarty->assign('firstVal',$firstVal);
	
	$field = current(PommoField::get(array('id' =>$_POST['ID'])));
	
	if (isset($_POST['logic'])) {
		$logic = array($_POST['logic'] => PommoFilter::getEnglish($_POST['logic']));
	}
	else {
		$logic = array();
		$f = array($field);
		foreach(PommoFilter::getLegalFilters($group, $f) as $logics)				
			foreach ($logics as $l)
				$logic[$l] = PommoFilter::getEnglish($l);
	}
	
	$smarty->assign('group_id',$group['id']);
	$smarty->assign('field',$field);
	$smarty->assign('logic',$logic);
	
	$smarty->display('admin/subscribers/ajax_field.tpl');
	Pommo::kill();
	
}
die();
?>