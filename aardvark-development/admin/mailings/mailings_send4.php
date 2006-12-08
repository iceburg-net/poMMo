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
// TODO -> Move throttler & personalizations to mailing data $input ($pommo->get('mailingData');)
//		else move $config to $_SESSION...
require ('../../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/classes/mailing.php');
Pommo::requireOnce($pommo->_baseDir.'inc/classes/mailer.php');
Pommo::requireOnce($pommo->_baseDir.'inc/classes/throttler.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/mailings.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/subscribers.php');

/**********************************
	STARTUP ROUTINES
 *********************************/
 
// skips serial and security code checking. For debbuing this script.
$skipSecurity = TRUE;

// # of mails to fetch from the queue at a time (Default: 100)
$queueSize = 100;

// set maximum runtime of this script in seconds (Default: 80). If unable to set (SAFE MODE,etc.), max runtime will default to 3 seconds less than current max.
$maxRunTime = 80;
if (ini_get('safe_mode'))
	$maxRunTime = ini_get('max_execution_time') - 10;
else
	set_time_limit($maxRunTime +90);

// start the timer
$start = time();

$serial = (empty ($_GET['serial'])) ? time() : addslashes($_GET['serial']);
$relayID = (empty ($_GET['relayID'])) ? 1 : $_GET['relayID'];
$code = (empty($_GET['securityCode'])) ? null : $_GET['securityCode'];
$test = (empty($_GET['testMailing'])) ? false : true;

if (!$skipSecurity && $relayID < 1 && $relayID > 4)
	PommoMailing::kill('Mailing stopped. Bad RelayID.', TRUE);
	

/**********************************
	INITIALIZATION METHODS
 *********************************/

$pommo->init(array('sessionID' => $serial, 'keep' => TRUE, 'authLevel' => 0, 'noDebug' => TRUE));
$dbo = & $pommo->_dbo;
$logger = & $pommo->_logger;

