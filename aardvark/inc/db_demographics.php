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

/** 
 * Don't allow direct access to this file. Must be called from
elsewhere
*/
defined('_IS_VALID') or die('Move along...');

// NOTE - type checkbox should be NULL in SQL if not checked!

// dbDemoArray: <array> - Returns an array of subscriber demographics. The array key
//  is the demographic_key, and it points to an array holding name, type, prompt, etc.
//  check if where is provided to limit this array to ACTIVE or a specific demographic
function & dbGetDemographics(& $dbo, $where = NULL) {
	
	require_once (bm_baseDir.'/inc/lib.txt.php');
	
	$demos = array ();
	$whereStr = '';

	if ($where == 'active')
		$whereStr = ' WHERE demographic_active=\'on\'';
	elseif (is_numeric($where)) $whereStr = ' WHERE demographic_id=\''.$where.'\'';

	$sql = 'SELECT * FROM '.$dbo->table['demographics'].$whereStr.' ORDER BY demographic_ordering';
	while ($row = $dbo->getRows($sql)) {
		$a = array ();
		$a['active'] = $row['demographic_active'];
		$a['ordering'] = $row['demographic_ordering'];
		$a['name'] = $row['demographic_name'];
		$a['prompt'] = $row['demographic_prompt'];
		$a['type'] = $row['demographic_type'];
		$a['normally'] = $row['demographic_normally'];
		$a['options'] = quotesplit($row['demographic_options']);
		$a['required'] = $row['demographic_required'];

		$demos[$row['demographic_id']] = $a;
	}
	return (!empty($demos)) ? $demos : false;
}

// dbdemographicCheck: <bool> - Returns true if a name/id demographic exists
function dbDemographicCheck(& $dbo, $demographicId) {

	// determine if we're to check for name or id -- note demographic names CANNOT be numeric
	if (is_numeric($demographicId))
		$sql = 'SELECT count(demographic_id) FROM '.$dbo->table['demographics'].' WHERE demographic_id=\''.$demographicId.'\'';
	else
		$sql = 'SELECT count(demographic_id) FROM '.$dbo->table['demographics'].' WHERE demographic_name=\''.$demographicId.'\'';

	return ($dbo->query($sql, 0)) ? true : false;
}

// dbdemographicAdd: <bool> - Returns true if a demographic of passed 'demographicname' was added
function dbDemographicAdd(& $dbo, $demographicName, $demographicType) {

	// demographic NAMES CANNOT BE NUMERIC, or duplicate
	if (is_numeric($demographicName) || dbdemographicCheck($dbo, $demographicName))
		return false;

	// get the last ordering
	$sql = 'SELECT demographic_ordering FROM '.$dbo->table['demographics'].' ORDER BY demographic_ordering DESC';
	$order = $dbo->query($sql, 0) + 1;

	$sql = 'INSERT INTO '.$dbo->table['demographics'].' SET demographic_name=\''.$demographicName.'\', demographic_type=\''.$demographicType.'\', demographic_ordering=\''.$order.'\'';
	return $dbo->affected($sql);
}

// dbDemographicDelete: <bool> - Returns true if the passed demographicId was deleted, false if nothing was.
function dbDemographicDelete(& $dbo, $demographicId) {

	// return false if a bad demographic was passed.
	if (!dbDemographicCheck($dbo, $demographicId))
		return false;

	// delete entries in subscriber/pending_data, and groups_criteria referencing this demographic
	$sql = 'DELETE FROM '.$dbo->table['pending_data'].' WHERE demographic_id=\''.$demographicId.'\'';
	$dbo->query($sql);
	$sql = 'DELETE FROM '.$dbo->table['subscribers_data'].' WHERE demographic_id=\''.$demographicId.'\'';
	$dbo->query($sql);
	$sql = 'DELETE FROM '.$dbo->table['groups_criteria'].' WHERE demographic_id=\''.$demographicId.'\'';
	$dbo->query($sql);

	// delete demographic
	$sql = 'DELETE FROM '.$dbo->table['demographics'].' WHERE demographic_id=\''.$demographicId.'\'';
	return $dbo->affected($sql);
}

// dbDemographicUpdate: <bool> - Returns true if a demographic's paramenters get updated
function dbDemographicUpdate(& $dbo, & $input) {

	if (!dbDemographicCheck($dbo, str2db($input['demographic_id'])))
		return false;

	$sql = 'UPDATE '.$dbo->table['demographics'].' SET demographic_name=\''.str2db($input['demographic_name']).'\', demographic_prompt=\''.str2db($input['demographic_prompt']).'\', demographic_required=\''.str2db($input['demographic_required']).'\', demographic_active=\''.str2db($input['demographic_active']).'\', demographic_normally=\''.str2db($input['demographic_normally']).'\' WHERE demographic_id=\''.str2db($input['demographic_id']).'\' LIMIT 1';
	return $dbo->affected($sql);
}

