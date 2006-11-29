<?php
/*
CREATE TABLE `pommomod_bounces` (
`bounce_id` MEDIUMINT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`bounce_email_bounced` VARCHAR( 100 ) NOT NULL ,
`bounce_mail` BLOB NOT NULL ,
`bounce_reason` VARCHAR( 200 ) NOT NULL ,
`user_id` MEDIUMINT NOT NULL
) ENGINE = innodb;


CREATE TABLE `pommomod_mailing_queue` (
`qid` smallint( 5 ) unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT ,
`fromname` varchar( 60 ) NOT NULL default '',
`fromemail` varchar( 60 ) NOT NULL default '',
`frombounce` varchar( 60 ) NOT NULL default '',
`subject` varchar( 60 ) NOT NULL default '',
`body` mediumtext NOT NULL ,
`altbody` mediumtext,
`ishtml` enum( 'on', 'off' ) NOT NULL default 'off',
`mailgroup` varchar( 60 ) NOT NULL default 'Unknown',
`date` datetime default NULL ,
`sent` int( 10 ) unsigned NOT NULL default '0',
`notices` longtext,
`charset` varchar( 15 ) NOT NULL default 'UTF-8'
) ENGINE = InnoDB DEFAULT CHARSET = latin1;


CREATE TABLE `pommomod_permgroups` (
`group_id` SMALLINT NOT NULL ,
`group_name` VARCHAR( 100 ) NOT NULL ,
`group_perm` VARCHAR( 200 ) NOT NULL ,
`group_desc` VARCHAR( 200 ) NOT NULL ,
PRIMARY KEY ( `group_id` )
) ENGINE = innodb;



CREATE TABLE LIST

CREATE TABLE LIST_USER

CREATE TABLE ....

 */

/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * Created: Corinna Thoeni <corinn at gmx dot net> - 08.09.2006
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/

// TODO in SafeSQL
// TODO normal Authentication
define('_IS_VALID', TRUE);


//if (!installed) {

		// Here the Database configuration user, pass table prefix and so on comes from
		include_once('../config.php');

		// anderer Prefix will ich
		$bmdb['prefix'] = 'pommomod_';
	
	
		
				// alter this db connection -> dbo
				$link = mysql_connect($bmdb['hostname'], $bmdb['username'], $bmdb['password'])
					or die("No DB connection: " . mysql_error());  echo "Database connection successful.<br>";
				mysql_select_db($bmdb['database']) 
					or die("Database selection failed.<br>");
		
		
		
		// Create the tables for plugin manager		// IF NOT EXISTS 
		/*$sqltab[] = "CREATE TABLE `pommomod_module` (
						`module_id` MEDIUMINT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
						`module_name` VARCHAR( 100 ) NOT NULL ,
						`module_desc` MEDIUMTEXT NOT NULL ,
						`module_beziehung` ENUM( 'MAIN', 'SUB' ) NOT NULL ,
						`module_active` BOOL NOT NULL,
						`plugin_id` MEDIUMINT NOT NULL
					) ENGINE = innodb;";*/

		$sqltab[] = "DROP TABLE `pommomod_plugin`";
		$sqltab[] = "DROP TABLE `pommomod_plugindata`";
		/*$sqltab[] = "DROP TABLE `pommomod_contains`";*/


		$sqltab[] = "CREATE TABLE IF NOT EXISTS `pommomod_plugin` (
						`plugin_id` MEDIUMINT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
						`plugin_uniquename` VARCHAR( 50 ) NOT NULL UNIQUE, 
						`plugin_name` VARCHAR( 100 ) NOT NULL ,
						`plugin_desc` MEDIUMTEXT NOT NULL , 
						`plugin_active` BOOL NOT NULL , 
						`plugin_super` MEDIUMINT( 9 ) NOT NULL DEFAULT '0' 
					) ENGINE = innodb;";	
					// null bei subrelation means no subrelation!!!
					// VERSION, BILD Versions handling evtl. alle v müssen gleich sein von 1 plugin


