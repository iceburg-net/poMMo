<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * SOURCE: http://bmail.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/
define('_IS_VALID', TRUE);

require('../../bootstrap.php');
require_once (bm_baseDir.'/inc/db_mailing.php');

$bMail = fireup('secure');
$dbo = & $bMail->openDB();

if (!empty ($_GET['command'])) {
	switch ($_GET['command']) {
		case "stop" :
			dbMailingStamp($dbo, "stop");
			break;
		case "restart" :
			dbMailingStamp($dbo, "restart");
			$sql = 'SELECT securityCode FROM '.$dbo->table['mailing_current'];
  			$code = $dbo->query($sql,0,0);
  			bmHttpSpawn(bm_baseUrl.'/admin/mailings/mailings_send4.php?securityCode='.$code);
			break;
		case "kill" :
			dbMailingEnd($dbo);
			break;
		case "clear" :
			$sql = 'UPDATE '.$dbo->table['mailing_current'].' SET notices=NULL';
			$dbo->query($sql);
			break;
		default :
			break;
	}
	sleep(1);
  	bmRedirect('mailing_status.php');
}