// dbDemographicOptionAdd: <bool> - Returns true if option(s) are added to a multiple choice demographic
// automatically rids any duplicate choices
function dbDemographicOptionAdd(& $dbo, & $demographic_id, & $option) {

	// ensure demographic_id exists
	if (!dbDemographicCheck($dbo, $demographic_id) || empty ($option))
		return false;

	// set option(s) into array
	require_once (bm_baseDir.'/inc/lib.txt.php');
	$options = quotesplit($option);

	// get existing option(s)	
	$sql = 'SELECT demographic_options FROM '.$dbo->table['demographics'].' WHERE demographic_id=\''.$demographic_id.'\'';
	$oldoptions = quotesplit($dbo->query($sql, 0));

	// merge old options with new ones, getting rid of any duplicates
	if (!empty ($oldoptions))
		$options = array_unique(array_merge($oldoptions, $options));

	// add new options to demographic in DB, exploding array to csv with array2csv function.
	$sql = 'UPDATE '.$dbo->table['demographics'].' SET demographic_options=\''.addslashes(array2csv($options)).'\' WHERE demographic_id=\''.$demographic_id.'\' LIMIT 1';
	return $dbo->affected($sql);
}

// dbDemographicOptionDelete: <int/false> - removes an option from a multiple choice demographic and
//  returns the # of subscribers affected (ones who chose this option). The option is removed from
//  subscriber_data, group_criteria, and pending_data. If a subscriber is affected, they get flagged to 'update their records'
function dbDemographicOptionDelete(& $dbo, & $demographic_id, & $option) {

	// get existing option(s)
	require_once (bm_baseDir.'/inc/lib.txt.php');
	$sql = 'SELECT demographic_options FROM '.$dbo->table['demographics'].' WHERE demographic_id=\''.$demographic_id.'\'';
	$oldoptions = quotesplit($dbo->query($sql, 0));

	if (empty($oldoptions) || !is_array($oldoptions))
		bmKill('Bad oldoptions in dbDemographicOptionDelete');
		
	// remove option from options array (if exists)
	$key = array_search(str2str($option), $oldoptions);
	if (is_numeric($key))
		unset ($oldoptions[$key]);

	// Remove option from demographic [ using adjusted oldoptions array ] 
	$sql = 'UPDATE '.$dbo->table['demographics'].' SET demographic_options=\''.addslashes(array2csv($oldoptions)).'\' WHERE demographic_id=\''.$demographic_id.'\' LIMIT 1';
	$dbo->query($sql);
	

	// search for subscribers who had this option selected. Delete it from their data + flag subscriber
	$subscribers = array('subscribers_id' => array(), 'data_id' => array());
	$sql = 'SELECT data_id, subscribers_id FROM '.$dbo->table['subscribers_data'].' WHERE demographic_id=\''.$demographic_id.'\' AND value=\''.str2db($option).'\'';
	while ($row = $dbo->getRows($sql,TRUE)) {
		$subscribers['subscribers_id'][] = $row[1];
		$subscribers['data_id'][] = $row[0];
	}
	$affected = @count($subscribers['data_id']);
	
	if ($affected) {
	$sql = 'DELETE FROM '.$dbo->table['subscribers_data'].' WHERE data_id IN(\''.implode('\',\'', $subscribers['data_id']).'\')';
	$dbo->query($sql);
	
	require_once (bm_baseDir.'/inc/db_subscribers.php');
	dbFlagSubscribers($subscribers['subscribers_id']);
	}
	
	// remove from groups_criteria...
	// get existing criteria matches
	$sql = 'SELECT criteria_id, value FROM '.$dbo->table['groups_criteria'].' WHERE demographic_id=\''.$demographic_id.'\'';
	while ($row = $dbo->getRows($sql, TRUE)) {
		$oldoptions = quotesplit($row[1]);
		// see if option matches one of this group's filtering criteria
		$key = array_search(str2str($option), $oldoptions);
		if ($key) { // MATCHES 
			// remove option from matches array
			unset ($oldoptions[$key]);
			// if there are still matches left, update criteria. If not, delete the row.
			if (empty ($oldoptions))
				$sql = 'DELETE FROM '.$dbo->table['groups_criteria'].' WHERE criteria_id=\''.$row[0].'\' LIMIT 1';
			else
				$sql = 'UPDATE '.$dbo->table['groups_criteria'].' SET value=\''.addslashes(array2csv($oldoptions)).'\' WHERE criteria_id=\''.$row[0].'\' LIMIT 1';
			$dbo->query($sql);
		}
	}
	// remove from pending_data
	$sql = 'DELETE FROM '.$dbo->table['pending_data'].' WHERE demographic_id=\''.$demographic_id.'\' AND value=\''.str2db($option).'\'';
	$dbo->query($sql);

	return $affected;
}
?>