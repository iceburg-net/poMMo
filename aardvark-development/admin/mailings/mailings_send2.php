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

require ('../../bootstrap.php');
require_once (bm_baseDir . '/inc/db_mailing.php');
require_once (bm_baseDir . '/inc/lib.txt.php');

$poMMo = & fireup('secure', 'keep');
$logger = & $poMMo->logger;
$dbo = & $poMMo->openDB();

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
$smarty = & bmSmartyInit();
$smarty->prepareForForm();

// check to see if a mailing is taking place (queue not empty)
if (!mailingQueueEmpty($dbo)) {
	bmKill(sprintf(_T('A mailing is already taking place. Please allow it to finish before creating another. Return to the %s Mailing Page %s'), '<a href="admin_mailings.php">', '</a>'));
}
	
// check if altBody should be imported from HTML
if (isset($_POST['altGen'])) {
	require_once (bm_baseDir.'/inc/lib.html2txt.php');
	$h2t = & new html2text($_POST['body']);
	$_POST['altbody'] = $h2t->get_text();
}

if (empty ($_POST)) {
	// ___ USER HAS NOT SENT FORM ___
	
	$formError = array ();
	$formError['fromname'] = $formError['body'] = _T('Cannot be empty.');
	$smarty->assign('formError', $formError);
	
	$_POST = $poMMo->get();

} elseif(isset($_POST['preview'])) {
	// ___ USER HAS SENT FORM ___
	
		// __ FORM IS VALID
		unset($_POST['preview']);
		$poMMo->set($_POST);
		bmRedirect('mailings_send3.php');
}

$smarty->assign($_POST);
$smarty->display('admin/mailings/mailings_send2.tpl');
bmKill();
?>
