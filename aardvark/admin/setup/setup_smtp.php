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
require_once (bm_baseDir.'/inc/db_procedures.php');
require_once (bm_baseDir.'/inc/class.bform.php');
$poMMo = & fireup("secure");
$dbo = & $poMMo->openDB();

// Read user requested changes	
if (!empty($_POST['addSmtpServer'])) {
	$server = array('host' => 'mail.localhost','port' => '25', 'auth' => 'off', 'user' => '', 'pass' => '');
	$input['smtp_'.key($_POST['addSmtpServer'])] = serialize($server);
	dbUpdateConfig($dbo,$input,TRUE);
}
elseif(!empty($_POST['updateSmtpServer'])) {
	$key = key($_POST['updateSmtpServer']);
	$server = array('host' => str2db($_POST['host'][$key]), 'port' => str2db($_POST['port'][$key]), 'auth' => str2db($_POST['auth'][$key]), 'user' => str2db($_POST['user'][$key]), 'pass' => str2db($_POST['pass'][$key]));
	$input['smtp_'.$key] = serialize($server);
	dbUpdateConfig($dbo,$input,TRUE);
}
elseif(!empty($_POST['deleteSmtpServer'])) {
	$input['smtp_'.key($_POST['deleteSmtpServer'])] = '';
	dbUpdateConfig($dbo,$input,TRUE);
}
elseif(!empty($_POST['throttle_smtp'])) {
	$input['throttle_SMTP'] = str2db($_POST['throttle_smtp']);
	dbUpdateConfig($dbo,$input);
}

/** poMMo templating system **/

// header settings -->
$_head = "\n<link href=\"".bm_baseUrl."/inc/css/bform.css\" rel=\"stylesheet\" type =\"text/css\">\n<script src=\"".bm_baseUrl."/inc/js/bform.js\" type=\"text/javascript\"></script>";

$_nologo = FALSE;
$_menu = array ();
$_menu[] = "<a href=\"".bm_baseUrl."/index.php?logout=TRUE\">Logout</a>";
$_menu[] = "<a href=\"admin_setup.php\">Setup Page</a>";
$_menu[] = "<a href=\"".$poMMo->_config['site_url']."\">".$poMMo->_config['site_name']."</a>";

// right bar settings -->
$_nomenu = FALSE; // turn off main "admin menu" in right bar
$_nodemo = FALSE; // turn off display of poMMo demonstration mode status

$_extmenu = array ();
$_extmenu['name'] = "poMMo Setup";
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
poMMo can be configured to use up to 4 SMTP servers to relay mail from. Each server can be individually regulated by its own throttle controller, or can share a global one. For maximum output, use individual throttle controllers. Throttling set 
from the main configuration page will be inherrited.
</p>
";

echo '<a href="setup_configure.php"><img src="'.bm_baseUrl.'/img/icons/back.png" align="middle" class="navimage" border=\'0\'>Return to configuration page</a>';

echo '<h2>SMTP Relays &raquo;</h2>';

// used to TEST SMTP connectivity
require_once (bm_baseDir.'/inc/phpmailer/class.phpmailer.php');
require_once (bm_baseDir.'/inc/phpmailer/class.smtp.php');

$smtpConfig = $poMMo->getConfig(array ('smtp_1', 'smtp_2', 'smtp_3', 'smtp_4','throttle_SMTP'));

$smtp[1] = unserialize($smtpConfig['smtp_1']);
$smtp[2] = unserialize($smtpConfig['smtp_2']);
$smtp[3] = unserialize($smtpConfig['smtp_3']);
$smtp[4] = unserialize($smtpConfig['smtp_4']);

if (empty($smtp[1]))
	$smtp[1] = array('host' => 'mail.localhost','port' => '25', 'auth' => 'off', 'user' => '', 'pass' => '');



$form = new bForm();
$form->inputLoad($poMMo->dataGet());
$form->startForm();
// print any errors that occured
$form->printErrors();

$form->newFieldSet("SMTP Throttling");

$form->newField("throttle_smtp");
$form->setField("prompt", "Throttle Controller:");
$form->setField("type", "select");
if (!empty ($smtpConfig['throttle_SMTP']))
	$form->setField("default", $smtpConfig['throttle_SMTP']);
else
	$form->setField("default", "individual");
$option = array ();
$option[] = "individual:Individual Throttler per Server";
$option[] = "shared:Share A Global Throttler";

$form->setField("option", $option); // always prefix with "option" to trigger option event.
$form->setField("notes", "(Throttle Control can be shared or individual)");
$form->setField("misc", " onChange=\"document.".$form->_name.".submit()\"");
$form->printField();