/*
		$sqltab[] = "CREATE TABLE `pommomod_contains` (
						`plugin_idmain` MEDIUMINT NOT NULL ,
						`plugin_idsub` MEDIUMINT NOT NULL 
					) ENGINE = innodb;";
*/

		$sqltab[] = "CREATE TABLE IF NOT EXISTS `pommomod_plugindata` (
						`data_id` MEDIUMINT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
						`plugin_id` MEDIUMINT NOT NULL ,
						`data_name` VARCHAR( 50 ) NOT NULL UNIQUE,
						`data_value` MEDIUMTEXT NOT NULL ,
						`data_type` ENUM( 'TXT', 'NUM', 'ENUM' ) NOT NULL ,
						`data_desc` MEDIUMTEXT NULL
					) ENGINE = innodb;";
					
			//User table make user unique!

			//Install Tables
			for ($i = 0; $i < count($sqltab); $i++) {
				$result = mysql_query($sqltab[$i]) or die("Query failed: <b style='color: red'>" . mysql_error()."</b><br>");
						echo "{$sqltab[$i]} "; echo "--->"; echo "<b>". $result. "</b><br>";
			}
				
				
// AUTHENTICATION
$sql[] = "INSERT INTO {$bmdb['prefix']}plugin (plugin_id, plugin_uniquename, plugin_name, plugin_active, plugin_super, plugin_desc) " .
		"VALUES ('33','authentication', 'Authentication Tools', FALSE, '0', 'Authenticate users with varios methods. Install here what methods to use');";
		
	$sql[] = "INSERT INTO {$bmdb['prefix']}plugin (plugin_id, plugin_uniquename, plugin_name, plugin_active, plugin_super, plugin_desc) " .
			"VALUES ('2','simpleldapauth', 'Simple LDAP Authentication', FALSE, '33', 'Authenticate users with a ldap bind to a ldap/ads server. No need to know further user/pass details or query details.');";
	$sql[] = "INSERT INTO {$bmdb['prefix']}plugin (plugin_id, plugin_uniquename, plugin_name, plugin_active, plugin_super, plugin_desc) " .
			"VALUES ('3','queryldapauth', 'LDAP AUth with Query', FALSE, '33', 'Authenticate users with a ldap query on a ldap/ads server. You need to know further user/pass details or query details.');";
	$sql[] = "INSERT INTO {$bmdb['prefix']}plugin (plugin_id, plugin_uniquename, plugin_name, plugin_active, plugin_super, plugin_desc) " .
			"VALUES ('4','dbauth', 'Database Authentication', FALSE, '33', 'Authenticate users on database data. The Administrator can add, delete set permissions for users. To use this activate User management!');";
	$sql[] = "INSERT INTO {$bmdb['prefix']}plugin (plugin_id, plugin_uniquename, plugin_name, plugin_active, plugin_super, plugin_desc) " .
			"VALUES ('5','simpledbldapauth', 'DB and LDAP AUthentication', FALSE, '33', 'Authenticate users on database data && LDAP!');";

		
// USERADMIN	
$sql[] = "INSERT INTO {$bmdb['prefix']}plugin (plugin_id, plugin_uniquename, plugin_name, plugin_active, plugin_super, plugin_desc) " .
		"VALUES ('22','useradmin', 'User Administration', FALSE, '0', 'Use more users in pommo. User Administration Tools.');";
$sql[] = "INSERT INTO {$bmdb['prefix']}plugin (plugin_id, plugin_uniquename, plugin_name, plugin_active, plugin_super, plugin_desc) " .
		"VALUES ('11','mailingqueue', 'Mail Queue', FALSE, '0', 'Write and send the mails separately.');";
