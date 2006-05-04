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
defined('_IS_VALID') or die('Move along...');

function parse_mysql_dump($ignoreerrors = false) {
	
	global $dbo;
	global $logger;
	
			$file_content = file(bm_baseDir."/install/sql.schema.php");
			if (empty ($file_content))
				bmKill(_T('Error installing. Could not read sql.schema.php'));
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
							$logger->addErr(_T('Database Error: ').$dbo->getError());
							return false;
						}
						$query = '';
					}
				}
			}
			return true;
		}
?>