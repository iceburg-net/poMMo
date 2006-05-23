<?php
die();
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
	STARTUP ROUTINES
 *********************************/

define('_IS_VALID', TRUE);
require ('../../bootstrap.php');
require (bm_baseDir . '/inc/class.bmailer.php');
require (bm_baseDir . '/inc/class.bthrottler.php');
require (bm_baseDir . '/inc/db_mailing.php');

$serial = (empty ($_GET['serial'])) ? time() : addslashes($_GET['serial']);
$bm_sessionName = $serial;

$poMMo = & fireup('sessionName');
$poMMo->loadConfig();
$dbo = & $poMMo->openDB();
$dbo->dieOnQuery(FALSE);


$logger = & $poMMo->logger;
$logger->addMsg(sprintf(_T('Background script with serial %d spawned'), $serial), 3);

if (empty ($poMMo->_config['list_exchanger'])) {
	// get list exchanger & smtp values. If more than 1 smtp relay exist, enter "multimode"
	$config = $poMMo->getConfig(array (
		'list_exchanger',
		'smtp_1',
		'smtp_2',
		'smtp_3',
		'smtp_4',
		'throttle_SMTP'
	));
	$poMMo->_config['list_exchanger'] = $config['list_exchanger'];
	$poMMo->_config['multimode'] = false;
	$poMMo->_config['throttler'] = 'shared';

	if ($config['list_exchanger'] == 'smtp') {
		if (!empty ($config['smtp_1'])) {
			$poMMo->_config['smtp_1'] = unserialize($config['smtp_1']);
			$logger->addMsg('SMTP Relay #1 detected', 1);
		}
		if (!empty ($config['smtp_2'])) {
			$poMMo->_config['multimode'] = true;
			$poMMo->_config['smtp_2'] = unserialize($config['smtp_2']);
			$logger->addMsg('SMTP Relay #2 detected, multimode enabled.', 1);
		}
		if (!empty ($config['smtp_3'])) {
			$poMMo->_config['multimode'] = true;
			$poMMo->_config['smtp_3'] = unserialize($config['smtp_3']);
			$logger->addMsg('SMTP Relay #3 detected, multimode enabled.', 1);
		}
		if (!empty ($config['smtp_4'])) {
			$poMMo->_config['multimode'] = true;
			$poMMo->_config['smtp_4'] = unserialize($config['smtp_4']);
			$logger->addMsg('SMTP Relay #4 detected, multimode enabled.', 1);
		}
		if ($config['throttle_SMTP'] == 'individual')
			$poMMo->_config['throttler'] = 'individual';
		$logger->addMsg('SMTP Throttle control set to: ' . $poMMo->_config['throttler'], 1);
	}
}

function bmMKill($reason) {
	global $logger;
	global $dbo;

	$logger->addMsg('Script Ending: ' . $reason, 2);

	// deduct value (this script) from DOS mail processor protection.
	$sql = 'UPDATE `' . $dbo->table['config'] . '` SET config_value=config_value-1 WHERE config_name=\'dos_processors\' LIMIT 1';
	$dbo->query($sql);
	
	$x = $logger->getMsg();
	
	// update DB notices
	$sql = 'UPDATE ' . $dbo->table['mailing_current'] . ' SET notices=CONCAT_WS(\',\',notices,\'' . mysql_real_escape_string(array2csv($x)) . '\')';
	$dbo->query($sql);

	bmKill($reason);
}


/**********************************
	SECURITY ROUTINES
 *********************************/

// DOS prevention
if ($poMMo->_config['dos_processors'] > 5)
	die();
else {
	$sql = 'UPDATE `' . $dbo->table['config'] . '` SET config_value=config_value+1 WHERE config_name=\'dos_processors\' LIMIT 1';
	$dbo->query($sql);
}

