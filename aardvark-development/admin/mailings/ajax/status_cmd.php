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
Pommo::requireOnce($pommo->_baseDir.'inc/lib/class.json.php');
Pommo::requireOnce($pommo->_baseDir.'inc/classes/mailctl.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/mailings.php');

$pommo->init(array('noDebug' => TRUE, ));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

//$pommo->toggleEscaping(); // _T and logger responses will be wrapped in htmlspecialchars

$mailing = current(PommoMailing::get(array('active' => TRUE)));


$json = array('success' => TRUE);
switch ($_GET['cmd']) {
	case 'cancel' : // cancel/end mailing
		PommoMailCtl::finish($mailing['id'], TRUE);
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
			PommoMailCtl::spawn($pommo->_baseUrl.'admin/mailings/mailings_send4.php?securityCode='.$mailing['code']);
		
		break;
}
$encoder = new json;
die($encoder->encode($json));
?>