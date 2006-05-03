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

/** 
* Don't allow direct access to this file. Must be called from
elsewhere
*/
defined('_IS_VALID') or die('Move along...');

// reads in an array of email addresses and inserts them into the queue table
function dbQueueCreate(& $dbo, & $input) {
	if (!is_array($input))
		die('<img src="' .
		bm_baseUrl . '/img/icons/alert.png" align="middle">dbQueueCreate() -> Bad Queue Passed.');

	// clear the table
	$sql = 'TRUNCATE TABLE ' . $dbo->table['queue'];
	$dbo->query($sql);

	foreach ($input as $email) {
		if (isset ($valStr))
			$valStr .= ',(\'' . $email . '\')';
		else
			$valStr = '(\'' . $email . '\')';
	}

	$sql = 'INSERT IGNORE INTO ' . $dbo->table['queue'] . ' (email) VALUES ' . $valStr;
	return $dbo->query($sql);
}

// Returns an array of emails + their domain from the queue. 
function & dbQueueGet(& $dbo, $id = '1', $limit = 100) {

	// purge our working queue
	$sql = 'UPDATE  '. $dbo->table['queue'] .' SET smtp_id=\'0\' WHERE smtp_id=\'' . $id . '\'';
	$dbo->query($sql);
	
	// mark our working queue
	$sql = 'UPDATE  '. $dbo->table['queue'] .' SET smtp_id=\'' . $id . '\' WHERE smtp_id=\'0\' LIMIT ' . $limit;
	$dbo->query($sql);

	// grab our working queue
	$sql = 'SELECT email FROM ' . $dbo->table['queue'] . ' WHERE smtp_id=\'' . $id . '\'';
	$emails = $dbo->getAll($sql, 'row', '0');
	
	// seperate emails into array([email],[domain])
	$retArray = array ();
	foreach ($emails as $email)
		$retArray[] = array (
			$email,
			substr($email,
			strpos($email,
			'@'
		) + 1));

	return $retArray;
}

function dbMailingCreate(& $dbo, & $input) {
	// generate security code
	$code = md5(rand(0, 5000) . time());

	// clear the current mailing from table if one exists ...
	$sql = 'TRUNCATE TABLE ' . $dbo->table['mailing_current'];
	$dbo->query($sql);

	// determine if this mailing is a HTML one or not..
	$html = "off";
	$altbody = '';
	if ($input['mailtype'] == "html") {
		$html = "on";
		if ($input['altInclude'] == 'yes' && !empty ($input['altbody']))
			$altbody = ' altbody=\'' . str2db($input['altbody']) . '\',';
	}

	// add this mailing to the mailing_current table.
	$sql = 'INSERT INTO ' . $dbo->table['mailing_current'] . ' SET fromname=\'' . str2db($input['fromname']) . '\', ' .
	'fromemail=\'' . str2db($input['fromemail']) . '\', frombounce=\'' . str2db($input['frombounce']) . '\', ' .
	'subject=\'' . str2db($input['subject']) . '\', body=\'' . str2db($input['body']) . '\',' . $altbody . ' ishtml=\'' . $html . '\', ' .
	'mailgroup=\'' . str2db($input['group_id']) . '\', subscriberCount=\'' . str2db($input['subscriberCount']) . '\', ' .
	'sent=\'0\', command=\'none\', status=\'stopped\', serial=NULL, securityCode=\'' . $code . '\'';
	$dbo->query($sql);
	
	// clear background processing scripts
	$sql = 'UPDATE `'.$dbo->table['config'].'` SET config_value=0 WHERE config_name=\'dos_processors\' LIMIT 1';
	$dbo->query($sql);

	return $code;
}

function dbMailingStamp(& $dbo, $arg) {
	switch ($arg) {
		case 'start' :
			$sql = 'UPDATE ' . $dbo->table['mailing_current'] . ' SET started=NOW()';
			break;
		case 'finished' :
			$sql = 'UPDATE ' . $dbo->table['mailing_current'] . ' SET finished=NOW()';
			break;
		case 'stop' :
			$sql = 'UPDATE ' . $dbo->table['mailing_current'] . ' SET command=\'stop\'';
			break;
		case 'restart' :
			$sql = 'UPDATE ' . $dbo->table['mailing_current'] . ' SET command=\'restart\'';
	}
	return ($dbo->query($sql)) ? true : false;
}

// checks the status or if a "command" has been issued for a mailing
function dbMailingPoll($dbo, $serial = '') {
	
	$sql = 'SELECT command, status, serial FROM ' . $dbo->table['mailing_current'];
	$dbo->query($sql);
	$row = mysql_fetch_row($dbo->_result);
	
	if ($row[2] != $serial) {
		bmMKill('Serials do not match, a different process has taken control?');
	}
	if ($row[0] == "stop") { // if script was sent the "stop" command...
		$sql = "UPDATE {$dbo->table['mailing_current']} SET status='stopped', command='none'";
		$dbo->query($sql);
		bmMKill('Mail processing has stopped as per Administrator\'s request');
	}
	elseif ($row[1] == "stopped") { // if mailing is in "stopped" status...
		bmMKill('Mail processing is in halted state. You must restart the mailing...');
	}
	return true;
}