// check to see if the mailing has been serialized (serial exists). If not, set the current mailing's serial to this scripts.
$sql = 'SELECT serial,securityCode,command,finished FROM ' . $dbo->table['mailing_current'] . ' LIMIT 1';
$dbo->query($sql);
$row = mysql_fetch_assoc($dbo->_result);

if (empty ($row['securityCode']) || $_GET['securityCode'] != $row['securityCode'])
	bmMKill('Script stopped for security reasons.');
elseif ($row['finished'] > 0) bmMKill('Mailing has completed.');
elseif (empty ($row['serial'])) { // if no serial has yet been entered for this mailing... serialize & start the mailing...
	$sql = "UPDATE {$dbo->table['mailing_current']} SET serial='" . $serial . "', status='started', command='none'";
	$dbo->query($sql);
}
elseif ($row['serial'] != $serial) {
	// if this script's serial & the mailings don't match, check if a restart command was given, or else kill the script.
	if ($row['command'] == 'restart') {
		$sql = "UPDATE {$dbo->table['mailing_current']} SET serial='" . $serial . "', command='none', status='started'";
		$dbo->query($sql);
		$logger->addMsg('Mailing resumed under script with serial ' . $serial, 3);
	} else
		bmMKill('Serials do not match. Another script is probably processing this mailing. To take control, stop and restart the mailing.');
}

/**********************************
 * MAILING INITIALIZATION
 *********************************/

// checks to see if mailing should be halted (or is in halted state...)
dbMailingPoll($dbo, $serial);

// spawn script per relay if in multimode
if ($poMMo->_config['multimode']) {
	if (empty ($_GET['relay_id'])) {
		if (!empty ($poMMo->_config['smtp_1']))
			bmHttpSpawn(bm_baseUrl.'/admin/mailings/mailings_send4.php?relay_id=1&serial=' .
			$serial . '&securityCode=' . $_GET['securityCode']);
		sleep(1); // delay to help prevent "shared" throttlers racing to create queue
		if (!empty ($poMMo->_config['smtp_2']))
			bmHttpSpawn(bm_baseUrl.'/admin/mailings/mailings_send4.php?relay_id=2&serial=' .
			$serial . '&securityCode=' . $_GET['securityCode']);
		if (!empty ($poMMo->_config['smtp_3']))
			bmHttpSpawn(bm_baseUrl.'/admin/mailings/mailings_send4.php?relay_id=3&serial=' .
			$serial . '&securityCode=' . $_GET['securityCode']);
		if (!empty ($poMMo->_config['smtp_4']))
			bmHttpSpawn(bm_baseUrl.'/admin/mailings/mailings_send4.php?relay_id=4&serial=' .
			$serial . '&securityCode=' . $_GET['securityCode']);
		bmMKill('Multimode detected. Spawning background scripts for SMTP relays.');
	}
	$bmMailer = & bmInitMailer($dbo, $_GET['relay_id']);
	$bmQueue = & dbQueueGet($dbo, $_GET['relay_id']);

	if ($poMMo->_config['throttler'] == 'individual')
		$bmThrottler = & bmInitThrottler($dbo, $bmQueue, $_GET['relay_id']);
	else
		$bmThrottler = & bmInitThrottler($dbo, $bmQueue);
} else {
	$bmMailer = & bmInitMailer($dbo);
	$bmQueue = & dbQueueGet($dbo);
	$bmThrottler = & bmInitThrottler($dbo, $bmQueue);
}

// set maximum runtime of this script in seconds
$maxRunTime = 110;
if (ini_get('safe_mode'))
	$maxRunTime = ini_get('max_execution_time') - 7;
else
	set_time_limit($maxRunTime +7);

// start throttler's timer
$bmThrottler->startScript($maxRunTime);

$logger->addMsg('Mailer+Queue+Throttler Initialized. Queue Size: ' . count($bmQueue) . ' mails.', 1);

$byteMask = $bmThrottler->byteTracking();
if ($byteMask > 1) // byte tracking/throttling enabled
	$bmMailer->trackMessageSize();

