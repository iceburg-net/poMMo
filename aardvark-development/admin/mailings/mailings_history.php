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
require ('../../bootstrap.php');
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

// fireup Monte's pager
$smarty->addPager($state['limit'], $tally);
$start = SmartyPaginate::getCurrentIndex();
SmartyPaginate::assign($smarty);


// Fetch Mailings
$mailings = PommoMailing::get(array(
	'noBody' => TRUE,
	'sort' => $state['sort'],
	'order' => $state['order'],
	'limit' => $state['limit'],
	'offset' => $start));
	
// calculates Mails / Hour
foreach(array_keys($mailings) as $key) {
	$m =& $mailings[$key];
	if(!empty($m['end']) && !empty($m['sent'])) {
		$start = strtotime($m['start']);
		$end = strtotime($m['end']);
		$m['mph'] = round(($m['sent'] / (($end - $start)+1)) * 3600);
	}
	else
		$m['mph'] = 0;
}


$smarty->assign('state',$state);
$smarty->assign('mailings', $mailings);
$smarty->assign('tally',$tally); // was "rowinset"

$smarty->display('admin/mailings/mailings_history.tpl');
Pommo::kill();
?>