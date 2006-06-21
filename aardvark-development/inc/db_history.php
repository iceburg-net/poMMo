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

//(ct)

/** 
 * Don't allow direct access to this file. Must be called from elsewhere
 */
defined('_IS_VALID') or die('Move along...');



/* Get the number of mailings in the table mailing_history of the database */
function & dbGetMailingCount(& $dbo) {

	$sql = 'SELECT count(id) FROM ' . $dbo->table['mailing_history'];

	if ($dbo->query($sql)) {
		if ($row = mysql_fetch_row($dbo->_result)) {
			return $row[0];
			exit;
		}
	}
	return 0;

} //dbGetMailingCount




/* Get the mailings history matrix */
function & dbGetMailingHistory(& $dbo, $start, $limit, $order, $orderType) {

	$dbo->dieOnQuery(TRUE);

	/*id, fromname, fromemail, frombounce, subject, body, ishtml, mailgroup, subscriberCount, started, finished, sent*/
	//$countmailings = $dbo->records();

	$sql = 'SELECT id, fromname, fromemail, frombounce, ishtml, mailgroup, subscriberCount, started, finished, sent';
	$sql .= ' FROM ' . $dbo->table['mailing_history'];
	$sql .= " ORDER BY " . $order . " " . $orderType . " LIMIT " . $start . " , " . $limit;


	if ($dbo->query($sql)) {

		// already checked in DBO class
		// if (!$dbo->_result) { $smarty = & bmSmartyInit();
		// bmKill(sprintf(_T('Database Query Error. Return to the %s Mailing Page %s'), '<a href="admin_mailings.php"', '</a>'));exit;		}

		if (mysql_num_rows($dbo->_result) == 0) {

			// No Rows found:
			$mailings = NULL;
		
		} else {

			// get data from db
		
			$i = 1;
		
			while ($row = mysql_fetch_assoc($dbo->_result)) {
		
				$mailings[$i]['mailid'] = $row['id'];
				$mailings[$i]['fromname'] = $row['fromname'];
				$mailings[$i]['fromemail'] = $row['fromemail'];
				$mailings[$i]['frombounce'] = $row['frombounce'];
				//$mailings[$i]['subject'] = $row['subject']; 
				//$mailings[$i]['body'] = $row['body']; 
				$mailings[$i]['ishtml'] = $row['ishtml']; 
				$mailings[$i]['mailgroup'] = $row['mailgroup']; 
				$mailings[$i]['subscriberCount'] = $row['subscriberCount']; 
				$mailings[$i]['started'] = $row['started']; 
				$mailings[$i]['finished'] = $row['finished']; 
				$mailings[$i]['sent'] = $row['sent']; 
			
				$i++;
			
			} //while 

		}

	}

	$dbo->dieOnQuery(FALSE);

	return $mailings;

} //dbGetMailingHistory



// Get Infos on a Mailing from a Array or numeric ID Information
function & dbGetMailingInfo(& $dbo, $selid) {

	$dbo->dieOnQuery(TRUE);
	
	// Do the selection from database
	if (is_numeric($selid)) {
	
		$sql = "SELECT id, fromname, fromemail, frombounce, subject, body, altbody, ishtml, mailgroup, subscriberCount, started, finished, sent";
		$sql .= " FROM " . $dbo->table['mailing_history'];
		$sql .= " WHERE id = " . $selid;
			
		if ($dbo->query($sql)) {
	
			while ($row = mysql_fetch_assoc($dbo->_result)) {
		
				$mailings[0]['id'] = $row['id'];
				$mailings[0]['fromname'] = $row['fromname'];
				$mailings[0]['fromemail'] = $row['fromemail'];
				$mailings[0]['frombounce'] = $row['frombounce'];
				$mailings[0]['subject'] = $row['subject']; 
				$mailings[0]['body'] = $row['body']; 
				$mailings[0]['altbody'] = $row['altbody']; 
				$mailings[0]['ishtml'] = $row['ishtml']; 
				$mailings[0]['mailgroup'] = $row['mailgroup']; 
				$mailings[0]['subscriberCount'] = $row['subscriberCount']; 
				$mailings[0]['started'] = $row['started']; 
				$mailings[0]['finished'] = $row['finished']; 
				$mailings[0]['sent'] = $row['sent']; 
							
				$i++;

			} //while
			
		} //if
		
	} elseif (is_array($selid)) {
	
		$sql = "SELECT id, fromname, fromemail, frombounce, subject, body, altbody, ishtml, mailgroup, subscriberCount, started, finished, sent";
		$sql .= " FROM " . $dbo->table['mailing_history'];
		$sql .= " WHERE id IN (" . implode(',', $selid).")";
			
		if ($dbo->query($sql)) {
	
			$i = 0;
			while ($row = mysql_fetch_assoc($dbo->_result)) {
		
				$mailings[$i]['id'] = $row['id'];
				$mailings[$i]['fromname'] = $row['fromname'];
				$mailings[$i]['fromemail'] = $row['fromemail'];
				$mailings[$i]['frombounce'] = $row['frombounce'];
				$mailings[$i]['subject'] = $row['subject']; 
				$mailings[$i]['body'] = $row['body']; 
				$mailings[$i]['altbody'] = $row['altbody']; 
				$mailings[$i]['ishtml'] = $row['ishtml']; 
				$mailings[$i]['mailgroup'] = $row['mailgroup']; 
				$mailings[$i]['subscriberCount'] = $row['subscriberCount']; 
				$mailings[$i]['started'] = $row['started']; 
				$mailings[$i]['finished'] = $row['finished']; 
				$mailings[$i]['sent'] = $row['sent']; 
							
				$i++;

			} //while
			
		} //if

	} else {
		//Something wrong
	}
	
	$dbo->dieOnQuery(TRUE);
	
	return $mailings;

} //dbGetMailInfo



// Removes one or more data records from the mailing_history table
// $delid can be numeric oder a Array
function & dbRemoveMailFromHistory($dbo, $delid) {

	if (is_numeric($delid)) {
		
		// delete from mailing_history table
		$sql = 'DELETE FROM '.$dbo->table['mailing_history'].' WHERE id = '.$delid;
		$dbo->query($sql);
		$ret = "Mailing with ID: " . $delid . " deleted.";
		return $ret;
		
	} elseif (is_array($delid)) {
	
		// delete array of mails from mailing_history table
		$sql = 'DELETE FROM '.$dbo->table['mailing_history'].' WHERE id IN ('.implode(',', $delid).')';
		$dbo->query($sql);
		$ret = "Mailing with ID: " . implode(',', $delid) . " deleted.";
		return $ret;

	} else {
	
		$ret = "Could not delete Mailing with ID: " . $delid . "ID Format Error.";
		return $ret;
		
	}

} //dbRemoveMailFromHistory










