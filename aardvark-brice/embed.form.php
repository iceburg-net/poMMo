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

require('bootstrap.php');

$poMMo = & fireup('install');
$dbo = & $poMMo->openDB();

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
$smarty = & bmSmartyInit();

// subscription forms will be activated from this template
// Function
$smarty->prepareForSubscribeForm();

$smarty->display('subscribe/form.subscribe.tpl');
die();
?>