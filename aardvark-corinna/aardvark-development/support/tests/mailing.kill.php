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
define('_poMMo_support', TRUE);

require ('../../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'install/helper.install.php');

if (bmIsInstalled())
	$pommo->init();
else
	$pommo->init(array('authLevel' => 0));
	
$dbo =& $pommo->_dbo;


$query = "DELETE FROM ".$dbo->table['mailing_current'];
if(!$dbo->query($query))
	die('ERROR deleting current mailings.');
	
$query = "
UPDATE ".$dbo->table['mailings']." 
SET status=2 
WHERE status=1";
if(!$dbo->query($query))
	die('ERROR updating mailing status.');
	
die('Current Mailing(s) Terminated.');