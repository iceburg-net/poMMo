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
define('_IS_VALID', TRUE);
define('_IS_SUPPORT', TRUE);

require ('bootstrap.php');

$poMMo = & fireup('secure');

echo<<<EOF

<hr>
<div style="width: 100%; text-align: center;">
	poMMo support v0.01
	<hr>
</div>

<br>
<a href="support.php?clearWork=TRUE">Clear Work Directory</a>
<br>
<hr>

<div style="width: 100%; text-align: center;">
	Status
	<hr>
</div>
EOF;

if (isset($_GET['clearWork'])) {
	function delDir($dirName) {
		if (empty ($dirName)) {
			return true;
		}
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
			if ($dirName != bm_workDir)
				@ rmdir($dirName) or die('Folder ' . $dirName . ' couldn\'t be deleted!');
		} else {
			return false;
		}
		return true;
	}
	
	echo (delDir(bm_workDir)) ? 'Work Directory Cleared' : 'Unable to Clear Work Directory -- Does it exist?';
}

bmKill();