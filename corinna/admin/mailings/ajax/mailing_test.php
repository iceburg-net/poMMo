<?php
/**
 * == Author: Brice Burgess <bhb@iceburg.net>
 *    - All rights reserved
 * == Created: Dec 4, 2006
 */
 
 // send a test mail to an address if requested
if (!empty($_POST['testMail'])) {
	if (isEmail($_POST['testTo'])) {
		require_once ($pommo->_baseDir.'inc/lib.mailings.php');
		$logger->addMsg(bmSendTestMailing($_POST['testTo'],$input));	
		}
	else
		$logger->addMsg(Pommo::_T('Invalid Email Address'));
}
?>
