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

/** 
 * Common class. Stored in session. Holds Configuration values, authentication state, etc..
*/

class Common {

	var $_dbo; // the database object

	var $_config; // configuration array to hold values loaded from the DB
	var $_authenticated; // TRUE if user has successfully logged on.
	var $_data; // Used to hold temporary data (such as an uploaded file's contents).. accessed via dataSet (sets), dataGet (returns), dataClear (deletes)
	var $logger; // holds the logger class object
	
	// default constructor
	function Common($arg = NULL) {
		$this->_dbo = FALSE;
		$this->_config = array ();
		$this->_authenticated = FALSE;
		$this->logger = new bmLogger();
	}

	// opens a connection to the database, returns the object. *removed* If TRUE is passed, a new connection will be made. Else, it will attempt to open an old one.
	function & openDB() {
		// don't create a new link if one already exists that we can use. Useful for pconnects + assigning dbo after fireup() -- as fireup loads a dbo to check versions
		// drop mysql_ping.. PHP 4.3 required
		//if (is_resource($this->_dbo->_link) && mysql_ping($this->_dbo->_link))  
		if (is_resource($this->_dbo->_link))
			return $this->_dbo;
		
		global $bmdb;
					
		$this->_dbo = new dbo($bmdb['username'], $bmdb['password'], $bmdb['database'], $bmdb['hostname'], $bmdb['prefix']);
			
		if (bm_debug == 'on') {
			$this->_dbo->debug(TRUE);
		}
		
		return $this->_dbo;
	}

	// Loads all autoloading config data from DB. Returns true if configuration data has been set to _config array
	function loadConfig() {
		$dbo = & $this->openDB();
		$dbo->dieOnQuery(FALSE);	
	

		$sql = 'SELECT * FROM '.$dbo->table['config'].' WHERE autoload=\'on\'';
		if ($dbo->query($sql)) {
			while ($row = mysql_fetch_assoc($dbo->_result))
				$this->_config[$row['config_name']] = $row['config_value'];
		}
		$dbo->dieOnQUery(TRUE);
		return (!empty ($this->_config['version'])) ? true : bmKill('poMMo does not appear to be set up.' .
				'Have you <a href="'.bm_baseUrl.'/install/install.php">Installed?</a>');
	}
	
	// Gets specified config value(s) from the DB. Pass a single or array of config_names
	function getConfig($arg) {
		$dbo = & $this->openDB();
		$dbo->dieOnQuery(FALSE);
		if (!is_array($arg))
			$arg = array($arg);
			
		$config = array();
		if ($arg[0] == 'all')
			$sql = 'SELECT config_name,config_value FROM '.$dbo->table['config'];
		else
			$sql = 'SELECT config_name,config_value FROM '.$dbo->table['config'].' WHERE config_name IN (\''.implode('\',\'',$arg).'\')';
		
		while ($row = $dbo->getRows($sql)) 
				$config[$row['config_name']] = $row['config_value'];
	
		$dbo->dieOnQUery(TRUE);
		return $config;
	}

	// Check if user has sucessfully logged on.
	function isAuthenticated() {
		return ($this->_authenticated) ? true : false;
	}

	// Set's authentication variable. TRUE = authenticated, FALSE/NULL = NOT... 
	function setAuthenticated($var) {
		return ($this->_authenticated = $var) ? true : false;
	}

	// PHASE OUT data*()   -- favor > store(), get(), keep()
	function dataSet($val) {
		return ($this->_data = $val) ? true : false;
	}
	
	function & dataGet() {
		if (empty ($this->_data))
			$this->_data = NULL;
		return $this->_data;
	}

	// PHASE OUT -> rename to clear  when all references to data*() are gone
	function dataClear() {
		return ($this->_data = array()) ? true : false;
	}
	
	function set($value) {
		if (!is_array($value))
			$value = array($value);
		return (empty($this->_data)) ? $this->_data = $value : $this->_data = array_merge($this->_data,$value);
	}
	
	function &get() {
		return $this->_data;
	}
}
?>