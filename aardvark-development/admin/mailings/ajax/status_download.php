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

$pommo->init();
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

$map = array(
	'sent' => 1,
	'unsent' => 0,
	'error' => 2);
	
$nameMap = array (
	0 => 'Unsent_Subscribers',
	1 => 'Sent_Subscribers',
	2 => 'Failed_Subscribers'
);

$i = (isset($map[$_GET['type']])) ? $map[$_GET['type']] : false;
if ($i === false)
	die();
	
$query = "
	SELECT s.email 
	FROM ".$dbo->table['subscribers']." s
	JOIN ".$dbo->table['queue']." q ON (s.subscriber_id = q.subscriber_id)
	WHERE q.status = %i";
$query = $dbo->prepare($query,array($i));
$emails = $dbo->getAll($query,'assoc','email');

$o = '';
foreach($emails as $e)
	$o .= "$e\r\n";
	
$size_in_bytes = strlen($o);
header("Content-disposition:  attachment; filename=".$nameMap[$i].".txt; size=$size_in_bytes");
print $o;
exit;  