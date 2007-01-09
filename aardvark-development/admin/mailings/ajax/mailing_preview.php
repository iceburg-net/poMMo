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
require ('../../../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/mailings.php');

$config = PommoAPI::configGet('public_history');
if($config['public_history'] == 'on') {
	$pommo->init(array('noDebug' => TRUE, 'keep' => TRUE, 'authLevel' => 0));
} else {
	$pommo->init(array('noDebug' => TRUE, 'keep' => TRUE));	
}
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

$input = (isset($_GET['mail_id'])) ? 
	current(PommoMailing::get(array('id' => $_GET['mail_id']))) :
	$input = $pommo->_session['state']['mailings_send2'];

die($input['body']);

?>