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

require('../../bootstrap.php');
require_once (bm_baseDir.'/inc/lib.import.php');
require_once (bm_baseDir.'/inc/db_demographics.php');
$poMMo = & fireup('secure', 'dataSave');
$dbo = & $poMMo->openDB();

/** poMMo templating system **/
// overide 'content' ID to allow for a WIDE page (auto adjusting width...)
$_head = '
	<style type="text/css" media="all">
		#content {
			width: auto;
			margin: 10 10 10 10;
			text-align:left;
		}</style>';
$_nosidebar = TRUE;
$_nologo = FALSE;
$_menu = array ();
$_menu[] = '<a href="'.bm_baseUrl.'/index.php?logout=TRUE">Logout</a>';
$_menu[] = '<a href="admin_subscribers.php">Subscribers Page</a>';
$_menu[] = '<a href="'.$poMMo->_config['site_url'].'">'.$poMMo->_config['site_name'].'</a>';
include (bm_baseDir.'/setup/top.php');
/** End templating system **/

echo '<div style="width:90%" align="right">';
echo "<a href=\"subscribers_import.php\">Upload a different file</a> &nbsp;&nbsp;&nbsp; &nbsp;";
echo "<a href=\"admin_subscribers.php\">Back to Subscribers Page</a>";
echo "</div>\n<hr>";

echo '<div align="center">';

// load data from session
$sessionArray = & $poMMo->dataGet();
$csvArray = & $sessionArray['csvArray'];

$demographics = dbGetDemographics($dbo);

