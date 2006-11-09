<?php
/*
 * ajax.php
 *
 * PROJECT: poMMo
 * COPYRIGHT: (c) 2006 Brice Burgess / All Rights Reserved
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL
 *
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * SOURCE: http://www.iceburg.net/brice/
 */

/**********************************
	INITIALIZATION METHODS
*********************************/


require ('../../bootstrap.php');
require_once ($pommo->_baseDir . '/inc/db_fields.php');

$pommo = & fireup('secure');
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

// key is order, value is demo ID
function updateList($array) {
	global $dbo;
	foreach($array as $key => $value) {
		if (!is_numeric($key) || !is_numeric($value))
			die(Pommo::_T('Error updating order'));
		
		$sql = 'UPDATE '.$dbo->table['subscriber_fields'].' set field_ordering='.$key.' WHERE field_id='.$value.' LIMIT 1';
		if (!$dbo->query($sql))
			die(Pommo::_T('Error updating order'));
	}
}

updateList($_POST['demoOrder']);
?>
