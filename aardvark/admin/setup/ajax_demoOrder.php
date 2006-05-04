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
define('_IS_VALID', TRUE);

require ('../../bootstrap.php');
require_once (bm_baseDir . '/inc/db_demographics.php');

$poMMo = & fireup('secure');
$logger = & $poMMo->logger;
$dbo = & $poMMo->openDB();

// key is order, value is demo ID
function updateList($array) {
	global $dbo;
	foreach($array as $key => $value) {
		if (!is_numeric($key) || !is_numeric($value))
			die(_T('Error updating order'));
		
		$sql = 'UPDATE '.$dbo->table['demographics'].' set demographic_ordering='.$key.' WHERE demographic_id='.$value.' LIMIT 1';
		if (!$dbo->query($sql))
			die(_T('Error updating order'));
	}
}

updateList($_POST['demoOrder']);
?>