if (!empty($_GET['import'])) { // check to see if we should import

	$importArray =& $sessionArray['importArray'];
	
	require_once (bm_baseDir.'/inc/db_subscribers.php');
	
	foreach($importArray['valid'] as $subscriber)
		dbSubscriberAdd($dbo,$subscriber);
		

	$flagArray = array();
	foreach($importArray['invalid'] as $subscriber) {
		dbSubscriberAdd($dbo,$subscriber);
		$flagArray[] = $subscriber['email'];
	}

	if (!empty($flagArray)) { // flag subscribers needing to update their reocrds
		$flagSubscribers =& dbGetSubscriber($dbo,$flagArray,'id');
		foreach ($flagSubscribers as $subscriber_id) {
			if (isset($valStr))
			$valStr .= ',('.$subscriber_id.',\'update\')';
			else
			$valStr = '('.$subscriber_id.',\'update\')';
		}
		$sql = 'INSERT INTO '.$dbo->table['subscribers_flagged'].' (subscribers_id,flagged_type) VALUES '.$valStr;
		$dbo->query($sql);
	}
	
	echo '<div style="width: 60%">';
echo '<p><h2>Import Complete!</h2></p>';

echo '<br><br><a href="admin_subscribers.php"><img src="'.bm_baseUrl.'/img/icons/back.png" class="navimage">Return</a> to subscribers page.';
}
elseif (!empty($_POST['preview'])) { // check to see if a preview has been requested
	
	require_once (bm_baseDir.'/inc/printouts.php');

	// prepare csvArray for import
	$importArray = csvPrepareImport($poMMo, $dbo, $demographics,$csvArray,$_POST['field']);
	
	// get count of subscribers to be imported
	$totalImported = count($importArray['valid'])+count($importArray['invalid']);
	
	if (!empty($importArray['invalid']))
		$invalidExists = TRUE;
	if (!empty($importArray['duplicate']))
		$duplicateExists = TRUE;

	// save Array to session
	$sessionArray['importArray'] = & $importArray;
	$poMMo->dataSet($sessionArray);
		
echo '<div style="width: 60%">';
echo '<p><h2>Import Preview</h2></p>';
echo '<b>'.$totalImported.'</b> Subscribers will be imported. Of these, <b>'.count($importArray['invalid']).'</b> will be flagged for update due to invalidity. You can email flagged subscribers from the mailings page.';
echo '<div>';
printConfirm($_SERVER['PHP_SELF'].'?import=TRUE', $_SERVER['PHP_SELF'], NULL, $okStr = 'Import Subscribers.', $backStr = 'Try Again.' );
echo '</div>';
}
else {
echo '<div style="width: 60%">';
echo '<p><h2>Upload Success!</h2></p>';
echo 'Optionally, you may match the below fields to a demographic. 
	If an imported subscriber is missing input for a required field, they will be <em>flagged</em>
	to update their information. When you are finished matching, click proceed to preview the
	subscribers that will be imported.';
echo '</div>';


function demoSelect($str = NULL, & $demographics) {
$optionStr = '<option value="ignore">Ignore Field</option><option value="ignore">----------------</option>';
foreach (array_keys($demographics) as $demographic_id)
	if ($str == $demographic_id)
		$optionStr .= '<option value="'.$demographic_id.'" SELECTED>'.$demographics[$demographic_id]['name'].'</option>';
	else
		$optionStr .= '<option value="'.$demographic_id.'">'.$demographics[$demographic_id]['name'].'</option>';
return $optionStr;
}

$numFields = count($csvArray[$csvArray['assignLine']]);
$numLines = count($csvArray) - 1;

// the entry to assign
$entry = & $csvArray[$csvArray['assignLine']];

// returns a alternating HTML color code, alterating every 2 times
function altColor($reset = FALSE) {
	static $alt = FALSE;
	if ($reset == 'reset') {
		$alt = FALSE;
		return;
	}
	if ($alt) {
		$alt = FALSE;
		return '#b7cfec';
	}
	$alt = TRUE;
	return '#87addc';
}

echo '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';

echo '<span style="font-size:14px; font-weight: bold; align: center;">';
echo '<br><table cellspacing="0" cellpadding="7"><tr><td></td>';
for ($i = 1; $i <= $numFields; $i ++)
	echo '<td bgcolor="'.altColor().'">Field #'.$i.'</td>';

altColor('reset');
echo '</tr><tr><td>line #</td>';
for ($i = 0; $i < $numFields; $i ++)
	if (isEmail($entry[$i])) {
		echo '<td bgcolor="'.altColor().'"><i>email</i><input type="hidden" name="field['.$i.']" value="email"></td>';
	}
	else
		echo '<td bgcolor="'.altColor().'"><SELECT name=field['.$i.']>'.@demoSelect($_POST['field'][$i],$demographics).'</SELECT></td>';
echo '</tr><tr>';

if ($csvArray['assignLine'] != 0) {
	altColor('reset');
	echo '<td style="border-right: thin dotted #000000;">1</td>';
	for ($i = 0; $i < $numFields; $i ++)
		echo '<td style="border-right: thin dotted #000000;">'.@($csvArray[0][$i]).'</td>';
	echo '</tr><tr>';
}

altColor('reset');
echo '<td style="border-right: thin dotted #000000;">'. ($csvArray['assignLine'] + 1).'</td>';
for ($i = 0; $i < $numFields; $i ++)
	echo '<td style="border-right: thin dotted #000000;">'.$entry[$i].'</td>';
echo '</tr><tr style="height: 2px;"><td style="border-right: thin dotted #000000; height: 2px;"></td><td colspan="'.$numFields.'" bgcolor="#000000" style="height: 2px;"></td></tr></table>';
echo '</span>';

echo '<br><b>'.$numLines.'</b> subscribers to import.<br>
	<img src="'.bm_baseUrl.'/img/icons/download.png"><br>
	<input type="submit" name="preview" value="Click to Preview Imports">';

}

if (!empty($invalidExists)) {	
echo '<div style="width: 60%">';
echo '<p><h2>Invalid Subscribers:</h2></p>';
$poMMo->printMessages('</li>','<li>');
echo '</div>';
}

if (!empty($duplicateExists)) {	
echo '<div style="width: 60%">';
echo '<p><h2>Duplicate Subscribers:</h2></p>';
foreach ($importArray['duplicate'] as $line)
	echo '<li>'.$line.'</li>';
echo '</div>';

}

echo '</div>';
include (bm_baseDir.'/setup/footer.php');
?>