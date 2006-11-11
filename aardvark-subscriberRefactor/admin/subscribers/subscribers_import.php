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
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/import.php');

$pommo->init();
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();


// Maximum File Size (in MB) 
$max_file_size = 2;
$smarty->assign('maxSize',$max_file_size * 1024 * 1024);

// Filename (in $_FILES array)
$fname = "csvfile";


// if file is uploaded, validate & re-direct.
if (!empty($_FILES[$fname]['tmp_name'])) {
	
	$csvArray =& csvPrepareFile($_FILES[$fname]['tmp_name']);
	
	if (is_array($csvArray)) {
		$sessionArray['csvArray'] =& $csvArray;
		$pommo->set($sessionArray);
		Pommo::redirect('subscribers_import2.php');
	}
}

$smarty->display('admin/subscribers/subscribers_import.tpl');
Pommo::kill();
?>