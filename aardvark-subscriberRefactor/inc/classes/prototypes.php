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


// basic types used by poMMo -- they are generally fed a row from the database
//  as an assosciatve array

/**
 * Field: A SubscriberField
 * ==SQL Schema==
 *	field_id		(int)			Database ID/Key
 *	field_active	('on','off')	If field is displayed on subscriber form
 *	field_ordering	(int)			Order in which field is displayed @ subscriber form	
 *	field_name		(str)			Descriptive name for field (used for short identification)
 *	field_prompt	(str)			Prompt assosiated with field on subscriber form
 *	field_normally	(str)			Default value of field on subscriber form
 *	field_array		(str)			A serialized array of  the field such as the options of multiple choice fields (drop down select)
 *	field_required	('on','off')	If field is required for subscription
 *	field_type		(enum)			checkbox, multiple, text, date, number
 */
 
class PommoField {
	var $o;
	function & PommoField($row) {
		$this->o = array(
			'id' => $row['field_id'],
			'name' => $row['field_name'],
			'prompt' => $row['field_prompt'],
			'type' => $row['field_type'],
			'normally' => $row['field_normally'],
			'required' => $row['required']
		);
		
		$this->o['array'] = (empty($row['field_array'])) ? array() : unserialize($row['field_array']);
		return;
	} 
}