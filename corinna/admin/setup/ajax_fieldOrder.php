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
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/fields.php');
$pommo->init();
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

foreach($_POST['fieldOrder'] as $val => $id) { // syntax for multi-row updates in in 1 query.
	$when .= $dbo->prepare("WHEN '%s' THEN '%s'",array($id,$val)).' ';
}

$query = "
	UPDATE ".$dbo->table['fields']."
	SET field_ordering = 
		CASE field_id ".$when." ELSE field_ordering END";
if (!$dbo->query($dbo->prepare($query)))
	echo('Error updating order');
			