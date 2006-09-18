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

//TODO
include_once('../pluginadmin/utils/db_confighandler.class.php');
//TODO

$poMMo = & fireup('secure');
$logger = & $poMMo->_logger;
$dbo = & $poMMo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
$smarty = & bmSmartyInit();

$smarty->assign('header',array(
	'main' => _T('poMMo Aardvark').' '.$poMMo->_config['version'],
	'sub' => _T('Powerful mailing list software for').' '.$poMMo->_config['list_name'] 
	));


//<corinna>
// TODO
// Wenn diese was weiss ich wo defined sind //in $pommo zB
// NEIN: 
// array = $pluginregistry->getConfigHTML/SMARTY()
// oder FÜR ALLE PLugin Registry[$i]->assigndata();
//oder so was
//$pluginshow = TRUE;
//if ($pluginshow) {
	$smarty->assign('showplugin', TRUE);
//}

$handler = new ConfigHandler($dbo);
$usershow = $handler->dbIsActiveByName('Benutzerverwaltung');
if ($usershow)  {
	$smarty->assign('showuser', TRUE);
}
//</corinna>


$smarty->display('admin/admin.tpl');

bmKill();
	