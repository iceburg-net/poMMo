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
require ('bootstrap.php');
$pommo->init(array('authLevel' => 0));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

Pommo::requireOnce($pommo->_baseDir.'inc/helpers/subscribers.php');

$x = PommoSubscriber::getAlt();

//var_dump($x);

?>
