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
Pommo::requireOnce($pommo->_baseDir.'install/helper.install.php');

if (bmIsInstalled())
	$pommo->init();
else
	$pommo->init(array('authLevel' => 0));

$maxRunTime = 2;
echo 'Initial Run Time: '.ini_get('max_execution_time').' seconds <br>';
if (ini_get('safe_mode')) {
	$maxRunTime = ini_get('max_execution_time') - 3;
	echo 'Safe mode is enabled<br>';
}
set_time_limit($maxRunTime);

echo '<br/> SLEEPING FOR RUNTIME -- FAILED IF NO OUTPUT BELOW THIS LINE';
echo '<hr>';
sleep(10);

echo '<br/> SUCCESS <br/>';
echo 'End Run Time: '.ini_get('max_execution_time').' seconds <br>';
	