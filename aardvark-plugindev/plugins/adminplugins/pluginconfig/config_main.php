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

require ('../../../bootstrap.php');

Pommo::requireOnce($pommo->_baseDir.'inc/helpers/validate.php');

$pommo->requireOnce($pommo->_baseDir.'plugins/lib/interfaces/interface.dbhandler.php');
$pommo->requireOnce($pommo->_baseDir.'plugins/adminplugins/pluginconfig/class.db_confighandler.php');
$pommo->requireOnce($pommo->_baseDir.'plugins/adminplugins/pluginconfig/class.pluginconfig.php');
/*Pommo::requireOnce($pommo->_baseDir.'plugins/lib/interfaces/interface.dbhandler.php');
Pommo::requireOnce($pommo->_baseDir.'plugins/adminplugins/pluginconfig/class.db_confighandler.php');
Pommo::requireOnce($pommo->_baseDir.'plugins/adminplugins/pluginconfig/class.pluginconfig.php');*/

$pommo->init();
$data = NULL;


/**
	//print_r($data['setupid']);
		//$blah = PommoValidate::subscriberData($data['setupid']);
		//if ($blah) echo "<br>ok<br>"; else echo "<br>ned ok!<br>";
		//print_r($data['setupid']);
 */

	/* collection of POST data*/

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
	
	if ($_REQUEST['switchplugin'] AND $_REQUEST['switchid']) {
		$data['switchid'] = $_REQUEST['switchid'];
		$data['active'] = $_REQUEST['active'];
	}
	
	if ($_REQUEST['switchcid'] AND $_REQUEST['switchcid']) { 
		$data['switchcid'] = $_REQUEST['switchcid'];
		$data['active'] = $_REQUEST['active'];
	}
	
	if ($_REQUEST['setallpluginsoff']) {
		$data['allpluginsoff'] = TRUE;
	}



$pluginconfig = new PluginConfig($pommo);

// Some Validation with a inherited class
//$blah = PommoValidate::subscriberData($_REQUEST); //$blah = FALSE;
$blah = "TRUE";
if ($blah) {
	//$data = $pluginconfig->extractData($_REQUEST); //validated data
	// data validator in the extract Data function??
	$pluginconfig->execute($data); //$data
} else { 
	Pommo::kill("config_main: Validation Error.");
}
	
// Pommo::redirect($pommo->_baseUrl.'plugins/adminplugins/pluginconfig/config_main.php');

?>
