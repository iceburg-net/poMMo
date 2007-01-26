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


// REWRITE...


/**********************************
	INITIALIZATION METHODS
*********************************/
require ('../../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/groups.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/fields.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/rules.php');

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
	PommoRules::deleteRule($group['id'], $_GET['fieldDelete'], $_GET['logic']);
	Pommo::redirect($_SERVER['PHP_SELF'].'?group_id='.$group['id']);
}

if(isset($_GET['groupDelete'])) {
	PommoRules::deleteGroup($group['id'], $_GET['groupDelete'], $_GET['logic']);
	Pommo::redirect($_SERVER['PHP_SELF'].'?group_id='.$group['id']);
}
	
$new = & PommoRules::getLegal($group, $fields);
$gnew = & PommoRules::getLegalGroups($group, $groups);

// organize existing rules into fieldID[logic] = array('values','...');

$english = PommoRules::getEnglish();

$rules = array();
foreach($group['rules'] as $rule) {
	if (!isset($rules[$rule['field_id']]))
		$rules[$rule['field_id']] = array();
	if (!isset($rules[$rule['field_id']][$rule['logic']]))
		$rules[$rule['field_id']][$rule['logic']] = array();
	array_push($rules[$rule['field_id']][$rule['logic']], $rule['value']);
}

$smarty->assign('group',$group);
$smarty->assign('groups',$groups);
$smarty->assign('fields',$fields);
$smarty->assign('new', $new);
$smarty->assign('gnew', $gnew);
$smarty->assign('rules', $rules);
$smarty->assign('english', $english);
$smarty->assign('tally', PommoGroup::tally($group));
$smarty->assign('ruleCount', count($group['rules']));

$smarty->display('admin/subscribers/groups_edit.tpl');
Pommo::kill();

?>