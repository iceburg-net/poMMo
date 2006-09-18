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
//defined('_IS_VALID') or die('Move along...');

require ('../bootstrap.php');

require_once (bm_baseDir . '/pluginadmin/utils/db_handler.class.php');

$poMMo	= & fireup("secure");
$logger	= & $poMMo->_logger;
$dbo	= & $poMMo->_dbo;

// Smarty part
$smarty = & bmSmartyInit();

$dbhandler = new DatabaseHandler($dbo);

// Get all the available Plugins from the database
$plugins = $dbhandler->dbFetchPlugins();


$smarty->assign('plugins' , $plugins);
$smarty->assign('returnStr' , 'poMMo Plugin Manager');


$smarty->display('plugins/pluginadmin/plugins.tpl');

bmKill();

?>
