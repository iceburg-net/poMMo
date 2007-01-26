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


$groups = & PommoGroup::get();
$fields = & PommoField::get();

$group =& $groups[$_REQUEST['group_id']];

if(empty($group))
	Pommo::redirect('subscribers_groups.php');
	

// change group name if requested
if (isset($_POST['rename']) && !empty ($_POST['group_name']))
	if (PommoGroup::nameChange($group['id'], $_POST['group_name']))
		Pommo::redirect($_SERVER['PHP_SELF'].'?group_id='.$group['id'].'&renamed='.$_POST['group_name']);
if (isset($_GET['renamed']))
	$logger->addMsg(Pommo::_T('Group Renamed'));

if(isset($_GET['fieldDelete'])) {
	PommoFilter::deleteField($group['id'], $_GET['fieldDelete'], $_GET['logic']);
	Pommo::redirect($_SERVER['PHP_SELF'].'?group_id='.$group['id']);
}

if(isset($_GET['groupDelete'])) {
	PommoFilter::deleteGroup($group['id'], $_GET['groupDelete'], $_GET['logic']);
	Pommo::redirect($_SERVER['PHP_SELF'].'?group_id='.$group['id']);
}
	
$new = & PommoFilter::getLegalFilters($group, $fields);
$gnew = & PommoFilter::getLegalGroups($group, $groups);

// organize existing criteria into fieldID[logic] = array('values','...');

$english = PommoFilter::getEnglish();

$filters = array();
foreach($group['criteria'] as $crit) {
	if (!isset($filters[$crit['field_id']]))
		$filters[$crit['field_id']] = array();
	if (!isset($filters[$crit['field_id']][$crit['logic']]))
		$filters[$crit['field_id']][$crit['logic']] = array();
	array_push($filters[$crit['field_id']][$crit['logic']], $crit['value']);
}

$smarty->assign('group',$group);
$smarty->assign('groups',$groups);
$smarty->assign('fields',$fields);
$smarty->assign('new', $new);
$smarty->assign('gnew', $gnew);
$smarty->assign('filters', $filters);
$smarty->assign('english', $english);
$smarty->assign('tally', PommoGroup::tally($group));
$smarty->assign('filterCount', count($group['criteria']));

$smarty->display('admin/subscribers/groups_edit.tpl');
Pommo::kill();

?>