//DEBUGGING LINE
//$query="UPDATE ".$dbo->table['mailing_current']." SET notices='yyy'"; $dbo->query($query); die();

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
		'throttle_SMTP',
		'throttle_MPS',
		'throttle_BPS',
		'throttle_DP',
		'throttle_DMPP',
		'throttle_DBPP'
	));
	$config =& $input['config'];

	if ($config['list_exchanger'] == 'smtp') {
		
		$config['multimode'] = false;
		
		if (!empty ($config['smtp_1'])) {
			$config['smtp_1'] = unserialize($config['smtp_1']);
			$logger->addMsg('SMTP Relay #1 detected', 1);
		}
		
		for($i = 2; $i < 5; $i++) {
			if (!empty($config['smtp_'.$i])) {
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

	$pommo->set(array('mailingData' => array('config' => $config)));
} 
else {
	$logger->addMsg(Pommo::_T('Background mailer spawned.'), 2);
}

$config =& $input['config'];

/**********************************
 * MAILING INITIALIZATION
 *********************************/

//DEBUGGING LINE
//$query="UPDATE ".$dbo->table['mailing_current']." SET notices='VVVerbosity - ".$logger->_verbosity."'"; $dbo->query($query); 

$mailing = current(PommoMailing::get(array('code' => $code, 'active' => TRUE)));
if (empty($mailing))
	PommoMailing::kill('Could not initialize a current mailing.');
$mailingID = $mailing['id'];

// SECURITY ROUTINES...
if(!empty($mailing['end']) && $mailing['end'] > 0)
	PommoMailing::kill('Mailing has completed.', TRUE);
	

if(empty($mailing['serial']))
	if (!PommoMailing::mark($serial,$mailing['id']))
		PommoMailing::kill('Unable to serialize Mailing', TRUE);

if (!$skipSecurity && $code != $mailing['code'])
	PommoMailing::kill('Mailing stopped for security reasons.', TRUE);


// Poll Mailing Status
PommoMailing::poll();


// If we're in multimode, spawn scripts (unless this is a spawn!)
if ($config['multimode'] && !empty($_GET['spawn']) && !$test) {
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
 * INITIALIZE Queue : POTENTIAL HALT
 *********************************/
$subscribers = PommoSubscriber::get(
	array('id' => PommoMailing::queueGet($relayID, $queueSize)));
	
while(empty($subscribers)) {
	if(PommoMailing::queueUnsentCount() < 1) {
		PommoMailing::finish($mailingID);
		die();	
	}
			
	sleep(10);
	
	if((time() - $start) > $maxRunTime) {
		PommoMailing::respawn();
		PommoMailing::kill('Max runtime reached. Respawning...');
	}
	
	$subscribers = PommoSubscriber::get(
		array('id' => PommoMailing::queueGet($relayID, $queueSize)));
		
}
	
// seperate emails into an array ([email],[domain]) to feed to throttler
$emails = array();
$emailHash = array(); // used to quickly lookup subscriberID based off email
foreach($subscribers as $s) {
	array_push($emails, array(
		$s['email'],
		substr($email,strpos($email,'@')+1)
		)
	);
	$emailHash[$s['email']] = $s['id'];
}

/**********************************
 * INITIALIZE Throttler
 *********************************/
	
$tid = ($config['throttle_SMTP'] == 'shared') ? 1 : $relayID;

if(empty($_SESSION['pommo']['throttler'][$tid]))
	$_SESSION['pommo']['throttler'] = array (
		$tid => array(
			'MPS' => $config['throttle_MPS'],
			'BPS' => $config['throttle_MPS'],
			'DP' => $config['throttle_DP'],
			'DMPP' => $config['throttle_DMPP'],
			'DBPP' => $config['throttle_DBPP'],
			'domainHistory' => array(),
			'genesis' => time(),
			'runtime' => $maxRunTime,
			'sent' => 0.0,
			'sentBytes' => 0.0
			)
		);
		
$throttler =& new PommoThrottler(
	$_SESSION['pommo']['throttler'][$tid], 
	$emails, 
	$_SESSION['pommo']['throttler'][$tid]['domainHistory'], 
	$_SESSION['pommo']['throttler'][$tid]['sent'],
	$_SESSION['pommo']['throttler'][$tid]['sentBytes']
	);
	

$byteMask = $throttler->byteTracking();
if ($byteMask > 1) // byte tracking/throttling enabled
	$mailer->trackMessageSize();

/**********************************
   PROCESS QUEUE
 *********************************/


$sent = array();
$failed = array();
$die = false;
$timer = time();
while(!$die) {
	
	// attempt to pull email from throttler's queue
	$mail = $throttler->pullQueue();
	
	// if an email was returned, send it.
	if (!empty($mail)) {
		
		// set $personal as subscriber if personalization is enabled 
		$personal = FALSE;
		if ($_SESSION['pommo']['personalization'])
			$personal =& $subscribers[$emailHash[$mail[0]]];
		
		if (!$mailer->bmSendmail($mail[0], $personal)) // sending failed, write to log  
			$failed[] = $mail[0];
		else
			$sent[] = $mail[0];
		

		// If throttling by bytes (bandwith) is enabled, add the size of the message to the throttler
		if ($byteMask > 1) {
			$bytes = $mailer->GetMessageSize();
			if ($byteMask > 2)
				$throttler->updateBytes($bytes, $mail[1]);
			else
				$throttler->updateBytes($bytes);
			$logger->addMsg('Added ' . $bytes . ' bytes to throttler.', 1);
		}
	}
	elseif ($throttler->getCommand() == 2) // kill command received
		$die = TRUE;
	
	// check if there's any mails in the
	if (!$throttler->mailsInQueue())
		$die = TRUE;
		
	// update & poll every 7 seconds || if logger is large
	if (((time() - $timer) > 7)) {
		PommoMailing::update($sent, $failed, $emailHash);
		PommoMailing::poll();
		
		$timer = time();
		$sent = array();
		$failed = array();
	}
}

// don't respawn if this is a test mailing
if ($test) {
	PommoMailing::finish($mailingID,TRUE,TRUE);
	PommoSubscriber::delete(4294967295);
	die();
}

PommoMailing::update($sent, $failed, $emailHash);
PommoMailing::respawn();
PommoMailing::kill('Queue empty or Runtime reached. Respawning...');
?>