$sql[] = "INSERT INTO {$bmdb['prefix']}plugin (plugin_id, plugin_uniquename, plugin_name, plugin_active, plugin_super, plugin_desc) " .
		"VALUES ('44','bouncehandler', 'Bounce Mail Handler', FALSE, '0', 'Write and send the mails separately.');";



/*
//$sql[] = "INSERT INTO {$bmdb['prefix']}contains (plugin_idmain, plugin_idsub) VALUES ('0','33');";
$sql[] = "INSERT INTO {$bmdb['prefix']}contains (plugin_idmain, plugin_idsub) VALUES ('33','2');";
$sql[] = "INSERT INTO {$bmdb['prefix']}contains (plugin_idmain, plugin_idsub) VALUES ('33','3');";
$sql[] = "INSERT INTO {$bmdb['prefix']}contains (plugin_idmain, plugin_idsub) VALUES ('33','4');";
$sql[] = "INSERT INTO {$bmdb['prefix']}contains (plugin_idmain, plugin_idsub) VALUES ('33','5');";
*/


// DATA
$sql[] = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value, data_desc) VALUES ('33','authentication_method', 'TXT', 'dbauth', 'Authentication Method to use.');";
$sql[] = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value, data_desc) VALUES ('2','simpleldap_server','TXT','ldaps://domcon.ict.tuwien.ac.at/', 'Desc');";
$sql[] = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value, data_desc) VALUES ('2','simpleldap_port','TXT','636', 'Desc');";
$sql[] = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value, data_desc) VALUES ('2','simpleldap_dn','TXT','@ICT.TUWIEN.AC.AT', 'Desc');";
$sql[] = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value, data_desc) VALUES ('3','queryldap_server','TXT','ldaps://domcon.ict.tuwien.ac.at/', 'Desc');";
$sql[] = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value, data_desc) VALUES ('3','queryldap_port','TXT','636', 'Desc');";
$sql[] = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value, data_desc) VALUES ('3','queryldap_base','TXT','dn=ICT,dn=TUWIEN,dn=AC,dn=AT', 'Desc');";
$sql[] = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value, data_desc) VALUES ('3','queryldap_dn','TXT','@ICT.TUWIEN.AC.AT', 'Desc');";
$sql[] = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value, data_desc) VALUES ('3','queryldap_user','TXT','myuser', 'Desc');";
$sql[] = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value, data_desc) VALUES ('3','queryldap_pass','TXT','mypassw', 'Desc');";
$sql[] = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value, data_desc) VALUES ('4','db_prefix','TXT','pommomod_', 'Table Prefix');";
$sql[] = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value, data_desc) VALUES ('5','dbldap_prefix','TXT','pommomod_', 'Table Prefix');";





