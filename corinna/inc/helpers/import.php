<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/

// This file is lib.txt.php and lib.import.php from PR13.2 & below
// it must be re-written & brought up to date!

 /* 
  * Formatting Functions -
  ****************************************************************/
  
/**
 * str2display: Formats user inputted text from forms to be displayed ("previewed").
 *    note: for populating form values, simply use: "value=htmlspecialchars($str);" 
 */
 
function str2display(& $string) {

	if (!get_magic_quotes_gpc())
		return nl2br(htmlspecialchars($string));
	return nl2br(htmlspecialchars(stripslashes($string)));
}

/**
 * str2str: Formats user inputted text from forms to be checked against other input.
 *    if magic quotes is on, strip slashes, if not, return string.
 */
 
 // TODO .. PHASE OUT -- as _GET & _POST are being removed.
function str2str(& $string) {
	if (!get_magic_quotes_gpc())
		return $string;
	return stripslashes($string);
}


/**
 *  array2csv: Takes an array, and returns a csv compliant string from its contents.
 *   If an array is not supplied, the argument will be returned in tact.
 */
function & array2csv(&$array) {
	$str = '';
	if (is_array($array)) {
		$str = implode(',', $array);
		return $str;
	}
	return $array;
}

 /* 
  * Validation functions - returns boolean based on rule matching input. 
  ****************************************************************/

/**
 * isEmail: returns true if $str looks like an email address
 */

function isEmail(& $string) {
    $p = '/^[a-z0-9!#$%&*+-=?^_`{|}~]+(\.[a-z0-9!#$%&*+-=?^_`{|}~]+)*';
    $p.= '@([-a-z0-9]+\.)+([a-z]{2,3}';
    $p.= '|info|arpa|aero|coop|name|museum)$/ix';
    return preg_match($p, $string);
}

function isChecked($string = NULL, $returnArray = NULL) {
		// returns true or false to designate if a string means a value is checked/TRUE. 
		// returns an array of valid Trues and Falses if designated

	$truth = array ("1", "YES", "yes", "Yes", "Y", "n", "CHECKED", "Checked", "checked", "CHECK", "Check", "check", "on", "ON", "On", "SELECTED", "Selected", "selected", "TRUE", "True", "true");
	$untruth = array ("0", "NO", "No", "no", "N", "n", "OFF", "Off", "off", "NULL", "FALSE", "False", "false");

	if (!empty ($returnArray))
		return array_merge($truth, $untruth);
	else {
		if (in_array($string, $truth))
			return true;
		return false;
	}
}



 /* 
  * Misc string functions
  ****************************************************************/


/**
 * quotesplit: for putting CSV-Like data into an array --> author: moritz @ php.net 
 * 
 * ie:
 * 1 , 3, 4
 * -> [1,3,4]
 * 
 * one; two;three
 * -> ['one','two','three']
 * 
 * "this is a string", "this is a string with , and ;", 'this is a string with quotes like " these', "this is a string with escaped quotes \" and \'.", 3
 * -> ['this is a string','this is a string with , and ;','this is a string with quotes like " these','this is a string with escaped quotes " and '.',3]
 */

function & quotesplit($s) {
	$r = Array ();
	$p = 0;
	$l = strlen($s);
	while ($p < $l) {
		while (($p < $l) && (strpos(" \r\t\n", $s[$p]) !== false))
			$p ++;
		if ($s[$p] == '"') {
			$p ++;
			$q = $p;
			while (($p < $l) && ($s[$p] != '"')) {
				if ($s[$p] == '\\') {
					$p += 2;
					continue;
				}
				$p ++;
			}
			$r[] = stripslashes(substr($s, $q, $p - $q));
			$p ++;
			while (($p < $l) && (strpos(" \r\t\n", $s[$p]) !== false))
				$p ++;
			$p ++;
		} else
			if ($s[$p] == "'") {
				$p ++;
				$q = $p;
				while (($p < $l) && ($s[$p] != "'")) {
					if ($s[$p] == '\\') {
						$p += 2;
						continue;
					}
					$p ++;
				}
				$r[] = stripslashes(substr($s, $q, $p - $q));
				$p ++;
				while (($p < $l) && (strpos(" \r\t\n", $s[$p]) !== false))
					$p ++;
				$p ++;
			} else {
				$q = $p;
				while (($p < $l) && (strpos(",;", $s[$p]) === false)) {
					$p ++;
				}
				$r[] = stripslashes(trim(substr($s, $q, $p - $q)));
				while (($p < $l) && (strpos(" \r\t\n", $s[$p]) !== false))
					$p ++;
				$p ++;
			}
	}
	return $r;
}


