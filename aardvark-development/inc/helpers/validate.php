<?php
/*
 * validate.php
 *
 * PROJECT: aardvark-subscriberRefactor
 * COPYRIGHT: (c) 2006 Brice Burgess / All Rights Reserved
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL
 *
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * SOURCE: http://www.iceburg.net/brice/
 */
 
 class PommoValidate {
 	// validates supplied subscriber data against fields
	// accepts a subscriber's data (array)
	// accepts a parameter array
	//   prune: (bool) true => if true, prune the data array (passed by reference)
	//     to recognized/checked fields
	//   ignore: (bool) false => if true, invalid fields will be pruned from $in array.
	//   active: (bool) true => if true, only check data against active fields. Typically true 
	//     if subscribing via form, false if admin importing. 
	//   log: (bool) true => if true, log invalid fields as error. Typicall true
	//     if subscribing via form, false if admin importing.
	// returns (bool) validation
	//   NOTE: has the MAGIC FUNCTIONALITY of converting date field input 
	//     to a UNIX TIMESTAMP. This is necessary for quick SQL comparisson of dates, etc.
	//   NOTE: has the MAGIC FUNCTIONALITY of trimming leading and trailing whitepace
	function subscriberData(&$in, $p = array('prune' => true, 'active' => true, 'log' => true, 'ignore' => false)) {
		global $pommo;
		$pommo->requireOnce($GLOBALS['pommo']->_baseDir. 'inc/helpers/fields.php');
		$logger =& $pommo->_logger;
		
		$fields = PommoField::get(array('active' => $p['active']));
		
		// array_intersect_key requires PHP 5.1 +, compat function -->
		if (!function_exists('array_intersect_key')) {
			function array_intersect_key() {
				$arrs = func_get_args();
				$result = array_shift($arrs);
				foreach ($arrs as $array) {
					foreach ($result as $key => $v)
						if (!array_key_exists($key, $array)) 
							unset($result[$key]);
				}		
				return $result;
			}
		}
		
		$valid = true;
		foreach ($fields as $id => $field) {
			
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
					if ($in[$id] != 'on' || $in['id'] != 'off') {
						if ($p['log'])
							$logger->addErr(sprintf(Pommo::_T('Illegal input for field %s.'),$field['prompt']));
						if ($p['ignore'])
							unset($in[$id]);
						$valid = false;
					}
					break;
				case "multiple":
					if (empty($in[$id]))
						break;
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
					if (empty($in[$id]))
						break;
						
					// convert date to unix timestamp (# of secs since j1 1970)
					$in[$id] = strtotime($in[$id]);
					if($in[$id] == 0 || !$in[$id]) {
						if ($p['log'])
							$logger->addErr(sprintf(Pommo::_T('Field (%s) must be a date.'),$field['prompt']));
						if ($p['ignore'])
							unset($in[$id]);
						$valid = false;
					}
					break;
				case "number":
					if (empty($in[$id]))
						break;
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
			$in = array_intersect_key($in,$fields);
			
		return $valid;
	}
 }
?>
