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

require ('../bootstrap.php');

require_once (bm_baseDir . '/pluginadmin/utils/db_handler.class.php');

$poMMo	= & fireup("secure");
$logger	= & $poMMo->_logger;
$dbo	= & $poMMo->_dbo;

$dbhandler = new DatabaseHandler($dbo);


//TODO stateVar approach??? in mailings_mod
//like this: 
//$action = $poMMo->stateVar('action',$_REQUEST['action']);
//$mailid = $poMMo->stateVar('mailid',$_REQUEST['mailid']);
// and then: 
/* if (empty($action) || empty($mailid)) {
	var_dump($action,$mailid,$_REQUEST);
	die();
	bmRedirect('mailings_history.php');*/



if (!empty($_REQUEST['pluginid'])) {
	$pluginid = $_REQUEST['pluginid'];
} else {
	$logger->addErr(_T('Plugin id not set'));
}

if (isset($_REQUEST['onlyactivate'])) {
//if ((!empty($_REQUEST['onlyactivate'])) && ((!empty($_REQUEST['pluginid'])) && (!empty($_REQUEST['setto'])))) {
	$str = $dbhandler->dbActivatePlugin($_REQUEST['pluginid'], $_REQUEST['setto']);
	bmRedirect('../../plugins.php');
}

//if (isset($_REQUEST['plugindata']) && $_REQUEST['blah']=='update') {
if ((!empty($_REQUEST['plugindata'])) && (!empty($_REQUEST['old']))) { 
	$old 		= $_REQUEST['old'];
	$plugindata = $_REQUEST['plugindata'];
	$keyarray = array_keys($plugindata);
	$valarray = array_values($plugindata);
	
	for ($i=0; $i <= count($plugindata); $i++) {
		//Change only if its altered
		if ($valarray[$i] != $old[$i]) {
			$changed[$i] = $dbhandler->dbUpdatePluginData($keyarray[$i], $valarray[$i]);
		}
	}
	$logger->addMsg(_T('Config altered: ' . implode("<br>", $changed)));
}

if (!empty($_REQUEST['activeswitch'])) {
	if ($_REQUEST['activeswitch'] == 'on') {
		$str = $dbhandler->dbActivatePlugin($pluginid, 1);
	} else {
		$str = $dbhandler->dbActivatePlugin($pluginid, 0);
	}
		$logger->addMsg(_T('Config altered (active switch set): ' . $str));
}


// Smarty part
$smarty = & bmSmartyInit();

// Get all the available Plugins from the database
$plugins = $dbhandler->dbFetchPluginInfo($pluginid);
$plugins['plugin_data'] = $dbhandler->dbFetchPluginData($pluginid);


$smarty->assign('plugins' , $plugins);
$smarty->assign('data' , 	$plugins['plugin_data']);

$smarty->assign('returnStr' , 'poMMo Plugin Setup');

$smarty->display('plugins/pluginadmin/setup_plugin.tpl');

bmKill();

?>
