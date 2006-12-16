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
require ('../../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/mailings.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/fields.php');

$pommo->init(array('keep' => TRUE));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();
$smarty->prepareForForm();

if (PommoMailing::isCurrent())
	Pommo::kill(sprintf(Pommo::_T('A Mailing is currently processing. Visit the %sStatus%s page to check its progress.'),'<a href="mailing_status.php">','</a>'));

// check if altBody should be imported from HTML
if (isset($_POST['altGen'])) {
	Pommo::requireOnce($pommo->_baseDir.'inc/lib/lib.html2txt.php');
	$h2t = & new html2text($_POST['body']);
	$_POST['altbody'] = $h2t->get_text();
}

// Get MailingData from SESSION.
$mailingData = $pommo->get('mailingData');
if (!$mailingData) {
	$mailingData = array ();
}
@$smarty->assign('ishtml', $mailingData['ishtml']);

if (empty ($_POST)) {
	// ___ USER HAS NOT SENT FORM ___

	$formError = array ();
	$formError['fromname'] = $formError['body'] = Pommo::_T('Cannot be empty.');
	$smarty->assign('formError', $formError);

	// load mailing data from session
	@$_POST['body'] = $mailingData['body'];
	@$_POST['altbody'] = $mailingData['altbody'];
	@$_POST['altInclude'] = $mailingData['altInclude'];

} elseif(isset($_POST['preview'])) {
	// ___ USER HAS SENT FORM ___

		// __ FORM IS VALID
		unset($_POST['preview']);
		$mailingData['body'] = $_POST['body'];
		$mailingData['altbody'] = $_POST['altbody'];
		$mailingData['altInclude'] = $_POST['altInclude'];
		$pommo->set(array('mailingData' => $mailingData));

		Pommo::redirect('mailings_send3.php');
}

$smarty->assign('fields',PommoField::get());
$smarty->assign($_POST);
$smarty->display('admin/mailings/mailings_send2.tpl');
Pommo::kill();
?>