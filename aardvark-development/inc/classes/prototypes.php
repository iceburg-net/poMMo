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

class PommoType {
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
	function & field() {
		return array(
			'id' => null,
			'active' => null,
			'ordering' => null,
			'name' => null,
			'prompt' => null,
			'normally' => null,
			'array' => array(),
			'required' => null,
			'type' => null
		);
	}
	
	/**
	 * Group: A Group of Subscribers
	 * ==SQL Schema==
	 *	group_id		(int)		Database ID/Key
	 *	group_name		(str)		Descriptive name for field (used for short identification)
	 *	
	 * ==Additional Columns from group_criteria==
	 * 
	 *  criteria_id		(int)		Database ID/Key
	 *  group_id		(int)		Correlating Group ID
	 *  field_id		(int)		Correlating Field ID
	 *  logic			(enum)		'is','not','greater','less','true','false','is_in','not_in'
	 *	value			(str)		Match Value
	 */
	function & group() {
		return array(
			'id' => null,
			'name' => null,
			'criteria' => array()
		);
	}
	
	/**
	 * Subscriber: A Subscriber
	 * ==SQL Schema==
	 *	id				(int)			Database ID/Key
	 *	email			(str)			Email Address
	 *	time_touched	(date)			Date last modified (records changed)
	 *	time_registered	(date)			Date registered (signed up)
	 *	flag			(enum)			('update',NULL) Subscribers flag (def: null)
	 *	ip				(str)			IP (tcp/ip) used to register
	 *	status			(enum)			'active','inactive','pending' (def: pending)
	 *
	 * == Additional columns for Pending ==
	 *	pending_id		(int)			Database ID/Key
	 *	subscriber_id	(int)			Subscriber ID in subscribers table
	 *	pending_code	(str)			Code to complete pending request
	 *	pending_type	(enum)			'add','del','change','password',NULL (def: null)
	 *	pending_array	(str)			Serialized Subscriber object (for update)
	 *
	 * == Additional Data Columns ==
	 *	data_id			(int)			Database ID/Key
	 *	field_id		(int)			Field ID in fields table
	 *	subscriber_id	(int)			Subscriber ID in subscribers table
	 *	value			(str)			Subscriber's field value
	 */
	
	function & subscriber() {
		return array(
			'id' => null,
			'email' => null,
			'touched' => null,
			'registered' => null,
			'flag' => null,
			'ip' => null,
			'status' => null,
			'data' => array()
		);
	}
	function & subscriberPending() {
		$o = PommoType::subscriber();
		$o['pending_code'] = $o['pending_array'] = $o['pending_type'] = null;
		return $o;
	}
	
	function & pending() {
		return array(
			'id' => null,
			'subscriber_id' => null,
			'code' => null,
			'array' => array(),
			'type' => null
		);
	}
}