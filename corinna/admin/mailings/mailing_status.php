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

// Progress bar modified from the works of Juha Suni <juha.suni@ilmiantajat.fi>
/**********************************
	INITIALIZATION METHODS
 *********************************/
require ('../../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/classes/mailing.php');

$pommo->init();
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();

$mailing = current(PommoMailing::get(array('active' => TRUE)));


$smarty->assign('mailing', $mailing);
$smarty->display('admin/mailings/mailing_status.tpl');
Pommo::kill();
?>