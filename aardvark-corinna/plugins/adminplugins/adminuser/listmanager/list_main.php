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

define('_IS_VALID', TRUE);

require ('../../../../bootstrap.php');
require_once (bm_baseDir . '/plugins/adminplugins/adminuser/listmanager/class.listplugin.php');


$poMMo = & fireup("secure");	//$logger	= & $poMMo->_logger; //$dbo	= & $poMMo->_dbo;

$data = NULL;
$listplugin = new ListPlugin($poMMo);



//GETPOST data
if ($_REQUEST['action']) {
	$data['action']	= $_REQUEST['action'];
	$data['id'] = $_REQUEST['id'];
	
	 if ($data['action'] == "delete") {
		$data['showDelete'] = TRUE;
		if (!empty($_REQUEST['deleteList'])) {

			$ret = $listplugin->deleteList($_REQUEST['listid'], $_REQUEST['userid']);
			if ($ret) $data['showDelete'] = FALSE;	
		}
	} elseif ($data['action'] == "edit") {
		$data['showEdit'] = TRUE;
		if (!empty($_REQUEST['editList'])) {
			$ret = $listplugin->editList($data['id'], $_REQUEST['listname'], $_REQUEST['listdesc'], $_REQUEST['userid']);
			if ($ret) $data['showEdit'] = FALSE;	
		}
	} elseif ($data['action'] == "add") {
		$data['showAdd'] = TRUE;
		if (!empty($_REQUEST['addList'])) {
			$ret = $listplugin->addList($_REQUEST['listname'], $_REQUEST['listdesc'], $_REQUEST['userid']);
			if ($ret) $data['showAdd'] = FALSE;	
		}
	}
	
	echo "<div style='color:red'>"; print_r($_REQUEST); echo "</div>";
}



$listplugin->execute($data);


?>

