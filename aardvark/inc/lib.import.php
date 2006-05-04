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

/** 
* Don't allow direct access to this file. Must be called from
elsewhere
*/
defined('_IS_VALID') or die('Move along...');

require_once (bm_baseDir.'/inc/lib.txt.php');

// Reads a CSV file and returns an array. Returns false if the file is invalid.
//  Validation -> 1 email address per line, # of cells not to exceed # of demographics by 5
//  Output array { ['assignLine'] => 'line # w/ most fields, [#linenumber] => array ([field1],[field2],[...]) }
function & csvPrepareFile(& $poMMo, & $dbo, & $uploadFile) {

	// set maximum fields / line based off # of demographics
	$sql = 'SELECT COUNT(demographic_id) FROM '.$dbo->table['demographics'];
	$maxFields = $dbo->query($sql, 0) + 5;

	// the most fields the parser encounters / line
	$mostFields = 0;

	// the array which will be returned
	$outArray = array ();

	// read the file into an array
	$parseFile = file($uploadFile);

	foreach ($parseFile as $line_num => $line) {

		$fields = @ quotesplit($line);
		$numFields = count($fields);

		// check to see if any fields were read in
		if (!$numFields || $numFields < 1) {
			$poMMo->addMessage('Line # '. ($line_num +1).' could not be processed. Is this valid?');
			continue; // skip this line, as it has failed sanity check.
		}

		// check to see if this line exceeded the maximum allowed fields
		if ($numFields > $maxFields) {
			$poMMo->addMessage('Line # '. ($line_num +1).' had too many fields.');
			continue; // skip this line, as it has failed sanity check.
		}

		$emailCount = 0;

		// travel through the fields, performing any validation
		foreach ($fields as $field) {
			if (isEmail($field))
				$emailCount ++;
		}

		if ($emailCount > 1) {
			$poMMo->addMessage('Line # '. ($line_num +1).' had more than 1 email address.');
			continue; // skip this line, as it has failed sanity check.
		}

		if ($emailCount == 0) {
			$poMMo->addMessage('Line # '. ($line_num +1).' had no email address.');
			continue; // skip this line, as it has failed sanity check.	
		}

		// check to see if this line has the most fields we've seen so far
		if ($numFields > $mostFields) {
			$mostFields = $numFields;
			$outArray['assignLine'] = $line_num;
		}

		$outArray[$line_num] = $fields;
	}

	// return false if there were errors
	if ($poMMo->isMessage())
		return false;

	return $outArray;
}

// csvPrepareImport: <array> returns an array of dbGetSubscriber style subscribers to import. 
// The array consists of 2 arrays, 'valid' and 'invalid'. If a subscriber is in 'invalid', they will be flagged to
//  update their records.
function csvPrepareImport(& $poMMo, & $dbo, & $demographics, & $csvArray, & $fieldAssign) {
	
	require_once (bm_baseDir.'/inc/db_subscribers.php');

	$outArray = array ('valid' => array (), 'invalid' => array (), 'duplicate' => array ());

	// array of required demographics
	$requiredArray = array ();
	foreach (array_keys($demographics) as $demographic_id)
		if ($demographics[$demographic_id]['required'] == 'on')
			$requiredArray[$demographic_id] = $demographics[$demographic_id]['name'];

	// find the field # holding the email address
	foreach (array_keys($fieldAssign) as $field_num) {
		if ($fieldAssign[$field_num] == 'email') {
			$emailField = $field_num;
			break;
		}
	}

	// go through each row of the csvArray, and validate the entries
	foreach (array_keys($csvArray) as $line) {
		if ($line === 'assignLine') // skip the assignment line -- TODO: remove assignment line kludge.
			continue;

		$entries = & $csvArray[$line];

		// begin the subscriber for this row
		$subscriber = array ('data' => array ());
		$valid = TRUE;

		// array of required demographics.
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

		// assign the demographic_id to this field
		$demographic_id = & $fieldAssign[$field_num];
		$demographic = & $demographics[$demographic_id];

		// validate this field
		switch ($demographic['type']) {
			case 'checkbox' :
				if ($value == 'on' || $value == 'ON' || $value == 'checked' || $value == 'CHECKED' || $value = 'yes' || $value == 'YES')
					$subscriber['data'][$demographic_id] = 'on';
				break;
			case 'multiple' :
				// verify the input matches a selection (for data congruency)
				$options = quotesplit($demographic['options']);
				if (in_array($value, $options)) {
					$subscriber['data'][$demographic_id] = mysql_real_escape_string($value);
				}
				else {
					$poMMo->addMessage('Subscriber on line '. ($line +1).' has an unknown option ('.$value.') for field '.$demographic['name'].'.');
					$valid = FALSE;
				}
				break;
			case 'date' : // validate if input is a date
				$date = strtotime($value);
				if ($date)
					$subscriber['data'][$demographic_id] = $date;
				else {
					$poMMo->addMessage('Subscriber on line '. ($line +1).' has an invalid date ('.$value.') for '.$demographic['name'].'.');
					$valid = FALSE;
				}
				break;
			case 'text' :
				$subscriber['data'][$demographic_id] = mysql_real_escape_string($value);
				break;
			case 'number' :
				if (is_numeric($value))
					$subscriber['data'][$demographic_id] = mysql_real_escape_string($value);
				else {
					$poMMo->addMessage('Subscriber on line '. ($line +1).' has a non number ('.$value.') for '.$demographic['name'].'.');
					$valid = FALSE;
				}
				break;
			default :
				die('Unknown Type in Import Process');
		}

		// tick off this field from the required demographics if it was required
		if (isset ($required[$demographic_id]))
			unset ($required[$demographic_id]);
	}

	if (!empty ($required)) {
		foreach (array_keys($required) as $demographic_id)
			$poMMo->addMessage('Subscriber on line '. ($line +1).' has a required field ('.$required[$demographic_id].') empty.');
		$valid = FALSE;
	}

	if ($valid)
		$outArray['valid'][] = $subscriber;
	else
		$outArray['invalid'][] = $subscriber;
}
return $outArray;
}
?>