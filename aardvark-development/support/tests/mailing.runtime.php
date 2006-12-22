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
 
// set maximum runtime of this script in seconds (Default: 80). 
$maxRunTime = 90;
if (ini_get('safe_mode'))
	$maxRunTime = ini_get('max_execution_time') - 10;
else
	set_time_limit(0);

define('_poMMo_support', TRUE);

require ('../../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'install/helper.install.php');

if (bmIsInstalled())
	$pommo->init();
else
	$pommo->init(array('authLevel' => 0));


echo 'Initial Run Time: '.ini_get('max_execution_time').' seconds <br>';
echo '<br/> SLEEPING FOR 90 SECONDS -- FAILED IF "SUCCESS" NEVER OUTPUTTED';
echo '<hr>';
ob_flush(); flush();
$i = 0;
while ($i < 90) {
	$i += 10;
	sleep(10);
	echo "$i <br />"; 
	ob_flush(); flush();
}

die('<hr>SUCCESS');