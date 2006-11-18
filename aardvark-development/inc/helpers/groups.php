<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2006 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/

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
		$in = array(
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
			foreach ($invalid as $i)
				$str .= " [$i] ";
			$logger->addErr("Group failed validation on; $str",1);
			return false;
		}
		return true;
	}
	
	// fetches groups from the database
	// accepts a filtering array -->
	//   id (array) -> an array of field IDs
	// returns an array of groups. Array key(s) correlates to group ID.
	function & get($p = array('id' => null)) {
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
					'value' => $row['value']
				);
				$o[$row['group_id']]['criteria'][$row['criteria_id']] = $c;
			}
		}
		
		return $o;
	}
	
	// gets the members of a group
	// accepts a group object (array)
	// accepts filter by status (str) either 'active' (default), 'inactive', 'pending' or NULL (any/all)
	// returns an array of subscriber IDs
	function & getMembers($group = null, $status = 'active') {
		global $pommo;
		$dbo =& $pommo->_dbo;
		$pommo->requireOnce($pommo->_baseDir.'inc/helpers/subscribers.php');
		
		$o = array();
		
		$f = array(
			'subscriber_data' => array(),
			'subscriber_pending' => array(),
			'subscribers' => array()
		);
			
		foreach($group['criteria'] as $c) {
			$f['subscriber_data'][$c['field_id']] = "{$c['logic']}: {$c['value']}";
		}
		
		return PommoSubscriber::getIdByAttr($f,$status);
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
		$query = $dbo->prepare($query,array(
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
	
	// deletes a group filter/criteria
	// accepts a single criteria ID (int) or array of criteria IDs
	// Returns success (bool)
	function filterDel($id = array()) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$query = "
			DELETE FROM ".$dbo->table['group_criteria']."
			WHERE criteria_id IN (%c)";
		$query = $dbo->prepare($query,array($id));
		return ($dbo->affected($query) > 0) ? TRUE : FALSE;
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
 }
?>