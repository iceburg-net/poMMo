<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * Created: Corinna Thoeni <corinn at gmx dot net> - 10.11.2006
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/

define('_IS_VALID', TRUE);

require ('../../../../bootstrap.php');
require_once (bm_baseDir . '/plugins/adminplugins/adminuser/authmanager/class.authplugin.php');

$poMMo = & fireup("secure");	//$logger	= & $poMMo->_logger; //$dbo	= & $poMMo->_dbo;


	// get/post data
	if ($_REQUEST['viewsetup']) {
		$data['setupid'] = $_REQUEST['setupid'];

	}
	if ($_REQUEST['changesetup']) {
		//Data to be changed
		$data['changeid'] = $_REQUEST['changeid'];
		$data['active'] = $_REQUEST['active'];
		$data['old'] = $_REQUEST['old'];
		$data['new'] = $_REQUEST['plugindata'];
	}
	
	print_r($_REQUEST);

	$authplugin = new AuthPlugin($poMMo);
	
	$authplugin->execute($data);



?>
