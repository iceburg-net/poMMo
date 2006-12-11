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
require_once ($pommo->_baseDir.'plugins/adminplugins/useradmin/usermanager/class.userplugin.php');


$pommo->init();	

$data = NULL;

//TODO weg
/*
echo "<h5 style='color:red'>REQUEST DATA: "; print_r($_REQUEST); echo "</h5>";
echo "<h5 style='color:red'>POST DATA: "; print_r($_POST); echo "</h5>";
echo "<h5 style='color:red'>GET DATA: "; print_r($_GET); echo "</h5>";
echo "<h5 style='color:red'>\$DATA DATA: "; print_r($data); echo "</h5>";
*/

if ($_REQUEST['action']) {
	$data['action']	= $_REQUEST['action'];
}
if ($_REQUEST['userid']) {
	$data['userid'] = $_REQUEST['userid'];
} elseif ($_REQUEST['groupid']) {
	$data['groupid'] = $_REQUEST['groupid'];
}


	// Generates template background and pagelist, logic to view, delete, send
	$userplugin = new UserPlugin($pommo);



	/* USE CASES */

	if ($data['action'] == 'add') {

		$data['showAddForm'] = TRUE;	// show form
		
		// If the add button is pressed
		if (!empty($_REQUEST['AddUser'])) {
			$ret = $userplugin->addUser($_REQUEST['username'], $_REQUEST['userpass'], $_REQUEST['userpasscheck'], $_REQUEST['usergroup']);
			if ($ret) Pommo::redirect($pommo->_baseUrl.'/plugins/adminplugins/useradmin/usermanager/user_main.php');
				//$data['showAddForm'] = FALSE;	
				
		}
		
	} elseif ($data['action'] == 'delete') {
		$data['showDelForm'] = TRUE;
		if (!empty($_REQUEST['DeleteUser'])) {
			$ret = $userplugin->deleteUser($_REQUEST['userid']);
			if ($ret) Pommo::redirect($pommo->_baseUrl.'/plugins/adminplugins/useradmin/usermanager/user_main.php');
				//$data['showDelForm'] = FALSE;	
		}	
	
	} elseif ($data['action'] == 'edit') {
		$data['showEditForm'] = TRUE;
		if (!empty($_REQUEST['EditUser'])) {
			$ret = $userplugin->editUser($_REQUEST['userid'], $_REQUEST['username'], $_REQUEST['userpass'], $_REQUEST['usergroup']);
			if ($ret) Pommo::redirect($pommo->_baseUrl.'/plugins/adminplugins/useradmin/usermanager/user_main.php');
				//$data['showEditForm'] = FALSE;	
		}

	}
	/* GROUP USE CASES */
	elseif ($data['action'] == 'addgroup') {
		$data['showGroupAddForm'] = TRUE;
		if (!empty($_REQUEST['AddGroup'])) {
			$ret = $userplugin->addPermGroup($_REQUEST['groupname'], $_REQUEST['groupperm'], $_REQUEST['groupdesc']);
			if ($ret) Pommo::redirect($pommo->_baseUrl.'plugins/adminplugins/useradmin/usermanager/user_main.php');
				//$data['showGroupAddForm'] = FALSE;	Pommo::redirect('/admin/admin.php');
		}
	} elseif ($data['action'] == 'delgroup') {
		$data['showGroupDelForm'] = TRUE;
		if (!empty($_REQUEST['DeleteGroup'])) {
			$ret = $userplugin->deletePermGroup($_REQUEST['groupid']);
			if ($ret) Pommo::redirect($pommo->_baseUrl.'/plugins/adminplugins/useradmin/usermanager/user_main.php');
				//$data['showGroupDelForm'] = FALSE;	
		}
	} elseif ($data['action'] == 'editgroup') {
		$data['showGroupEditForm'] = TRUE;
		if (!empty($_REQUEST['EditGroup'])) {
			$ret = $userplugin->editPermGroup($_REQUEST['groupid'], $_REQUEST['groupname'], $_REQUEST['groupperm'], $_REQUEST['groupdesc']);
			if ($ret) Pommo::redirect($pommo->_baseUrl.'/plugins/adminplugins/useradmin/usermanager/user_main.php');
				//$data['showGroupEditForm'] = FALSE;	
		}
	}
	

	$userplugin->execute($data);



/* $data['mailings_queue']['limit'] 	= $_REQUEST['limit'];
$data['mailings_queue']['sortOrder']= $_REQUEST['sortOrder'];
$data['mailings_queue']['sortBy'] 	= $_REQUEST['sortBy'];
$data['page'] 	= $_REQUEST['page'];			//for Pager class*/

?>
