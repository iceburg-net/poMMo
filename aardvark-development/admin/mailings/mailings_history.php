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
	'limit' => 10,
	'sort' => 'started',
	'order' => 'asc'),
	$_REQUEST);
	
$tally = PommoMailing::tally();

// Instantiate Pager class (Using modified template from author)
$p = new Pager();
$start = $p->findStart($state['limit']);
$pages = $p->findPages($tally, $state['limit']);
// $pagelist : echo to print page navigation.
$pagelist = $p->pageList($_GET['page'], $pages);


// Fetch Mailings
$mailings = PommoMailing::get(array(
	'noBody' => TRUE,
	'sort' => $state['sort'],
	'order' => $state['order'],
	'limit' => $state['limit'],
	'offset' => $start));


$smarty->assign('pagelist',$pagelist);
$smarty->assign('state',$state);
$smarty->assign('mailings', $mailings);
$smarty->assign('tally',$tally); // was "rowinset"

$smarty->display('admin/mailings/mailings_history.tpl');
Pommo::kill();
?>