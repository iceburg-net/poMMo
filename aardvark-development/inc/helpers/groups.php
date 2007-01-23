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

// include the group prototype object 
$GLOBALS['pommo']->requireOnce($GLOBALS['pommo']->_baseDir. 'inc/classes/prototypes.php');

/**
 * Group: A Group of Subscribers
 * ==SQL Schema==
 *	group_id		(int)		Database ID/Key
 *	group_name		(str)		Descriptive name for field (used for short identification)
 *	
 * ==Additional Columns from group_criteria==
 * 
 *  criteria_id		(int)		Database ID/Key
 *  group_id		(int)		Correlating Group ID
 *  field_id		(int)		Correlating Field ID
 *  logic			(enum)		'is','not','greater','less','true','false','is_in','not_in'
 *	value			(str)		Match Value
 */
 
 class PommoGroup {
 	var $_name; // name of group
 	var $_group; // the group object
 	var $_tally; // the group tally
 	var $_status; // subscriber status (0(inactive),1(active),2(pending))
 	var $_memberIDs; // array of member IDs (if group is numeric)
 	var $_id; // ID of bgroup
 	
 	// ============ NON STATIC METHODS ===================
 	function PommoGroup($groupID = NULL, $status = 1) {
 		$GLOBALS['pommo']->requireOnce($GLOBALS['pommo']->_baseDir. 'inc/helpers/subscribers.php');
 		$this->_status = $status;
 		if (!is_numeric($groupID)) {
 			$this->_group = null;
 			$this->_name = Pommo::_T('All Subscribers');
 			$this->_memberIDs = null;
 			$this->_id = null;
 			$this->_tally = PommoSubscriber::tally($status);
 			return;
 		}
		$this->_group = current(PommoGroup::get(array('id' => $groupID)));
		$this->_name =& $this->_group['name'];
		$this->_memberIDs = PommoGroup::getMemberIDs($this->_group,$status);
		$this->_tally = count($this->_memberIDs);
		$this->_id= $groupID;
		return;
 	}
 	
 	// returns ordered / filtered / limited member IDs -- scoped to current group member IDs
 	function & members($p = array()) {
 		$GLOBALS['pommo']->requireOnce($GLOBALS['pommo']->_baseDir. 'inc/helpers/subscribers.php');
 		if(is_array($this->_memberIDs)) 
 			$p['id'] =& $this->_memberIDs;
 		else // status was already passed when fetching IDs
 			$p['status'] = $this->_status;
		return PommoSubscriber::get($p);
 	}
 	
 	// ============ STATIC METHODS ===================
 	
 	// make a group template
	// accepts a group template (assoc array)
	// return a group object (array)
	function & make($in = array()) {
		$o = PommoType::group();
		return PommoAPI::getParams($o, $in);
	}
	
	// make a group template based off a database row (group/group_criteria schema)
	// accepts a group template (assoc array)  
	// return a group object (array)
	function & makeDB(&$row) {
		$in = @array(
		'id' => $row['group_id'],
		'name' => $row['group_name']);
		$o = PommoType::group();
		return PommoAPI::getParams($o,$in);
	}
	
	// group validation
	// accepts a group object (array)
	// returns true if group ($in) is valid, false if not
	function validate(&$in) {
		global $pommo;
		$logger =& $pommo->_logger;
		
		$invalid = array();

		if (empty($in['name']))
			$invalid[] = 'name';
		if (!is_array($in['criteria']))
			$invalid[] = 'criteria';
			
		if (!empty($invalid)) {
			$logger->addErr("Group failed validation on; ".implode(',',$invalid),1);
			return false;
		}
		return true;
	}
	
	// fetches groups from the database
	// accepts a filtering array -->
	//   id (array) -> an array of field IDs
	// returns an array of groups. Array key(s) correlates to group ID.
	function & get($p = array()) {
		$defaults = array('id' => null);
		$p = PommoAPI :: getParams($defaults, $p);
		
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$o = array();
		
		$query = "
			SELECT g.group_id, g.group_name, c.criteria_id, c.field_id, c.logic, c.value
			FROM " . $dbo->table['groups']." g
			LEFT JOIN " . $dbo->table['group_criteria']." c 
				ON (g.group_id = c.group_id)
			WHERE
				1
				[AND g.group_id IN(%C)]";
		$query = $dbo->prepare($query,array($p['id']));
		
		while ($row = $dbo->getRows($query)) {
			if (empty($o[$row['group_id']]))
				$o[$row['group_id']] = PommoGroup::makeDB($row);
			
			if(!empty($row['criteria_id'])) {
				$c = array (
					'field_id' => $row['field_id'],
					'logic' => $row['logic'],
					'value' => $row['value'],
				);
				$o[$row['group_id']]['criteria'][$row['criteria_id']] = $c;
			}
		}
		
		return $o;
	}
	
	// fetches group name(s) from the database
	// accepts a filtering array -->
	//   id (array) -> an array of field IDs
	// returns an array of group names. Array key(s) correlates to group ID.
	function & getName($id = array()) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$o = array();
		
		$query = "
			SELECT *
			FROM " . $dbo->table['groups']."
			WHERE
				1
				[AND group_id IN(%C)]";
		$query = $dbo->prepare($query,array($id));
		
		while ($row = $dbo->getRows($query))
			$o[$row['group_id']] = $row['group_name'];
			
		return $o;
	}
	
	// gets the members of a group
	// accepts a group object (array)
	// accepts filter by status (str) either 1 (active) (default), 0 (inactive), 2 (pending) 
	// accepts a toggle (bool) to return IDs or Group Tally
	// returns an array of subscriber IDs
	function & getMemberIDs($group, $status = 1) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		if (empty($group['criteria'])) {
			$o = array();
			return $o;
		}
		
		$f = array();
		
		foreach($group['criteria'] as $c)
			$f['subscriber_data'][$c['field_id']][] = "{$c['logic']}: {$c['value']}";

		if(!is_numeric($status))
			Pommo::kill('Invalid status passed to getMemberIDs()', TRUE);
		$f['subscribers'] = array('status' => array("equal: $status"));
		
		return PommoSubscriber::getIDByAttr($f);
	}
	
	// Returns the # of members in a group
	// accepts a group object (array)
	// accepts filter by status (int) either 1 (active) (default), 0 (inactive), 2 (pending)
	// accepts a toggle (bool) to return IDs or Group Tally
	// returns a tally (int)
	function tally($group, $status = 1) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		$pommo->requireOnce($GLOBALS['pommo']->_baseDir. 'inc/classes/sql.gen.php');
		
		if (empty($group['criteria']))
			return 0;
		
		$f = array();
		
		foreach($group['criteria'] as $c)
			$f['subscriber_data'][$c['field_id']][] = "{$c['logic']}: {$c['value']}";
			
		if(!is_numeric($status))
			Pommo::kill('Invalid status passed to tally()', TRUE);
		$f['subscribers'] = array('status' => array("equal: $status"));
		
		$sql = array('where' => array(), 'join' => array());
		if (!empty($f['subscribers']))
			$sql = array_merge_recursive($sql, PommoSQL::fromFilter($f['subscribers'],'s'));
		
		$sql = array_merge_recursive($sql, PommoSQL::fromFilter($f['subscriber_data'],'d'));
		
		$joins = implode(' ',$sql['join']);
		$where = implode(' ',$sql['where']);
		
		$query = "
			SELECT count(DISTINCT s.subscriber_id)
			FROM ". $dbo->table['subscribers']." s
			".$joins."
			WHERE 1 ".$where;
		return $dbo->query($query,0);
	}
	
	// adds a group to the database
	// accepts a group object (array)
	// returns the database ID of the added group or FALSE if failed
	function add(&$in) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		if (!PommoGroup::validate($in))
			return false;
			
		$query = "
		INSERT INTO " . $dbo->table['groups'] . "
		SET
		group_name='%s'";
		$query = $dbo->prepare($query,@array(
			$in['name']
		));
		
		if (!$dbo->query($query))
			return false;
		
		return $dbo->lastId();
	}
	
	// removes a group from the database
	// accepts a single ID (int) or array of IDs 
	// returns the # of deleted groups (int). 0 (false) if none.
	function delete(&$id) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$query = "
			DELETE
			FROM " . $dbo->table['groups'] . "
			WHERE group_id IN(%c)";
		$query = $dbo->prepare($query,array($id));
		
		$affected = $dbo->affected($query);
	
		// remove filters referencing this group
		$query = "
			DELETE FROM ".$dbo->table['group_criteria']."
			WHERE 
				group_id IN (%c)
				OR (value IN (%c) AND (logic='is_in' OR logic='not_in'))";
		$query=$dbo->prepare($query,array($id,$id));
			
		return $affected;
	}
	
	// Returns the # of filters affected by a group deletion
	// accepts a single ID (int) or array of IDs.
	// Returns a count (int) of affected filters. 0 if none.
	function filtersAffected($id = array()) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$query = "
			SELECT DISTINCT count(criteria_id)
			FROM ".$dbo->table['group_criteria']."
			WHERE 
				group_id IN (%c)
				OR (value IN (%c) AND (logic='is_in' OR logic='not_in'))";
		$query=$dbo->prepare($query,array($id,$id));
		return $dbo->query($query,0);
	}
	
	// Checks if a group name exists
	// accepts a name (str)
	// returns (bool) true if exists, false if not
	function nameExists($name = null) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$query = "
			SELECT count(group_id)
			FROM ".$dbo->table['groups']."
			WHERE group_name='%s'";
		$query=$dbo->prepare($query,array($name));
		return (intval($dbo->query($query,0)) > 0) ? true : false;
	}
	
	// renames a group
	// accepts a group ID (int)
	// accepts a name (str)
	// returns success (bool)
	function nameChange($id, $name) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$query = "
			UPDATE ".$dbo->table['groups']."
			SET group_name='%s'
			WHERE group_id=%i";
		$query=$dbo->prepare($query,array($name,$id));
		return ($dbo->affected($query) > 0) ? TRUE : FALSE;
	}
 }
?>