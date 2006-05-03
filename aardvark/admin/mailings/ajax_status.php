<?php

/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * SOURCE: http://bmail.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/

/**********************************
	INITIALIZATION METHODS
 *********************************/
define('_IS_VALID', TRUE);

require ('../../bootstrap.php');
require_once (bm_baseDir . '/inc/class.json.php');

$bMail = & fireup('secure');
$logger = $bMail->logger;
$dbo = & $bMail->openDB();

$noMailing = FALSE;
$sql = 'SELECT subscriberCount, sent, notices, status, command FROM ' . $dbo->table['mailing_current'];
$dbo->query($sql);
if ($row = mysql_fetch_assoc($dbo->_result)) {
	$subscriberCount = $row['subscriberCount'];
	$sent = $row['sent'];
	$notices = quotesplit($row['notices']);
	$status = $row['status'];
	$command = $row['command'];
} else {
	$noMailing = TRUE;
	$subscriberCount = 0;
	$sent = 0;
	$percent = 100;
	$status = 'finished';
}

// end the mailing?
if (!$noMailing) {
	if ($subscriberCount == $sent) {
		$status = 'finished';
		require_once (bm_baseDir . '/inc/db_mailing.php');

		if (mailingQueueEmpty($dbo))
			dbMailingEnd($dbo);
	}
	$percent = round($sent * (100 / $subscriberCount));
}

// make JSON return
$json = array();
$encoder = new json;

$json['percent'] = $percent;
$json['sent'] = $sent;
$json['status'] = $status;

$json['command'] = (empty($command)) ? null : $command;
$json['notices'] = (empty($notices)) ? null : $notices;

header('x-json: '.$encoder->encode($json));
?>