<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * Created: Corinna Thoeni <corinn at gmx dot net> - 28.09.2006
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


require_once (bm_baseDir.'/plugins/adminplugins/adminuser/interfaces/interface.dbhandler.php');

// Cool DB Query Wrapper from Monte Ohrt
require_once (bm_baseDir.'/inc/safesql/SafeSQL.class.php');


//TODO
// Change all to some table management :$dbo->table['mailing_history'] and store table aliases in $poMMo
// Problem: $poMMo as a static / global variable?
// add $logger
// TODO maybe make plain/html/on/off consistent?


class QueueDBHandler implements iDbHandler {

	private $dbo;
	private $safesql;


	public function __construct($dbo) {
		$this->dbo = $dbo;
		$this->safesql =& new SafeSQL_MySQL;
	}


	/** Returns if the Plugin itself is active */
	public function & dbPluginIsActive($pluginame) {
		$sql = $this->safesql->query("SELECT plugin_active FROM %s " .
				"WHERE plugin_uniquename='%s' ", 
			array(pommomod_plugin, $pluginame) );
		return $this->dbo->query($sql, 0);	//row 0
	}



	/** Write a Mailing to the mailing queue */
	public function & dbSaveToQueue($input) {
		
		if ($input['ishtml'] == "plain") {
			$html = "off";
		} elseif ($input['ishtml'] == "html") {
			$html = "on";
		} else {
			// $html has a wrong value? this should not happen if the plain/html value is always written from the send script
			// $retstr = "QueuePlugin: db_queue: Value ishtml undefined.";
			echo "QueueDBHAndler: dbSaveToQUeue: ishtml undefined.";
		}
		
		// Write Mailgroup ID in table because we want to send the mail in the future (consistency/mailgroup can change)
		$sql = $this->safesql->query("INSERT INTO %s (fromname, fromemail, frombounce, subject, body, altbody, ishtml, 
			mailgroup, sent, date, charset) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s','%s', NOW(), '%s'); ",
			array (pommomod_mailing_queue, $input['fromname'], $input['fromemail'], $input['frombounce'], $input['subject'],
				$input['body'], $input['altbody'], $html, $input['mailgroup'], 0, $input['charset']) );

		return $this->dbo->query($sql);	// Should be false or true in this case.
		
	} //dbSaveToQueue


	/** Get the number of mailings in the table mailing_history of the database */
	public function & dbGetMailingCount() {
		$sql = $this->safesql->query("SELECT count(qid) FROM %s ", 
			array(pommomod_mailing_queue) );
		$count = $this->dbo->query($sql,0);	// -> row 0
		return ($count) ? $count : 0;	//Return the count or 0 and not a FALSE (FALSE = no result in this case)
	} //dbGetMailingCount



	/** Get the mailings history matrix */
	public function & dbGetMailingQueue($start, $limit, $order, $orderType) {
		
		/*$sql = $safesql->query("SELECT q.qid, q.fromname, q.fromemail, q.frombounce, q.subject, 
				q.ishtml, g.group_name AS mailgroup, q.date, q.sent FROM %s AS q, %s AS g WHERE q.mailgroup = g.group_id 
				ORDER BY %s %s LIMIT %s, %s ", 	array(pommomod_mailing_queue, $dbo->table['groups'], $order, $orderType, $start, $limit) );*/
		
		$sql = $this->safesql->query("SELECT qid, fromname, fromemail, frombounce, subject, 
			ishtml, (SELECT group_name FROM %s WHERE group_id=(SELECT mailgroup FROM %s LIMIT 1 ) LIMIT 1 ) AS mailgroup,
			date, sent FROM %s ORDER BY %s %s LIMIT %s, %s ", 
				array($this->dbo->table['groups'], $this->dbo->table['groups'], pommomod_mailing_queue, $order, $orderType, $start, $limit) );	

		$mailings = array();

		while ($row = $this->dbo->getRows($sql)) {
		 	
		 	// If mailgroup == NULL -> String 'all'
	 		$mailgroup = $row['mailgroup'];
	 		if ($mailgroup == '') {		
	 			$mailgroup = 'all';
	 		}		
	 		$mailings[] = array(
	 			'mailid' => $row['qid'],
	 			'creator' => $row['fromname'],
	 			'subject' => $row['subject'],
	 			'ishtml' => $row['ishtml'],
	 			'mailgroup' => $mailgroup, //$row['mailgroup'],
	 			'date' => $row['date'],
	 			'sent' => $row['sent'],
	 		);
		 }
		return $mailings;	// $mailings is void or a matrix/array
		
	} //dbGetMailingQueue
		
		
		
	/** Get data on a single Mailing from numeric ID Information */
	// %q is for multiple mail loading, maybe later for now i don't want to use the time to make things pretty in smarty. 
	public function & dbGetMailingData($id) {

		if (!empty($id)) {
			
			//TODO # recipients in history is written into the database
			//(SELECT count(group_id) FROM pommo_groups WHERE)AS subscriberCount, 
			$sql = $this->safesql->query("SELECT qid, fromname, fromemail, frombounce, subject, body, altbody, ishtml,
				mailgroup AS mailgroupid, (SELECT group_name FROM %s WHERE group_id=(SELECT mailgroup FROM %s LIMIT 1 ) LIMIT 1 ) AS mailgroup,
				
				charset FROM %s WHERE qid IN (%q) ", 
				array($this->dbo->table['groups'], pommomod_mailing_queue, pommomod_mailing_queue, $id) );

			$mailing = $this->dbo->getRows($sql);
			
			if ($mailing['ishtml'] == 'on') {
				$mailing['ishtml'] = 'html';
			} elseif ($mailing['ishtml'] == 'off') {
				$mailing['ishtml'] = 'plain';
			} else {
				echo "QueueDBHandler: dbGetMailingData: ishtml on/off/plain/html conversion problem<br>";	//logger
			}
			return $mailing;
			
		} else {
			echo "QueueDBHandler: dbGetMailingData: id is not numeric or id is empty."; //logger
		}
		
	} //dbGetMailData
		
		
	/** Removes one or more data records from the mailing_history table, $del id has to be 1 number */
	// allow multiple mailing deletion for later
	public	function & dbRemoveMailFromQueue($delid) {
			
		if (empty($delid))
			return false;

		$sql = $this->safesql->query("DELETE FROM %s WHERE qid IN (%q) ", 
			array(pommomod_mailing_queue, $delid) );
			
		return $this->dbo->query($sql);	// Return BOOL of the deletion process
		
	} //dbRemoveMailFromQueue
	
		
		
} //QueueHandler

?>
