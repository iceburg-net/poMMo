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

/**********************************
	INITIALIZATION METHODS
 *********************************/
define('_IS_VALID', TRUE);

require('../bootstrap.php');

$poMMo = & fireup('secure');
$logger = & $poMMo->_logger;
$dbo = & $poMMo->_dbo;


//<corinna>

//TODO store user information from $poMMo
//echo "<h3>SESSION:"; print_r($_SESSION); echo "</h3>";
//echo "<h3>poMMo:"; print_r($poMMo); echo "</h3>";
$loggeduser = & $poMMo->_loggeduser;
$user = $loggeduser['user'];
$perm = $loggeduser['perm'];


// TODO
// Wenn diese was weiss ich wo defined sind //in $pommo zB
// array = $pluginregistry->getConfigHTML/SMARTY()
// oder FÜR ALLE PLugin Registry[$i]->assigndata();
//TODO ERROR HANDLING-> HAVE YOU INSTALLED PLUGINS?
// This will have to be enabled in the config file to show this

//if ($useplugins)
if (($user != 'admin') AND $useplugins) {	// wenn einer user sich einloggt!

	require_once (bm_baseDir . '/plugins/multiuser/class.multiuser.php'); 
	$multiuser = new MultiUser($poMMo);
	$multiuser->display();
	
}

//else
if ($user == 'admin') {	// proceed in the normal way

	/**********************************
		SETUP TEMPLATE, PAGE
	 *********************************/
	$smarty = & bmSmartyInit();
	
	$smarty->assign('header',array(
		'main' => _T('poMMo Aardvark').' '.$poMMo->_config['version'],
		'sub' => _T('Powerful mailing list software for').' '.$poMMo->_config['list_name'] 
		));
	
	
	if ($useplugins) {
		// Show additional functionality from the plugins
		// Plugin SETUP should be showed
		$smarty->assign('showplugin', TRUE);
	}
	
	$smarty->display('admin/admin.tpl');

} //corinna

bmKill();

?>