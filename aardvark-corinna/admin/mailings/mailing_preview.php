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
	
		echo "<h1>"; print_r($poMMo); echo "</h1><br><br>$poMMo->get('mailingData')<p>";
	// TODO --> rewrite corinna's method to use mailingData['body'] (get rid of requestr, etc. dbGetHTMLBody(), etc.)
	
	$append = NULL;
	if (isset($_GET['viewid'])) // coming from mailings_history
		$append = $_GET['viewid']; 
		$mailingData =& $poMMo->get('mailingData'.$append); //why append id?
	if (get_magic_quotes_gpc()) {
		echo stripslashes($mailingData['body']);
	} else {
		echo $mailingData['body'];
	}
	
	//TODO don't forget to put away, it prints 2 times  mailingData
	var_dump($mailingData['body']);
?>