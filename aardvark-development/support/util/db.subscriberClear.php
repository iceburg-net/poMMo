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
 
// Clears the entire database, resets auto increment values
 
/**********************************
	INITIALIZATION METHODS
 *********************************/
define('_poMMo_support', TRUE);
require ('../../bootstrap.php');
$pommo->init();

$dbo =& $pommo->_dbo;

$a = array(
	'subscribers' => $dbo->table['subscribers'],
	'subscriber_data' => $dbo->table['subscriber_data']
);

foreach($a as $id => $table) {
	if($id == 'config' || $id == 'updates')
		continue;
		
	$query = "DELETE FROM ".$table;
	if(!$dbo->query($query))
		die('ERROR deleting '.$id); 
		
	$query = "ALTER TABLE ".$table." AUTO_INCREMENT = 1";
	if(!$dbo->query($query))
		die('ERROR setting AUTO_INCREMENT on '.$id); 
}
	
die('Subscribers Reset.');