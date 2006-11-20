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
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/filters.php');

$pommo->init();
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();
$smarty->assign('returnStr', Pommo::_T('Groups Page'));


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

$groups = & PommoGroup::get();
$fields = & PommoField::get();

$group =& $groups[$_REQUEST['group_id']];

if(empty($group))
	Pommo::redirect('subscribers_groups.php');
	
$new = & PommoFilter::getLegalFilters($groups, $fields);
$gnew = & PommoFilter::getLegalGroups($group, $groups);

// organize existing criteria into fieldID[logic] = array('values','...');

$english = array(
	'is' => Pommo::_T('is'),
	'not' => Pommo::_T('is not'),
	'true' => Pommo::_T('is checked'),
	'false' => Pommo::_T('is not checked'),
	'greater' => Pommo::_T('is greater than'),
	'less' => Pommo::_T('is less than'),
	'is_in' => Pommo::_T('or in group'),
	'not_in' => Pommo::_T('and not in group')
);

$filters = array();
foreach($group['criteria'] as $crit) {
	if (!isset($filters[$crit['field_id']]))
		$filters[$crit['field_id']] = array();
	if (!isset($filters[$crit['field_id']][$crit['logic']]))
		$filters[$crit['field_id']][$crit['logic']] = array();
	array_push($filters[$crit['field_id']][$crit['logic']], $crit['value']);
}

$smarty->assign('group',$group);
$smarty->assign('fields',$fields);
$smarty->assign('new', $new);
$smarty->assign('gnew', $gnew);
$smarty->assign('filters', $filters);
$smarty->assign('english', $english);
$smarty->assign('tally', count(PommoGroup::getMembers($group)));
$smarty->assign('filterCount', count($group['criteria']));

$smarty->display('admin/subscribers/groups_edit.tpl');
Pommo::kill();

?>