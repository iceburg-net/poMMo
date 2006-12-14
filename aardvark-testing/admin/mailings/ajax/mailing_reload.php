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
require ('../../../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/mailings.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/groups.php');

$pommo->init(array('noDebug' => TRUE, 'keep' => TRUE));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

$mailing = current(PommoMailing::get(array('id' => $_GET['mail_id'])));
if (empty($mailing))
	Pommo::kill('Unable to load mailing');
	
// change group name to ID
$groups = PommoGroup::get();
$gid = 'all';
foreach($groups as $group) 
	if ($group['name'] == $mailing['group'])
		$gid = $group['id'];
		

$pommo->set(array(
	'mailingData' => array (
		'fromname' => $mailing['fromname'],
		'fromemail' => $mailing['fromemail'],
		'frombounce' => $mailing['frombounce'],
		'subject' => $mailing['subject'],
		'ishtml' => $mailing['ishtml'],
		'charset' => $mailing['charset'],
		'mailgroup' => $gid,
		'altbody' => $mailing['altbody'],
		'body' => $mailing['body']
		)
	));
Pommo::redirect($pommo->_baseUrl.'admin/mailings/mailings_send.php');
?>