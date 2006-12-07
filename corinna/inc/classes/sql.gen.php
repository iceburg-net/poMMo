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

// common SQL clauses

class PommoSQL {
	// returns where clauses as array
	// accepts a attribute filtering array.
	//   array_key == column, value is filter table filter table (subscriber_pending, subscriber_data, subscribers)
	//   e.g. 
	//   array('pending_code' => array("not: 'abc1234'", "is: 'def123'", "is: '2234'")); 
	//   array(12 => array("not: 'Milwaukee'")); (12 -- numeric -- is alias for field_id=12)
	//   array('status' => array('equal: active'))
	// accepts a table prefix (e.g. WHERE prefix.column = 'value')
	// returns SQL WHERE + JOIN clauses (array)
	function & fromFilter(&$in, $p = null) {
		global $pommo;
		$dbo =& $pommo->_dbo;
		
		$where = $joins = array();
			
		// parse column => logic => value from array
		$filters = array();
		foreach ($in as $col => $val) 
			PommoSQL::getLogic($col,$val,$filters);
		

		// get where &/or joins
		foreach($filters as $col => $l) { 
			if (is_numeric($col)) { // "likely" encountered a field_id in subscriber_data... 
				foreach($l as $logic => $vals) {
					$i = count($joins);
					$join = "LEFT JOIN {$dbo->table['subscriber_data']} $p$i ON (s.subscriber_id = $p$i.subscriber_id AND $p$i.field_id=$col AND ";
					switch ($logic) {
						case "is" :
							$joins[] = $dbo->prepare("[".$join."$p$i.value IN (%Q))]",array($vals)); break;
						case "not":
							$joins[] = $dbo->prepare("[".$join."$p$i.value NOT IN (%Q))]",array($vals)); break;
						case "less":
							$joins[] = $dbo->prepare("[".$join."$p$i.value < %I)]",array($vals)); break;
						case "greater":
							$joins[] = $dbo->prepare("[".$join."$p$i.value > %I)]",array($vals)); break;
						case "true":
							$joins[] = $join."$p$i.value = 'on')"; break;
						case "false":
							$joins[] = $join."$p$i.value != 'on')"; break;
					}
				}
			}
			else {
				foreach($l as $logic => $vals) {
					switch ($logic) {
						case "is" :
							$where[] = $dbo->prepare("[AND $p.$col IN (%Q)]",array($vals)); break;
						case "not":
							$where[] = $dbo->prepare("[AND $p.$col NOT IN (%Q)]",array($vals)); break;
						case "less":
							$where[] = $dbo->prepare("[AND $p.$col < %I]",array($vals)); break;
						case "greater":
							$where[] = $dbo->prepare("[AND $p.$col > %I]",array($vals)); break;
						case "true":
							$where[] = "AND $p.$col = 'on'"; break;
						case "false":
							$where[] = "AND $p.$col != 'on'"; break;
						case "equal":
							$where[] = $dbo->prepare("[AND $p.$col = '%S']", array($vals[0])); break;
					}
				}
			}
		}
		// add joins to where clause -- TODO: this is where OR filtering can be looked up!
		$c = count($joins);
		for ($i=0; $i < $c; $i++)
			$where[] = "AND $p$i.subscriber_id IS NOT NULL"; // for an "or", this could be left out!
		
		return array('where' => $where, 'join' => $joins);
	}
	
	// get the column(s) logic + value(s)
	function getLogic(&$col, &$val, &$filters) {
		if (is_array($val)) {
			foreach($val as $v)
				PommoSQL::getLogic($col,$v,$filters);
		}
		else {
			// extract logic ($matches[1]) + value ($matches[2]) 
			preg_match('/^(?:(not|is|less|greater|true|false|equal):)?(.*)$/i',$val,$matches);
			if (!empty($matches[1])) { 
				if (empty($filters[$col]))
					$filters[$col] = array();
				if (empty($filters[$col][$matches[1]]))
					$filters[$col][$matches[1]] = array();
				array_push($filters[$col][$matches[1]],trim($matches[2]));
			}
		}
	}
}
?>