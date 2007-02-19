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

require_once ($pommo->_baseDir.'plugins/adminplugins/useradmin/respmanager/class.respplugin.php');
require_once ($pommo->_baseDir.'plugins/adminplugins/useradmin/respmanager/class.db_resphandler.php'); 



$data = NULL;
$respplugin = new RespPlugin($pommo);



//GETPOST data
if ($_REQUEST['action']) {
	$data['action']	= $_REQUEST['action'];
	
	
	 if ($data['action'] == "showAdd") {
		$data['showAdd'] = TRUE;
		//$data['userid'] = $_REQUEST['userid'];		//$data['listid'] = $_REQUEST['listid'];
		if (!empty($_REQUEST['addResp'])) {
			$ret = $respplugin->addResponsiblePerson($_REQUEST['userid'], $_REQUEST['realname'], $_REQUEST['surname'], $_REQUEST['bounceemail']);
			if ($ret) { 
				$data['showAdd'] = FALSE;	
				Pommo::redirect($pommo->_baseUrl.'/plugins/adminplugins/useradmin/respmanager/resp_main.php');
			}
		}
	} elseif ($data['action'] == "showEdit") {
		$data['editid'] = $_REQUEST['editid'];
		$data['showEdit'] = TRUE;
		if (!empty($_REQUEST['editResp'])) {
			$ret = $respplugin->editResponsiblePerson($data['editid'], $_REQUEST['realname'], $_REQUEST['surname'], $_REQUEST['bounceemail']);
			if ($ret) $data['showEdit'] = FALSE;	
		}
	} elseif ($data['action'] == "showDel") {
		$data['delid'] = $_REQUEST['delid'];
		$data['showDel'] = TRUE;
		if (!empty($_REQUEST['delResp'])) {
			$ret = $respplugin->deleteResponsiblePerson($data['delid']);
			if ($ret) $data['showDel'] = FALSE;	
		}
	}

}



$respplugin->execute($data);


?>

