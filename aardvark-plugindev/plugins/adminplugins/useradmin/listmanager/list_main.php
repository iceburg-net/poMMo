<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * Created: Corinna Thoeni <corinn at gmx dot net> - 18.10.2006
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/

require ('../../../../bootstrap.php');

$pommo->init();

$pommo->requireOnce($pommo->_baseDir.'plugins/adminplugins/useradmin/listmanager/class.db_listhandler.php');
$pommo->requireOnce($pommo->_baseDir.'plugins/adminplugins/useradmin/listmanager/class.listplugin.php');


$data = NULL;

$listplugin = new ListPlugin($pommo);


//GETPOST data
if ($_REQUEST['action']) {
	$data['action']	= $_REQUEST['action'];
	
	 if ($data['action'] == "delete") {
		$data['showDelete'] = TRUE;
		//$data['userid'] = $_REQUEST['userid'];
		$data['listid'] = $_REQUEST['listid'];
		if (!empty($_REQUEST['deleteList'])) {
			$ret = $listplugin->deleteList($data['listid'], $_REQUEST['userid']);
			if ($ret) $data['showDelete'] = FALSE;	
		}
	} elseif ($data['action'] == "edit") {
		$data['listid'] = $_REQUEST['listid'];
		$data['showEdit'] = TRUE;
		if (!empty($_REQUEST['editList'])) {
			$ret = $listplugin->editList($data['listid'], $_REQUEST['listname'], $_REQUEST['listdesc']);
			if ($ret) $data['showEdit'] = FALSE;	
		}
	} elseif ($data['action'] == "add") {
		$data['showAdd'] = TRUE;
		if (!empty($_REQUEST['addList'])) {
			$ret = $listplugin->addList($_REQUEST['listname'], $_REQUEST['listdesc'], 
				$_REQUEST['senderemail'], $_REQUEST['userarray'], $_REQUEST['grouparray']);
			if ($ret) $data['showAdd'] = FALSE;	
		}
	}
	
}


$listplugin->execute($data);


// Pommo::redirect($pommo->_baseUrl.'plugins/adminplugins/pluginconfig/config_main.php');

?>

