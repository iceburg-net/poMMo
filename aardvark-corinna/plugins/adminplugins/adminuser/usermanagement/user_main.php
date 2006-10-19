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


define('_IS_VALID', TRUE);

require ('../../../../bootstrap.php');
require_once (bm_baseDir . '/plugins/adminplugins/adminuser/usermanagement/class.UserPlugin.php');

$poMMo = & fireup("secure");
//$logger	= & $poMMo->_logger; //$dbo	= & $poMMo->_dbo;


//TODO weg
echo "<h3 style='color:red'>REQUEST DATA: "; print_r($_REQUEST); echo "</h3>";


// All POST GET $ REQUEST data
$data['action'] 	= $_REQUEST['action'];
$data['userid'] 	= $_REQUEST['userid'];
/*
// All POST/GET data we get from interaction with the FORM in the template
$data['mailings_queue']['limit'] 	= $_REQUEST['limit'];
$data['mailings_queue']['sortOrder']= $_REQUEST['sortOrder'];
$data['mailings_queue']['sortBy'] 	= $_REQUEST['sortBy'];

$data['page'] 	= $_REQUEST['page'];			//for Pager class
*/


// Generates template background and pagelist, logic to view, delete, send
$userplugin = new UserPlugin($poMMo);
$userplugin->execute($data);


/* clean up	// unset $_REQUEST action and mailid data
	unset($data['action']);
	unset($data['mailid']);*/
	

?>

