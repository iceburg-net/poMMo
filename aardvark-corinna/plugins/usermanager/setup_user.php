<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * Created: Corinna Thoeni <corinn at gmx dot net> - 07.09.2006
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
define('_IS_VALID', TRUE);

require ('../../bootstrap.php');

require_once (bm_baseDir . '/plugins/usermanager/db_userhandler.class.php');

$poMMo	= & fireup("secure");
$logger	= & $poMMo->_logger;
$dbo	= & $poMMo->_dbo;

$dbhandler = new UserHandler($dbo);



if (!empty($_REQUEST['userid'])) {
	$userid = $_REQUEST['userid'];
} else {
	$logger->addErr(_T('User id not set'));
}

if ((!empty($_REQUEST['userdata'])) && (!empty($_REQUEST['old']))) { 
/*	$old 		= $_REQUEST['old'];
	$userdata 	= $_REQUEST['userdata'];
	print_r($old); print_r($userdata);
	$keyarray = array_keys($userdata);
	$valarray = array_values($userdata);
	
	for ($i=0; $i <= count($userdata); $i++) {
		//Change only if its altered
		if ($valarray[$i] != $old[$i]) {
			$changed[$i] = $dbhandler->dbUpdateUserData($keyarray[$i], $valarray[$i]);
		}
	}*/
	$logger->addMsg(_T('Config altered: ' . implode("<br>", $changed)));
}

/*
if (isset($_REQUEST['activeswitch'])) {
	if ($_REQUEST['activeswitch'] == 'on') {
		$str = dbActivatePlugin($dbo, $pluginid, 1);
	} else {
		$str = dbActivatePlugin($dbo, $pluginid, 0);
	}
	echo $str;
}*/



//echo "<br><h3>AKTIV: "; print_r($_REQUEST); echo "</h3><br>";
//echo "<br><h3>Variablen:"; print_r($_REQUEST['plugindata']); echo "</h3><br>";
 

// Smarty part
$smarty = & bmSmartyInit();

$user = $dbhandler->dbFetchUserInfo($_REQUEST['userid']);


$smarty->assign('user' , $user);

$smarty->assign('returnStr' , 'poMMo User Setup');
$smarty->assign($_POST);

$smarty->display('plugins/usermanager/setup_user.tpl');

bmKill();

?>

