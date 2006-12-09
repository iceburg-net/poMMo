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
Pommo::requireOnce($pommo->_baseDir.'inc/classes/mailctl.php');

$pommo->init(array('authLevel' => 0, 'install' => TRUE));

$initial = (isset($_GET['initial'])) ? $_GET['initial'] : time();
$respawn = (isset($_GET['respawn'])) ? $_GET['respawn'] : false;


$fileContent = "<?php die(); ?>\n[initial] = $initial \n";
if($respawn)
	$fileContent .= "[respawn] = $respawn";


if (!$handle = fopen($pommo->_workDir . '/mailing.test.php', 'w')) 
Pommo::kill('Unable to write to test file');

if (fwrite($handle, $fileContent) === FALSE) 
	Pommo::kill('Unable to write to test file');
fclose($handle);

sleep(1);

if(!$respawn)
	PommoMailCtl::spawn($pommo->_baseUrl.'support/tests/mailing.test2.php?initial='.$initial.'&respawn='.time());
	
Pommo::kill();