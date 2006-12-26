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
	
	switch ($rev) {
		case 26 : // Aardvark PR14
			if (!PommoAPI::configUpdate(array('revision' => 27), true))
				return false;
		case 27 : // Aardvark PR14.1
			// (gets executed ) echo 'xxx';
			break;
		default: 
			return false;
	} 
	return true;
}

?>