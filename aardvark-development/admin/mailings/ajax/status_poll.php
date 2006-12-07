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
Pommo::requireOnce($pommo->_baseDir.'inc/lib/class.json.php');
Pommo::requireOnce($pommo->_baseDir.'inc/classes/mailing.php');

$pommo->init(array('noDebug' => TRUE, 'keep' => TRUE));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

$pommo->toggleEscaping(); // _T and logger responses will be wrapped in htmlspecialchars

$json = array(
	'percent' => null,
	'status' => null,
	'statusText' => null,
	'sent' => null,
	'incAttempt' => FALSE,
	'command' => FALSE
);

$statusText = array(
	1 => Pommo::_T('Processing'),
	2 => Pommo::_T('Stopped'),
	3 => Pommo::_T('Frozen'),
	4 => Pommo::_T('Finished')
);

$mailing = current(PommoMailing::get(array('active' => TRUE)));

// status >> 1: Processing  2: Stopped  3: Frozen  4: Finished
if ($mailing['status'] != 1)
	$json['status'] = 4;
elseif($mailing['current_status'] == 'stopped')
	$json['status'] = 2;
else
	$json['status'] = 1;


// check for frozen mailing
$timestamp = $pommo->get('timestamp');
if (empty($timestamp))
	$timestamp = $mailing['touched']; // get retuns a blank array -- not false

if ($json['status'] != 4) {
	if ($mailing['command'] != 'none' || $mailing['touched'] == $timestamp)
		$json['incAttempt'] = TRUE;
	if ($mailing['command'] != 'none')
		$json['command'] = TRUE;
	if ($_GET['attempt'] > 3)
		$json['status'] = 3;
}

$pommo->set(array('timestamp' => $mailing['touched']));


$json['statusText'] = $statusText[$json['status']];

// trim last 50 notices
if(!empty($mailing['notices'])) {
	$json['notices'] = explode('||',$mailing['notices'], 50);
	$newNotices = str_replace(implode('||',$json['notices']),'',$mailing['notices']);
	$query = "
		UPDATE ".$dbo->table['mailing_current']."
		SET notices='%s'
		WHERE current_id=%i";
	$query = $dbo->prepare($query, array($newNotices, $mailing['id']));
	$dbo->query($query);
}

$query = "
	SELECT count(subscriber_id) 
	FROM ".$dbo->table['queue']."
	WHERE status > 0";
$json['sent'] = ($json['status'] == 4) ? 
	$mailing['sent'] :
	$dbo->query($query,0);

$json['percent'] = ($json['status'] == 4) ?
	100 :
	round($json['sent'] * (100 / $mailing['tally']));

$encoder = new json;
die($encoder->encode($json));
?>