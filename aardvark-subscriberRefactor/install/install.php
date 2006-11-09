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
require ('../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'install/helper.install.php');
$pommo->init(array('authLevel' => 0, 'noInit' => TRUE));
$pommo->reloadConfig();

session_start(); // required by smartyValidate. TODO -> move to prepareForForm() ??
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;
$dbo->dieOnQuery(FALSE);

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();
$smarty->prepareForForm();

// Check to make sure poMMo is not already installed.
if (bmIsInstalled()) {
	$logger->addErr(Pommo::_T('poMMo is already installed.'));
	$smarty->assign('installed', TRUE);
	$smarty->display('install.tpl');
	Pommo::kill();
}



if (isset ($_REQUEST['disableDebug']))
	unset ($_REQUEST['debugInstall']);
elseif (isset ($_REQUEST['debugInstall'])) $smarty->assign('debug', TRUE);

if (!SmartyValidate :: is_registered_form() || empty ($_POST)) {
	// ___ USER HAS NOT SENT FORM ___
	SmartyValidate :: connect($smarty, true);
	
	SmartyValidate :: register_validator('list_name', 'list_name', 'notEmpty', false, false, 'trim');
	SmartyValidate :: register_validator('site_name', 'site_name', 'notEmpty', false, false, 'trim');
	SmartyValidate :: register_validator('site_url', 'site_url', 'isURL');
	SmartyValidate :: register_validator('admin_password', 'admin_password', 'notEmpty', false, false, 'trim');
	SmartyValidate :: register_validator('admin_password2', 'admin_password:admin_password2', 'isEqual');
	SmartyValidate :: register_validator('admin_email', 'admin_email', 'isEmail');

	$formError = array ();
	$formError['list_name'] = $formError['site_name'] = $formError['admin_password'] = Pommo::_T('Cannot be empty.');
	$formError['admin_password2'] = Pommo::_T('Passwords must match.');
	$formError['site_url'] = Pommo::_T('Must be a valid URL');
	$formError['admin_email'] = Pommo::_T('Must be a valid email');
	$smarty->assign('formError', $formError);
} else {
	// ___ USER HAS SENT FORM ___
	SmartyValidate :: connect($smarty);

	if (SmartyValidate :: is_valid($_POST)) {
		// __ FORM IS VALID
		if (isset ($_POST['installerooni'])) {

			
			// drop existing poMMo tables
			foreach (array_keys($dbo->table) as $key) {
				$table = $dbo->table[$key];
				$sql = 'DROP TABLE IF EXISTS ' . $table;
				$dbo->query($sql);
			}
			
			if (isset ($_REQUEST['debugInstall']))
				$dbo->debug(TRUE);

			// install poMMo
			//require_once ($pommo->_baseDir . '/inc/db_procedures.php');
			$install = parse_mysql_dump();

			if ($install) {
				// installation of DB went OK, set configuration values to user supplied ones

				$pass = $_POST['admin_password'];

				// install configuration
				$_POST['admin_password'] = md5($_POST['admin_password']);
				PommoAPI::configUpdate($_POST);

				// load configuration [depricated?], set message defaults.
				Pommo::requireOnce($pommo->_baseDir.'inc/helpers/configuration.php');
				PommoHelperConfig::messageResetDefault('all');

				$logger->addMsg(Pommo::_T('Installation Complete! You may now login and setup poMMo.'));
				$logger->addMsg(Pommo::_T('Login Username: ') . 'admin');
				$logger->addMsg(Pommo::_T('Login Password: ') . $pass);

				$smarty->assign('installed', TRUE);
			} else {
				// INSTALL FAILED

				$dbo->debug(FALSE);

				// drop existing poMMo tables
				foreach (array_keys($dbo->table) as $key) {
					$table = $dbo->table[$key];
					$sql = 'DROP TABLE IF EXISTS ' . $table;
					$dbo->query($sql);
				}

				$logger->addErr('Installation failed! Enable debbuging to expose the problem.');
			}
		}
	} else {
		// __ FORM NOT VALID
		$logger->addMsg(Pommo::_T('Please review and correct errors with your submission.'));
	}
}
$smarty->assign($_POST);
$smarty->display('install.tpl');
Pommo::kill();
?>