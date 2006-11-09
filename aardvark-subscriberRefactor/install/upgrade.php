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
if ($pommo->_config['revision'] == $pommo->_revision && !isset ($_REQUEST['forceUpgrade']) && !isset ($_REQUEST['continue'])) {
	$logger->addErr(sprintf(Pommo::_T('poMMo appears to be up to date. If you want to force an upgrade, %s click here %s'), '<a href="' . $_SERVER['PHP_SELF'] . '?forceUpgrade=TRUE">', '</a>'));
	$smarty->display('upgrade.tpl');
	Pommo::kill();
}

// include the upgrade procedure file
Pommo::requireOnce($pommo->_baseDir . '/install/helper.upgrade.php');

if (isset ($_REQUEST['disableDebug']))
	unset ($_REQUEST['debugInstall']);
elseif (isset ($_REQUEST['debugInstall'])) $smarty->assign('debug', TRUE);

if (empty($_REQUEST['continue'])) {
	if (!bmIsInstalled())
		$logger->addErr(sprintf(Pommo::_T('poMMo does not appear to be installed! Please %s INSTALL %s before attempting an upgrade.'), '<a href="' . $pommo->_baseUrl . 'install/install.php">', '</a>'));
	else
		$logger->addErr(sprintf(Pommo::_T('To upgrade poMMo, %s click here %s'), '<a href="' . $pommo->_baseUrl . 'install/upgrade.php?continue=TRUE">', '</a>'));
} else {
	$smarty->assign('attempt', TRUE);

	if (isset ($_REQUEST['debugInstall']))
		$dbo->debug(TRUE);

	$dbo->dieOnQuery(FALSE);
	if (bmUpgrade($dbo)) {
		$logger->addErr(Pommo::_T('Upgrade Complete!'));

		// Read in RELEASE Notes -- TODO -> use file_get_contents() one day when everyone has PHP 4.3
		$filename = $pommo->_baseDir . '/docs/RELEASE';
		$handle = fopen($filename, "r");
		$x = fread($handle, filesize($filename));
		fclose($handle);

		$smarty->assign('notes', $x);
		$smarty->assign('upgraded', TRUE);
	} else {
		$logger->addErr(Pommo::_T('Upgrade Failed!'));
	}
}

$smarty->display('upgrade.tpl');
Pommo::kill();
?>
