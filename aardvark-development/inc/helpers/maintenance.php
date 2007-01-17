<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2006 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/licenses/gpl.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. Notify the above author of modifications to contents within.
 * 
 *  WHY? Because this is a community project -- purposely released under the GPL.
 *    We'd love to have the possiblity to include your derivative works! 
 *    We'd love to coordinate around your development efforts!
 *    We'd love to assist you with your changes!
 *    DON'T BE A STRANGER!
 * 
 ** [END HEADER]**/
 
 class PommoHelperMaintenance {
 	
 	function perform() {
 		global $pommo;
 		PommoHelperMaintenance::memorizeBaseURL();
 		if(is_file($pommo->_workDir.'/import.csv'))
 			if (!unlink($pommo->_workDir.'/import.csv'))
 				Pommo::kill('Unable to remove import.csv');
 		return true;
 		
 	}
 	// write baseURL to maintenance.php in config file syntax (to be read back by embedded apps)
 	function memorizeBaseURL() {
 		global $pommo;
 		
 		if (!$handle = fopen($pommo->_workDir . '/maintenance.php', 'w'))
			Pommo::kill('Unable to prepare maintenance.php for writing');
			
		$fileContent = "<?php die(); ?>\n[baseURL] = \"$pommo->_baseUrl\"\n";
		
		if (!fwrite($handle, $fileContent)) 
			Pommo::kill('Unable to perform maintenance');
		
		fclose($handle);
 	}
 	
 	function rememberBaseURL() {
 		global $pommo;
 		$config = PommoHelper::parseConfig($pommo->_workDir . '/maintenance.php');
 		return $config['baseURL'];
 	}
 }
?>
