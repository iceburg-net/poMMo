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
require ('../../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/import.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/subscribers.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/validate.php');

$pommo->init(array('keep' => TRUE));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

$dupes = $tally = $flagged = 0;
$fp = fopen($pommo->_workDir.'/import.csv','r') 
	or die('Unable to open CSV file');

while (($row = fgetcsv($fp,2048,',','"')) !== FALSE) {
	$subscriber = array(
		'email' => false,
		'registered' => time(),
		'ip' => $_SERVER['REMOTE_ADDR'],
		'status' => 1,
		'data' => array());
	foreach ($row as $key => $col) {
		$fid =& $_POST['f'][$key];
		if (is_numeric($fid))
			$subscriber['data'][$fid] = $col;
		elseif ($fid == 'email' && PommoHelper::isEmail($col))
			$subscriber['email'] = $col;
		elseif ($fid == 'registered')
			$subscriber['registered'] = strtotime($col);
		elseif ($fid == 'ip')
			$subscriber['ip'] = $col;
	}
	if ($subscriber['email']) {
		// check for dupe
		// TODO -- DO THIS IN BATCH ??
		if (PommoHelper::isDupe($subscriber['email'])) {
			$dupes++;
			continue;
		}

		// validate/fix data
		if(!PommoValidate::subscriberData($subscriber['data'], array(
			'log' => false,
			'ignore' => true,
			'active' => false)))
			$subscriber['flag'] = 9;

		// add subscriber
		if (PommoSubscriber::add($subscriber)) {
			$tally++;
			if (isset($subscriber['flag']))
				$flagged++;
		}
	}

}
unlink($pommo->_workDir.'/import.csv');
echo ('<div class="warn"><p>'.sprintf(Pommo::_T('%s subscribers imported! Of these, %s were flagged to update their records.'),$tally, $flagged).'<p>'.sprintf(Pommo::_T('%s duplicates encountered.'),$dupes).'</p></div>');
die(Pommo::_T('Complete!').' <a href="subscribers_import.php">'.Pommo::_T('Return to').' '.Pommo::_T('Import').'</a>');
?>