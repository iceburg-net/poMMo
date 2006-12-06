<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * Created: Corinna Thoeni <corinn at gmx dot net> - 16.11.2006
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/


class BouncePOP {

	//BOUNCE SETTINGS
	private $bounce_protocol = 'POP';	//pop
	private $bounce_mailbox_host = 'pop.gmx.net';
	private $bounce_mailbox_port = "110/pop3/notls";


	private $bounce_mailbox_user = 'corinna-pommo@gmx.net';
	private $bounce_mailbox_pass = 'A6Q00VAAS';
	
	
	/**
	 * 2 methods
	 * GET BOUNCES on CLICK -> push!
	 * GET BOUNCES with a Script ->	poll
	 */
	
	public function connectIMAP() {
		
		//$mailbox="{mail.domain.com:143/imap/notls}";
		$mailbox="{pop.gmx.net:110/pop3/notls}"; //This works...

		//Some mail server requires you to provide username@domain.com so you can always use. user@doamin.com
		$conn = imap_open($mailbox, $this->bounce_mailbox_user, $this->bounce_mailbox_password);

		//Some server may ask for username as "user=user@domain.com"


		echo $conn;
		
		
	}
	
	
	public function getBounces() {
		
	}
	
	public function storeToDB() {
		
	}
	

	
}


?>
