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

class PommoHelperL10n {
	function init($language, $baseDir) {

		if (!is_file($baseDir . 'language/' . $language . '/LC_MESSAGES/pommo.mo'))
			die('<img src="themes/shared/images/icons/alert.png" align="middle"><br><br>Unknown Language (' .
			$language . ')');

		// check for gettext support
		if (!function_exists('gettext')) {
			die('<img src="themes/shared/images/icons/alert.png" align="middle"><br><br>No PHP Gettext Support for non-English (' .
			$language . ') translation!');
		}

		// set the locale
		if (!PommoHelperL10n::_setLocale(LC_MESSAGES, $language)) {
			
			if (!strpos($language,'_')) {
			$language = $language.'_'.strtoupper($language);
			}
			
			die('<img src="themes/shared/images/icons/alert.png" align="middle"><br><br>Locale for (' .
			$language . ') not supported by system');
		}

		// set gettext environment
		$domain = 'pommo';
		bindtextdomain($domain, $baseDir . 'language');
		textdomain($domain);
		if (function_exists('bind_textdomain_codeset')) {
			bind_textdomain_codeset($domain, 'UTF-8');
		}
		
		// register the global (to be looked up by the static Pommo::_T() method)
		$GLOBALS['pommol10n'] = TRUE;
	}

	// taken from Gallery2
	function _setlocale($category, $locale) {
		
		// append _LC to locale
		if (!strpos($locale,'_')) {
			$locale = $locale.'_'.strtoupper($locale);
		}
		
		if (($ret = setlocale($category, $locale)) !== false) {
			return $ret;
		}
		/* Try just selecting the language */
		if (($i = strpos($locale, '_')) !== false && ($ret = setlocale($category, substr($locale, 0, $i))) !== false) {
			return $ret;
		}
		/*
		 * Try appending some character set names; some systems (like FreeBSD) need this.
		 * Some require a format with hyphen (e.g. gentoo) and others without (e.g. FreeBSD).
		 */
		foreach (array (
				'UTF-8',
				'UTF8',
				'utf8',
				'ISO8859-1',
				'ISO8859-2',
				'ISO8859-5',
				'ISO8859-7',
				'ISO8859-9',
				'ISO-8859-1',
				'ISO-8859-2',
				'ISO-8859-5',
				'ISO-8859-7',
				'ISO-8859-9',
				'EUC',
				'Big5'
			) as $charset) {
			if (($ret = setlocale($category, $locale . '.' . $charset)) !== false) {
				return $ret;
			}
		}
		return false;
	}

	function translate($msg) {
		return gettext($msg);
	}

	function translatePlural($msg, $plural, $count) {
		return ngettext($msg, $plural, $count);
	}

}
?>