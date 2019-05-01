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
require_once($pommo->_baseDir.'inc/helpers/groups.php');
require_once($pommo->_baseDir.'inc/helpers/fields.php');
require_once($pommo->_baseDir.'inc/helpers/rules.php');
			
$pommo->init();
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	JSON OUTPUT INITIALIZATION
 *********************************/
require_once($pommo->_baseDir.'inc/classes/json.php');
$json = new PommoJSON();

// Remember the Page State
$state =& PommoAPI::stateInit('groups_edit');

// EXAMINE CALL
switch ($_REQUEST['call']) {

	case 'displayRule' :
	
		/**********************************
			SETUP TEMPLATE, PAGE
		 *********************************/
		require_once($pommo->_baseDir.'inc/classes/template.php');
		$template = new PommoTheme();

		$group = current(PommoGroup::get(array('id' => $state['group'])));
		if(empty($group))
			die('invalid input');
	
		if($_REQUEST['ruleType'] == 'field') {
			$field = current(PommoField::get(array('id' => $_REQUEST['fieldID'])));
			$logic = (isset($_REQUEST['logic']) && $_REQUEST['logic'] != "0") ? $_REQUEST['logic'] : false;
			$type = ($_REQUEST['type'] == 'or') ? 'or' : 'and';
			
			$values = array();
			
			// check to see if we're editing [logic is passed *only* when edit button is clicked]
			if ($logic)
				foreach($group['rules'] as $rule) {
					if($rule['logic'] == $logic && $rule['field_id'] == $_REQUEST['fieldID']) {
						$values[] = ($field['type'] == 'date') ? 
							PommoHelper::timeFromStr($rule['value']) :
							$rule['value'];
					}
				}

			$firstVal = (empty($values)) ? false : array_shift($values);

			$logic = ($logic) ? 
				PommoRules::getEnglish(array($logic)) : 
				PommoRules::getEnglish(end(PommoRules::getLegal($group,array($field))));
				
			$template->assign('type', $type);
			$template->assign('field',$field);
			$template->assign('logic',$logic);
			$template->assign('values',$values);
			$template->assign('firstVal',$firstVal);
		
			$template->display('admin/subscribers/ajax/rule.field.tpl');
			Pommo::kill();
		}
		elseif($_REQUEST['ruleType'] == 'group') {
			$match = PommoGroup::getNames($_REQUEST['fieldID']);
			$key = key($match);
			
			$template->assign('match_name',$match[$key]);
			$template->assign('match_id',$key);
			
			$template->display('admin/subscribers/ajax/rule.group.tpl');
			Pommo::kill();
		}
	break;
	
	case 'addRule': 
		switch($_REQUEST['logic']) {
			case 'is_in':
			case 'not_in':
				PommoRules::addGroupRule($state['group'], $_REQUEST['field'], $_REQUEST['logic']);
				break;
			case 'true':
			case 'false':
				PommoRules::addBoolRule($state['group'], $_REQUEST['field'], $_REQUEST['logic']);
				break;
			case 'is':
			case 'not':
			case 'less':
			case 'greater':
				
				$values = array_unique($_REQUEST['match']);
				$type = ($_REQUEST['type'] == 'or') ? 'or' : 'and';
				
				PommoRules::addFieldRule($state['group'], $_REQUEST['field'], $_REQUEST['logic'], $values, $type);
				break;
		}
		$json->add('callbackFunction','redirect');
		$json->add('callbackParams',$pommo->_baseUrl.'admin/subscribers/groups_edit.php');
		$json->serve();
	break;
	
	case 'updateRule' :
		require_once($pommo->_baseDir.'inc/classes/sql.gen.php');
		$group =& current(PommoGroup::get(array('id' => $state['group'])));
		$rules = PommoSQL::sortRules($group['rules']);
		
		switch ($_REQUEST['request']) {
			case 'update' :
				if($_REQUEST['type'] == 'or' && count($rules['and']) < 2) {
					$json->add('callbackFunction','resume');
					$json->success(Pommo::_T('At least 1 "and" rule must exist before an "or" rule takes effect.'));
				}
				PommoRules::changeType($group['id'], $_REQUEST['fieldID'], $_REQUEST['logic'], $_REQUEST['type']);
				break;
				
			case 'delete' :
				PommoRules::deleteRule($group['id'], $_REQUEST['fieldID'], $_REQUEST['logic']);
				break;
		}
		$json->add('callbackFunction','redirect');
		$json->add('callbackParams',$pommo->_baseUrl.'admin/subscribers/groups_edit.php');
		$json->serve();
	break;

	case 'renameGroup': 
		if (!empty($_REQUEST['group_name']))
			if (PommoGroup::nameChange($state['group'], $_REQUEST['group_name']))
				$json->success(Pommo::_T('Group Renamed'));
			$json->fail('invalid group name');
		break;

	default:
		die('invalid request passed to '.__FILE__);
	break;
}

die();
?>