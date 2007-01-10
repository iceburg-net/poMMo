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
 
/**********************************
	INITIALIZATION METHODS
 *********************************/
define('_poMMo_support', TRUE);
require ('../../bootstrap.php');
$pommo->init();

function delDir($dirName) {
	global $pommo;
	
	if (empty ($dirName)) 
		return true;
		
	if (file_exists($dirName)) {
		$dir = dir($dirName);
		while ($file = $dir->read()) {
			if ($file != '.' && $file != '..') {
				if (is_dir($dirName . '/' . $file)) {
					delDir($dirName . '/' . $file);
				} else {
					@ unlink($dirName . '/' . $file) or die('File ' . $dirName . '/' . $file . ' couldn\'t be deleted!');
				}
			}
		}
		$dir->close();
		if ($dirName != $pommo->_workDir)
			@ rmdir($dirName) or die('Folder ' . $dirName . ' couldn\'t be deleted!');
	} else {
		return false;
	}
	return true;
}

echo (delDir($pommo->_workDir)) ? 'Work Directory Cleared' : 'Unable to Clear Work Directory -- Does it exist?';