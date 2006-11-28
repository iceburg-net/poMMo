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
 
 class PommoValidate {
 	// validates supplied subscriber data against fields
	// accepts a subscriber's data (array)
	// accepts a parameter array
	//   prune: (bool) true => if true, prune the data array (passed by reference)
	//     to only recognized/checked fields
	//   ignore: (bool) false => if true, invalid fields will be pruned from $in array.
	//   active: (bool) true => if true, only check data against active fields. Typically true 
	//     if subscribing via form, false if admin importing. 
	//	 skipReq: (bool) false => if true, skip the required check AND empty fields.
	//   log: (bool) true => if true, log invalid fields as error. Typicall true
	//     if subscribing via form, false if admin importing.
	// returns (bool) validation
	//   NOTE: has the MAGIC FUNCTIONALITY of converting date field input 
	//     to a UNIX TIMESTAMP. This is necessary for quick SQL comparisson of dates, etc.
	//   NOTE: has the MAGIC FUNCTINALITY of changing "true"/"false" to checkbox "on"/off equivelent
	//   NOTE: has the MAGIC FUNCTIONALITY of trimming leading and trailing whitepace
	
	// TODO -> should fields be passed by reference? e.g. are they usually already available when subscriberData() is called?
	function subscriberData(&$in, $p = array()) {
		$defaults = array(
			'prune' => true,
			'active' => true,
			'log' => true,
			'ignore' => false,
			'skipReq' => false);
		$p = PommoAPI :: getParams($defaults, $p);
		
		global $pommo;
		$pommo->requireOnce($GLOBALS['pommo']->_baseDir. 'inc/helpers/fields.php');
		$logger =& $pommo->_logger;
		
		$fields = PommoField::get(array('active' => $p['active']));
		
		$valid = true;
		foreach ($fields as $id => $field) {
			
			if (!isset($in[$id]) && $p['skipReq'])
				continue;
			$in[$id] = trim($in[$id]);
			
			if ($field['required'] == 'on' && empty($in[$id])) {
				if ($p['log'])
					$logger->addErr(sprintf(Pommo::_T('%s is a required field.'),$field['prompt']));
				if ($p['ignore'])
					unset($in[$id]);
				$valid = false;
			}
			
			switch ($field['type']) {
				case "checkbox":
					if (strtolower($in[$id]) == 'true')
						$in[$id] = 'on';
					if (strtolower($in[$id]) == 'false')
						$in[$id] = '';
					if ($in[$id] != 'on' && $in['id'] != '') {
						if ($p['log'])
							$logger->addErr(sprintf(Pommo::_T('Illegal input for field %s.'),$field['prompt']));
						if ($p['ignore'])
							unset($in[$id]);
						$valid = false;
					}
					break;
				case "multiple":
					if (is_array($in[$id])) {
						foreach ($in[$id] as $key => $val)
							if (!in_array($val, $field['array'])) {
								if ($p['log'])
									$logger->addErr(sprintf(Pommo::_T('Illegal input for field %s.'),$field['prompt']));
								if ($p['ignore'])
									unset($in[$id][$key]);
								$valid = false;
							}
					}
					elseif (!in_array($in[$id], $field['array'])) {
						if ($p['log'])
							$logger->addErr(sprintf(Pommo::_T('Illegal input for field %s.'),$field['prompt']));
						if ($p['ignore'])
							unset($in[$id]);
						$valid = false;
					}
					break;
				case "date": // TODO -- ENHANCE/VERIFY THIS!
					// convert date to unix timestamp (# of secs since j1 1970)
					$in[$id] = strtotime($in[$id]);
					if($in[$id] == 0 || !$in[$id]) {
						if ($p['log'])
							$logger->addErr(sprintf(Pommo::_T('Field (%s) must be a date (mm/dd/yyyy).'),$field['prompt']));
						if ($p['ignore'])
							unset($in[$id]);
						$valid = false;
					}
					break;
				case "number":
					if (!is_numeric($in[$id])) {
						if ($p['log'])
							$logger->addErr(sprintf(Pommo::_T('Field (%s) must be a number.'),$field['prompt']));
						if ($p['ignore'])
							unset($in[$id]);
						$valid = false;
					}
				break;
			}	
		}
		// prune
		if($p['prune'])
			$in = PommoHelper::arrayIntersect($in,$fields);
			
		return $valid;
	}
 }
?>
