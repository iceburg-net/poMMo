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

Pommo::requireOnce($pommo->_baseDir.'inc/classes/mailctl.php');

$initial = time();

if(!PommoMailCtl::spawn($pommo->_baseUrl.'support/tests/mailing.test2.php?initial='.$initial)) 
	Pommo::kill('Initial Spawn Failed');

sleep(6);

if (!is_file($pommo->_workDir . '/mailing.test.php'))
	die('Spawning Failed');
	
$o = PommoHelper::parseConfig($pommo->_workDir . '/mailing.test.php', 'w');

echo 'INITIAL SPAWN: '. ((is_numeric($o['initial']) && $initial == $o['initial']) ? 'SUCCESS' : 'FAILED');
echo '<br/>';
echo 'SECOND SPAWN: '. ((is_numeric($o['respawn'])) ? 'SUCCESS' : 'FAILED');
echo '<br/>';