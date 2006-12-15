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
 
// Clears the entire database, resets auto increment values
 
/**********************************
	INITIALIZATION METHODS
 *********************************/
define('_poMMo_support', TRUE);

require ('../../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'install/helper.install.php');

if (bmIsInstalled())
	$pommo->init();
else
	$pommo->init(array('authLevel' => 0));
	
$dbo =& $pommo->_dbo;


foreach($dbo->table as $id => $table) {
	if($id == 'config' || $id == 'updates')
		continue;
		
	$query = "DELETE FROM ".$table;
	if(!$dbo->query($query))
		die('ERROR deleting '.$id); 
		
	$query = "ALTER TABLE ".$table." AUTO_INCREMENT = 1";
	if(!$dbo->query($query))
		die('ERROR setting AUTO_INCREMENT on '.$id); 
}
	
die('Database Reset.');