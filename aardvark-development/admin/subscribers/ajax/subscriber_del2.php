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
require ('../../../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/subscribers.php');

$pommo->init(array('noDebug' => TRUE));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

$pommo->toggleEscaping(); // _T and logger responses will be wrapped in htmlspecialchars


function jsonKill($msg, $success = "false", $ids = array()) {
	$json = "{success: $success, msg: \"".$msg."\", ids: [".implode(',',$ids)."]}";
	die($json);
}


$emails = array();
if (isset($_POST['emails'])) {
	$in = array_unique(preg_split("/[\s,]+/", $_POST['emails']));
	foreach($in as $email) {
		if (PommoHelper::isEmail($email))
			array_push($emails,$email);
	}
}

$c = 0;
if (count($emails) > 0)  {
	$ids = PommoSubscriber::getIDByEmail($emails);
	$c = PommoSubscriber::delete($ids);
}

if ($c == 0)
	jsonKill(Pommo::_T('No subscribers were removed.'),"false");

jsonKill(sprintf(Pommo::_T('You have removed %s subscribers!'), $c),"true", $ids);
	 
?>