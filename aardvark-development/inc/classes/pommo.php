<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2006 Brice Burgess / All Rights Reserved    
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
 * Common class. Holds Configuration values, authentication state, etc.. (revived from session)
*/

class Pommo {
	var $_revision = '26'; // poMMo's revision #

	var $_dbo; // holds the database object
	var $_logger; // holds the logger (messaging) object
	var $_auth; // holds the authentication object

	var $_baseDir; // poMMo's base directory (e.g. /home/www/site1/pommo/)
	var $_baseUrl; // poMMo's base URL (e.g. http://www.site1.com/pommo/) - null = autodetect
	var $_workDir; // poMMo's working (writable) directory (e.g. /home/www/site1/pommo/cache/)
	var $_hostname; // WebServer hostname (e.g. www.site1.com) - null = autodetect
	var $_hostport; // WebServer port (e.g. 80) - null = autodetect
	var $_language; // language to translate to (via Pommo::_T())
	var $_debug; // debug status, either 'on' or 'off'
	var $_verbosity; // logging + debugging verbosity (1(most)-3(less|default))

	var $_config; // configuration array to hold values loaded from the DB
	var $_data; // Used to hold temporary data (such as an uploaded file's contents).. accessed via set (sets), get (returns), clear(deletes)
	var $_state; // Used to hold the state of pages -- e.g. variables that should be stored like 'limit, order, etc'

	// default constructor
	function Pommo($baseDir) {
		$this->_baseDir = $baseDir;
		$this->preInit();

		$this->_config = array ();
		$this->_auth = null;
	}

	// preInit() populates poMMo's core with values from config.php 
	//  initializes the logger + database
	function preInit() {
		// read in config.php (configured by user)
		// TODO -> write a web-based frontend to config.php creation
		$config = PommoHelper::parseConfig($this->_baseDir . 'config.php');
		
		// check to see if config.php was "properly" loaded
		if (count($config) < 5) {
			die('<img src="themes/shared/images/icons/alert.png" align="middle"><br><br>Could not read config.php');
		}

		$this->_workDir = (empty($config['workDir'])) ? $this->_baseDir . 'cache' : $config['workDir'];
		$this->_hostport = (empty($config['hostport'])) ? $_SERVER['SERVER_PORT'] : $config['hostport'];
		$this->_hostname = (empty($config['hostname'])) ? $_SERVER['HTTP_HOST'] : $config['hostname'];
		$this->_http = ((@strtolower($_SERVER['HTTPS']) == 'on') ? 'https://' : 'http://') . $this->_hostname;
		$this->_debug = (($config['debug'] == 'on') || ($config['debug'] == 'off')) ? $config['debug'] : 'off';
		$this->_verbosity = (empty($config['verbosity'])) ? 3 : $config['verbosity'];
		$this->_language = (empty($config['lang'])) ? 'en' : strtolower($config['lang']);
		
		// include translation (l10n) methods if language is not English
		if ($this->_language != 'en') {
			$this->requireOnce($this->_baseDir . 'inc/helpers/l10n.php');
			PommoHelperL10n::init($this->_language, $this->_baseDir);
		}
		
		// set base URL (e.g. http://mysite.com/news/pommo => 'news/pommo/')
		// TODO -> provide validation of baseURL ?
		if (isset ($config['baseURL'])) {
			$this->_baseUrl = $config['baseURL'];
		} else {
			// If we're called from an outside (embedded) script, read baseURL from "last known good".
			// Else, set it based off of REQUEST
			if (defined('_poMMo_embed')) {
				$this->requireOnce($this->_baseDir . 'inc/helpers/maintenance.php');
				$this->_baseUrl = PommoHelperMaintenance :: rememberBaseURL();
			} else {
				$baseUrl = preg_replace('@/(inc|setup|user|install|admin(/subscribers|/user|/mailings|/setup)?)$@i', '', dirname($_SERVER['PHP_SELF']));
				$this->_baseUrl = ($baseUrl == '/') ? $baseUrl : $baseUrl . '/';
			}
		}
		

		// set the current "section" -- should be "user" for /user/* files, "mailings" for /admin/mailings/* files, etc. etc.
		$this->_section = preg_replace('@^admin/?@i', '', str_replace($this->_baseUrl, '', dirname($_SERVER['PHP_SELF'])));

		// initialize database link
		$this->requireOnce($this->_baseDir . 'inc/lib/safesql/SafeSQL.class.php');
		$this->requireOnce($this->_baseDir . 'inc/classes/db.php');
		$this->_dbo = new PommoDB($config['db_username'], $config['db_password'], $config['db_database'], $config['db_hostname'], $config['db_prefix']);

		// initialize logger
		$this->requireOnce($this->_baseDir . 'inc/classes/log.php');
		$this->_logger = new PommoLog($this->_verbosity); // NOTE -> this clears messages that may have been retained (not outputted) from logger.

		// if debugging is set in config.php, enable debugging on the database.
		if ($this->_debug == 'on') {
			$this->_dbo->debug(TRUE);
		}

	}

