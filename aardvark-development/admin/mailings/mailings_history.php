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
require ('../../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/lib/class.pager.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/mailings.php');

$pommo->init();
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();
$smarty->assign('returnStr', Pommo::_T('Mailings Page'));


/** SET PAGE STATE
 * limit	- # of mailings per page
 * sort		- Sorting of Mailings [subject, mailgroup, subscriberCount, started, etc.]
 * order	- Order Type (ascending - ASC /descending - DESC)
 */
// Initialize page state with default values overriden by those held in $_REQUEST
$state =& PommoAPI::stateInit('mailings_history',array(
	'limit' => 15,
	'sort' => 'started',
	'order' => 'ASC'),
	$_REQUEST);
	
$tally = PommoMailing::tally()
$mailcount = dbGetMailingCount($dbo); // func in inc/db_history.php

/* Instantiate Pager class (Using modified template from author) */
$p = new Pager();
if ($p->findStart($limit) > $mailcount) $_GET['page'] = '1';
$pages = $p->findPages($mailcount, $limit);
$start = $p->findStart($limit); 
$pagelist = $p->pageList($_GET['page'], $pages);


// Fetch Mailings
$mailings = & dbGetMailingHistory($dbo, $start, $limit, $sortBy, $sortOrder); // func in inc/db_history.php

// If there are mailings display them
$smarty->assign('mailings', $mailings);
$smarty->assign('state',$state);
$smarty->assign('pagelist', $pagelist);
$smarty->assign('rowsinset', $mailcount);

$smarty->display('admin/mailings/mailings_history.tpl');

Pommo::kill();
?>