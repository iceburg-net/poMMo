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
	require_once (bm_baseDir.'/inc/db_history.php'); // for DB retrieval of body

	$poMMo =& fireup("secure","dataSave");
	$logger = & $poMMo->logger;	//ct
	$dbo = & $poMMo->openDB();	//ct


	//ct
	//print_r($poMMo); 

	// if there is a specific id given get the database record from DB.mailing_history
	// i choosed this approach, so i don't  $POST around Body Data in the mailings_history.php and mailings_mod.php
	if ((!empty($_REQUEST['viewid'])) && (!empty($_REQUEST['action']))) {

		// if action = viewhtml
		$viewid = $_REQUEST['viewid'];
		$text = dbGetHTMLBody($dbo, $viewid);
		$mailbody['body'] = $text['body'];
		
		$poMMo->set($mailbody);
		
	} //end ct
	
	
	$html =& $poMMo->dataGet();
 
	if (get_magic_quotes_gpc()) {
		echo stripslashes($html['body']);
	} else {
		echo $html['body'];
	}
		

?>
