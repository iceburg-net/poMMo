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
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/groups.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/fields.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/subscribers.php');

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
 * info		- (hide/show) Time Registered/Updated, IP address
 * 
 * status	- Filter by subscriber status (active, inactive, pending, all)
 * group	- Filter by group members (groupID or 'all')
 */
// Initialize page state with default values overriden by those held in $_REQUEST
$state =& PommoAPI::stateInit('subscribers_manage',array(
	'limit' => 150,
	'sort' => 'email',
	'order' => 'ASC',
	'status' => 1,
	'group' => 'all',
	'info' => 'hide'),
	$_REQUEST);

if($state['sort'] != 'email')
	$state['info'] = 'show';

// get the group
$group = new PommoGroup($state['group'], $state['status']);

// fireup Monte's pager
$smarty->addPager($state['limit'], $group->_tally);
$start = SmartyPaginate::getCurrentIndex();
SmartyPaginate::assign($smarty);


// get the subscribers details
$subscribers = $group->members(array(
	'sort' => $state['sort'],
	'order' => $state['order'],
	'limit' => $state['limit'],
	'offset' => $start));
	

$smarty->assign('state',$state);
$smarty->assign('subscribers',$subscribers);
$smarty->assign('tally',$group->_tally);
$smarty->assign('groups',PommoGroup::get());
$smarty->assign('fields',PommoField::get());

$smarty->display('admin/subscribers/subscribers_manage.tpl');
Pommo::kill();
?>