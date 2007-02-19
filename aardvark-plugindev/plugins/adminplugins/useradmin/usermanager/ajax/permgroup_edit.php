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
require ('../../../../../bootstrap.php');

$pommo->init(array('noDebug' => TRUE));

	
/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();


Pommo::requireOnce($pommo->_baseDir.'plugins/adminplugins/useradmin/usermanager/class.db_userhandler.php');
$dbhandler = new UserDBHandler();
$smarty->assign('groupid', $_REQUEST['groupid']);
$smarty->assign('perm', $dbhandler->dbFetchPermissions($_REQUEST['groupid']));
$smarty->assign('group', $dbhandler->dbFetchPermInfo($_REQUEST['groupid']));


$smarty->display('plugins/adminplugins/useradmin/usermanager/ajax/permgroup_edit.tpl');
Pommo::kill();

?>