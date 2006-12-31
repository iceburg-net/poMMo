<?php
// poMMo update routines

// upgrades poMMo
// returns bool (true if upgraded)
function PommoUpgrade() {
	global $pommo;
	
	// fetch the current/old revision
	$config = PommoAPI::configGet('revision');
	
	while($config['revision'] < $pommo->_revision) {
		if(!PommoRevUpgrade(intval($config['revision'])))
			return false;
		$config = PommoAPI::configGet('revision');
	}
	return true;
}

// upgrades to a revisions steps
function PommoRevUpgrade($rev) {
	global $pommo;
	$logger =& $pommo->_logger;
	$dbo =& $pommo->_dbo;
	
	switch ($rev) {
		case 26 : // Aardvark PR14

			// manually add the serial column
			$query = "ALTER TABLE ".$dbo->table['updates']." ADD `serial` INT UNSIGNED NOT NULL";
			if(!$dbo->query($query))
				Pommo::kill('Could not add serial column');
				
			if (!PommoInstall::incUpdate(1,
			"ALTER TABLE {$dbo->table['updates']} DROP `update_id` , DROP `update_serial`"
			,"Dropping old Update columns")) return false;
			
			if (!PommoInstall::incUpdate(2,
			"ALTER TABLE {$dbo->table['updates']} ADD PRIMARY KEY ( `serial` )"
			,"Adding Key to Updates Table")) return false;
			
			if (!PommoInstall::incUpdate(3,
			"CREATE TABLE {$dbo->table['mailing_notices']} (
				`mailing_id` int(10) unsigned NOT NULL,
				`notice` varchar(255) NOT NULL,
				`touched` timestamp NOT NULL,
				KEY `mailing_id` (`mailing_id`)
			)"
			,"Adding Mailing Notice Table")) return false;
			
			if (!PommoInstall::incUpdate(4,
			"ALTER TABLE {$dbo->table['mailing_current']} DROP `notices`"
			,"Dropping old Notice column")) return false;			
			
			// bump revision
			if (!PommoAPI::configUpdate(array('revision' => 27), true))
				return false;
		case 27 : // Aardvark PR14.1
			
			if (!PommoInstall::incUpdate(5,
			"CREATE TABLE {$dbo->table['subscriber_update']} (
				`email` varchar(60) NOT NULL,
  				`code` char(32) NOT NULL ,
  				`activated` datetime NULL default NULL ,
  				`touched` timestamp(14) NOT NULL,
				PRIMARY KEY ( `email` )
			)"
			,"Adding Update Activation Table")) return false;
			
			Pommo::requireOnce($pommo->_baseDir . 'inc/helpers/messages.php');
			PommoHelperMessages::resetDefault();
			
			// bump revision
			if (!PommoAPI::configUpdate(array('revision' => 28), true))
				return false;
		
		case 28 : // Aardvark PR14.2
			// gets executed
			break;
		default: 
			return false;
	} 
	return true;
}

?>