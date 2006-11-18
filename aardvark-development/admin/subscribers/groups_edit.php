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
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/fields.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/validate.php');

$pommo->init();
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();
$smarty->prepareForForm();
$smarty->assign('returnStr', Pommo::_T('Groups Page'));

$groups = & PommoGroup::get();
$fields = & PommoField::get();
$criteria = & PommoValidate::getLegalCriteria($groups, $fields);
$group =& $groups[$_REQUEST['group_id']];
if(empty($group))
	Pommo::redirect('subscribers_groups.php');


// delete criteria if requested
if (!empty ($_GET['delete'])) {
	if (PommoGroup::filterDel($_GET['filter_id']))
		$logger->addMsg(Pommo::_T('Filter Removed'));
}

// change group name if requested
if (isset ($_POST['rename']) && !empty ($_POST['group_name']))
	if (PommoGroup::nameChange($group['id'], $_POST['group_name']))
		Pommo::redirect($_SERVER['PHP_SELF'].'?group_id='.$group['id']);

// add filter if requested
if (isset ($_POST['add'])) {
	$logger->addMsg(Pommo::_T('Filter Added'));
	Pommo::_T('Filter failed validation');
	
}

// update a filter if requested 
if (isset ($_POST['update'])) {
	$logger->addMsg(Pommo::_T('Filter Updated'));
	$logger->addMsg('Update failed');
}

$smarty->assign('group',$group);
$smarty->assign('fields', $fields);
$smarty->assign('groups', $groups);
$smarty->assign('criteria', $criteria);
$smarty->assign('tally', count(PommoGroup::getMembers($group)));
$smarty->assign('filterCount', count($group['criteria']));

$smarty->display('admin/subscribers/groups_edit.tpl');
Pommo::kill();

?>