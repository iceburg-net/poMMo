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
require (bm_baseDir . '/install/helper.install.php');

$poMMo = & fireup('install');
$dbo = & $poMMo->_dbo;

// allow access to this page if not installed 
if (bmIsInstalled() && !$poMMo->isAuthenticated()) {
	bmKill(sprintf(_T('Denied access. You must %s logon %s to access this page...'),
		 '<a href="'.bm_baseUrl.'/index.php?referer='.$_SERVER['PHP_SELF'].'">',
		'</a>'));
	die();
}

echo<<<EOF

<hr>
<div style="width: 100%; text-align: center;">
	poMMo support v0.01
	<hr>
</div>

<ul>
	<li><a href="support.php?clearWork=TRUE">Clear Work Directory</a></li>
	<br>
	<li><a href="support.php?checkSpawn=TRUE">Test Mailing Processor</a></li>
</ul>
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
elseif (isset($_GET['checkSpawn'])) {
	
	echo 'Attempting to spawn a background script... please wait.<br><br>';
	ob_flush();
	flush();
	
	// call background script. Script writes time() as $testTime to workdir/test.php. Include file to compare.
	bmHttpSpawn(bm_baseUrl.'/inc/sup.testmailer.php');
	sleep(5);
	@include(bm_workDir.'/test.php');
	echo (isset($testTime) && ((time() - $testTime) < 6))? 'Background Spawning SUCCESS' :
		'Background Spawning FAILED, mailings will not process. Seek support.';
	
}

bmKill();