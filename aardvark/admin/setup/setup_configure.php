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

// TODO, make demo_mode a radio on/off . Validate further advanced settings.. ie. mailNum can't exceed MailMax, etc. They should also be divisible...'

define('_IS_VALID', TRUE);

require('../../bootstrap.php');
require_once (bm_baseDir.'/inc/db_procedures.php');
require_once (bm_baseDir.'/inc/class.bform.php');
$bMail = & fireup("secure");
$dbo = & $bMail->openDB();
$form = new bForm();

$updated = FALSE;
// ## FORM: CHECK FORM INPUT
if ($form->validateForm()) {
	// ## FORM: VALID INPUT. UPDATE CONFIG

	// convert password to MD5 if given...
	if (!empty ($form->_input['admin_password']))
		$form->_input['admin_password'] = md5($form->_input['admin_password']);

	/*/ convert demo checkbox if off 
	if (empty($form->_input['demo_mode']))
		$form->_input['demo_mode'] = 'off'; */

	dbUpdateConfig($dbo, $form->_input);

	$bMail->loadConfig();
	$updated = TRUE;
	$form->inputClear();
}

// ## FORM: FORM WAS NOT SUBMITTED, OR HAD INVALID INPUT.

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
Use this page to configure bMail. You can change the login information, set website and mailing list parameters, end enable demonstration mode. If you enable demonstration mode, no emails will be sent from the system.
</p>
";

if ($updated)
	echo "<p><b>Settings Updated!</b></p>";

// setup a shortcut for an often typed string. class="required" references styling in bform.css
$reqStr = '<span class="required" title="This field is required.">*</span>';

$config = $bMail->getConfig(array('admin_username','site_success','site_confirm','list_fromname','list_fromemail','list_frombounce','list_exchanger','list_confirm','mailMax','mailNum','mailSize','mailDelay'));

$form->inputLoad($bMail->dataGet());
$form->startForm();

// print any errors that occured
$form->printErrors();

$form->newFieldSet("Administrative");

$form->newField("admin_username");
$form->setField("prompt", "Administrator Username: ".$reqStr);
$form->setField("type", "text");
$form->setField("default", "enter username");
if (!empty ($config['admin_username']))
	$form->setField("init", $config['admin_username']);
$form->setField("notes", "(you will use this to login)");
$form->setField("misc", "maxlength=\"60\" size=\"32\"");
$form->setField("validate", "req");
$form->setField("error", "Invalid Administrator Username");
$form->printField();

$form->newField("admin_password");
$form->setField("prompt", "Administrator Password: ");
$form->setField("type", "text");
$form->setField("default", "enter new password");
$form->setField("notes", "(you will use this to login)");
$form->setField("misc", "maxlength=\"60\" size=\"32\"");
$form->printField();

$form->newField("admin_password2");
$form->setField("prompt", "Retype Password: ");
$form->setField("type", "text");
$form->setField("default", "enter password again");
$form->setField("notes", "(enter password again)");
$form->setField("misc", "maxlength=\"60\" size=\"32\"");
$form->setField("validate", "opt.same");
$form->setField("error", "admin_password:Passwords did not match");
$form->printField();

$form->newField("admin_email");
$form->setField("prompt", "Administrator Email: ".$reqStr);
$form->setField("type", "text");
$form->setField("default", "enter email");
if (!empty ($bMail->_config['admin_email']))
	$form->setField("init", $bMail->_config['admin_email']);
$form->setField("notes", "(email address of administrator)");
$form->setField("misc", "maxlength=\"60\" size=\"32\"");
$form->setField("validate", "req.email");
$form->setField("error", "Invalid Administrator Email");
$form->printField();

$form->endFieldSet();

$form->newFieldSet("Website");

$form->newField("site_name");
$form->setField("prompt", "Website Name: ".$reqStr);
$form->setField("type", "text");
$form->setField("default", "enter website name");
if (!empty ($bMail->_config['site_name']))
	$form->setField("init", $bMail->_config['site_name']);
$form->setField("notes", "(The name of your Website)");
$form->setField("misc", "maxlength=\"60\" size=\"32\"");
$form->setField("validate", "req");
$form->setField("error", "Invalid website name");
$form->printField();

$form->newField("site_url");
$form->setField("prompt", "Website URL: ".$reqStr);
$form->setField("type", "text");
$form->setField("default", "enter website url");
if (!empty ($bMail->_config['site_url']))
	$form->setField("init", $bMail->_config['site_url']);
$form->setField("notes", "(Your websites web address)");
$form->setField("misc", "maxlength=\"60\" size=\"32\"");
$form->setField("validate", "req.url");
$form->setField("error", "Invalid website URL");
$form->printField();

$form->newField("site_success");
$form->setField("prompt", "Success URL: ");
$form->setField("type", "text");
$form->setField("default", "enter success url (optional)");
if (!empty ($config['site_success']))
	$form->setField("init", $config['site_success']);
$form->setField("notes", "(Webpage users will see upon successfull subscription. Leave blank to display default welcome page.)");
$form->setField("misc", "maxlength=\"60\" size=\"32\"");
$form->setField("validate", "opt.url");
$form->setField("error", "Invalid success URL");
$form->printField();

