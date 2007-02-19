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


require ('../../../../bootstrap.php');
$pommo->init();	

Pommo::requireOnce($pommo->_baseDir.'plugins/adminplugins/useradmin/usermanager/class.db_userhandler.php');
Pommo::requireOnce($pommo->_baseDir.'plugins/adminplugins/useradmin/usermanager/class.userplugin.php');


// Generates template background and pagelist, logic to view, delete, send
$userplugin = new UserPlugin();


print_r($_REQUEST);


	/* USE CASES */
	if ($_REQUEST['AddUser']) {
		$ret = $userplugin->addUser($_REQUEST['username'], $_REQUEST['userpass'], $_REQUEST['userpasscheck'], $_REQUEST['usergroup']);
	}
	if ($_REQUEST['DeleteUser']) {
		$ret = $userplugin->deleteUser($_REQUEST['userid']);
	}
	if ($_REQUEST['EditUser']) {
		$ret = $userplugin->editUser($_REQUEST['userid'], $_REQUEST['username'], $_REQUEST['userpass'], $_REQUEST['usergroup']);
	}
	
	if ($_REQUEST['AddGroup']) {
		$ret = $userplugin->addPermGroup($_REQUEST['groupname'], $_REQUEST['groupperm'], $_REQUEST['groupdesc']);
	}
	if ($_REQUEST['DeleteGroup']) {
		$ret = $userplugin->deletePermGroup($_REQUEST['groupid']);
	}
	if ($_REQUEST['EditGroup']) {
		$ret = $userplugin->editPermGroup($_REQUEST['groupid'], $_REQUEST['groupname'], $_REQUEST['groupperm'], $_REQUEST['groupdesc']);
	}


$data = NULL;
$userplugin->execute($data);


?>