/**
 *			//$sql[] = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value, data_desc) VALUES ('3','queryldap_authmethod','TXT','simple', 'Desc');";

		$sql[] = "INSERT INTO {$bmdb['prefix']}plugin (plugin_id, plugin_name, plugin_version, plugin_active, plugin_category, plugin_desc) " .
				"VALUES ('5','dbldapauth', 'PR0', FALSE, 'SUB', 'Authenticate users with LDAP before validating them in the intern DB. When a user does not exist insert in DB and let Administrator decide what to do.');";
		

 			//$sql[] = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value, data_desc) VALUES ('2','simpleldap_authmethod','TXT','simple', 'Desc');";
			//$sql[] = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value, data_desc) VALUES ('2','simpleldap_base','TXT','dn=ICT,dn=TUWIEN,dn=AC,dn=AT', 'Desc');";		

		$sql[] = "INSERT INTO {$bmdb['prefix']}plugin (plugin_id, plugin_name, plugin_version, plugin_active, plugin_desc) " .
				"VALUES ('1','usermanager', 'PR0', '0', 'Use more Users in your pommo!!! Set rights, groups and other options');";

		$sql[] = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value, data_desc) " .
					"VALUES ('1','user_max','TXT','3', 'Maximal amount of users that can use the system.');";
		
		$sql[] = "INSERT INTO {$bmdb['prefix']}plugin (plugin_id, plugin_name, plugin_version, plugin_active, plugin_desc) VALUES ('2','Authent', 'PR1', '0', 'Authenticate at login time with various authentication methods like DB Users, LDAP, both...');";

		$sql[] = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value, data_desc) VALUES ('1','user_max','TXT','3', 'Maximal amount of users that can use the system.');";
		$sql[] = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value, data_desc) VALUES ('1','user_dbname','TXT','pommo', 'Database name, standard is the value in \$bmdb[\'database\']');";
		$sql[] = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value, data_desc) VALUES ('1','user_dbprefix','TXT','pommouser_');";
		$sql[] = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value, data_desc) VALUES ('1','user_dbuser','TXT','iuser');";
		$sql[] = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value, data_desc) VALUES ('1','user_dbpass','TXT','passw');";

		$sql[] = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value) VALUES ('1','auth_method','ENUM','no,simpleLDAP,queryLDAP,DB');";	// 1 wählen!

		$sql[] = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value) VALUES ('2','ldap_server','TXT','ldaps://domcon.ict.tuwien.ac.at/');";
		$sql[] = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value) VALUES ('2','ldap_port','TXT','636');";
		$sql[] = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value) VALUES ('2','ldap_authmethod','TXT','simple');";
		$sql[] = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value) VALUES ('2','ldap_user','TXT','user');";
		$sql[] = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value) VALUES ('2','ldap_pass','TXT','passw');";
		$sql[] = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value) VALUES ('2','ldap_base','TXT','dn=ICT,dn=TUWIEN,dn=AC,dn=AT');";
		$sql[] = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value) VALUES ('2','ldap_dn','TXT','@ICT.TUWIEN.AC.AT');";


$sql[] = "INSERT INTO {$bmdb['prefix']}plugin (plugin_id, plugin_name, plugin_version, plugin_active, plugin_category, plugin_desc) VALUES ('20','Wollmilch', 'PR0', '0', 'MAIN', 'Super eierlegende Wollmilch Sau Plugin!');";
$sql[] = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value, data_desc) VALUES ('20','woll_wichtigedaten','TXT','blah', 'blah');";
$sql[] = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value, data_desc) VALUES ('20','woll_telefon','TXT','1234567', 'blah');";
$sql[] = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value, data_desc) VALUES ('20','woll_geheimevariable','TXT','hehehe', 'blah');";
$sql[] = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value, data_desc) VALUES ('20','woll_frage','TXT','antwort', 'blah');";
$sql[] = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value, data_desc) VALUES ('20','woll_variable','TXT','wert', 'blah');";
$sql[] = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value, data_desc) VALUES ('20','woll_blah','TXT','von blah kommt blah', 'blah');";

$sql[] = "INSERT INTO {$bmdb['prefix']}plugin (plugin_id, plugin_name, plugin_version, plugin_active, plugin_category, plugin_desc) VALUES ('21','Nada', 'PR0', '0', 'MAIN', 'Kann nix und macht nix..');";
$sql[] = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value, data_desc) VALUES ('21','nada_var1','TXT','wert 1', 'blah');";
$sql[] = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value, data_desc) VALUES ('21','nada_var2','TXT','wert2', 'blah');";
$sql[] = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value, data_desc) VALUES ('21','nada_var3','TXT','3', 'blah');";
$sql[] = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value, data_desc) VALUES ('21','nada_var4','TXT','wertvier', 'blah');";

**/

		
				// Execute queries 
				for ($i = 0; $i < sizeof($sql); $i++) {
					
					$result = mysql_query($sql[$i]) or die("Query failed: " . mysql_error()."<br>");
					echo $sql[$i]; echo " --->"; echo "<b>". $result. "</b><br>";
				}
				
				echo "<b>Install complete.</b><br><br>";
				mysql_close($link);
		
//} //Problems??? in else

define('_IS_VALID', FALSE);

?>

