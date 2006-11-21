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
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/groups.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/fields.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/subscribers.php');
Pommo::requireOnce($pommo->_baseDir.'inc/lib/class.pager.php');

$pommo->init();
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;
	
/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();
$smarty->assign('returnStr', Pommo::_T('Subscribers Page'));


/** SET PAGE STATE
 * limit	- The Maximum # of subscribers to show per page
 * sort		- The subscriber field to sort by (email, ip, time_registered, time_touched, status)
 * order	- Order Type (ascending - ASC /descending - DESC)
 * 
 * status	- Filter by subscriber status (active, inactive, pending, all)
 * group	- Filter by group members (groupID or 'all')
 */
// Initialize page state with default values overriden by those held in $_REQUEST
$state =& PommoApi::stateInit('subscribers_manage',array(
	'limit' => 100,
	'sort' => 'email',
	'order' => 'ASC',
	'status' => 'active',
	'group' => 'all'),
	$_REQUEST);
	

// get subscriber count
$members = array();
if (is_numeric($state['group'])) {
	$members =& PommoGroup::getMembers($state['group'], $state['status']);
	$tally = count($members);
}
else {
	$tally = PommoGroup::tally('all');
}
// Instantiate Pager class (Using modified template from author)
$p = new Pager($appendUrl);
$start = $p->findStart($limit);
$pages = $p->findPages($groupCount, $limit);
// $pagelist : echo to print page navigation.
$pagelist = $p->pageList($_GET['page'], $pages);




$smarty->assign('fields', $fields);
$smarty->assign('groups',$groups);
$smarty->assign('table',$table);
$smarty->assign('group_id',$group_id);
$smarty->assign('limit',$limit);
$smarty->assign('order',$order);
$smarty->assign('orderType',$orderType);
$smarty->assign('subscribers',$subscribers);
$smarty->assign('pagelist',$pagelist);
$smarty->assign('groupCount',$groupCount);

$smarty->display('admin/subscribers/subscribers_manage.tpl');
Pommo::kill();
?>