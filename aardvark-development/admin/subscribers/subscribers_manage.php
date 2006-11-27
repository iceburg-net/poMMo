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
	'limit' => 150,
	'sort' => 'email',
	'order' => 'ASC',
	'status' => 'active',
	'group' => 'all'),
	$_REQUEST);

// get the group
$group = new PommoGroup($state['group'], $state['status']);

// Instantiate Pager class (Using modified template from author)
$p = new Pager();
$start = $p->findStart($state['limit']);
$pages = $p->findPages($group->_tally, $state['limit']);
// $pagelist : echo to print page navigation.
$pagelist = $p->pageList($_GET['page'], $pages);

// get the subscribers details
$subscribers = $group->members(array(
	'sort' => $state['sort'],
	'order' => $state['order'],
	'limit' => $state['limit'],
	'offset' => $start));
	

$smarty->assign('pagelist',$pagelist);
$smarty->assign('state',$state);
$smarty->assign('subscribers',$subscribers);
$smarty->assign('tally',$group->_tally);
$smarty->assign('groups',PommoGroup::get());
$smarty->assign('fields',PommoField::get());

$smarty->display('admin/subscribers/subscribers_manage.tpl');
Pommo::kill();
?>