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
	STARTUP ROUTINES
 *********************************/

define('_IS_VALID', TRUE);
require ('../bootstrap.php');

$poMMo = & fireup('install');

// Tests the background Mail processor. Spawned via httpspawn. Write the time to cache directory

if (!$handle = fopen(bm_workDir . '/test.php', 'w')) {
	die();
}

$fileContent = '<?php $testTime=' . time() . '; ?>';

fwrite($handle, $fileContent);
fclose($handle);
?>