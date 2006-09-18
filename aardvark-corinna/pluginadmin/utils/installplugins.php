<?php
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

/**
 * INSTALL PLUGINS (deactivated) in the database with its tables and standard configuration
 * To alter the standard configuration visit <pommourl>/pluginadmin/plugins.php
 * 
 * Available Plugins:
 * PluginRegistry		-	Manages Plugin configurations, Metaplugin, 
 * 							uses tables [prefix_]plugin and [prefix_]plugindata
 * 
 * Auth			 		-	Authenticates a login attempt via LDAP oder standard 
 * 							(look in user table if there is a user named like this)
 * 							no tables (config row in plugin table)
 * Usermanager			-	Permits pommo Administrators to delegate some of the functions to other users
 * 							table [prefix_]user
 * FooPlugin			-	Don't know what this one does'
 * 							no tables
 * 
 */


// If you want other values define here
//$bmdb['pluginprefix'] = "";
/*	$bmdb['username'] = 'pommo-user';
	$bmdb['password'] = 'pomodoro';
	$bmdb['hostname'] = 'localhost';
	$bmdb['database'] = 'pommo'; 
	$bmdb['prefix'] = 'pommo_';
	//define('bm_lang','en');
	//define('bm_debug','off');
	//define('bm_verbosity',3);
	//define('bm_workDir','/path/to/pommoCache');
	//define('bm_baseUrl', '/mysite/newsletter');  
*/

// Here the Database configuration user, pass table prefix and so on comes from
include_once('../../config.php');
// anderer Prefix will ich
$bmdb['prefix'] = 'pommomod_';


		// alter this db connection -> dbo
		$link = mysql_connect($bmdb['hostname'], $bmdb['username'], $bmdb['password'])
			or die("No DB connection: " . mysql_error());  echo "Database connection successful.<br>";
		mysql_select_db($bmdb['database']) 
			or die("Database selection failed.<br>");


// Create the tables for plugin manager (plugin.php)
// IF NOT EXISTS 
$sqltab[0] = "CREATE TABLE {$bmdb['prefix']}plugin (
				`plugin_id` MEDIUMINT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
				`plugin_name` VARCHAR( 100 ) NOT NULL ,
				`plugin_version` VARCHAR( 10 ) NOT NULL ,
				`plugin_active` BOOL NOT NULL ,
				`plugin_desc` MEDIUMTEXT NOT NULL
			) ENGINE = innodb;";

$sqltab[1] = "CREATE TABLE `pommomod_plugindata` (
				`data_id` MEDIUMINT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
				`plugin_id` MEDIUMINT NOT NULL ,
				`data_name` VARCHAR( 50 ) NOT NULL ,
				`data_value` MEDIUMTEXT NOT NULL ,
				`data_type` ENUM( 'TXT', 'NUM', 'OBJ' ) NOT NULL
			) ENGINE = innodb;";

//$sqltab[2] = "CREATE TABLE IF NOT EXISTS {$bmdb['prefix']}user ";


		//Install Tables
		$result = mysql_query($sqltab[0]) or die("Query failed: " . mysql_error()."<br>");
				echo "{$sqltab[0]} "; echo "--->"; echo "<b>". $result. "</b><br>";
		$result = mysql_query($sqltab[1]) or die("Query failed: " . mysql_error()."<br>");
				echo "{$sqltab[1]} "; echo "--->"; echo "<b>". $result. "</b><br>";
		/*$result = mysql_query($sqltab[2]) or die("Anfrage fehlgeschlagen: " . mysql_error()."<br>");
				echo "---"; echo "<b>". $result. "</b><br>";*/
		
		
		
// install standard data for plugins (4 test values)
$sql[0] = "INSERT INTO {$bmdb['prefix']}plugin (plugin_id, plugin_name, plugin_version, plugin_active, plugin_desc) VALUES ('1','Auth', 'PR1', '0', 'Authenticate at login time with various authentication methods like LDAP...');";
$sql[1] = "INSERT INTO {$bmdb['prefix']}plugin (plugin_id, plugin_name, plugin_version, plugin_active, plugin_desc) VALUES ('2','Benutzerverwaltung', 'PR0', '0', 'Use more Users in your pommo!!!');";
$sql[2] = "INSERT INTO {$bmdb['prefix']}plugin (plugin_id, plugin_name, plugin_version, plugin_active, plugin_desc) VALUES ('3','Wollmilch', 'PR0', '0', 'Super eierlegende Wollmilch Sau Plugin!');";
$sql[3] = "INSERT INTO {$bmdb['prefix']}plugin (plugin_id, plugin_name, plugin_version, plugin_active, plugin_desc) VALUES ('5','Nada', 'PR0', '0', 'Kann nix und macht nix..');";
 
 
// standard data (22 test values)
//maybe add a long and detailed description for the datum?????
$sql[4]  = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value) VALUES ('1','ldap_server','TXT','ldaps://domcon.ict.tuwien.ac.at/');";
$sql[5]  = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value) VALUES ('1','ldap_port','TXT','636');";
$sql[6]  = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value) VALUES ('1','ldap_authmethod','TXT','simple');";
$sql[7]  = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value) VALUES ('1','ldap_user','TXT','user');";
$sql[8]  = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value) VALUES ('1','ldap_pass','TXT','passw');";
$sql[9]  = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value) VALUES ('1','ldap_base','TXT','dn=ICT,dn=TUWIEN,dn=AC,dn=AT');";
$sql[10] = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value) VALUES ('1','ldap_dn','TXT','@ICT.TUWIEN.AC.AT');";

$sql[11]  = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value) VALUES ('2','user_max','TXT','3');";
$sql[12]  = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value) VALUES ('2','user_dbname','TXT','pommo');";
$sql[13]  = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value) VALUES ('2','user_dbprefix','TXT','pommouser_');";
$sql[14]  = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value) VALUES ('2','user_dbuser','TXT','iuser');";
$sql[15]  = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value) VALUES ('2','user_dbpass','TXT','passw');";

$sql[16]  = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value) VALUES ('3','woll_wichtigedaten','TXT','blah');";
$sql[17]  = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value) VALUES ('3','woll_telefon','TXT','1234567');";
$sql[18]  = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value) VALUES ('3','woll_geheimevariable','TXT','hehehe');";
$sql[19]  = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value) VALUES ('3','woll_frage','TXT','antwort');";
$sql[20]  = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value) VALUES ('3','woll_variable','TXT','wert');";
$sql[21]  = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value) VALUES ('3','woll_blah','TXT','von blah kommt blah');";

$sql[22]  = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value) VALUES ('5','nada_var1','TXT','wert 1');";
$sql[23]  = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value) VALUES ('5','nada_var2','TXT','wert2');";
$sql[24]  = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value) VALUES ('5','nada_var3','TXT','3');";
$sql[25]  = "INSERT INTO {$bmdb['prefix']}plugindata (plugin_id, data_name, data_type, data_value) VALUES ('5','nada_var4','TXT','wertvier');";


		// Execute queries 
		for ($i = 0; $i <= 25; $i++) {
			
			$result = mysql_query($sql[$i]) or die("Query failed: " . mysql_error()."<br>");
			echo $sql[$i]; echo " --->"; echo "<b>". $result. "</b><br>";
		}
		
		echo "<b>Install complete.</b><br><br>";
		mysql_close($link);


define('_IS_VALID', FALSE);

?>

