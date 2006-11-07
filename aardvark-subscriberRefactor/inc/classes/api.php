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

// common API

class PommoAPI {

	function & getParams(& $defaults, & $args) {
		$p = array_merge($defaults, $args);

		// make sure all submitted parameters are "known" by verifying size of final array
		if (count($p) > count($defaults)) {
			$backtrace = debug_backtrace();
			$this->kill('Unknown argument passed to PommoAPI::getParams() from function ' . $backtrace[1]['function'] . ' on line ' . $backtrace[1]['line'] . ' of file ' . $_SERVER['PHP_SELF']);
		}

		return $p;
	}

	// Returns base configuration data from SESSION. If optional argument is supplied, configuration will be loaded from
	// the database & stored in SESSION.
	function getConfigBase($fromDB = FALSE) {
		global $pommo;
		$dbo = & $pommo->_dbo;
		$dbo->dieOnQuery(FALSE);

		if ($fromDB || empty ($_SESSION['pommo']['config'])) {
			$_SESSION['pommo']['config'] = array ();
			$sql = 'SELECT * FROM ' . $dbo->table['config'] . ' WHERE autoload=\'on\'';
			if ($dbo->query($sql)) {
				while ($row = mysql_fetch_assoc($dbo->_result))
					$_SESSION['pommo']['config'][$row['config_name']] = $row['config_value'];
			}
		}

		// check for valid configuration data & DB against file version. 
		$sql = 'SELECT config_value FROM ' . $dbo->table['config'] . ' WHERE config_name=\'revision\'';
		$revision = $dbo->query($sql, 0);
		if (!$revision)
			$this->kill(sprintf(Pommo::_T('Error loading configuration. Have you %s installed %s ?'), '<a href="' . $pommo->_baseUrl . 'install/install.php">', '</a>'));
		elseif ($pommo->_revision != $revision) $this->kill(sprintf(Pommo::_T('Version Mismatch. Have you %s upgraded %s ?'), '<a href="' . $pommo->_baseUrl . 'install/upgrade.php">', '</a>'));

		$dbo->dieOnQUery(TRUE);

		return $_SESSION['pommo']['config'];
	}

	// Gets specified config value(s) from the DB. 
	// Pass a single or array of config_names, returns array of their name>value.
	function getConfig($arg) {
		global $pommo;
		$dbo = & $pommo->_dbo;
		$dbo->dieOnQuery(FALSE);

		if (!is_array($arg))
			$arg = array (
				$arg
			);

		$config = array ();
		if ($arg[0] == 'all')
			$sql = 'SELECT config_name,config_value FROM ' . $dbo->table['config'];
		else
			$sql = 'SELECT config_name,config_value FROM ' . $dbo->table['config'] . ' WHERE config_name IN (\'' . implode('\',\'', $arg) . '\')';

		while ($row = $dbo->getRows($sql))
			$config[$row['config_name']] = $row['config_value'];

		$dbo->dieOnQUery(TRUE);
		return $config;
	}

	
	

}
?>
