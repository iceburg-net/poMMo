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
Pommo::requireOnce($pommo->_baseDir.'inc/lib/class.json.php');
Pommo::requireOnce($pommo->_baseDir.'inc/classes/mailing.php');

$pommo->init(array('noDebug' => TRUE, ));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

//$pommo->toggleEscaping(); // _T and logger responses will be wrapped in htmlspecialchars

$mailing = current(PommoMailing::get(array('active' => TRUE)));


$json = array('success' => TRUE);
switch ($_GET['cmd']) {
	case 'cancel' : // cancel/end mailing
		PommoMailing::finish($mailing['id'], TRUE);
		break;
	case 'restart' : // restart mailing
	case 'stop' :  // pause mailing
		$query = "
			UPDATE ".$dbo->table['mailing_current']."
			SET command='".$_GET['cmd']."'
			WHERE current_id=%i";
		$query = $dbo->prepare($query,array($mailing['id']));
		if (!$dbo->query($query))
			$json['success'] = FALSE;
		
		if($_GET['cmd'] == 'restart')
			PommoMailing::respawn(array('code' => $mailing['code'], 'relayID' => null, 'serial' => null, 'spawn' => null));
		break;
}
$encoder = new json;
die($encoder->encode($json));
?>