	/** 
	 * init -> called by page to load page state, populate config, and track authentication. 
	 *	valid args [ passed as Pommo::init(array('arg' => val, 'arg2' => val)) ] ->
	 *		authLevel	:	check that authenticated permission level is at least authLevel (non authenticated == 0). die if not high enough. [default: 1]
	 *		keep		:	keep data stored in session. [default: false]
	 *		session		:	explicity set session name. [default: null]
	 * 		install		:	bypass loading of config/version checking [default: false]
     */

	function init($args = array ()) {

		$defaults = array (
			'authLevel' => 1,
			'keep' => FALSE,
			'noSession' => FALSE,
			'sessionID' => NULL
		);
	
		// merge submitted parameters
		$p = PommoAPI :: getParams($defaults, $args);
		
		// make sure workDir is writable
		if (!is_dir($this->_workDir . '/pommo/smarty') && !defined('_poMMo_support')) {
			if (!is_dir($this->_workDir))
				$this->kill(sprintf($this->_T('Work Directory (%s) not found! Make sure it exists and the webserver can write to it. You can change its location from the config.php file.'), $this->_workDir));
			if (!is_writable($this->_workDir))
				$this->kill(sprintf($this->_T('Cannot write to Work Directory (%s). Make sure it has the proper permissions.'), $this->_workDir));
			if (ini_get('safe_mode') == "1")
				$this->kill(sprintf($this->_T('Working Directory (%s) cannot be created under PHP SAFE MODE. See Documentation, or disable SAFE MODE.'), $this->_workDir));
			if (!is_dir($this->_workDir . '/pommo'))
				if (!mkdir($this->_workDir . '/pommo'))
					$this->kill($this->_T('Could not create directory') . ' ' . $this->_workDir . '/pommo');
			if (!mkdir($this->_workDir . '/pommo/smarty'))
				$this->kill($this->_T('Could not create directory') . ' ' . $this->_workDir . '/pommo/smarty');
		}

		// read configuration data
		$this->_config = PommoAPI :: configGetBase();
		
		
		// Bypass SESSION creation, reading of config, authentication checks and return
		//  if 'noSession' passed
		if ($p['noSession'])
			return;

		// start the session
		if (!empty($p['sessionID']))
			session_id($p['sessionID']);
		session_start();

		// create placeholder for $_SESSION['pommo'] if this is a new session
		if (empty ($_SESSION['pommo'])) {
			$_SESSION['pommo'] = array (
				'auth' => array (),
				'data' => array ()
			);
		}

		// check authentication levels
		$this->requireOnce($this->_baseDir . '/inc/classes/auth.php');
		$this->_auth = new PommoAuth(array (
			'requiredLevel' => $p['authLevel']
		));

		// clear _data unless 'keep' is true. Create SESSION reference.
		if (!$p['keep']) {
			$_SESSION['pommo']['data'] = array ();
		}
		$this->_data = & $_SESSION['pommo']['data'];

	}
	
	// reload base configuration from database
	function reloadConfig() {
		return $this->_config = PommoAPI :: configGetBase(TRUE);
	}
	
	/**
	 *  Translation (l10n) Function
	 */
	 
	 function _T($msg) {
		return ($GLOBALS['pommol10n']) ? PommoHelperL10n::translate($msg) : $msg;
	}

	function _TP($msg, $plural, $count) { // for plurals
		return ($GLOBALS['pommol10n']) ? PommoHelperL10n::translatePlural($msg, $plural, $count) : $msg;
	}


	/**
	 *  _data Handler functions ==>
	 *    $pommo->_data is a reference to $_SESSION['pommo']['data'], an array in the Session
	 *    which holds any data we'd like to persist through pages. This array is cleared by default 
	 *    unless explicity saved by passing the 'keep' argument to the $pommo->init() function.
	 */