// Reads a CSV file and returns an array. Returns false if the file is invalid.
//  Validation -> 1 email address per line, # of cells not to exceed # of fields by 5
//  Output array {  array ('lineWithMostFields' => 0, 'emailField' => '', 'csvFile' => array([field1],[field2],[...]) }
function & csvPrepareFile(& $uploadFile) {

	global $logger;
	global $dbo;
	global $poMMo;
	
	
	// set maximum fields / line based off # of fields
	$sql = 'SELECT COUNT(field_id) FROM '.$dbo->table['subscriber_fields'];
	$maxFields = $dbo->query($sql, 0) + 5;

	// the most fields the parser encounters / line
	$mostFields = 0;

	// the array which will be returned
	$outArray = array ('lineWithMostFields' => 0, 'emailField' => '', 'csvFile' => array());

	// read the file into an array
	$parseFile = file($uploadFile);

	$fail = 0;
	foreach ($parseFile as $line_num => $line) {

		if ($fail > 3) {
			$logger->addMsg(_T('Maximum failures reached. CSV processing aborted.'));
			break;
		}
		$fields = @ quotesplit($line);
		$numFields = count($fields);

		// check to see if any fields were read in
		if (!$numFields || $numFields < 1) {
			$logger->addMsg(sprintf(_T('Line #%s could not be processed.'),$line_num +1));
			$fail++;
			continue; // skip this line, as it has failed sanity check.
		}

		// check to see if this line exceeded the maximum allowed fields
		if ($numFields > $maxFields) {
			$logger->addMsg(sprintf(_T('Line #%s had too many fields.'),$line_num +1));
			$fail++;
			continue; // skip this line, as it has failed sanity check.
		}

		$emailCount = 0;

		// travel through the fields, performing any validation
		foreach ($fields as $key => $field) {
			if (isEmail($field)) {
				if (!empty($outArray['emailField']) && $key != $outArray['emailField']) {
					$logger->addMsg(sprintf(_T('Line #%s had email address in a different field(cell).'),$line_num +1));
					$fail++;
					continue;
				}
				$outArray['emailField'] = $key;
				$emailCount ++;
			}
		}

		if ($emailCount > 1) {
			$logger->addMsg(sprintf(_T('Line #%s had more than one email address.'),$line_num +1));
			$fail++;
			continue; // skip this line, as it has failed sanity check.
		}

		if ($emailCount == 0) {
			$logger->addMsg(sprintf(_T('Line #%s had no email address.'),$line_num +1));
			$fail++;
			continue; // skip this line, as it has failed sanity check.	
		}

		// check to see if this line has the most fields we've seen so far
		if ($numFields > $mostFields) {
			$mostFields = $numFields;
			$outArray['lineWithMostFields'] = $line_num;
		}

		$outArray['csvFile'][$line_num] = $fields;
	}

	// return false if there were errors
	if ($fail)
		return false;

	return $outArray;
}

// csvPrepareImport: <array> returns an array of dbGetSubscriber style subscribers to import. 
// The array consists of 2 arrays, 'valid' and 'invalid'. If a subscriber is in 'invalid', they will be flagged to
//  update their records.

