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

define('_IS_VALID', TRUE);

require ('../../bootstrap.php');

require_once (bm_baseDir . '/plugins/pluginregistry/class.pluginregistry.php');

$poMMo	= & fireup("secure");
$logger	= & $poMMo->_logger;
$dbo	= & $poMMo->_dbo;

$smarty = & bmSmartyInit();

$pluginregistry = new PluginRegistry($dbo);



if (!empty($_REQUEST['switch']) && (!empty($_REQUEST['pluginid']))) {

	$str = $pluginregistry->switchPlugin($_REQUEST['pluginid'], $_REQUEST['setto']);
	$logger->addMsg(_T('Plugin State switched.'));
	$pluginregistry->updateActive();
}

if (!empty($_REQUEST['edit']) && (!empty($_REQUEST['pluginid']))) {

	if (!empty($_REQUEST['closeedit'])) {
		
		$old 		= $_REQUEST['old'];	echo $old."<br>";
		$plugindata = $_REQUEST['plugindata'];	echo $plugindata."<br>";
		$keyarray = array_keys($plugindata);
		$valarray = array_values($plugindata);
		
		for ($i=0; $i <= count($plugindata); $i++) {
			//Change only if its altered
			if ($valarray[$i] != $old[$i]) {
				$changed[$i] = $pluginregistry->updatePluginData($keyarray[$i], $valarray[$i]);
			}
		}
		$logger->addMsg(_T('Config altered: ' . implode("<br>", $changed)));
	
	} else {
		$editid = $_REQUEST['pluginid'];
	
		$plugindata = $pluginregistry->getPluginData($editid);
		
		//TODO
		$smarty->assign('dropdown' , $pluginregistry->getDropDown($editid));

		$smarty->assign('highlight' , $editid);
		$smarty->assign('plugin' , $plugindata);
		$smarty->assign('data' ,   $plugindata['plugin_data']);
		$smarty->assign('showedit' , TRUE);
	}
}


// Get all the available Plugins from the database
$smarty->assign('plugins' , $pluginregistry->getPluginMatrix());
$smarty->assign('returnStr' , 'poMMo Plugin Manager');

$smarty->display('plugins/pluginregistry/plugins.tpl');

bmKill();

?>

