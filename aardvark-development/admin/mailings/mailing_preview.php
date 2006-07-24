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
 
	define('_IS_VALID', TRUE);

	require('../../bootstrap.php');
	$poMMo = & fireup('secure', 'keep');
	
	// TODO --> rewrite corinna's method to use mailingData['body'] (get rid of requestr, etc. dbGetHTMLBody(), etc.)
	
	$mailingData =& $poMMo->get('mailingData');
	if (get_magic_quotes_gpc()) {
		echo stripslashes($mailingData['body']);
	} else {
		echo $mailingData['body'];
	}
?>
