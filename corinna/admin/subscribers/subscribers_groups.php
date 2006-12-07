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

$pommo->init();
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();
$smarty->prepareForForm();


// add group if requested
if (!empty ($_POST['group_name'])) {
	if (PommoGroup::nameExists($_POST['group_name']))
		$logger->addMsg(sprintf(Pommo::_T('Group name (%s) already exists'),$_POST['group_name']));
	else {
		$group = PommoGroup::make(array('name' => $_POST['group_name']));
		if (PommoGroup::add($group))
			$logger->addMsg(sprintf(Pommo::_T('Group %s Added'),$_POST['group_name']));
	}
}

if (!empty ($_GET['delete'])) {
	// make sure it is a valid group
	$group = current(PommoGroup::get(array('id' => $_GET['group_id'])));
	if (empty($group))
		Pommo::redirect($_SERVER['PHP_SELF']);

	$affected = PommoGroup::filtersAffected($group['id']);

	// See if this change will affect any subscribers, if so, confirm the change.
	if ($affected > 1 && empty ($_GET['dVal-force'])) {
		$smarty->assign('confirm', array (
			'title' => Pommo::_T('Confirm Action'),
			'nourl' => $_SERVER['PHP_SELF'] . '?group_id=' . $_GET['group_id'],
			'yesurl' => $_SERVER['PHP_SELF'] . '?group_id=' . $_GET['group_id'] . '&delete=TRUE&dVal-force=TRUE',
			'msg' => sprintf(Pommo::_T('%1$s filters belong this group . Are you sure you want to remove %2$s?'), '<b>' . $affected . '</b>','<b>' . $group['name'] . '</b>')));
		$smarty->display('admin/confirm.tpl');
		Pommo::kill();
	} else {
		// delete group
		if (!PommoGroup::delete($group['id']))
			$logger->addMsg(Pommo::_T('Group cannot be deleted.'));
		else
			$logger->addMsg(sprintf(Pommo::_T('%s deleted.'),$group['name']));
	}
}

// Get array of mailing groups. Key is ID, value is name
$groups = PommoGroup::get();

$smarty->assign('groups',$groups);
$smarty->display('admin/subscribers/subscribers_groups.tpl');
Pommo::kill();
?>