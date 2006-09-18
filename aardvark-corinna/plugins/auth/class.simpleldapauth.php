<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * Created: Corinna Thoeni <corinn at gmx dot net> - 04.09.2006
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/

include_once(bm_baseDir.'/plugins/auth/class.authenticator.php');

class SimpleLdapAuth extends Authenticator {
 	
 	public function __construct($dbo) {
 		parent::__construct($dbo);
		$this->setName("Simple LDAP Authenticator");
 	}
 	
 	
	public function execute() {
		$ldapconn = "";

		//Connect to LDAP Server
		if ($this->ldapconf['ldap_server']) {
			$ldapconn = ldap_connect($this->ldapconf['ldap_server']);		// or die( "connect: Connection to {$this->ldapuri} unavailable.<br>" );	// is dirty
		} else {
			$this->handleError("Host not reachable: {$this->ldapconf['ldap_server']}");
			return FALSE;
		}
		
		if ($ldapconn) {
			ldap_set_option(NULL, LDAP_OPT_DEBUG_LEVEL, 7);

			//Set Protocol to LDAPv3
			if (!ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3)) {
				$this->handleError("Failed to set LDAP Protocol version to 3, TLS not supported.");
				return FALSE;
			}
			
			// if user or pass is empty
			if ( (empty($this->vars['user'])) || (empty($this->vars['pass'])) )  {
				$this->handleError("PASSWORD FIELD ODER USER FIELD EMPTY<br>");
				return FALSE;
			} else {

				// TODO �berlegen was mit @ICT.TUWIEN
				//if (substring(==$this->ldapconf['ldap_dn'] in $this->vars['user']))
				$usrname = $this->vars['user'] . "@ICT.TUWIEN.AC.AT";
				// wenn in weiter,
				// wenn nicht dann anh�ngen -> concat.
				
				$ldapbind = ldap_bind($ldapconn, $usrname, $this->vars['pass']);
					//What to do to get rid of the warning? 
					//Warning: ldap_bind() [function.ldap-bind]: Unable to bind to server: 
					//Can't contact LDAP server in [url]
				if ($ldapbind) {
					// Bind with this credentials went ok! Authentication ok!
					ldap_close($ldapconn);
					return TRUE;
				} else {
					//Invalid credentials, Authentication failed
					//TODO this is not a error catch this bind-exception
					$this->handleError("Authentication failed!<br>");
					ldap_close($ldapconn);
					return FALSE;
				}
			}
		} else {
			//Connect not ok!
			$this->handleError("Connect failed.");
			ldap_close($ldapconn);
			return FALSE;
		}
		
	} //execute
	
} // SimpleLdapAuth

?>
