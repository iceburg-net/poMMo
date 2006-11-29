<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2006 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/

// Start Output buffering
ob_start();

// Include core components
require(dirname(__FILE__) . '/inc/helpers/common.php'); // base helper functions
require(dirname(__FILE__) . '/inc/classes/api.php'); // base API
require(dirname(__FILE__) . '/inc/classes/pommo.php'); // base object

// Setup the core global. All utility is tucked away within this global to reduce namespace
// pollution and possible collissions when poMMo is embedded in another application.
$GLOBALS['pommo'] = new Pommo(dirname(__FILE__) . '/');

/*
 * Disable session.use_trans_sid to mitigate performance-penalty
 * (do it before any output is started) [from gallery2]
 */
if (!defined('SID')) {
    @ini_set('session.use_trans_sid', 0);
}

// turn off magic quotes -- NOTE; this may break embedded scripts?
// clean user input of slashes added by magic quotes. TODO; optimize this.
if (get_magic_quotes_gpc()) {
	if (!empty ($_POST))
		$_POST = PommoHelper :: slashStrip($_POST);
	if (!empty ($_GET))
		$_GET = PommoHelper :: slashStrip($_GET);
}

// Assign alias to the core global which can be used by the script calling bootstrap.php
$pommo =& $GLOBALS['pommo'];
?>