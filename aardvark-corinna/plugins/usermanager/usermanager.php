<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * Created: Corinna Thoeni <corinn at gmx dot net> - 13.09.2006
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/


define('_IS_VALID', TRUE);

require ('../../bootstrap.php');

require_once (bm_baseDir . '/plugins/usermanager/db_userhandler.class.php');

$poMMo	= & fireup("secure");
$logger	= & $poMMo->_logger;
$dbo	= & $poMMo->_dbo;

$dbhandler = new UserHandler($dbo);



// Smarty part
$smarty = & bmSmartyInit();

// Get all the available Plugins from the database
$user = $dbhandler->dbFetchUser($dbo);


$smarty->assign('user' , $user);

$smarty->assign('returnStr' , 'poMMo User Manager');
$smarty->assign($_POST);

$smarty->display('plugins/usermanager/usermanager.tpl');

bmKill();



class UserManager {
	
	//TODO
	//hängt von auth methode ab
	// wenn eine anmeldung dann gucken ob in db
	// wenn ldap anmeldung erfolgreich dann neu in db anlegen
	// rights management
	
	
}


?>
