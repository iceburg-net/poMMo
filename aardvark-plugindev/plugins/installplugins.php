<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * Created: Corinna Thoeni <corinn at gmx dot net> - 30.11.2006
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/

require('../bootstrap.php');
$pommo->init();

if ($pommo->_useplugins) {
	
	$prefix = "pommo_";		// Change this to $pommo->tables->...
	$pluginprefix = "pommomod_";
	$dbstr = "ENGINE = innodb ";
	$charset = "DEFAULT CHARSET = latin1 ";
	
	
	echo "<html><body style='font-size: 0.5em; font-family: Courier, Courier New, sans-serif; color:black; '><div style='white-space:nowrap;'>";
	
	$link = $pommo->_link;
	$safesql = & $pommo->_dbo->_safeSQL; 	//$safesql =& new SafeSQL_MySQL;
	
	/*	$link = mysql_connect($host, $user, $pass)
			or die("No DB connection: " . mysql_error());  echo "Database connection successful.<br>";
	mysql_select_db($database) 
			or die("Database selection failed.<br>"); 	*/
	
	
	
	$sqltab[] = $safesql->query("DROP TABLE IF EXISTS `%sbounce` , `%smailingqueue`, " .
			"`%slist` , `%slist_rp` , `%srp_group`, `%sresponsibleperson` , `%suser`, `%sperm`, " .
			"`%splugin`, `%splugincategory` , `%splugindata`  ", 
			array($pluginprefix,$pluginprefix,$pluginprefix,$pluginprefix,$pluginprefix,
				  $pluginprefix,$pluginprefix,$pluginprefix,$pluginprefix,$pluginprefix,
				  $pluginprefix) );


		/****************************** CREATE TABLES *********************************/  
		//SMALLINT UNSIGNED  0 - 65535                         (SIGNED 	-32768 	32767)
  	  	//ON DELETE / ON UPDATE CASCADE  FOREIGN KEY
		//PRIMARY KEY ( `group_id` )


		$sqltab[] = $safesql->query("CREATE TABLE `%splugincategory` ( " .
							"`cat_id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY , " .
							"`cat_name` VARCHAR( 75 ) NOT NULL UNIQUE, " .
							"`cat_desc` VARCHAR( 250 ) NOT NULL, " .
							"`cat_active` BOOL NOT NULL  DEFAULT '0' " .
							") %s %s; ",
						array($pluginprefix, $dbstr, $charset) );

		$sqltab[] = $safesql->query("CREATE TABLE `%splugin` ( " .
							"`plugin_id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY , " . 
							"`plugin_uniquename` VARCHAR( 75 ) NOT NULL, " . 	//UNIQUE
							"`plugin_name` VARCHAR( 100 ) NOT NULL , " . 		//TEXT?
							"`plugin_desc` VARCHAR( 250 ) NOT NULL , " . 
							"`plugin_active` BOOL NOT NULL  DEFAULT '0' , " . 
							"`plugin_version` VARCHAR( 10 ) NOT NULL DEFAULT '0', " .  
							"`cat_id` SMALLINT UNSIGNED NOT NULL REFERENCES %splugincategory(cat_id) " . 
							") %s %s; ",
						array($pluginprefix, $pluginprefix, $dbstr, $charset) );

		$sqltab[] = $safesql->query("CREATE TABLE `%splugindata` ( " .
							"`data_id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY , " .
							"`data_name` VARCHAR( 75 ) NOT NULL UNIQUE, " .
							"`data_value` VARCHAR( 150 ) NOT NULL, " .
							"`data_type` ENUM( 'TXT', 'NUM', 'BOOL' ) NOT NULL DEFAULT 'TXT', " . 
							"`data_desc`  VARCHAR( 250 ) NOT NULL, " . 
							"`plugin_id` SMALLINT UNSIGNED NOT NULL REFERENCES %splugin(plugin_id) " . 
							") %s %s; ",
						array($pluginprefix, $pluginprefix, $dbstr, $charset) );


		// like mailtable
		$sqltab[] = $safesql->query("CREATE TABLE `%smailingqueue` ( " . 
							"`qid` SMALLINT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT , " . 
							"`fromname` varchar( 60 ) NOT NULL default '', " . 
							"`fromemail` varchar( 60 ) NOT NULL default '', " . 
							"`frombounce` varchar( 60 ) NOT NULL default '', " . 
							"`subject` varchar( 60 ) NOT NULL default '', " . 
							"`body` mediumtext NOT NULL , " . 
							"`altbody` mediumtext, " . 
							"`ishtml` enum( 'on', 'off' ) NOT NULL default 'off', " . 
							"`mailgroup` varchar( 60 ) NOT NULL default 'Unknown', " . 
							"`date` datetime default NULL , " . 
							"`sent` int( 10 ) unsigned NOT NULL default '0', " . 
							"`notices` longtext, " . 
							"`charset` varchar( 15 ) NOT NULL default 'UTF-8' " . 
							") %s %s; ",
						array($pluginprefix, $dbstr, $charset) );

		$sqltab[] = $safesql->query("CREATE TABLE `%sbounce` ( " .
							"`bounce_id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY , " .
							"`bounce_email_bounced` VARCHAR( 100 ) NOT NULL , " .
							"`bounce_mail` MEDIUMTEXT NOT NULL , " .		//siehe altbody
							"`bounce_reason` VARCHAR( 200 ) NOT NULL , " .
							"`subscriber_id` SMALLINT UNSIGNED NOT NULL REFERENCES %ssubscribers(subscribers_id) " .
							") %s %s; ",
						array($pluginprefix, $bmdb['prefix'], $dbstr, $charset) );



		$sqltab[] = $safesql->query("CREATE TABLE `%sperm` ( " .
							"`perm_id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY , " .
							"`perm_name` VARCHAR( 75 ) NOT NULL UNIQUE, " .
							"`perm_perm` VARCHAR( 200 ) NOT NULL , " .
							"`perm_desc` VARCHAR( 250 ) NOT NULL  " .
							") %s %s; ",
						array($pluginprefix, $dbstr, $charset) );

		$sqltab[] = $safesql->query("CREATE TABLE `%suser` ( " .
							"`user_id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY , " .
							"`user_name` VARCHAR( 75 ) NOT NULL UNIQUE , " .
							"`user_pass` VARCHAR( 150 ) NOT NULL , " .										//MD5
							"`perm_id` SMALLINT UNSIGNED NOT NULL REFERENCES %sperm(perm_id) , " .
							"`user_created` DATETIME NOT NULL , " .											//TIMESTAMP
							"`user_lastlogin` DATETIME NOT NULL , " .
							"`user_logintries` SMALLINT UNSIGNED NOT NULL, " .					//AUTO_INCREMENT
							"`user_lastedit` DATETIME NOT NULL , " .
							"`user_active` BOOL NOT NULL  DEFAULT '0' " .
							") %s %s; ",
						array($pluginprefix, $pluginprefix, $dbstr, $charset) );

		$sqltab[] = $safesql->query("CREATE TABLE `%sresponsibleperson` ( " .
							"`user_id` SMALLINT UNSIGNED NOT NULL REFERENCES %suser(user_id) , " .
							"`rp_realname` VARCHAR( 150 ) NOT NULL UNIQUE, " .
							"`rp_bounceemail` VARCHAR( 100 ) NOT NULL , " .
							"`rp_sonst` VARCHAR( 250 ) NOT NULL  " .
							") %s %s; ",
						array($pluginprefix, $pluginprefix, $dbstr, $charset) );


		$sqltab[] = $safesql->query("CREATE TABLE `%slist` ( " .
							"`list_id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY , " .
							"`list_name` VARCHAR( 150 ) NOT NULL , " .
							"`list_senderinfo` VARCHAR( 150 ) NOT NULL , " .
							"`list_desc` VARCHAR( 250 ) NOT NULL , " .
							"`list_created` DATETIME NOT NULL , " .
							"`list_sentmailings` INT UNSIGNED NOT NULL  , " .	//AUTO_INCREMENT
							"`list_active` BOOL NOT NULL  DEFAULT '0'  " .
							/* list_mailing ist auch n:m. Mailings mit listen verknüpfen  */
							") %s %s; ",
						array($pluginprefix, $dbstr, $charset) );


		/* n:m Relations */
		$sqltab[] = $safesql->query("CREATE TABLE `%slist_rp` ( " .
							"`list_id` SMALLINT UNSIGNED NOT NULL REFERENCES %slist(list_id) , " .
							"`user_id` SMALLINT UNSIGNED NOT NULL REFERENCES %sresponsibleperson(user_id)  " .
							/* sonstige daten wie zuteilungsdatum oder so??*/
							") %s %s; ",
						array($pluginprefix, $pluginprefix, $pluginprefix, $dbstr, $charset) );

		$sqltab[] = $safesql->query("CREATE TABLE `%srp_group` ( " .	//group is a pommo-table
							"`user_id` SMALLINT UNSIGNED NOT NULL REFERENCES %sresponsibleperson(user_id) , " .
							"`group_id` SMALLINT UNSIGNED NOT NULL REFERENCES %sgroups(group_id) " .			
							") %s %s; ",
						array($pluginprefix, $pluginprefix, $bmdb['prefix'], $dbstr, $charset) );


		
		//---Install Tables---
		for ($i = 0; $i < count($sqltab); $i++) {
			$result = mysql_query($sqltab[$i]) or die("Query failed: <b style='color: red'>" . mysql_error() . 
															"<br> --->Statement" . $sqltab[$i] . "</b><br>");
				echo "{$sqltab[$i]} "; echo "<br> --->"; echo "<b>". $result. "</b><br>";
		}





		/***********************  INSERT DATA ***************************/

		/* bounce nix */
		/* mailing queue nix */
	
		/* plugincategory */
		$sql[] = $safesql->query("INSERT INTO `%splugincategory` (cat_name, cat_active, cat_desc) " .
									"VALUES ('auth', '0', 'Multiuser category. If you plan to install multiuser support with pommo, here are the tools+setup for it. Authenticate users with varios methods. Install here what methods to use') ", 
						array($pluginprefix) );
		$sql[] = $safesql->query("INSERT INTO `%splugincategory` (cat_name, cat_active, cat_desc) " .
									"VALUES ('utils', '0', 'Useful tools for Klickverfolgung, mailing queue and so on') ", 
						array($pluginprefix) );	
		$sql[] = $safesql->query("INSERT INTO `%splugincategory` (cat_name, cat_active, cat_desc) " .
									"VALUES ('bounce', '0', 'bounce mail setup, various bounce methods') ", 
						array($pluginprefix) );	
		
		/* plugins */
		$sql[] = $safesql->query("INSERT INTO %splugin (plugin_uniquename, plugin_name, plugin_desc, plugin_active, plugin_version, cat_id) " .
				"VALUES ('simpleldapauth', 'Simple LDAP Authentication', 'Authenticate users with a ldap bind to a ldap/ads server in your network. No need to know further user/pass details or query details.', " .
				"'0', '0.1', (SELECT cat_id FROM %splugincategory WHERE cat_name='auth' LIMIT 1)) ",
						array($pluginprefix, $pluginprefix) );	
		$sql[] = $safesql->query("INSERT INTO %splugin (plugin_uniquename, plugin_name, plugin_desc, plugin_active, plugin_version, cat_id) " .
				"VALUES ('queryldapauth', 'LDAP Auth with Query', 'Authenticate users with a ldap query on a ldap/ads server. You need to know further user/pass details or query details.', " .
				"'0', '0.1', (SELECT cat_id FROM %splugincategory WHERE cat_name='auth' LIMIT 1) ) ",
						array($pluginprefix, $pluginprefix) );	
		$sql[] = $safesql->query("INSERT INTO %splugin (plugin_uniquename, plugin_name, plugin_desc, plugin_active, plugin_version, cat_id) " .
				"VALUES ('dbauth', 'Database Authentication', 'Authenticate users on database data. The Administrator can add, delete set permissions for users. To use this activate User management!', " .
				"'0', '0.1', (SELECT cat_id FROM %splugincategory WHERE cat_name='auth' LIMIT 1)) ",
						array($pluginprefix, $pluginprefix) );
		$sql[] = $safesql->query("INSERT INTO %splugin (plugin_uniquename, plugin_name, plugin_desc, plugin_active, plugin_version, cat_id) " .
				"VALUES ('useradmin', 'User Administration Plugin', 'Thius makes sense in combination with a authentication method (Usually:dbauth)', " .
				"'0', '0.1', (SELECT cat_id FROM %splugincategory WHERE cat_name='auth' LIMIT 1)) ",
						array($pluginprefix, $pluginprefix) );	
		$sql[] = $safesql->query("INSERT INTO %splugin (plugin_uniquename, plugin_name, plugin_desc, plugin_active, plugin_version, cat_id) " .
				"VALUES ('bouncepop', 'Bounce Mail Handler', 'Redirect bounced / unzustellbare Mails zu responsible Persons with POP oder with a web interface. for this you need a mailbox where the bounces are stored.', " .
				"'0', '0.1', (SELECT cat_id FROM %splugincategory WHERE cat_name='bounce' LIMIT 1)) ",
						array($pluginprefix, $pluginprefix) );
		$sql[] = $safesql->query("INSERT INTO %splugin (plugin_uniquename, plugin_name, plugin_desc, plugin_active, plugin_version, cat_id) " .
				"VALUES ('bouncehandler', 'Bounce Mail Handler', 'Redirect bounced / unzustellbare Mails zu responsible Persons with POP oder with a web interface. for this you need a mailbox where the bounces are stored.', " .
				"'0', '0.1', (SELECT cat_id FROM %splugincategory WHERE cat_name='bounce' LIMIT 1)) ",
						array($pluginprefix, $pluginprefix) );
		$sql[] = $safesql->query("INSERT INTO %splugin (plugin_uniquename, plugin_name, plugin_desc, plugin_active, plugin_version, cat_id) " .
				"VALUES ('mailingqueue', ' Mail Queue', 'Store Sendings in the database for later sending.', " .
				"'0', '0.1', (SELECT cat_id FROM %splugincategory WHERE cat_name='utils' LIMIT 1)) ",
						array($pluginprefix, $pluginprefix) );	
		$sql[] = $safesql->query("INSERT INTO %splugin (plugin_uniquename, plugin_name, plugin_desc, plugin_active, plugin_version, cat_id) " .
				"VALUES ('clickstats', 'Mouse Click Statistics', 'Look how many of your mailings are viewed or: how many oif the links in the mail are followed.', " .
				"'0', '0.1', (SELECT cat_id FROM %splugincategory WHERE cat_name='utils' LIMIT 1)) ",
						array($pluginprefix, $pluginprefix) );	

		/* plugindata */
		$sql[] = $safesql->query("INSERT INTO %splugindata (data_name, data_value, data_desc, data_type, plugin_id) VALUES " .
				"('simpleldap_server', 'ldaps://domcon.ict.tuwien.ac.at/', 'The server where the LDAP bind is directed.', " .
				"'TXT', (SELECT plugin_id FROM %splugin WHERE plugin_uniquename='simpleldapauth' LIMIT 1) ) ",
						array($pluginprefix, $pluginprefix) );	
		$sql[] = $safesql->query("INSERT INTO %splugindata (data_name, data_value, data_desc, data_type, plugin_id) VALUES " .
				"('simpleldap_port', '636', 'The port of the server for LDAP bind (Usually 636).', " .
				"'TXT', (SELECT plugin_id FROM %splugin WHERE plugin_uniquename='simpleldapauth' LIMIT 1) ) ",
						array($pluginprefix, $pluginprefix) );	
		$sql[] = $safesql->query("INSERT INTO %splugindata (data_name, data_value, data_desc, data_type, plugin_id) VALUES " .
				"('simpleldap_dn', '@ICT.TUWIEN.AC.AT', 'DN ... blah', " .
				"'TXT', (SELECT plugin_id FROM %splugin WHERE plugin_uniquename='simpleldapauth' LIMIT 1) ) ",
						array($pluginprefix, $pluginprefix) );	

		$sql[] = $safesql->query("INSERT INTO %splugindata (data_name, data_value, data_desc, data_type, plugin_id) VALUES " .
				"('queryldap_server', 'ldaps://domcon.ict.tuwien.ac.at/', 'Server blah', " .
				"'TXT', (SELECT plugin_id FROM %splugin WHERE plugin_uniquename='queryldapauth' LIMIT 1) ) ",
						array($pluginprefix, $pluginprefix) );	
		$sql[] = $safesql->query("INSERT INTO %splugindata (data_name, data_value, data_desc, data_type, plugin_id) VALUES " .
				"('queryldap_port', '636', 'Port... blah', " .
				"'TXT', (SELECT plugin_id FROM %splugin WHERE plugin_uniquename='queryldapauth' LIMIT 1) ) ",
						array($pluginprefix, $pluginprefix) );	
		$sql[] = $safesql->query("INSERT INTO %splugindata (data_name, data_value, data_desc, data_type, plugin_id) VALUES " .
				"('queryldap_base', 'dn=ICT,dn=TUWIEN,dn=AC,dn=AT', ' base ... blah', " .
				"'TXT', (SELECT plugin_id FROM %splugin WHERE plugin_uniquename='queryldapauth' LIMIT 1) ) ",
						array($pluginprefix, $pluginprefix) );	
		$sql[] = $safesql->query("INSERT INTO %splugindata (data_name, data_value, data_desc, data_type, plugin_id) VALUES " .
				"('queryldap_dn', '@ICT.TUWIEN.AC.AT', 'DN ... blah', " .
				"'TXT', (SELECT plugin_id FROM %splugin WHERE plugin_uniquename='queryldapauth' LIMIT 1) ) ",
						array($pluginprefix, $pluginprefix) );	
		$sql[] = $safesql->query("INSERT INTO %splugindata (data_name, data_value, data_desc, data_type, plugin_id) VALUES " .
				"('queryldap_user', 'myuser', 'DN ... blah', " .
				"'TXT', (SELECT plugin_id FROM %splugin WHERE plugin_uniquename='queryldapauth' LIMIT 1) ) ",
						array($pluginprefix, $pluginprefix) );	
		$sql[] = $safesql->query("INSERT INTO %splugindata (data_name, data_value, data_desc, data_type, plugin_id) VALUES " .
				"('queryldap_pass', 'mypassw', 'DN ... blah', " .
				"'TXT', (SELECT plugin_id FROM %splugin WHERE plugin_uniquename='queryldapauth' LIMIT 1) ) ",
						array($pluginprefix, $pluginprefix) );	

		$sql[] = $safesql->query("INSERT INTO %splugindata (data_name, data_value, data_desc, data_type, plugin_id) VALUES " .
				"('dbauth_writeldapusertodb', 'TRUE', 'The server where the LDAP bind is directed.', " .
				"'BOOL', (SELECT plugin_id FROM %splugin WHERE plugin_uniquename='dbauth' LIMIT 1) ) ",
						array($pluginprefix, $pluginprefix) );
		
		$sql[] = $safesql->query("INSERT INTO %splugindata (data_name, data_value, data_desc, data_type, plugin_id) VALUES " .
				"('useradmin_maxuser', '20', 'Maximal users in DB!.', " .
				"'NUM', (SELECT plugin_id FROM %splugin WHERE plugin_uniquename='useradmin' LIMIT 1) ) ",
						array($pluginprefix, $pluginprefix) );
		
		$sql[] = $safesql->query("INSERT INTO %splugindata (data_name, data_value, data_desc, data_type, plugin_id) VALUES " .
				"('bouncepop_server', 'mail.gmx.net', '.', " .
				"'TXT', (SELECT plugin_id FROM %splugin WHERE plugin_uniquename='bouncepop' LIMIT 1) ) ",
						array($pluginprefix, $pluginprefix) );
		$sql[] = $safesql->query("INSERT INTO %splugindata (data_name, data_value, data_desc, data_type, plugin_id) VALUES " .
				"('bouncepop_port', '636', 'The port of the server for POPPING (Usually 636).', " .
				"'TXT', (SELECT plugin_id FROM %splugin WHERE plugin_uniquename='bouncepop' LIMIT 1) ) ",
						array($pluginprefix, $pluginprefix) );	
		$sql[] = $safesql->query("INSERT INTO %splugindata (data_name, data_value, data_desc, data_type, plugin_id) VALUES " .
				"('bouncepop_user', 'corinna-pommo@gmx.net', '.', " .
				"'TXT', (SELECT plugin_id FROM %splugin WHERE plugin_uniquename='bouncepop' LIMIT 1) ) ",
						array($pluginprefix, $pluginprefix) );	
		$sql[] = $safesql->query("INSERT INTO %splugindata (data_name, data_value, data_desc, data_type, plugin_id) VALUES " .
				"('bouncepop_pass', 'A6Q00VAAS', '.', " .
				"'TXT', (SELECT plugin_id FROM %splugin WHERE plugin_uniquename='bouncepop' LIMIT 1) ) ",
						array($pluginprefix, $pluginprefix) );

								/* zu entscheiden */				
		$sql[] = $safesql->query("INSERT INTO %splugindata (data_name, data_value, data_desc, data_type, plugin_id) VALUES " .
				"('mailqueue_opt', 'blah', 'DN ... blah', " .
				"'TXT', (SELECT plugin_id FROM %splugin WHERE plugin_uniquename='mailingqueue' LIMIT 1) ) ",
						array($pluginprefix, $pluginprefix) );	
		$sql[] = $safesql->query("INSERT INTO %splugindata (data_name, data_value, data_desc, data_type, plugin_id) VALUES " .
				"('clickstats_opt', 'blah', 'DN ... blah', " .
				"'TXT', (SELECT plugin_id FROM %splugin WHERE plugin_uniquename='clickstats' LIMIT 1) ) ",
						array($pluginprefix, $pluginprefix) );	





			// Execute queries 
			for ($i = 0; $i < sizeof($sql); $i++) {
					$result = mysql_query($sql[$i]) or die("<b style='color: green; '>Query failed: " . mysql_error() . 
											"<br> --->Statement" . $sql[$i] . "</b><br>");
					echo $sql[$i]; echo "<br> --->"; echo "<b>". $result. "</b><br>";
			}
				
			echo "<b>Install complete.</b><br><br>";
			//mysql_close($link);

			echo "</div><body><html>";
	
} else {
	echo "Could not install plugins. Please mark the config variable useplugins as TRUE";
} 
	

?>


