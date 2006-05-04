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

// deeply (recursive) strip an array of slashes added by magic quotes. --  usually called on _GET && _POST
//  when a HTML Form is prepared (via class.template.php)
function bmStripper($input) {
	if (is_array($input)) {
		foreach ($input as $key => $value) {
			$input[$key] = bmStripper($value);
		}
		return $input;
	} else {
		return stripslashes($input);
	}
}

// spawns a page in the background, used by mail processor.
function bmHttpSpawn($page) {
	$errno = "";
	$errstr = "";
	$cbSock = fsockopen($_SERVER['HTTP_HOST'], $_SERVER['SERVER_PORT'], $errno, $errstr, 5);
	if ($cbSock) {
		fwrite($cbSock, "GET " . $page . " HTTP/1.0\r\n" . "Host: {$_SERVER['HTTP_HOST']}\r\n\r\n");
	} else
		return false;
	return true;
		/*die('Could not spawn background page. Errno = ' .
		$errno . ', Errstr = ' . $errstr); */
}

function bmRedirect($url, $msg = NULL, $kill = true) {

	// adds http & baseURL if they aren't already provided... allows code shortcuts ;)
	//  if url DOES NOT start with '/', the section will automatically be appended
	if (!preg_match('@^https*://@i', $url)) {
		if (!preg_match('@^' . bm_baseUrl . '@i', $url)) {
			if (substr($url, 0, 1) != '/') {
				if (bm_section != 'user') {
					$url = bm_http . bm_baseUrl . '/admin/' . bm_section . '/' . $url;
				} else {
					$url = bm_http . bm_baseUrl . '/' . bm_section . '/' . $url;
				}
			} else {
				$url = bm_http . bm_baseUrl . '/' . $url;
			}
		} else {
			$url = bm_http . $url;
		}
	}
	header('Location: ' . $url);
	if ($kill)
		if ($msg)
			bmKill($msg);
		else
			bmKill(_T('Redirecting, please wait...'));
	return;
}

function bmKill($msg = NULL) {
	
	if (bm_debug == 'on' && bm_section != 'user') { // don't debug if section == user.'
		require (bm_baseDir . '/inc/lib.debugger.php');
		bmDebug();
	}
	
	if ($msg)
		die('<div style="float: left;"><img src="' . bm_baseUrl . '/img/icons/alert.png" align="bottom"></div>' . $msg);
	die();
}

/**
 *  l10n Methods
 * 
 *   for now text domain is set to "messages", and the language .mo
 *     file must exist as /inc/languages/[language]/LC_MESSAGES/messages.mo
 * 
 *   Use _T('string');  to translate a string -- yes this may be "ugly" for now... plural wrapper in gettext.inc
 */
$l10n = FALSE;

// for speed, don't load PHP gettext libraries if language is english or not set...
// TODO --> SET LANGUAGE VIA INSTALL SCRIPT _GET / CONFIGURATION / ?GET
if (defined('bm_lang') && trim(bm_lang) != '' && bm_lang != 'en') {
	if (!is_file(bm_baseDir . '/language/' . bm_lang . '/LC_MESSAGES/pommo.mo'))
		bmKill(bm_lang .
		' -> language not found/supported');

	require (bm_baseDir . '/inc/gettext/gettext.inc');

	$domain = 'pommo';
	$encoding = 'UTF-8';
	$l10n = TRUE;

	T_setlocale(LC_MESSAGES, bm_lang);
	T_bindtextdomain($domain, bm_baseDir . '/language');
	T_bind_textdomain_codeset($domain, $encoding);
	T_textdomain($domain);
}

// translation functions. return unmodified string if l10n is off.

function _T($msg) {
	global $l10n;
	return ($l10n) ? T_($msg) : $msg;
}

function _TP($msg, $plural, $count) { // for plurals
	global $l10n;
	return ($l10n) ? T_ngettext($msg, $plural, $count) : $msg;
}


/**
 *  SMARTY TEMPLATE FUNCTIONS
 */

function & bmSmartyInit() {
	global $poMMo;
	require (bm_baseDir . '/inc/class.template.php');
	$smarty = new bTemplate();

	// ___ SETUP TEMPLATE ___

	// set theme
	//$theme = $poMMo->_config['theme'];
	$theme = 'default';

	// set directories
	$smarty->_themeDir = bm_baseDir . '/themes/';
	$smarty->template_dir = $smarty->_themeDir . $theme;
	$smarty->config_dir = $smarty->_themeDir . $theme . '/configs';
	$smarty->cache_dir = bm_workDir . '/pommo/smarty';
	$smarty->compile_dir = bm_workDir . '/pommo/smarty';
	$smarty->plugins_dir = array (
			'plugins', // the default under SMARTY_DIR
	bm_baseDir . '/inc/smarty-plugins/gettext'
	);

	// set variables available to template
	$smarty->assign('url', array (
		'theme' => array (
			'shared' => bm_baseUrl . '/themes/shared',
			'this' => bm_baseUrl . '/themes/' . $theme
		),
		'base' => bm_baseUrl,
		'http' => bm_http
	));

	// config is not loaded during install... check for it first!
	if (isset ($poMMo->_config['version'])) {
		$smarty->assign('config', array (
			'app' => array (
				'path' => bm_baseDir,
				'version' => $poMMo->_config['version'],
				'weblink' => '<a href="http://pommo.sourceforge.net/">' . _T('poMMo Website'
			) . '</a>'
		), 'site_name' => $poMMo->_config['site_name'], 'site_url' => $poMMo->_config['site_url'], 'list_name' => $poMMo->_config['list_name'], 'admin_email' => $poMMo->_config['admin_email'], 'demo_mode' => $poMMo->_config['demo_mode']));
	} else {
		$smarty->assign('config', array (
			'app' => array (
				'path' => bm_baseDir,
				'weblink' => '<a href="http://pommo.sourceforge.net/">' . _T('poMMo Website'
			) . '</a>'
		)));
	}

	// set gettext overload functions (see block.t.php...)
	$smarty->_gettext_func = '_T'; // calls _T($str)
	$smarty->_gettext_plural_func = '_TP';

	// assign page title
	$smarty->assign('title', '. ..poMMo.. .');

	// assign section (used for sidebar template)
	$smarty->assign('section', bm_section);
	return $smarty;
}
?>