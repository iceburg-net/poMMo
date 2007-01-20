<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2006 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/licenses/gpl.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. Notify the above author of modifications to contents within.
 * 
 *  WHY? Because this is a community project -- purposely released under the GPL.
 *    We'd love to have the possiblity to include your derivative works! 
 *    We'd love to coordinate around your development efforts!
 *    We'd love to assist you with your changes!
 *    DON'T BE A STRANGER!
 * 
 ** [END HEADER]**/

class PommoHelperL10n {
	function init($language, $baseDir) {

		if (!is_file($baseDir . 'language/' . $language . '/LC_MESSAGES/pommo.mo'))
			Pommo::kill('Unknown Language (' .$language . ')');

		// load gettext emulation layer if PHP is not compiled w/ gettext support
		if (!function_exists('gettext')) {
			Pommo::requireOnce($baseDir.'inc/lib/gettext/gettext.php');
			Pommo::requireOnce($baseDir.'inc/lib/gettext/gettext.inc');
		}
		
		// set the locale
		if (!PommoHelperL10n::_setLocale(LC_MESSAGES, $language, $baseDir)) {
			
			// *** SYSTEM LOCALE COULD NOT BE USED, USE EMULTATION ****
			Pommo::requireOnce($baseDir.'inc/lib/gettext/gettext.php');
			Pommo::requireOnce($baseDir.'inc/lib/gettext/gettext.inc');
			if (!PommoHelperL10n::_setLocaleEmu(LC_MESSAGES, $language, $baseDir))
				Pommo::kill('Error setting up language translation!');
		}
		else {
		
			// *** SYSTEM LOCALE WAS USED ***
			if (!defined('_poMMo_gettext')) {	
				// set gettext environment
				$domain = 'pommo';
				bindtextdomain($domain, $baseDir . 'language');
				textdomain($domain);
				if (function_exists('bind_textdomain_codeset'))
					bind_textdomain_codeset($domain, 'UTF-8');
			}
		}
	}
	
	function _setlocaleEmu($category, $locale, $baseDir) {
		$domain = 'pommo';
		$encoding = 'UTF-8';

		T_setlocale($category, $locale);
		T_bindtextdomain($domain, $baseDir . '/language');
		T_bind_textdomain_codeset($domain, $encoding);
		T_textdomain($domain);
		
		return true;
	}

	// setlocale modified from from Gallery2
	function _setlocale($category, $locale, $baseDir) {
		
		if (defined('_poMMo_gettext'))
			return PommoHelperL10n::_setLocaleEmu($category, $locale, $baseDir);
		
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
		if (defined('_poMMo_gettext'))
			return T_($msg);
		return gettext($msg);
	}

	function translatePlural($msg, $plural, $count) {
		if (defined('_poMMo_gettext'))
			return T_ngettext($msg, $plural, $count);
		return ngettext($msg, $plural, $count);
	}

}
?>
