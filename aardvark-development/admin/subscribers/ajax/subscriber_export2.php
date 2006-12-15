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
require('../../../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/subscribers.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/fields.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/groups.php');

$pommo->init();
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

$pommo->toggleEscaping(TRUE);

$state =& PommoAPI::stateInit('subscribers_manage');
$fields = array_keys(PommoField::get());
	

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
		
	$o = '';
	
	function csvWrap(&$in) {
		$in = '"'.addslashes($in).'"';
		return;
	}
	foreach($subscribers as $sub) {
		$d = array();
		
		// normalize field order in export
		foreach($fields as $id)
			if(array_key_exists($id,$sub['data']))
				$d[$id] = $sub['data'][$id];
			else
				$d[$id] = null;
		
		$s = array($sub['email']); // can add IP time_registered, etc. to this....
		
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