// fields: dbGetFields 
// csvFile: 2D array of fields [lines of file]
// fieldAssign - array of field assignments; e.g. array(2) { [0]=>  string(5) "email" [1]=>  string(2) "13" } 
function csvPrepareImport(& $fields, & $csvFile, $fieldAssign) {
	
	global $poMMo;
	global $dbo;
	global $logger;
	require_once (bm_baseDir.'inc/db_subscribers.php');

	$outArray = array ('valid' => array (), 'invalid' => array (), 'duplicate' => array ());

	// array of required fields
	$requiredArray = array ();
	foreach (array_keys($fields) as $field_id)
		if ($fields[$field_id]['required'] == 'on')
			$requiredArray[$field_id] = $fields[$field_id]['name'];

	// find the field # holding the email address
	foreach (array_keys($fieldAssign) as $field_num) {
		if ($fieldAssign[$field_num] == 'email') {
			$emailField = $field_num;
			break;
		}
	}

	// go through each row of the csvFile, and validate the entries
	foreach (array_keys($csvFile) as $line) {

		$entries = & $csvFile[$line];

		// begin the subscriber for this row
		$subscriber = array ('data' => array ());
		$valid = TRUE;

		// array of required fields.
		$required = $requiredArray;

		// check if this is the email field
		//  TODO -> why not send an array to isDupeEmail for faster query?
		if (!isDupeEmail($dbo, $entries[$emailField]))
			$subscriber['email'] = mysql_real_escape_string($entries[$emailField]);
		else {
			$outArray['duplicate'][] = $entries[$emailField].' (line '.($line+1).')';
			continue;
		}

	// go through each field in a row
	foreach ($entries as $field_num => $value) {

		if ($fieldAssign[$field_num] == 'ignore' || $field_num == $emailField)
			continue;

		// trim the value of whitespace
		$value = trim($value);

		// if the value is empty, skip. Required fields will be checked below
		if (empty ($value))
			continue;

		// assign the field_id to this field
		$field_id = $fieldAssign[$field_num];
		$field = $fields[$field_id];

		// validate this field
		switch ($field['type']) {
			case 'checkbox' :
				if ($value == 'on' || $value == 'ON' || $value == 'checked' || $value == 'CHECKED' || $value = 'yes' || $value == 'YES')
					$subscriber['data'][$field_id] = 'on';
				break;
				case 'multiple' :
                // verify the input matches a selection (for data congruency)
                if (in_array($value, $field['array'])) {
                    $subscriber['data'][$field_id] = mysql_real_escape_string($value);
                }
                else {
                    $logger->addMsg(sprintf(_T('Subscriber on line %1$s has an unknown option (%2$s) for field %3$s'),$line + 1,$value, $field['name']));
                    $valid = FALSE;
                }
                break;
		case 'date' : // validate if input is a date
				$date = strtotime($value);
				if ($date)
					$subscriber['data'][$field_id] = $date;
				else {
					$logger->addMsg(sprintf(_T('Subscriber on line %1$s has an invalid date (%2$s) for field %3$s'),$line + 1,$value, $field['name']));
					$valid = FALSE;
				}
				break;
			case 'text' :
				$subscriber['data'][$field_id] = mysql_real_escape_string($value);
				break;
			case 'number' :
				if (is_numeric($value))
					$subscriber['data'][$field_id] = mysql_real_escape_string($value);
				else {
					$logger->addMsg(sprintf(_T('Subscriber on line %1$s has a non number (%2$s) for field %3$s'),$line + 1,$value, $field['name']));
					$valid = FALSE;
				}
				break;
			default :
				Pommo::kill('Unknown Type in Import Process');
		}

		// tick off this field from the required fields if it was required
		if (isset ($required[$field_id]))
			unset ($required[$field_id]);
	}

	if (!empty ($required)) {
		foreach (array_keys($required) as $field_id)
		$logger->addMsg(sprintf(_T('Subscriber on line %1$s has a empty required field (%2$s)'),$line + 1,$fields[$field_id]['name']));			
		$valid = FALSE;
	}

	if ($valid)
		$outArray['valid'][] = $subscriber;
	else
		$outArray['invalid'][] = $subscriber;
}
return $outArray;
}
