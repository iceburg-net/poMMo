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
define('_IS_VALID', TRUE);
 
require('../../bootstrap.php');
require_once (bm_baseDir.'/inc/db_history.php');		// Mailing History Database Handling
require_once (bm_baseDir.'/inc/class.pager.php');


$poMMo =& fireup("secure");
$logger = & $poMMo->logger;
$dbo = & $poMMo->openDB();


	/* Setup Vars
	 * limit		- Nr. of Mailings displayed per Pager-Site
	 * mailcount	- Nr. of Mailings in mailing_history table
	 */
	//$action = 		str2db($_REQUEST['action']);		echo "ACTION:".$action."<br>";
	$limit = 		(empty ($_REQUEST['limit'])) ? '10' : str2db($_REQUEST['limit']);
	$orderType = 	(empty ($_REQUEST['orderType'])) ? 'ASC' : str2db($_REQUEST['orderType']);
	$order = 		(empty ($_REQUEST['order'])) ? 'id' : str2db($_REQUEST['order']);
	$appendUrl = 	'&limit='.$limit."&order=".$order."&orderType=".$orderType;
	$mailcount =	dbGetMailingCount($dbo);		// in inc/db_history.php


	/* Instantiate Pager class (Using modified template from author) */
	// This seems to not handle the case, that when we are on the last page of multiple pages,
	// and then choose to increase the diplay number then the start value is too great
	// eg. limit=5, 3 pages, go to page 3 -> then choose limit=10 
	// -> no mailings found because of start = 20 
	// its doing right, but less user friendly it it says no mailing, but its only that there are no mailings in this range
	$p = new Pager($appendUrl);
	$start = $p->findStart($limit);					//echo "<br>START: " . $start . "<br>";
	$pages = $p->findPages($mailcount, $limit); 	//echo "<br>PAGES: " . $pages . "<br>";
	// $pagelist : echo to print page navigation. -- TODO: adding appendURL to every link gets VERY LONG!!! come up w/ new plan!
	$pagelist = $p->pageList($_GET['page'], $pages);



	// Fetch Mailings
	$mailings = & dbGetMailingHistory($dbo, $start, $limit, $order, $orderType);		// in inc/db_history.php
	$mailsdisplayed = $dbo->records();

	if (empty($mailings)) {

		// There are no mailings present in the mailing history, or some query malfunction
		
		$nomailing = true;
		//$dberror = sprintf(_T('Database Query Error. Return to the %s Mailing Page %s'), '<a href="admin_mailings.php"', '</a>');


		/**********************************
				SETUP TEMPLATE PAGE
		 *********************************/
		$smarty = & bmSmartyInit();
			
		$smarty->assign('returnStr', _T('Mailings Page'));
		$smarty->assign('returnStr2', _T('Mailings History'));
		$smarty->assign('nomailing', $nomailing);

		if (!empty($errorstr)) { $smarty->assign('errorstr', $errorstr); }

		$smarty->display('admin/mailings/mailings_history.tpl');
		bmKill();
		
		
	} else {	

		// If there are mailings display them

		/**********************************
				SETUP TEMPLATE PAGE
		 *********************************/
		$smarty = & bmSmartyInit();
		$smarty->assign('returnStr', _T('Mailings Page'));
		$smarty->assign('mailings',$mailings);
		$smarty->assign('limit',$limit);
		$smarty->assign('order',$order);
		$smarty->assign('orderType',$orderType);
		$smarty->assign('pagelist',$pagelist);
		$smarty->assign('rowsinset', $mailcount);

		if (!empty($errorstr)) { $smarty->assign('errorstr', $errorstr); }

		$smarty->display('admin/mailings/mailings_history.tpl');
		bmKill();

	}
	

?>

