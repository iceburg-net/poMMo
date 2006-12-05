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


require ('../../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/classes/mailing.php');
Pommo::requireOnce($pommo->_baseDir.'inc/classes/throttler.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/mailings.php');

/**********************************
	STARTUP ROUTINES
 *********************************/
 
// skips serial and security code checking. For debbuing this script.
$skipSecurity = FALSE;

// # of mails to fetch from the queue at a time (Default: 100)
$queueSize = 100;

// set maximum runtime of this script in seconds (Default: 80). If unable to set (SAFE MODE,etc.), max runtime will default to 3 seconds less than current max.
$maxRunTime = 80;
if (ini_get('safe_mode'))
	$maxRunTime = ini_get('max_execution_time') - 10;
else
	set_time_limit($maxRunTime +90);

$serial = (empty ($_GET['serial'])) ? time() : addslashes($_GET['serial']);
$relayID = (empty ($_GET['relayID'])) ? 1 : $_GET['relayID']; 
if (!$skipSecurity && $relayID < 1 && $relayID > 4)
	PommoMailing::kill('Mailing stopped. Bad RelayID.', TRUE);


/**********************************
	INITIALIZATION METHODS
 *********************************/

$pommo->init(array('sessionID' => $serial, 'keep' => TRUE));
$dbo = & $pommo->_dbo;
$logger = & $pommo->_logger;

// don't die on query so we can capture logs'
// NOTE: Be extra careful to check the success of queries/methods!
$dbo->dieOnQuery(FALSE); 

$input = $pommo->get('mailingData');

if (empty($input['config'])) {
	$logger->addMsg(Pommo::_T('Background mailer spawned.'), 3);
	
	// get list exchanger & smtp values. If more than 1 smtp relay exist, enter "multimode"
	$input['config'] = PommoAPI::configGet(array (
		'list_exchanger',
		'smtp_1',
		'smtp_2',
		'smtp_3',
		'smtp_4',
		'throttle_SMTP'
	));
	$config =& $input['config'];

	if ($config['list_exchanger'] == 'smtp') {
		
		$config['multimode'] = false;
		
		if (!empty ($config['smtp_1'])) {
			$config['smtp_1'] = unserialize($config['smtp_1']);
			$logger->addMsg('SMTP Relay #1 detected', 1);
		}
		
		for($i = 2; $i < 5; $i++) {
			if (empty($config['smtp_'.$i])) {
				$config['multimode'] = true;
				$config['smtp_'.$i] = unserialize($config['smtp_'.$i]);
				$logger->addMsg('SMTP Relay #'.$i.' detected', 1);
			}
		}
		
		if($config['throttle_SMTP'] != 'shared' && $config['throttle_SMTP'] != 'individual')
			PommoMailing::kill('Illegal throttle_SMTP value');
		
		$logger->addMsg('SMTP Throttle Mode: ' . $config['throttle_SMTP'], 1);
		if ($pommo->_config['multimode'])
			$logger->addMsg('Multimode enabled', 1);
	}
} else {
	$logger->addMsg(Pommo::_T('Background mailer spawned.'), 2);
}

$config =& $input['config'];


/**********************************
 * MAILING INITIALIZATION
 *********************************/
 
$mailing = current(PommoMailing::get(array('code' => $_GET['securityCode'], 'active' => TRUE)));
if (empty($mailing))
	PommoMailing::kill('Could not initialize a current mailing.'); 


// SECURITY ROUTINES...
if(!empty($mailing['end']) && $mailing['end'] > 0)
	PommoMailing::kill('Mailing has completed.', TRUE);

if(empty($mailing['serial']))
	if (!PommoMailing::mark($serial,$mailing['id']))
		PommoMailing::kill('Unable to serialize Mailing', TRUE);
		
if (!$skipSecurity && empty($_GET['securityCode']))
	PommoMailing::kill('Mailing stopped for security reasons.', TRUE);

	
// Poll Mailing Status
PommoMailing::poll($mailing['id']);


// If we're in multimode, spawn scripts (unless this is a spawn!)
if ($config['multimode'] && !isset($_GET['spawn'])) {
	for ($i = 1; $i < 5; $i++) {
		if(!empty($config['smtp_'.$i])) {
			PommoMailing::respawn(array('spawn' => 'TRUE', 'relay_id' => $i));
			sleep(3); // prevent a shared throttler race
		}
	}
	PommoMailing::kill(Pommo::_T('Multimode detected. Spawning a background mailer per SMTP relay'));
}

// check if message body contains personalizations
// personalizations are cached in session

if(!isset($_SESSION['pommo']['personalization'])) {
	Pommo::requireOnce($pommo->_baseDir.'inc/helpers/personalize.php');
	
	$_SESSION['pommo']['personalization'] = FALSE;
	$matches = array();
	preg_match('/\[\[[^\]]+]]/', $mailing['body'], $matches);
	if (!empty($matches))
		$_SESSION['pommo']['personalization'] = TRUE;
	preg_match('/\[\[[^\]]+]]/', $mailing['altbody'], $matches);
	if (!empty($matches))
		$_SESSION['pommo']['personalization'] = TRUE;

	// cache personalizations in session
	if ($_SESSION['pommo']['personalization']) {
		$_SESSION['pommo']['personalization_body'] = PommoHelperPersonalize::get($mailing['body']);
		$_SESSION['pommo']['personalization_altbody'] = PommoHelperPersonalize::get($mailing['altbody']);
	}
}

