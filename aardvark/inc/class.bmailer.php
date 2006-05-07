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

/** 
 * Don't allow direct access to this file. Must be called from
elsewhere
*/
defined('_IS_VALID') or die('Move along...');

// load the phpmailer class taken from http://phpmailer.sourceforge.net/
require_once (bm_baseDir . '/inc/phpmailer/class.phpmailer.php');
require_once (bm_baseDir . '/inc/lib.txt.php');

// create bMailer class (an extension of PHPMailer)
class bMailer extends PHPMailer {

	var $_fromname;
	var $_fromemail;
	var $_frombounce;

	var $_subject;
	var $_body;

	var $_exchanger; // sendmail,mail,smtp ... currently mail or sendmail are used TODO add smtp
	var $_sentCount; // counter for mails sent sucessfully.

	var $_demonstration;
	var $_validated; // if this is TRUE, skip all validation checks + setting of all parameters other than "to" .. this is used for bulk mailing

	// default constructor....

	// called like $pommo = new bMailer(fromname,fromemail,frombounce, exchanger)
	//  If an argument is not supplied, resorts to default value (from setup/config.php).
	function bMailer($fromname = NULL, $fromemail = NULL, $frombounce = NULL, $exchanger = NULL, $demonstration = NULL) {

		$listConfig = $_SESSION['poMMo']->getConfig(array (
			'list_fromname',
			'list_fromemail',
			'list_frombounce',
			'list_exchanger'
		));
		if (empty ($fromname))
			$fromname = $listConfig['list_fromname'];

		if (empty ($fromemail))
			$fromemail = $listConfig['list_fromemail'];

		if (empty ($frombounce))
			$frombounce = $listConfig['list_frombounce'];

		if (empty ($exchanger))
			$exchanger = $listConfig['list_exchanger'];

		if (empty ($demonstration))
			$demonstration = $_SESSION["poMMo"]->_config['demo_mode'];

		// initialize object's values

		$this->_fromname = $fromname;
		$this->_fromemail = $fromemail;
		$this->_frombounce = $frombounce;
		$this->_exchanger = $exchanger;
		$this->_demonstration = $demonstration;

		$this->_subject = NULL;
		$this->_body = NULL;

		$this->_validated = FALSE;

		$this->_sentCount = 0;

		$langPath = bm_baseDir . '/inc/phpmailer/language/';
		if (!$this->SetLanguage('en', $langPath))
			die('<img src="' . bm_baseUrl . '/themes/shared/images/icons/alert.png" align="middle">bMailer(): Unable to set language.');

	}

	// toggles demonstration mode on or off if sepcified, or else uses the configured mode. Returns value.
	function toggleDemoMode($val = NULL) {
		if ($val == "on")
			$this->_demonstration = "on";
		elseif ($val == "off") $this->_demonstration = "off";
		else
			$this->_demonstration = $_SESSION["poMMo"]->_config['demo_mode'];
		return $this->_demonstration;
	}

	// enable to track size (in bytes) of sent messages.
	function trackMessageSize($bool = TRUE) {
		$this->SaveMessageSize = $bool;
	}

	// returns the size (in bytes) of the last sent message
	function GetMessageSize() {
		return $this->LastMessageSize;
	}

	// sets the SMTP relay for this mailer
	function setRelay(& $smtp) {
		if (!empty ($smtp['host']))
			$this->Host = $smtp['host'];
		if (!empty ($smtp['port']))
			$this->Port = $smtp['port'];
		if (!empty ($smtp['auth']) && $smtp['auth'] == 'on') {
			$this->SMTPAuth = TRUE;
			if (!empty ($smtp['user']))
				$this->Username = $smtp['user'];
			if (!empty ($smtp['pass']))
				$this->Password = $smtp['pass'];
		}
	}

	// Gets called before sending a mail to make sure all is proper (during prepareMail). Returns false if messages were created must pass global poMMo object (TODO maybe rename to site??)
	function validate() {

		if (empty ($this->_fromname))
			$_SESSION["poMMo"]->addMessage("Name cannot be blank.");

		if (!isEmail($this->_fromemail))
			$_SESSION["poMMo"]->addMessage("From email must be a valid email address.");

		if (!isEmail($this->_frombounce))
			$_SESSION["poMMo"]->addMessage("Bounce email must be a valid email address.");

		if (empty ($this->_subject))
			$_SESSION["poMMo"]->addMessage("Subject cannot be blank.");

		if (empty ($this->_body))
			$_SESSION["poMMo"]->addMessage("Message content cannot be blank.");

		// if Messages exist, return false..	
		if ($_SESSION["poMMo"]->isMessage())
			return false;

		return true;
	}

