<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/licenses/gpl.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. Notify the above author of modifications to contents within.
 * 
 *  WHY? Because this is a community project -- purposely released under the GPL.
 *    We'd love to have the possiblity to include your derivative works! 
 *    We'd love to coordinate around your development efforts!
 *    We'd love to assist you with your changes!
 *    DON'T BE A STRANGER!
 * 
 ** [END HEADER]**/

/**********************************
	INITIALIZATION METHODS
 *********************************/
$lang = false;
if (isset($_POST['lang'])) {
	define('_poMMo_lang', $_POST['lang']);
	$lang = true;
}
	
require('../bootstrap.php');
$pommo->init();
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();

$smarty->assign('header',array(
	'main' => 'poMMo '.$pommo->_config['version'],
	'sub' => sprintf(Pommo::_T('Powerful mailing list software for %s'),$pommo->_config['list_name']) 
	));

if($lang)
	$logger->addErr(Pommo::_T('You have changed the language for this session. To make this the default language, you must update your config.php file.'));

$smarty->assign('lang',($pommo->_slanguage) ? $pommo->_slanguage : $pommo->_language);	
$smarty->display('admin/admin.tpl');
Pommo::kill();
	