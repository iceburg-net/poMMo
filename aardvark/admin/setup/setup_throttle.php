<?php

/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * SOURCE: http://bmail.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/

define('_IS_VALID', TRUE);

require('../../bootstrap.php');
require_once (bm_baseDir.'/inc/db_procedures.php');
$bMail = & fireup("secure");
$dbo = & $bMail->openDB();

// Read user requested changes	
if (!empty($_POST['throttle-restore'])) {
	$input = array ('throttle_MPS' => 3, 'throttle_BPS' => 0, 'throttle_DP' => 10, 'throttle_DBPP' => 0,'throttle_DMPP' => 0);
	dbUpdateConfig($dbo,$input,TRUE);
}
elseif(!empty($_POST['throttle-submit'])) {
	$input = array ('throttle_MPS' => str2db($_POST['mps']), 'throttle_BPS' => str2db($_POST['kbps']), 'throttle_DP' => str2db($_POST['dp']), 'throttle_DBPP' => str2db($_POST['dbpp']),'throttle_DMPP' => str2db($_POST['dmpp']));
	dbUpdateConfig($dbo,$input,TRUE);
}


/** bMail templating system **/

// header settings -->
$_head = "\n<link href=\"".bm_baseUrl."/inc/css/bform.css\" rel=\"stylesheet\" type =\"text/css\">\n<script src=\"".bm_baseUrl."/inc/js/bform.js\" type=\"text/javascript\"></script>";

$_nologo = FALSE;
$_menu = array ();
$_menu[] = "<a href=\"".bm_baseUrl."/index.php?logout=TRUE\">Logout</a>";
$_menu[] = "<a href=\"admin_setup.php\">Setup Page</a>";
$_menu[] = "<a href=\"".$bMail->_config['site_url']."\">".$bMail->_config['site_name']."</a>";

// right bar settings -->
$_nomenu = FALSE; // turn off main "admin menu" in right bar
$_nodemo = FALSE; // turn off display of bMail demonstration mode status

$_extmenu = array ();
$_extmenu['name'] = "bMail Setup";
$_extmenu['links'] = array ();
$_extmenu['links'][] = "<a href=\"setup_configure.php\">Configure</a>";
$_extmenu['links'][] = "<a href=\"setup_demographics.php\">Demographics</a>";
$_extmenu['links'][] = "<a href=\"setup_form.php\">Setup Form</a>";

include (bm_baseDir.'/setup/top.php');

/** End templating system **/

echo "
<h1>Settings</h1>

<img src=\"".bm_baseUrl."/img/icons/settings.png\" class=\"articleimg\">

<p>
bMail can be configured to throttle the sending of mails so you don't overload your server or slam a common domain (such as hotmail/yahoo.com). Mail volume and bandwith can be limited. Additionally, you can control how many mails and kilobytes may be sent to a single domain per a specified time frame.
</p>
";

echo '<a href="setup_configure.php"><img src="'.bm_baseUrl.'/img/icons/back.png" align="middle" class="navimage" border=\'0\'>Return to configuration page</a>';

echo '<h2>Throttling &raquo;</h2>';

$config= $bMail->getConfig(array('throttle_MPS', 'throttle_BPS', 'throttle_DP', 'throttle_DBPP','throttle_DMPP'));

require(bm_baseDir.'/inc/printouts.php');
printThrottleForm($config);


include (bm_baseDir.'/setup/footer.php');
?>