function dbMailingUpdate(& $dbo, & $sentMails) {
	global $logger;
	
	// update DB
	$sql = 'UPDATE '.$dbo->table['mailing_current'].' SET sent=sent + '.count($sentMails).', notices=CONCAT_WS(\',\',notices,\''. mysql_real_escape_string(array2csv($logger->getMsg())) .'\')';
	$dbo->query($sql);

	// flush queue of sent mails
	if (!empty($sentMails)) {
	$sql = 'DELETE FROM ' . $dbo->table['queue'] . ' WHERE email IN (\''. implode('\',\'', $sentMails) . '\')';
	$dbo->query($sql);
	}
	return;
}

function dbMailingEnd(&$dbo) {
 	$sql = 'INSERT INTO '.$dbo->table['mailing_history'].' (fromname, fromemail, frombounce, subject, body, altbody, ishtml, mailgroup, subscriberCount, started, finished, sent) SELECT fromname, fromemail, frombounce, subject, body, altbody, ishtml, mailgroup, subscriberCount, started, finished, sent FROM '.$dbo->table['mailing_current'].' LIMIT 1';
 	$dbo->query($sql);
 	
 	$sql = 'TRUNCATE TABLE '.$dbo->table['mailing_current'];
	$dbo->query($sql);
	$sql = 'TRUNCATE TABLE '.$dbo->table['queue'];
	$dbo->query($sql);
 }
 

function mailingQueueEmpty(& $dbo) {
	$sql = 'SELECT email FROM ' . $dbo->table['queue'] . ' LIMIT 1';
	return ($dbo->query($sql,0)) ? false : true;
}

function & bmInitMailer(& $dbo, $relay_id = 1) {
	if (isset ($_SESSION["bMailer_" . $relay_id]))
		return $_SESSION["bMailer_" . $relay_id];

	global $bMail;
	global $logger;

	$sql = "SELECT ishtml,fromname,fromemail,frombounce,subject,body,altbody FROM " . $dbo->table['mailing_current'];
	$dbo->query($sql);
	$row = mysql_fetch_assoc($dbo->_result);

	$html = FALSE;
	$altbody = NULL;
	if ($row['ishtml'] == "on") {
		$html = TRUE;
		if (!empty ($row['altbody']))
			$altbody = db2mail($row['altbody']);
	}

	// load new bMailer into session
	$_SESSION["bMailer_" . $relay_id] = new bMailer(db2mail($row['fromname']), $row['fromemail'], $row['frombounce']);

	// reference it as $Mail
	$bmMailer = & $_SESSION["bMailer_" . $relay_id];
	
	$logger->addMsg('bmMailer initialized with relay ID #'.$relay_id,1);

	// prepare the Mail with prepareMail()	-- if it fails, stop the mailing & report errors.
	if (!$bmMailer->prepareMail(db2mail($row['subject']), db2mail($row['body']), $html, $altbody)) {
		$logger->addMsg('prepareMail() returned false.',3);
		$logger->addMsg($bMail->getMessages(" "));
		$sql = 'UPDATE '.$dbo->table['mailing_current'].' SET status=\'stopped\', notices=CONCAT_WS(\',\',notices,\''. mysql_real_escape_string(array2csv($logger->getMsg())) .'\')';
		$dbo->query($sql);
		bmMKill('prepareMail() returned errors.');
	}

	// Set the appropriate SMTP relay and keep SMTP connection up
	if ($bMail->_config['list_exchanger'] == 'smtp') {
		$bmMailer->setRelay($bMail->_config['smtp_' . $relay_id]);
		$bmMailer->SMTPKeepAlive = TRUE;
	}
	return $bmMailer;
}

function & bmInitThrottler(& $dbo, & $queue, $relay_id = 1) {
	if (isset ($_SESSION["bThrottle_" . $relay_id]))
		return $_SESSION["bThrottle_" . $relay_id];

	global $bMail;

	$config = $bMail->getConfig(array (
		'throttle_MPS',
		'throttle_BPS',
		'throttle_DP',
		'throttle_DMPP',
		'throttle_DBPP'
	));

	$_SESSION["bThrottle_" . $relay_id] = new bThrottler(time(), $queue, $config['throttle_MPS'], intval($config['throttle_BPS'] * 1024), $config['throttle_DP'], $config['throttle_DMPP'], intval($config['throttle_DBPP'] * 1024));
	return $_SESSION["bThrottle_" . $relay_id];
}
?>