	// Sets up the mail message. If message is HTML, indicate by setting 3rd argument to TRUE.
	// TODO -> pass by reference??
	function prepareMail($subject = NULL, $body = NULL, $HTML = FALSE, $altbody = NULL) {

		$this->_subject = $subject;
		$this->_body = $body;

		// ** Set PHPMailer class parameters

		if ($this->_validated == FALSE) {

			// Validate mail parameters
			if (!$this->validate()) {
				return false;
			}
			// TODO -> should I just set PHPMailer parameters in the 1st place & skip $this->_paramName ?
			// TODO -> pass these by reference ??

			$this->FromName = $this->_fromname;
			$this->From = $this->_fromemail;
			$this->AddReplyTo = $this->_frombounce;
			$this->Subject = $this->_subject;

			// make sure exchanger is valid, DEFAULT to PHP Mail
			if ($this->_exchanger != "mail" && $this->_exchanger != "sendmail" && $this->_exchanger != "smtp")
				$this->_exchanger = "mail";
			$this->Mailer = $this->_exchanger;

			if ($this->Mailer == 'smtp') { // loads the default relay (#1) -- use setRelay() to change.
				$config = $_SESSION['poMMo']->getConfig(array('smtp_1'));
				$smtp = unserialize($config['smtp_1']);
	
				if (!empty ($smtp['host']))
					$this->Host = $smtp['host'];
				if (!empty ($smtp['port']))
					$this->Port = $smtp['port'];
				if (!empty ($smtp['auth']) && $smtp['auth'] == 'on') {
					$this->SMTPAuth = TRUE;
					if (!empty ($smtp['user']))
						$this->Username = $smtp['user'];
					if (!empty ($smtp['pass']))
						$this->Password = $smtp['pass'];
				}
			}

			// if altbody exists, set message type to HTML + add alt body
			if ($HTML) {
				$this->IsHTML(TRUE);
				if (!empty ($altbody))
					$this->AltBody = $altbody;
			}

			$this->Body = $this->_body;

			// passed all sanity checks...
			$this->_validated = TRUE;
		}
		return TRUE;
	}

	// ** SEND MAIL FUNCTION --> pass an array of senders, or a single email address for single mode
	function bmSendmail(& $to) { // TODO rename function send in order to not confuse w/ PHPMailer's Send()?

		if ($this->_validated == FALSE) {
			$_SESSION["poMMo"]->addMessage("poMMo has not passed sanity checks. has prepareMail been called?");
			return false;
		}
		// make sure $to is valid, or send errors...
		elseif (empty ($to)) {
			$_SESSION["poMMo"]->addMessage("To email supplied to send() command is empty.");
			return false;
		}

		$errors = array ();

		if ($this->_demonstration == "off") { // If poMMo is not in set in demonstration mode, SEND MAILS...

			// if $to is not an array (single email address has been supplied), simply send the mail.
			if (!is_array($to)) {
				$this->AddAddress($to);

				// send the mail. If unsucessful, add error message.
				if (!$this->Send())
					$errors[] = "Mailing failed: " . $this->ErrorInfo;
				
				$this->ClearAddresses();

			} else {
				// MULTI MODE! -- antiquated.
				// incorporate BCC+Enveloping in here if type is SMTP
				// TODO Play w/ the size limiting of arrays sent here

				/*
								foreach (array_keys($to) as $key) {
									
									if ($key == "0") 
							 			$this->AddAddress($to[$key]);
									else
							  			$this->AddBcc($to[$key]);
								} */

				foreach (array_keys($to) as $key) {
					$this->ClearAddresses();
					$this->AddAddress($to[$key]);
					// send the mail. If unsucessful, add error message.
					if (!$this->Send())
						$errors[] = 'Sending to: ' . $to[$key] . ', Error: ' . $this->ErrorInfo;
				}

			}
		} else {
			require_once ('lib.txt.php');
			$errors[] = "Mail to: " . array2csv($to)." not sent: Demonstration active.";
		}

		// if message(s) exist, return false. (Sending failed w/ error messages)
		if (!empty ($errors)) {
			foreach ($errors as $error)
				$_SESSION["poMMo"]->addMessage($error);
			return false;
		}
		return true;
	}
}
?>