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

class PommoDebug {
	function bmDebug() {
		global $pommo;
		echo "\n\n<br><br><b>BASIC DEBUG</b><hr><br>\n\n";
		echo "\n\nPHP: " . phpversion() . "<br><br>\n\n";
		echo "\n\nMYSQL CLIENT: " . mysql_get_client_info() . "<br><br>\n\n";
		echo "\n\nMYSQL HOST: " . mysql_get_host_info() . "<br><br>\n\n";
		echo "\n\nMYSQL SERVER: " . mysql_get_server_info() . "<br><br>\n\n";
		echo "\n\nBACKTRACE: " . $this->bmBacktrace() . "<br><br>\n\n";
		echo "\nBaseURL:" . $pommo->_baseUrl . "<br>\n";
		echo "\n HTTP:" . $pommo->_http . "<br>\n";
		echo "\nBaseDir: " . $pommo->_baseDir . "<br>\n";
		echo "\nWorkDir:" . $pommo->_workDir . "<br>\n";
		echo "\nLang:" . $pommo->_lang . "<br>\n";
		echo "\nVerbosity:" . $pommo->_verbosity . "<br>\n";
		echo "\nRevision: " . $pommo->_revision . "<br>\n";
		echo "\nSection: " . $pommo->_section . "<br>\n";

		echo "\n\n<br><br><b>CONFIG DEBUG</b><hr><br>\n\n";
		$config = PommoAPI :: getConfig('all');
		if (!empty ($config)) {
			echo "\n\n<br>CONFIG:<br>\n\n";
			foreach ($config as $name => $value) {
				if ($name == 'admin_username' || $name == 'admin_password')
					$value = '**CENSOR** - ' . strlen($value);
				elseif ($name == 'messages') continue;

				echo "\n$name: $value <br>\n";
			}
		} else
			echo "\n\n<br>CONFIG: could not load\n\n";
	}

	function bmBacktrace() {
		if (!function_exists('debug_backtrace')) {
			return 'PHP VERSION < 4.3, NO BACKTRACE';
		}
		$output = "<div style='text-align: left; font-family: monospace;'>\n";
		$output .= "<b>Backtrace:</b><br />\n";
		$backtrace = debug_backtrace();

		foreach ($backtrace as $bt) {
			$args = '';
			foreach ($bt['args'] as $a) {
				if (!empty ($args)) {
					$args .= ', ';
				}
				switch (gettype($a)) {
					case 'integer' :
					case 'double' :
						$args .= $a;
						break;
					case 'string' :
						$a = htmlspecialchars(substr($a, 0, 64)) . ((strlen($a) > 64) ? '...' : '');
						$args .= "\"$a\"";
						break;
					case 'array' :
						$args .= 'Array(' . count($a) . ')';
						break;
					case 'object' :
						$args .= 'Object(' . get_class($a) . ')';
						break;
					case 'resource' :
						$args .= 'Resource(' . strstr($a, '#') . ')';
						break;
					case 'boolean' :
						$args .= $a ? 'True' : 'False';
						break;
					case 'NULL' :
						$args .= 'Null';
						break;
					default :
						$args .= 'Unknown';
				}
			}
			$output .= "<br />\n";
			@ $output .= "<b>file:</b> {$bt['line']} - {$bt['file']}<br />\n";
			@ $output .= "<b>call:</b> {$bt['class']}{$bt['type']}{$bt['function']}($args)<br />\n";
		}
		$output .= "</div>\n";
		return $output;
	}

}
?>
