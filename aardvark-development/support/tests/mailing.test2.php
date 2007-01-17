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
$pommo->init(array('install' => TRUE));

Pommo::requireOnce($pommo->_baseDir.'inc/classes/mailctl.php');

$code = (empty($_GET['securityCode'])) ? null : $_GET['securityCode'];
$spawn = (!isset($_GET['spawn'])) ? 0 : ($_GET['spawn'] + 1);

$fileContent = "<?php die(); ?>\n[code] = $code\n[spawn] = $spawn\n";

if (!$handle = fopen($pommo->_workDir . '/mailing.test.php', 'w')) 
	die('Unable to write to test file');

if (fwrite($handle, $fileContent) === FALSE) 
	die('Unable to write to test file');
	
fclose($handle);

if($spawn > 0)
	die();

sleep(1);

$page = $pommo->_baseUrl.'support/tests/mailing.test2.php';
PommoMailCtl::respawn(array('code' => $code, 'spawn' => $spawn), $page);
	
die();