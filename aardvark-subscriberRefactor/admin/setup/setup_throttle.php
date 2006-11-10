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
$pommo->init();
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();
$smarty->prepareForForm();
$smarty->assign('returnStr',Pommo::_T('Configure'));



// Read user requested changes	
if (!empty($_POST['throttle-restore'])) {
	$input = array ('throttle_MPS' => 3, 'throttle_BPS' => 0, 'throttle_DP' => 10, 'throttle_DBPP' => 0,'throttle_DMPP' => 0);
	PommoAPI::configUpdate($input,TRUE);
}
elseif(!empty($_POST['throttle-submit'])) {
	$input = array ('throttle_MPS' => $_POST['mps'], 'throttle_BPS' => $_POST['kbps'], 'throttle_DP' => $_POST['dp'], 'throttle_DBPP' => $_POST['dbpp'],'throttle_DMPP' => $_POST['dmpp']);
	PommoAPI::configUpdate($input,TRUE);
}

$config= PommoAPI::configGet(array('throttle_MPS', 'throttle_BPS', 'throttle_DP', 'throttle_DBPP','throttle_DMPP'));

$smarty->assign($config);
$smarty->display('admin/setup/setup_throttle.tpl');
Pommo::kill();
?>