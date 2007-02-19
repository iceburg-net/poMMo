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
$pommo->init(array('authLevel' => 1));		//array('keep' => TRUE;);
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;


echo "<div style='color:blue;'>-----begin-----<br>POMMO"; print_r($pommo->_auth); echo "<br>"; print_r($pommo->_auth); echo "<br>"; 
echo "SESSION: "; print_r($_SESSION); echo "</div>-----end-----<br><br>";

$key = "123456";
echo "<h1>Welcome "; print_r($_SESSION['pommo'.$key]['username']);  echo "</h1><br>";



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


//this ? > was forgotten or intended?
?> 
