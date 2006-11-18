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
			global $pommo;
			if ($pommo->_verbosity < 3)
				var_dump($defaults,$args);
			Pommo::kill('Unknown argument passed to PommoAPI::getParams()', TRUE);
		}

		return $p;
	}

	// Returns base configuration data from SESSION. If optional argument is supplied, configuration will be loaded from
	// the database & stored in SESSION.
	function configGetBase($fromDB = FALSE) {
		global $pommo;
		$dbo = & $pommo->_dbo;
		$dbo->dieOnQuery(FALSE);

		if ($fromDB || empty ($_SESSION['pommo']['config'])) {
			$_SESSION['pommo']['config'] = array ();
			$query = "
				SELECT config_name, config_value
				FROM ".$dbo->table['config'] ."
				WHERE autoload='on'";
			$query = $dbo->prepare($query);
			
			while ($row = $dbo->getRows($query))
				$_SESSION['pommo']['config'][$row['config_name']] = $row['config_value'];
		}
		
		if (!$fromDB) { // check file revision against database revision
		$query = "
			SELECT config_value
			FROM ".$dbo->table['config'] ."
			WHERE config_name='revision'";
		$query = $dbo->prepare($query);
		
		$revision = $dbo->query($query, 0);
		if (!$revision)
			$this->kill(sprintf(Pommo :: _T('Error loading configuration. Have you %s installed %s ?'), '<a href="' . $pommo->_baseUrl . 'install/install.php">', '</a>'));
		elseif ($pommo->_revision != $revision) $this->kill(sprintf(Pommo :: _T('Version Mismatch. Have you %s upgraded %s ?'), '<a href="' . $pommo->_baseUrl . 'install/upgrade.php">', '</a>'));
		}
		
		$dbo->dieOnQUery(TRUE);
		return $_SESSION['pommo']['config'];
	}

	// Gets specified config value(s) from the DB. 
	// Pass a single or array of config_names, returns array of their name>value.
	function configGet($arg) {
		global $pommo;
		$dbo = & $pommo->_dbo;
		$dbo->dieOnQuery(FALSE);


		if ($arg == 'all')
			$arg = null;
			
		$query = "
			SELECT config_name,config_value
			FROM ". $dbo->table['config']."
			[WHERE config_name IN(%Q)]";
		$query = $dbo->prepare($query,array($arg));
		
		while ($row = $dbo->getRows($query))
			$config[$row['config_name']] = $row['config_value'];

		$dbo->dieOnQUery(TRUE);
		return $config;
	}

	// update the config table. 
	//  $input must be an array as key:value ([config_name] => config_value)
	function configUpdate($input, $force = FALSE) {
		global $pommo;
		$dbo = & $pommo->_dbo;

		if (!is_array($input))
			Pommo :: kill('Bad input passed to updateConfig', TRUE);
			
		// if this is password, skip if empty
		if (isset($input['admin_password']) && empty($input['admin_password']))
			unset($input['admin_password']);

		// get eligible config rows/options to change
		$force = ($force) ? null : 'on';
		$query = "
			SELECT config_name
			FROM " . $dbo->table['config'] . "
			WHERE config_name IN(%q)
			[AND user_change='%S']";
		$query = $dbo->prepare($query, array (array_keys($input), $force));

		// update rows/options
		while ($row = $dbo->getRows($query)) { // multi-row update in a single query syntax
			$when .= $dbo->prepare("WHEN '%s' THEN '%s'",array($row['config_name'],$input[$row['config_name']])).' ';
			$where[] = $row['config_name']; // limits multi-row update query to specific rows (vs updating entire table)
		}
		$query = "
			UPDATE " . $dbo->table['config'] . "
			SET config_value =
				CASE config_name ".$when." ELSE config_name END
			[WHERE config_name IN(%Q)]";
		if (!$dbo->query($dbo->prepare($query,array($where))))
			die('Error updating config');
		return true;
	}
}
?>