$form->endFieldSet();
	

$addServer = FALSE;
for ($i=1; $i < 5; $i++) {

if (empty($smtp[$i])) {
	if (!$addServer)
		$addServer = $i;
	continue;	
}

$form->newFieldSet("SMTP #".$i."");
	
	$test[$i]= new PHPMailer();
	
	if (!empty($smtp[$i]['host']))
		$test[$i]->Host = $smtp[$i]['host']; 
	if (!empty($smtp[$i]['port']))
		$test[$i]->Port = $smtp[$i]['port']; 
	if (!empty($smtp[$i]['auth']) && $smtp[$i]['auth'] == 'on') {
		$test[$i]->SMTPAuth = TRUE; 
		if (!empty($smtp[$i]['user']))
			$test[$i]->Username = $smtp[$i]['user']; 
		if (!empty($smtp[$i]['pass']))
			$test[$i]->Password = $smtp[$i]['pass']; 
	}
	echo '<div class="field">SMTP Status: <label></label>';
	if (@$test[$i]->SmtpConnect()) {
		echo '<img src="'.bm_baseUrl.'/img/icons/ok.png" align="middle"> Connected to SMTP Server.';
		$test[$i]->SmtpClose();	
	}
	else
		echo '<img src="'.bm_baseUrl.'/img/icons/nok.png" align="middle"> Could not connect to SMTP Server.';
	echo '</div>';
	

	$form->newField('host['.$i.']');
	$form->setField("prompt", "SMTP Host: ");
	$form->setField("type", "text");
	$form->setField("default", "type address of SMTP server");
	if (!empty ($smtp[$i]['host']))
		$form->setField("init", $smtp[$i]['host']);
	$form->setField("notes", "(IP Address or Name of your SMTP server)");
	$form->setField("misc", "maxlength=\"60\" size=\"32\"");
	$form->printField();

	$form->newField('port['.$i.']');
	$form->setField("prompt", "SMTP Port: ");
	$form->setField("type", "text");
	$form->setField("default", "Port # of SMTP server");
	if (!empty ($smtp[$i]['host']))
		$form->setField("init", $smtp[$i]['port']);
	else
		$form->setField("init", '25');
	$form->setField("notes", "(Port # of SMTP server [usually 25])");
	$form->setField("misc", "maxlength=\"60\" size=\"32\"");
	$form->printField();

	$form->newField('auth['.$i.']');
	$form->setField("prompt", "SMTP Authentication: ");
	$form->setField("type", "checkbox");
	$form->setField("notes", "Check this box to enable SMTP Authentication [usually off].");
	if (!empty ($smtp[$i]['auth']) && ($smtp[$i]['auth'] == "on"))
		$form->setField("default", "checked");
	else
		$form->setField("default", "unchecked");
	$form->printField();

	$form->newField('user['.$i.']');
	$form->setField("prompt", "SMTP Username: ");
	$form->setField("type", "text");
	$form->setField("default", "type SMTP username");
	if (!empty ($smtp[$i]['user']))
		$form->setField("init", $smtp[$i]['user']);
	$form->setField("notes", "(Username for your SMTP server)");
	$form->setField("misc", "maxlength=\"60\" size=\"32\"");
	$form->printField();

	$form->newField('pass['.$i.']');
	$form->setField("prompt", "SMTP Password: ");
	$form->setField("type", "text");
	$form->setField("default", "type SMTP password");
	if (!empty ($smtp[$i]['pass']))
		$form->setField("init", $smtp[$i]['pass']);
	$form->setField("notes", "(Password for your SMTP server)");
	$form->setField("misc", "maxlength=\"60\" size=\"32\"");
	$form->printField();
	
	echo '<div class="field"><input type="submit" name="updateSmtpServer['.$i.']" id="updateSmtpServer['.$i.']" value="Update Relay #'.$i.'">';
	if ($i > 1)
		echo '&nbsp;&nbsp; -- &nbsp;&nbsp;<input type="submit" name="deleteSmtpServer['.$i.']" id="deleteSmtpServer['.$i.']" value="Remove Relay #'.$i.'">';
	else 
		echo '&nbsp;&nbsp; -- &nbsp;&nbsp;Relay #1 is your default relay';
	echo '</div>';

$form->endFieldSet();

}

// print out option to Add another server
if ($addServer)
	echo '<br><br><div class="field"><input type="submit" name="addSmtpServer['.$addServer.']" id="addSmtpServer['.$addServer.']" value="Add Another Relay"></div>';

echo '</form>';

include (bm_baseDir.'/setup/footer.php');
?>