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

PommoAPI::stateReset(array('mailings_send','mailings_send2'));

$state =& PommoAPI::stateInit('mailings_send',array(
	'fromname' => $mailing['fromname'],
	'fromemail' => $mailing['fromemail'],
	'frombounce' => $mailing['frombounce'],
	'list_charset' => $mailing['charset'],
	'subject' => $mailing['subject'],
	'ishtml' => $mailing['ishtml'],
	'mailgroup' => $gid
	));

$altInclude = (empty($mailing['altbody'])) ? 'no' : 'yes';

$state =& PommoAPI::stateInit('mailings_send2',array(
	'body' => $mailing['body'],
	'altbody' => $mailing['altbody'],
	'altInclude' => $altInclude,
	'editorType' => 'wysiwyg'
	));

Pommo::redirect($pommo->_baseUrl.'admin/mailings/mailings_send.php');
?>