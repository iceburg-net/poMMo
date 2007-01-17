<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
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
 
/**********************************
	INITIALIZATION METHODS
 *********************************/
define('_poMMo_support', TRUE);
require ('../../bootstrap.php');
$pommo->init();

Pommo::requireOnce($pommo->_baseDir.'inc/classes/mailctl.php');

$code = PommoHelper::makeCode();

if(!PommoMailCtl::spawn($pommo->_baseUrl.'support/tests/mailing.test2.php?securityCode='.$code)) 
	Pommo::kill('Initial Spawn Failed! You must correct this before poMMo can send mails.');

sleep(6);

if (!is_file($pommo->_workDir . '/mailing.test.php')) {
	// make sure we can write to the file
	if (!$handle = fopen($pommo->_workDir . '/mailing.test.php', 'w')) 
		die('Unable to write to test file!');
	fclose($handle);
	unlink($pommo->_workDir.'/mailing.test.php');
	
	die('Initial Spawn Failed! You must correct this before poMMo can send mails.');
}
	
$o = PommoHelper::parseConfig($pommo->_workDir . '/mailing.test.php');
unlink($pommo->_workDir.'/mailing.test.php') or die('could not remove mailing.test.php');

if (!isset($o['code']) || $o['code'] != $code)
	die ('Spawning Failed. Codes did not match.');
	
if (!isset($o['spawn']) || $o['spawn'] == 0)
	die ('Inital spawn success. Respawn failed!');

die('Initial spawn success. Respawn success. Spawning Works!');