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
require('../bootstrap.php');
$pommo->init();
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;


//corinna
echo "<div style='color:green;'>$ pommo: ";
print_r($pommo);
echo "<br><br> $ session: ";
print_r($_SESSION);
echo "<br><br> $ request: ";
print_r($_REQUEST);
echo "</div>";
//corinna


/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();

//corinna TODO CORINNA
// Distinguish between admin and normal users
// if plugins are activated the admin gets a additional plugin config menu point
/*if ($pommo->_useplugins) {
	
	// Rightmanagement: if (accesslevel == 5)
	$accessLevel = $pommo->_auth->_permissionLevel;
	
	if (($user != 'admin') AND $accessLevel != 5) {
		echo "<h1>show multiuser interface.</h1>";
	}
	
	if (($user == 'admin') AND $accessLevel == 5) {
		// Show additional functionality from the plugins: Plugin SETUP button
		$smarty->assign('showplugin', TRUE);
	}
	
}*/
//corinna



$smarty->assign('header',array(
	'main' => 'poMMo '.$pommo->_config['version'],
	'sub' => sprintf(Pommo::_T('Powerful mailing list software for %s'),$pommo->_config['list_name']) 
	));
	
$smarty->display('admin/admin.tpl');
Pommo::kill();






/* OLD DELETE
//<corinna>

//TODO store user information from $poMMo
//echo "<h3>SESSION:"; print_r($_SESSION); echo "</h3>";
//echo "<h3>poMMo:"; print_r($poMMo); echo "</h3>";
$loggeduser = & $poMMo->_loggeduser;
$user = $loggeduser['user'];
$perm = $loggeduser['perm'];

//if ($useplugins)
if (($user != 'admin') AND $useplugins) {	// wenn einer user sich einloggt!

	require_once ($pommo->_baseDir . '/plugins/multiuser/class.multiuser.php'); 
	$multiuser = new MultiUser($poMMo);
	$multiuser->display();
	
}

//else
if ($user == 'admin') {	// proceed in the normal way

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

*/

//this ? > was forgotten or intended?
?> 
