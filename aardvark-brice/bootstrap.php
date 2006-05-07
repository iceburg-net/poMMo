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
 * Bootstrapping
*/
define('bm_baseDir', dirname(__FILE__));
define('bm_baseUrl', preg_replace('@/(/inc|setup|user|install|admin(/subscribers|/user|/mailings|/setup)?)$@i', '', dirname($_SERVER['PHP_SELF'])));
define('bm_http', (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 'https://' : 'http://').$_SERVER['HTTP_HOST']);
define('pommo_revision', '20');

// get current "section" -- should be "user" for /user/* files, "mailings" for /admin/mailings/* files, etc. etc.
define('bm_section',preg_replace('@^/?(admin)?/@i','',str_replace(bm_baseUrl,'',dirname($_SERVER['PHP_SELF']))));

@include(bm_baseDir.'/config.php');
defined('bm_lang') or die('<img src="'.bm_baseUrl.'/themes/shared/images/icons/alert.png" align="middle"><br><br>
Language not defined! Have you installed the config.php file? See the included config.sample.php for an example.
<br><br>
DE translation, etc.
');

/** 
 * Include globally available functions and classes
*/

require(bm_baseDir.'/inc/lib.common.php');
require(bm_baseDir.'/inc/class.common.php');
require(bm_baseDir.'/inc/class.dbo.php');
require(bm_baseDir.'/inc/class.logger.php');


/** 
 * Test basic functionalities
*/
if (!defined('bm_workDir'))
	define('bm_workDir',bm_baseDir.'/cache');
	
if (!is_dir(bm_workDir.'/pommo/smarty')) {
	if (!is_dir(bm_workDir))
		bmKill('<strong>'.bm_workDir.'</strong> : '._T('Work Directory not found! Make sure it exists and the webserver can write to it. You can change its location from the config.php file.'));
	if (!is_writable(bm_workDir))
		bmKill('<strong>'.bm_workDir.'</strong> : '._T('Webserver cannot write to Work Directory. Make sure it has the proper permissions.'));
	if (!is_dir(bm_workDir.'/pommo'))
		if (!mkdir(bm_workDir.'/pommo'))
			bmKill(_T('Could not create directory'). ' '.bm_workDir.'/pommo');
	if (!mkdir(bm_workDir.'/pommo/smarty'))
		bmKill(_T('Could not create directory'). ' '.bm_workDir.'/pommo/smarty');
} 

// NOTE -> this is meant to be in class.template.php! -- however, it must remain
// here until smarty migration is complete or str2db is replaced using:
//  a) ezSQL DBO abstraction
//  b) Monte's safeSQL class

		// strip out those bastard slashes
		if (get_magic_quotes_gpc()) {
			if (!empty($_POST))
				$_POST = bmStripper($_POST);
			if (!empty($_GET))
				$_GET = bmStripper($_GET);
		}

/** 
 * fireup -> called by page to load site state. 
 *   valid args:
 *     secure - check authentication state. die if not authenticated
 *     dataSave - keeps the _data array loaded in the session. It is cleared by default.
 *     loadConfig - loads the configuration file (useful during config changes)
*/

// note: called by reference so it doesn't make a copy of its return object (held in session)
function & fireup() {

	
	// get list of arguments to set preinit, and postinit environment
	$arg_list = func_get_args(); // can this be copied below in place of $arg_list???
	$skip = FALSE;
	foreach (array_keys($arg_list) as $key) {
		$arg = & $arg_list[$key];
		switch ($arg) {
			case 'secure' :
				$bm_secure = TRUE;
				break;
			case 'dataSave' : // PHASE OUT -> dataSave
			case 'keep' :
				$bm_dataSave = TRUE;
				break;
			case 'loadConfig' :
				$bm_loadConfig = TRUE;
				break;
			case 'sessionName' : // if provided and parent script has $bm_sessionName set, session ID will be set to $bm_sessionName
				global $bm_sessionName;
				break;
			case  'install': // bypasses loading of config/version checking by returning below.
				return new Common();
			default :
				die('Unknown arg ('.$arg.') passed to fireup() in '.$_SERVER['PHP_SELF']);
		}
	}
	
	// start the session
	if (!empty($bm_sessionName))
		session_id($bm_sessionName);
	session_start();
	
	// load common class. If $bm_preInit exists, the common class in $_SESSION is overwritten
	if (isset ($_SESSION["poMMo"])) 
		$poMMo = & $_SESSION["poMMo"];
	else {
		$poMMo = new Common();
		$poMMo->loadConfig();
	}	

	// check that config has been loaded
	if (empty($poMMo->_config) || count($poMMo->_config) < 5) {
		$poMMo->loadConfig();
		if (count($poMMo->_config) < 5)
			bmKill(sprintf(_T('Error loading configuration. Have you %s installed %s ?'),
			'<a href="'.bm_baseUrl.'/install/install.php">',
			'</a>'));
	}
		
	// checks version of DB against file version
	$dbo = $poMMo->openDB();
	$dbo->dieOnQuery(FALSE);
	$sql = 'SELECT config_value FROM '.$dbo->table['config'].' WHERE config_name=\'revision\'';
	$revision = $dbo->query($sql,0);
	if (!$revision)
		bmKill(sprintf(_T('Error loading configuration. Have you %s installed %s ?'),
			'<a href="'.bm_baseUrl.'/install/install.php">',
			'</a>'));
	elseif (pommo_revision != $dbo->query($sql,0))
		bmKill(sprintf(_T('Version Mismatch. Have you %s upgraded %s ?'),
		'<a href="'.bm_baseUrl.'/install/upgrade.php">',
		'</a>'));
	$dbo->dieOnQuery(TRUE);
	
	// load common class into session
	$_SESSION["poMMo"] = & $poMMo;

	if (isset($bm_secure) && !$poMMo->isAuthenticated() )
		bmKill(sprintf(_T('Denied access. You must %s logon %s to access this page...'),
		 '<a href="'.bm_baseUrl.'/index.php">',
		'</a>'));
		
	if (!isset($bm_dataSave)) // PHASE OUT -> when _messages gone, perform actual dataClear func..
		$poMMo->dataClear();
		
	if (isset($bm_loadConfig))
		$poMMo->loadConfig();

	// returns a copy of the object
	return $poMMo;
}
?>