/**********************************
 * PREPARE THE MAILER
 *********************************/
$html = ($mailing['ishtml'] == 'on') ? TRUE : FALSE;

$mailer = new PommoMailer($mailing['fromname'],$mailing['fromemail'],$mailing['frombounce'], $config['list_exchanger'],NULL,$mailing['charset'], $_SESSION['pommo']['personalization']);

if (!$mailer->prepareMail($mailing['subject'], $mailing['body'], $html, $mailing['altbody']))
	PommoMailing::kill('prepareMail() returned errors.');
	
// Set appropriate SMTP relay
if ($config['list_exchanger'] == 'smtp') {
	$mailer->setRelay($config['smtp_' . $relayID]);
	$mailer->SMTPKeepAlive = TRUE;
}

$logger->addMsg('Mailer initialized with for Relay # '.$relayID,1);



/**********************************
 * INITIALIZE Queue, Throttler
 *********************************/
 
$queue = PommoMailing::queueGet($relayID, $queueSize);


/*
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
*/
	

	$bmMailer = & bmInitMailer($dbo, $_GET['relay_id']);
	$bmQueue = & dbQueueGet($dbo, $_GET['relay_id'], $queueSize);

	if ($pommo->_config['throttler'] == 'individual')
		$bmThrottler = & bmInitThrottler($dbo, $bmQueue, $_GET['relay_id']);
	else
		$bmThrottler = & bmInitThrottler($dbo, $bmQueue);
} else {
	$bmMailer = & bmInitMailer($dbo);
	$bmQueue = & dbQueueGet($dbo, 1, $queueSize);
	$bmThrottler = & bmInitThrottler($dbo, $bmQueue);
}

}


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

	// update mailing status in database and flush sent mails from queue
	dbMailingUpdate($dbo, $sentMails);

	// poll mailing	
	dbMailingPoll($serial);

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
	global $pommo;
	global $timer;

	// check if there are mails in throttler queue, return true if throttler's queue is empty
	if (!$bmThrottler->mailsInQueue())
		return true;

	// attempt to pull email from throttler's queue
	$mail = $bmThrottler->pullQueue();

	// if an email was returned, send it.
	if ($mail) {
		if (!$bmMailer->bmSendmail($mail[0])) // sending failed, write to log  
			$logger->addMsg(Pommo::_T('Error Sending Mail'));

		// If throttling by bytes (bandwith) is enabled, add the size of the message to the throttler
		if ($byteMask > 1) {
			$bytes = $bmMailer->GetMessageSize();
			if ($byteMask > 2)
				$bmThrottler->updateBytes($bytes, $mail[1]);
			else
				$bmThrottler->updateBytes($bytes);
			$logger->addMsg('Added ' . $bytes . ' bytes to throttler.', 1);
		}

		// add email to sent mail array
		$sentMails[] = $mail[0];
	}
	elseif ($bmThrottler->getCommand() == 2) // kill command received
	return false;

	// Every 10-ish seconds, or to prevent MySQL update "flood", launch updateDB() which; 
	// updates mailing status in database, removes sent mails from queue, and perform a "poll" 
	if ((time() - $timer) > 9 || count($sentMails) > 40 || $logger->isMsg() > 40)
		updateDB($sentMails, $timer);

	// recurisve call to processQueue()
	return proccessQueue();
}

// process the queue until it is empty or kill command received
while (proccessQueue()) {

	updateDB($sentMails, $timer);

	// fetch emails from queue
	$bmQueue = array ();
	$bmQueue = & dbQueueGet($dbo, $_GET['relay_id'], $queueSize);
	

	// if queue is empty, end mailing and kill script.	
	if (empty($bmQueue)) {
		if ($pommo->_config['multimode']) {
			// before killing check to see if we're in multimode and queue is truly empty
			$sql = 'SELECT COUNT(*) FROM ' . $dbo->table['queue'] . ' LIMIT 1';
			if ($dbo->query($sql,0)) {
				// the queue is not empty, another relay is working on it. Sleep 10 seconds then break (respawn)
				sleep(10);
				break;
			}
		}
		
		dbMailingStamp($dbo, "finished");
		if ($bmMailer->SMTPKeepAlive == TRUE)
			$bmMailer->SmtpClose();
		bmMKill('Mailing finished!',TRUE);
	}
	else {
	// else, repopulate throttler's queue
	$bmThrottler->loadQueue($bmQueue);
	$logger->addMsg('Adding more mails to the throttler queue.', 1);
	}
}

updateDB($sentMails, $timer);

// kill signal sent from throttler (max exec time likely reached), respawn.	
if (!empty ($_GET['relay_id']))
	bmSpawn($pommo->_baseUrl .
	'admin/mailings/mailings_send4.php?relay_id=' .
	$_GET['relay_id'] . '&serial=' . $serial . '&securityCode=' . $_GET['securityCode']);
else
	bmSpawn($pommo->_baseUrl .
	'admin/mailings/mailings_send4.php?serial=' . $serial . '&securityCode=' . $_GET['securityCode']);

bmMKill('Respawned... Max exec time likely reached.');

//echo 'Ready to respawn <a href="mailings_send4.php?serial=' . $serial . '&securityCode=' . $_GET['securityCode'].'">here</a>';
?>
