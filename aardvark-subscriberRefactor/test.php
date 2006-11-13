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

if( !function_exists('memory_get_usage') )
{
   function memory_get_usage()
   {
    
           //We now assume the OS is UNIX
           //Tested on Mac OS X 10.4.6 and Linux Red Hat Enterprise 4
           //This should work on most UNIX systems
           $pid = getmypid();
           exec("ps -eo%mem,rss,pid | grep $pid", $output);
           $output = explode("  ", $output[0]);
           //rss is given in 1024 byte units
           return $output[1] * 1024;
   }
}

/**********************************
	INITIALIZATION METHODS
 *********************************/
require ('bootstrap.php');
$pommo->init(array('authLevel' => 0));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/subscribers.php');

$time_start = microtime(true);

$email = array('lfoh4@ou9bzq2.com','0br0swn@cz27f2djy2s3bd.net','ni5@qioe0h8w4p.gov');

$x = PommoSubscriber::isEmail($email);
var_dump($x); die();

/*
$x = PommoSubscriber::getEmail(array('status' => 'inactive'));
*/

$time_end = microtime(true);
$time = $time_end - $time_start;
echo 'size of X: '.count($x).'<br>';
echo "Completed in $time seconds<br>";
echo "Memory Usage -> ".memory_get_usage();

var_dump(current($x));


?>