$form->newField("site_confirm");
$form->setField("prompt", "Confirm URL: ");
$form->setField("type", "text");
$form->setField("default", "enter confirm url (optional)");
if (!empty ($config['site_confirm']))
	$form->setField("init", $config['site_confirm']);
$form->setField("notes", "(Webpage users will see upon subscription attempt. Leave blank to display default confirmation page.)");
$form->setField("misc", "maxlength=\"60\" size=\"32\"");
$form->setField("validate", "opt.url");
$form->setField("error", "Invalid success URL");
$form->printField();

$form->endFieldSet();

$form->newFieldSet("Mailing List");

$form->newField("demo_mode");
$form->setField("prompt", "Demonstration Mode: ");
$form->setField("type", "checkbox");
$form->setField("notes", "Check this box to enable demonstration mode.");
if (!empty ($bMail->_config['demo_mode']) && ($bMail->_config['demo_mode'] == "on"))
	$form->setField("default", "checked");
else
	$form->setField("default", "unchecked");
$form->printField();

$form->newField("list_confirm");
$form->setField("prompt", "Email Confirmation: ");
$form->setField("type", "checkbox");
$form->setField("notes", "Check to validate email upon subscription attempt.");
if (!empty ($config['list_confirm']) && ($config['list_confirm'] == "on"))
	$form->setField("default", "checked");
else
	$form->setField("default", "unchecked");
//$form->setField("misc", " disabled");
$form->printField();

$form->newField("list_name");
$form->setField("prompt", "List Name: ".$reqStr);
$form->setField("type", "text");
$form->setField("default", "enter list name");
if (!empty ($bMail->_config['list_name']))
	$form->setField("init", $bMail->_config['list_name']);
$form->setField("notes", "(The name of your Mailing List)");
$form->setField("misc", "maxlength=\"60\" size=\"32\"");
$form->setField("validate", "req");
$form->setField("error", "Invalid list name ");
$form->printField();

$form->newField("list_fromname");
$form->setField("prompt", "From Name: ");
$form->setField("type", "text");
$form->setField("default", "enter from name");
if (!empty ($config['list_fromname']))
	$form->setField("init", $config['list_fromname']);
$form->setField("notes", "(Default name mailings will be sent from)");
$form->setField("misc", "maxlength=\"60\" size=\"32\"");
$form->printField();

$form->newField("list_fromemail");
$form->setField("prompt", "From Email: ");
$form->setField("type", "text");
$form->setField("default", "enter from email");
if (!empty ($config['list_fromemail']))
	$form->setField("init", $config['list_fromemail']);
$form->setField("notes", "(Default email mailings will be sent as)");
$form->setField("misc", "maxlength=\"60\" size=\"32\"");
$form->setField("validate", "opt.email");
$form->setField("error", "Invalid from email");
$form->printField();

$form->newField("list_frombounce");
$form->setField("prompt", "Bounce Address: ");
$form->setField("type", "text");
$form->setField("default", "enter from bounce email");
if (!empty ($config['list_frombounce']))
	$form->setField("init", $config['list_frombounce']);
$form->setField("notes", "(Default email mailings will be sent as)");
$form->setField("misc", "maxlength=\"60\" size=\"32\"");
$form->setField("validate", "opt.email");
$form->setField("error", "Invalid bounce email");
$form->printField();

$form->endFieldSet();

$form->newFieldSet("Advanced");

$form->newField("list_exchanger");
$form->setField("prompt", "Mail Exchanger:");
$form->setField("type", "select");
if (!empty ($config['list_exchanger']))
	$form->setField("default", $config['list_exchanger']);
else
	$form->setField("default", "sendmail");
$option = array ();
$option[] = " :Select mail exchanger"; // note the " :" prefix -- this creates an empty value which will match the required rule if nothing was selected
$option[] = "sendmail:Sendmail";
$option[] = "mail:PHP Mail Function (defaults)";
$option[] = "smtp:SMTP Relay";
$option[] = "exim:Exim MTA(disabled)";

$form->setField("option", $option); // always prefix with "option" to trigger option event.
$form->setField("notes", "Select Mail Exchanger");
//$form->setField("misc", " onChange=\"document.".$form->_name.".submit()\"");
$form->printField();

if ($config['list_exchanger'] == 'smtp') {
	echo '<div class="field"><a href="setup_smtp.php"><img src="'.bm_baseUrl.'/img/icons/right.png" align="center" border="0"></a> &nbsp; SMTP Config:';
	echo ' <a href="setup_smtp.php">Click Here</a> to setup SMTP Relay(s)</div><br /><br />';
}

echo '<div class="field"><a href="setup_messages.php"><img src="'.bm_baseUrl.'/img/icons/right.png" align="center" border="0"></a> &nbsp; Messages:';
echo ' <a href="setup_messages.php">Click Here</a> to customize messages.<div class="notes">(define the email message sent for subscription, unsubscription, and updates)</div></div><br />';

echo '<div class="field"><a href="setup_throttle.php"><img src="'.bm_baseUrl.'/img/icons/right.png" align="center" border="0"></a> &nbsp; Throttling:';
echo ' <a href="setup_throttle.php">Click Here</a> to setup mail throttler.<div class="notes">(controls mails per second, bytes per second, and domain limits)</div></div><br />';


$form->endFieldSet();

// print the submit button and close the form element
$form->endForm();

include (bm_baseDir.'/setup/footer.php');
?>