/**********************************
   PROCESS QUEUE
 *********************************/

// TODO -> all these globals seem kludgey.. use iterative, or a sender object.

$sentMails = array ();
$timer = time();

function updateDB(& $sentMails, & $timer) {
	global $serial;
	global $dbo;
	global $bmThrottler;
	global $logger;

	// update mailing status in database and flush sent mails from queue
	dpoMMoingUpdate($dbo, $sentMails);

	// poll mailing	
	dbMailingPoll($dbo, $serial);

	// reset variables
	$sentMails = array ();
	$timer = time();
}

// recursively proccess the throttler, returns true if queue is empty, false if not.
function proccessQueue() {
	global $bmThrottler;
	global $bmMailer;
	global $logger;
	global $byteMask;
	global $sentMails;
	global $poMMo;
	global $timer;

	// check if there are mails in throttler queue, return true if throttler's queue is empty
	if (!$bmThrottler->mailsInQueue())
		return true;

	// attempt to pull email from throttler's queue
	$mail = $bmThrottler->pullQueue();

	// if an email was returned, send it.
	if ($mail) {
		if (!$bmMailer->bmSendmail($mail[0])) // sending failed, write to log  
			$logger->addMsg(_T('Error Sending Mail'));

		// If throttling by bytes (bandwith) is enabled, add the size of the message to the throttler
		if ($byteMask > 1) {
			$bytes = $bmMailer->GetMessageSize();
			if ($byteMask > 2)
				$bmThrottler->updateBytes($bytes, $mail[1]);
			else
				$bmThrottler->updateBytes($bytes);
			$logger->addMsg('Added ' . $bytes . ' to throttler.', 1);
		}

		// add email to sent mail array
		$sentMails[] = $mail[0];
	}
	elseif ($bmThrottler->getCommand() == 2) // kill command received
	return false;

	// Every 10-ish seconds, or to prevent MySQL update "flood", launch updateDB() which; 
	// updates mailing status in database, removes sent mails from queue, and perform a "poll" 
	if ((time() - $timer) > 10 || count($sentMails) > 40 || $logger->isMsg() > 40)
		updateDB($sentMails, $timer);

	// recurisve call to processQueue()
	return proccessQueue();
}

// process the queue until it is empty or kill command received
while (proccessQueue()) {

	updateDB($sentMails, $timer);

	// fetch emails from queue
	$bmQueue = array ();
	if (!empty ($_GET['relay_id']))
		$bmQueue = & dbQueueGet($dbo, $_GET['relay_id']);
	else
		$bmQueue = & dbQueueGet($dbo);

	// if queue is empty, end mailing and kill script.	
	if (empty ($bmQueue)) {
		dpoMMoingStamp($dbo, "finished");
		if ($bmMailer->SMTPKeepAlive == TRUE)
			$bmMailer->SmtpClose();
		bmMKill('Mailing finished!');
	}

	// else, repopulate throttler's queue
	$bmThrottler->loadQueue($bmQueue);
	$logger->addMsg('Adding more mails to the throttler queue.', 1);
}

updateDB($sentMails, $timer);

// kill signal sent from throttler (max exec time likely reached), respawn.	
if (!empty ($_GET['relay_id']))
	bmHttpSpawn(bm_baseUrl.'/admin/mailings/mailings_send4.php?relay_id=' .
	$_GET['relay_id'] . '&serial=' . $serial . '&securityCode=' . $_GET['securityCode']);
else
	bmHttpSpawn(bm_baseUrl.'/admin/mailings/mailings_send4.php?serial=' . $serial . '&securityCode=' . $_GET['securityCode']);

bmMKill('Respawned... Max exec time likely reached.');

//echo 'Ready to respawn <a href="mailings_send4.php?serial=' . $serial . '&securityCode=' . $_GET['securityCode'].'">here</a>';
?>