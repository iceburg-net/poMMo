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
require('../../../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/subscribers.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/fields.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/groups.php');

$pommo->init(array('noDebug' => TRUE));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

$pommo->toggleEscaping(TRUE);

$state =& PommoAPI::stateInit('subscribers_manage');
$fields = PommoField::get();
	

$ids = FALSE;
if(!empty($_POST['ids']))
	$ids = explode(',',$_POST['ids']);


// ====== CSV EXPORT ======
if($_POST['type'] == 'csv') {
	if (!$ids) {
		$group = new PommoGroup($state['group'], $state['status']);
		$subscribers = $group->members();
	}
	else 
		$subscribers = PommoSubscriber::get(array('id' => $ids));
	
	// supply headers
	$o = '"'.Pommo::_T('Email').'"';
	if(!empty($_POST['registered']))
		$o .= ',"'.Pommo::_T('Date Registered').'"';
	if(!empty($_POST['ip']))
		$o .= ',"'.Pommo::_T('IP Address').'"';
	foreach($fields as $f)
		$o.=",\"{$f['name']}\"";
	$o .= "\r\n";
	
	function csvWrap(&$in) {
		$in = '"'.addslashes($in).'"';
		return;
	}
	foreach($subscribers as $sub) {
		$d = array();
		
		// normalize field order in export
		foreach(array_keys($fields) as $id)
			if(array_key_exists($id,$sub['data']))
				$d[$id] = $sub['data'][$id];
			else
				$d[$id] = null;
		
		$s = array($sub['email']);
		if(!empty($_POST['registered']))
			$s[] = $sub['registered'];
		if(!empty($_POST['ip']))
			$s[] = $sub['ip'];
		
		array_walk($d, 'csvWrap');
		array_walk($s, 'csvWrap');
		
		$a = array_merge($s,$d);
		$o .= implode(',',$a)."\r\n";
	}
	
	$size_in_bytes = strlen($o);
	header("Content-disposition:  attachment; filename=poMMo_".Pommo::_T('Subscribers').".csv; size=$size_in_bytes");
	print $o;
	
	die();
}

// ====== TXT EXPORT ======

if (!$ids) {
	$group = new PommoGroup($state['group'], $state['status']);
	$ids =& $group->_memberIDs; 	
}

$emails = PommoSubscriber::getEmail(array('id' => $ids));

$o = '';
foreach($emails as $e)
	$o .= "$e\r\n";
	
$size_in_bytes = strlen($o);
header("Content-disposition:  attachment; filename=poMMo_".Pommo::_T('Subscribers').".txt; size=$size_in_bytes");
print $o;
die();

