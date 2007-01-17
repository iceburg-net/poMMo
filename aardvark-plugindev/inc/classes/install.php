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

class PommoInstall {
 	
 	// parses a SQL file (usually generated via mysqldump)
 	// text like ':::table:::' will be replaced with $dbo->table['table']; (to add prefix)
 	
 	function parseSQL($ignoreerrors = false, $file = false) {
 		global $pommo;
		$dbo =& $pommo->_dbo;
		$logger =& $pommo->_logger;
	
		if (!$file)
			$file = $pommo->_baseDir."install/sql.schema.php";
			
		$file_content = @file($file);
		if (empty ($file_content))
			Pommo::kill('Error installing. Could not read '.$file);
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
 	
 	// verifies if poMMo has been installed.
 	// returns bool (true if installed)
 	function verify() {
 		global $pommo;
		$dbo =& $pommo->_dbo;
 		
 		if (is_object($dbo)) {
			$sql = 'SHOW TABLES LIKE \'' . $dbo->table['groups'] . '\'';
			if ($dbo->records($sql))
				return true;
		}
	
		return false;
 	}
 	
 	// performs an update increment
 	// checks if the update has already been performed
 	// returns update status
 	function incUpdate($serial, $sql, $msg = "Performing Update") {
 		global $pommo;
 		$dbo =& $pommo->_dbo;
 		$logger =& $pommo->_logger;
 		
 		if (!is_numeric($serial))
 			Pommo::kill('Invalid serial passed; '.$serial);
 			
 		$msg = $serial . ". $msg ...";
 			
		$query = "
			SELECT serial FROM ".$dbo->table['updates']." 
			WHERE serial=%i";
		$query = $dbo->prepare($query,array($serial));
		if ($dbo->records($query)) {
			$msg .= "skipped.";
			$logger->addMsg($msg);
			return true;
		}
			
		$query = $dbo->prepare($sql);
		if (!$dbo->query($query)) {
			$msg .= "FAILED.";
			$logger->addErr($msg);
			return false;
		}
		
		$query = "
			INSERT INTO ".$dbo->table['updates']." 
			(serial) VALUES(%i)";
		$query = $dbo->prepare($query,array($serial));
		if (!$dbo->query($query))
			Pommo::kill('Unable to serialize');
		
		$msg .= "done.";
		$logger->addMsg($msg);
		return true;
 	}
}
?>