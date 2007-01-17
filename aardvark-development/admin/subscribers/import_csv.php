<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/licenses/gpl.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. Notify the above author of modifications to contents within.
 * 
 *  WHY? Because this is a community project -- purposely released under the GPL.
 *    We'd love to have the possiblity to include your derivative works! 
 *    We'd love to coordinate around your development efforts!
 *    We'd love to assist you with your changes!
 *    DON'T BE A STRANGER!
 * 
 ** [END HEADER]**/
 
 /**********************************
	INITIALIZATION METHODS
 *********************************/
require ('../../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/import.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/subscribers.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/fields.php');

$pommo->init(array('keep' => TRUE));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
Pommo::requireOnce($pommo->_baseDir.'inc/classes/template.php');
$smarty = new PommoTemplate();
$smarty->assign('returnStr',Pommo::_T('Import'));

$preview = $pommo->get('preview');

// find the most columns in a row
$cols = 0;
foreach($preview as $row) {
	$c = count($row);
	if($c > $cols)
		$cols = $c;
}

$smarty->assign('preview',$preview);
$smarty->assign('colNum',$cols);
$smarty->assign('fields',PommoField::get());

$smarty->display('admin/subscribers/import_csv.tpl');
Pommo::kill();
?>