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
 * Don't allow direct access to this file. Must be called from elsewhere
 */
defined('_IS_VALID') or die('Move along...');

// Cool DB Query Wrapper from Monte Ohrt
require_once (bm_baseDir.'/inc/safesql/SafeSQL.class.php');




/* Get the number of mailings in the table mailing_history of the database */
function & dbGetMailingCount(& $dbo) {

	$safesql =& new SafeSQL_MySQL;
	$sql = $safesql->query("SELECT count(id) FROM %s ", array($dbo->table['mailing_history']) );

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

	//id, fromname, fromemail, frombounce, subject, body, ishtml, mailgroup, subscriberCount, started, finished, sent
	//$countmailings = $dbo->records();
	
	$safesql =& new SafeSQL_MySQL;
	$sql = $safesql->query("SELECT id, fromname, fromemail, frombounce, subject, ishtml, mailgroup, 
		subscriberCount, started, finished, sent FROM %s ORDER BY %s %s LIMIT %s, %s ", 
		array($dbo->table['mailing_history'], $order, $orderType, $start, $limit) );

	if ($dbo->query($sql)) {

		if (mysql_num_rows($dbo->_result) == 0) {

			// No Rows found:
			$mailings = NULL;
		
		} else {

			// get data from db
		
			$i = 1;
		
			while ($row = mysql_fetch_assoc($dbo->_result)) {
		
				$mailings[$i]['mailid'] = $row['id'];
				//$mailings[$i]['fromname'] = $row['fromname'];
				//$mailings[$i]['fromemail'] = $row['fromemail'];
				//$mailings[$i]['frombounce'] = $row['frombounce'];
				$mailings[$i]['subject'] = $row['subject']; 
				//$mailings[$i]['body'] = $row['body']; 
				$mailings[$i]['ishtml'] = $row['ishtml']; 
				$mailings[$i]['mailgroup'] = $row['mailgroup']; 
				$mailings[$i]['subscriberCount'] = $row['subscriberCount']; 
				$mailings[$i]['started'] = $row['started']; 
				$mailings[$i]['finished'] = $row['finished']; 
				$mailings[$i]['sent'] = $row['sent']; 
				$mailings[$i]['duration'] = $row['started']-$row['finished'];
				$mailings[$i]['mpm'] = ($row['started']-$row['finished']) / $row['sent'];
			
				$i++;
			
			} //while 

		}

	}

	$dbo->dieOnQuery(FALSE);
	
	return $mailings;

} //dbGetMailingHistory



// Get Infos on a Mailing from a Array or numeric ID Information
function & dbGetMailingInfo(& $dbo, $selid) {

	global $logger;

	$dbo->dieOnQuery(TRUE);
	
	// Do the selection from database
	if (is_numeric($selid)) {
	
		$safesql =& new SafeSQL_MySQL;
		$sql = $safesql->query("SELECT id, fromname, fromemail, frombounce, subject, body, altbody, 
			ishtml, mailgroup, subscriberCount, started, finished, sent FROM %s WHERE id = %i ",
			array($dbo->table['mailing_history'], $selid) );
			
		if ($dbo->query($sql)) {
	
			while ($row = mysql_fetch_assoc($dbo->_result)) {
		
				$mailings[0]['id'] = $row['id'];
				$mailings[0]['fromname'] = $row['fromname'];
				$mailings[0]['fromemail'] = $row['fromemail'];
				$mailings[0]['frombounce'] = $row['frombounce'];
				$mailings[0]['subject'] = $row['subject']; 
				$mailings[0]['ishtml'] = $row['ishtml']; 
				// If Mail is HTML Body we get only the Altbody, else we get the body
				if ($row['ishtml'] == 'on') {
					//$mailings[0]['body'] = $row['body']; //This we get later when we choose tho see the HTML body
					$mailings[0]['altbody'] = $row['altbody']; 
				} elseif ($row['ishtml'] == 'off') {
					$mailings[0]['body'] = $row['body']; 				
				}
				$mailings[0]['mailgroup'] = $row['mailgroup']; 
				$mailings[0]['subscriberCount'] = $row['subscriberCount']; 
				$mailings[0]['started'] = $row['started']; 
				$mailings[0]['finished'] = $row['finished']; 
				$mailings[0]['sent'] = $row['sent']; 
					
			} //while
			
		} //if
		
	} elseif (is_array($selid)) {
	
		$safesql =& new SafeSQL_MySQL;
		$sql = $safesql->query("SELECT id, fromname, fromemail, frombounce, subject, body, altbody, 
			ishtml, mailgroup, subscriberCount, started, finished, sent FROM %s WHERE id IN (%q)", 
			array($dbo->table['mailing_history'], $selid) );

			
		if ($dbo->query($sql)) {
	
			$i = 0;
			while ($row = mysql_fetch_assoc($dbo->_result)) {
		
				$mailings[$i]['id'] = $row['id'];
				$mailings[$i]['fromname'] = $row['fromname'];
				$mailings[$i]['fromemail'] = $row['fromemail'];
				$mailings[$i]['frombounce'] = $row['frombounce'];
				$mailings[$i]['subject'] = $row['subject']; 
				// If Mail is HTML Body we get only the Altbody, else we get the body
				if ($row['ishtml'] == 'on') {
					//$mailings[$i]['body'] = $row['body']; //This we get later when we choose tho see the HTML body
					$mailings[$i]['altbody'] = $row['altbody']; 
				} elseif ($row['ishtml'] == 'off') {
					$mailings[$i]['body'] = $row['body']; 				
				}
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
	
		//Something wrong with the selid / selid Array
		$logger->addErr(_T("Problem during mailing details selection. The supplied ID has wrong format."));
		$mailings = NULL;
		
	}
	
	$dbo->dieOnQuery(TRUE);
	
	return $mailings;

} //dbGetMailInfo



// Removes one or more data records from the mailing_history table
// $delid can be numeric oder a Array
function & dbRemoveMailFromHistory(& $dbo, $delid) {

	if (is_numeric($delid)) {
		
		// delete from mailing_history table
		$safesql =& new SafeSQL_MySQL;
		$sql = $safesql->query("DELETE FROM %s WHERE id = %i ", array($dbo->table['mailing_history'], $delid) );
		$dbo->query($sql);

		//$logger->addMsg(_T("Mailing deleted: ". $delid));
		return true;
		
	} elseif (is_array($delid)) {
	
		// delete array of mails from mailing_history table
		$safesql =& new SafeSQL_MySQL;
		$sql = $safesql->query("DELETE FROM %s WHERE id IN (%q) ", array($dbo->table['mailing_history'], $delid) );
		$dbo->query($sql);

		//$logger->addMsg(_T("Mailing deleted: ". implode(',', $delid)));
		return true;

	} else {
		
		// There is a ID Format Error (not numeric, and no Array)
		// $logger->addErr(_T("Could not delete Mailing with ID: ". $delid));
		return false;
		
	}
	
} //dbRemoveMailFromHistory



function & dbGetHTMLBody(& $dbo, $selid) {

	$dbo->dieOnQuery(TRUE);
	
	$safesql =& new SafeSQL_MySQL;
	$sql = $safesql->query("SELECT body, ishtml FROM %s WHERE id = %i ",
			array($dbo->table['mailing_history'], $selid) );
			
	if ($dbo->query($sql)) {
	
		while ($row = mysql_fetch_assoc($dbo->_result)) {

			$mailbody['id'] = $row['id'];		
			$mailbody['body'] = $row['body']; 
					
		} //while

	} //if
	
	$dbo->dieOnQuery(TRUE);
	
	return $mailbody;
		
} //dbGetHTMLBody

 
