<?php
/*
 * ajax.php
 *
 * PROJECT: poMMo
 * COPYRIGHT: (c) 2006 Brice Burgess / All Rights Reserved
 * LICENSE: http://www.gnu.org/licenses/gpl.html GNU/GPL
 *
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * SOURCE: http://www.iceburg.net/brice/
 */

/**********************************
	INITIALIZATION METHODS
*********************************/
require ('../../../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/fields.php');
$pommo->init(array('noDebug' => TRUE));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

function jsonKill($msg, $success = "false") {
	$json = "{success: $success, msg: \"".$msg."\"}";
	die($json);
}


$when = '';
foreach($_POST['grid'] as $order => $id) { // syntax for multi-row updates in in 1 query.
	$id = substr($id,2);
	$when .= $dbo->prepare("WHEN '%s' THEN '%s'",array($id,$order)).' ';
}

$query = "
	UPDATE ".$dbo->table['fields']."
	SET field_ordering = 
		CASE field_id ".$when." ELSE field_ordering END";
if (!$dbo->query($dbo->prepare($query)))
	jsonKill('Error Updating Order');
	
jsonKill(Pommo::_T('Order Updated.'), "true");
			