	function set($value) {
		if (!is_array($value))
			$value = array (
				$value
			);
		return (empty ($this->_data)) ? $this->_data = $value : $this->_data = array_merge($this->_data, $value);
	}

	function & get($name = NULL) {
		if ($name) {
			return (empty ($this->_data[$name])) ? false : $this->_data[$name];
		}
		return $this->_data;
	}

	/**
	 *  Page State Functions ==>
	 */

	function stateInit($name = 'default', $state = array ()) {
		if (empty ($_SESSION['state_' . $name])) {
			$_SESSION['state_' . $name] = $state;
		}
		$this->_state = & $_SESSION['state_' . $name];
		return;
	}

	// used to access or set state Vars
	// TODO -> remove str2db (dbSanitize) when queries are made safe by DB abstraction class
	function stateVar($varName, $varValue = NULL) {
		if (!empty ($varValue)) {
			$this->_state[$varName] = dbSanitize($varValue);
		}
		return (isset ($this->_state[$varName])) ? $this->_state[$varName] : false;
	}
	
	// redirect, require, kill base Functions
	
	function redirect($url, $msg = NULL, $kill = true) {
	global $pommo;
		// adds http & baseURL if they aren't already provided... allows code shortcuts ;)
		//  if url DOES NOT start with '/', the section will automatically be appended
		if (!preg_match('@^https?://@i', $url)) {
			if (strpos($url, $pommo->_baseUrl) === false) { 
				if (substr($url, 0, 1) != '/') {
					if ($pommo->_section != 'user' && $pommo->_section != 'admin') {
						$url = $pommo->_http . $pommo->_baseUrl . 'admin/' . $pommo->_section . '/' . $url;
					} else {
						$url = $pommo->_http . $pommo->_baseUrl . $pommo->_section . '/' . $url;
					}
				} else {
					$url = $pommo->_http . $pommo->_baseUrl . str_replace($pommo->_baseUrl,'',substr($url,1)); 
				}
			} else {
				$url = $pommo->_http . $url;
			}
		}
		header('Location: ' . $url);
		if ($kill)
			if ($msg)
				$pommo->kill($msg);
			else
				$pommo->kill($pommo->_T('Redirecting, please wait...'));
		return;
	}
	
	// kill => used to terminate a script
	function kill($msg = NULL, $backtrace = FALSE) {
		global $pommo;
		
		// if nothing is in the output buffer, create a valid XHTML page.
		if (!ob_get_length()) {
			echo ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">');
			echo ('<title>poMMo Error</title>'); // Very basics added for valid output
		}

		// output passed message
		if ($msg)
			echo '<div class="error fatal"><img src="' . $pommo->_baseUrl . 'themes/shared/images/icons/alert.png" alt="alert icon" /> ' . $msg . '</div>';

		// output debugging info if enabled (in config.php)
		if ($pommo->_debug == 'on' && $pommo->_section != 'user') { // don't debug if section == user.'
			if (is_object($pommo)) {
				$pommo->requireOnce($pommo->_baseDir . 'inc/helpers/debug.php');
				$debug = new PommoHelperDebug();
				$debug->bmDebug();
			}
		}
		
		
		if ($backtrace) {
			$backtrace = debug_backtrace();
			echo @ '<h2>BACKTRACE</h2>'
				.'<p>'.@str_ireplace($pommo->_baseDir,'',$backtrace[1]['file']).':'.$backtrace[1]['line'].' '.$backtrace[1]['function'].'()</p>'
				.'<p>'.@str_ireplace($pommo->_baseDir,'',$backtrace[2]['file']).' '.$backtrace[2]['function'].'()</p>'
				.'<p>'.@str_ireplace($pommo->_baseDir,'',$backtrace[3]['file']).' '.$backtrace[3]['function'].'()</p>';
		}

		// print and clear output buffer
		ob_end_flush();
		
		// kill script
		die();
	}
	
	// faster performance than standard require_once
	// TODO -> extend function to make "smart" -- auto paths, jail to poMMo directory, etc.
	function requireOnce($file) {
		static $files;

		if (!isset ($files[$file])) {
			require ($file);
			$files[$file] = TRUE;
		}
	}
	
	
	
}
?>
