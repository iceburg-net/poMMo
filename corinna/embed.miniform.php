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

define('_poMMo_embed', TRUE);
require(dirname(__FILE__).'/bootstrap.php');
$pommo->init(array('authLevel' => 0, 'noSession' => TRUE));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();

// subscription forms will be activated from this template
$smarty->prepareForSubscribeForm();

// assign referer since this is an embedded form
$smarty->assign('referer',htmlspecialchars($_SERVER['PHP_SELF']));

$smarty->display('subscribe/form.mini.tpl');
?>