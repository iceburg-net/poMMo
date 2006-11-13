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

require ('../../bootstrap.php');
require_once (bm_baseDir . '/plugins/mailingqueue/class.QueuePlugin.php');


$poMMo = & fireup("secure");


//TODO weg
//echo "<h3 style='color:red'>REQUEST DATA: "; print_r($_REQUEST); echo "</h3>";



// All POST/GET data we get from interaction with the FORM in the template
$data['mailings_queue']['limit'] 	= $_REQUEST['limit'];
$data['mailings_queue']['sortOrder']= $_REQUEST['sortOrder'];
$data['mailings_queue']['sortBy'] 	= $_REQUEST['sortBy'];

$data['action'] 	= $_REQUEST['action'];
$data['mailid'] 	= $_REQUEST['mailid'];

$data['page'] 	= $_REQUEST['page'];			//for Pager class


//TODO WEG
//echo "<h3 style='color:red'>DATA DATA: "; print_r($data); echo "</h3>";


$queueplugin = new QueuePlugin($poMMo);

// Generates template background and pagelist, logic to view, delete, send
$queueplugin->execute($data);


	// unset $_REQUEST action and mailid data
	//unset($data['action']);
	//unset($data['mailid']);


?>

