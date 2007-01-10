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


// Initialize page state with default values overriden by those held in $_REQUEST
$state =& PommoAPI::stateInit('mailings_send2',array(
	'body' => '',
	'altbody' => '',
	'altInclude' => 'no',
	'editorType' => 'wysiwyg'
	),
	$_POST);

@$smarty->assign('ishtml', $pommo->_session['state']['mailings_send']['ishtml']);

// check if altBody should be imported from HTML
if (isset($_POST['altGen'])) {
	Pommo::requireOnce($pommo->_baseDir.'inc/lib/lib.html2txt.php');
	$h2t = & new html2text($_POST['body']);
	$_POST['altbody'] = $h2t->get_text();
}

if (!SmartyValidate :: is_registered_form() || empty ($_POST)) {
	// ___ USER HAS NOT SENT FORM ___

	SmartyValidate :: connect($smarty, true);
	SmartyValidate :: register_validator('body', 'body', 'notEmpty', false, false, 'trim');
	
	$formError = array ();
	$formError['body'] = Pommo::_T('Cannot be empty.');
	$smarty->assign('formError', $formError);

} else {
	// ___ USER HAS SENT FORM ___
	SmartyValidate :: connect($smarty);
	
	if (SmartyValidate :: is_valid($_POST) && isset($_POST['preview'])) {
		// __ FORM IS VALID
		SmartyValidate :: disconnect();
		Pommo::redirect('mailings_send3.php');
	}
	// __ FORM NOT VALID
	if (isset($_POST['preview']))
		$logger->addMsg(Pommo::_T('Please review and correct errors with your submission.'));	
}

$smarty->assign('fields',PommoField::get());
$smarty->assign($state);
$smarty->display('admin/mailings/mailings_send2.tpl');
Pommo::kill();
?>