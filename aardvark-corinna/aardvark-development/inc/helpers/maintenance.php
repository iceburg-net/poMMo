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
 
 class PommoHelperMaintenance {
 	
 	// write baseURL to maintenance.php in config file syntax (to be read back by embedded apps)
 	function memorizeBaseURL() {
 		global $pommo;
 		
 		if (!$handle = fopen($pommo->_workDir . '/maintenance.php', 'w')) {
			Pommo::kill('Unable to perform maintenance');
		}
		$fileContent = "<?php die(); ?>\n[baseURL] = \"$pommo->_baseUrl\"\n";
		if (fwrite($handle, $fileContent) === FALSE) {
			Pommo::kill('Unable to perform maintenance');
		}
		
		fclose($handle);
 	}
 	
 	function rememberBaseURL() {
 		global $pommo;
 		$config = PommoHelper::parseConfig($pommo->_workDir . '/maintenance.php');
 		return $config['baseURL'];
 	}
 }
?>
