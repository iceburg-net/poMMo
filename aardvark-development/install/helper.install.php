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

// TODO : Make array of queries.. send array to dbo->query(); update query function to allow arrays...
// TODO : Play with output buffering...
// TODO : load array of done serials @ beginning of loop.. not per each update!!!
// TODO : delete from data tables where demographic type is checkbox & value is off
//      * ensure program behaves similarly (ignoring 'off') -- ie. user_update2.php removes 'off's

// NOTE TO SELF -- all updates in a upgrade must be serialized, and their serial incremented!

function parse_mysql_dump($ignoreerrors = false) {
	global $pommo;
	$dbo =& $pommo->_dbo;
	$logger =& $pommo->_logger;

			$file_content = @file($pommo->_baseDir."install/sql.schema.php");
			if (empty ($file_content))
				Pommo::kill('Error installing. Could not read '.$pommo->_baseDir.'install/sql.schema.php');
			$query = '';
			foreach ($file_content as $sql_line) {
				$tsl = trim($sql_line);
				if (($sql_line != "") && (substr($tsl, 0, 2) != "--") && (substr($tsl, 0, 1) != "#")) {
					$query .= $sql_line;
					if (preg_match("/;\s*$/", $sql_line)) {
						$matches = array();
						preg_match('/:::(.+):::/',$query,$matches);
						if ($matches[1])
							$query = preg_replace('/:::(.+):::/',$dbo->table[$matches[1]], $query);
							$query = trim($query);
						if (!$dbo->query($query) && !$ignoreerrors) {
							$logger->addErr(Pommo::_T('Database Error: ').$dbo->getError());
							return false;
						}
						$query = '';
					}
				}
			}
			return true;
		}
		

// <bool> Returns true if the program is installed, false if not. TODO: eventaully allow for table prefixing..
function bmIsInstalled() {
	global $pommo;
	$dbo =& $pommo->_dbo;
	
	if (is_object($dbo)) {
		$sql = 'SHOW TABLES LIKE \'' . $dbo->table['groups'] . '\'';
		if ($dbo->records($sql))
			return true;
	}
	
	return false;
}

// Returns the poMMo revision the user is upgrading from
function getOldVersion(& $dbo) {
	$oldRevision = NULL;

	$sql = "SELECT config_value FROM {$dbo->table['config']} WHERE config_name='revision'";
	$oldRevision = $dbo->query($sql, 0);
	if (is_numeric($oldRevision))
		return $oldRevision;

	// Revision was not found in database... check to see if we're dealing w/ an OLD version of poMMo
	$sql = "SELECT * FROM {$dbo->table['subscriber_data']} LIMIT 1";
	if ($dbo->records($sql)) {
		$sql = "SELECT * FROM {$dbo->table['config']} LIMIT 1";
		if (!$dbo->query($sql))
			$oldRevision = 5; // if there are demographics in subscriber_data, but the config table does not exist, we're using Aardvark PR6 or before.'
	}
	return $oldRevision;
}

// updates the version + revision in the DB
function bmBumpVersion(& $dbo, $revision, $versionStr) {
	global $logger;

	$logger->addMsg(Pommo::_T('Bumping poMMo version to: ') . $versionStr);
	// TODO : Make array of queries.. send array to dbo->query(); update query function to allow arrays...
	$sql = 'UPDATE `' . $dbo->table['config'] . '` SET config_value=\'' . $revision . '\' WHERE config_name=\'revision\'';
	$dbo->query($sql);
	$sql = 'UPDATE `' . $dbo->table['config'] . '` SET config_value=\'' . $versionStr . '\' WHERE config_name=\'version\'';
	$dbo->query($sql);
}

// returns true if a update has already been performed before [protects against user refreshing upgrade page/allows incremental upgrades]
function checkUpdate($serial, & $dbo) {
	$sql = "SELECT update_serial FROM {$dbo->table['updates']} WHERE update_serial='" . $serial . "'";
	if ($dbo->records($sql))
		return true;
	return false;
}

// returns true if part of a upgrade was sucessfully performed
function performUpdate(& $sql, & $dbo, $serial, $message = NULL, $check = TRUE, $sqlBool = FALSE) {

	global $logger;

	// check to see if this was already performed. Bypassed if check is false
	if ($check)
		if (checkUpdate($serial, $dbo))
			return true;

	// if sqlBool is true (passed as argument), update has been done elsewhere. 
	// evaluate against $sql [should be passed as <bool> if the update was performed elsewhere]
	if ($sqlBool)
		$sqlBool = $sql;
	else { // perform the query

		// convert sql querty to an array if it isn't already
		if (!is_array($sql))
			$sql = array (
				$sql
			);

		$sqlBool = TRUE;
		foreach ($sql as $query)
			if (!$dbo->query($query))
				$sqlBool = FALSE;
	}

	// If an update has been performed, serialize it. Return status of update.
	if ($sqlBool) {
		$sql = "INSERT INTO {$dbo->table['updates']} (update_serial) VALUES('" . $serial . "')";
		if ($dbo->affected($sql) != 1) {
			$logger->addMsg(sprintf(Pommo::_T('Failed to properly serialize update %s : %s'), $serial, $message));
			return false;
		}
		$logger->addMsg($serial . '. ' . $message . '... ' . Pommo::_T('success!'));
		return TRUE;
	} else {
		$logger->addMsg($serial . '. ' . $message . '... <span style="font-weight: bold; background-color: red; color: white;">' . Pommo::_T('FAILED!') . '</span>');
		return FALSE;
	}
}
?>