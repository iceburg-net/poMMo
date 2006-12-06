<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * Created: Corinna Thoeni <corinn at gmx dot net> - 21.09.2006
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


class BounceDBHandler implements iDbHandler {

	private $dbo;
	private $safesql;


	public function __construct($dbo) {
		$this->dbo = $dbo;
		$this->safesql =& new SafeSQL_MySQL;
	}

	/** Returns if the Plugin itself is active */
	public function & dbPluginIsActive($pluginname) {
		$sql = $this->safesql->query("SELECT plugin_active FROM %s " .
				"WHERE plugin_uniquename='%s' ", 
			array(pommomod_plugin, $pluginname) );
		return $this->dbo->query($sql, 0);	//row 0
	}
	
	
	public function dbGetBounceMatrix() {
		$sql = $this->safesql->query("SELECT bounce_id, bounce_email_bounced, bounce_header, bounce_mailbody, bounce_reason, email " .
				"FROM %s LEFT JOIN %s ON subscriber_id=subscribers_id",	// JOINEN mit Users
			array( 'pommomod_bounce', 'pommo_subscribers' ) );
		$i=0; $bounce = NULL;
		while ($row = $this->dbo->getRows($sql)) {
			$bounce[$i] = array(
				'id' 		=> $row['bounce_id'],
				'email'		=> $row['bounce_email_bounced'],
				'header'	=> $row['bounce_header'],
				'mailbody'	=> $row['bounce_mailbody'],
				'reason'	=> $row['bounce_reason'],
				'subscriber'	=> $row['email'],
				'maillist'	=> 'maillist here',//$row['']
				);
			$i++;
		}
		return $bounce;
	}
	
	
	
	public function dbInsertParsedBounce($email, $header, $body, $reason, $subscriber) {
		// Select subscribers ID first!!
		$email = $header = $body = $reason = $subscriber = NULL;
		$sql = $this->safesql->query("INSERT INTO %s (bounce_email_bounced, bounce_header, " .
				"bounce_mailbody, bounce_reason, subscriber_id) VALUES ('%s', '%s', '%s', '%s', '%i') ", 
			array( 'pommomod_bounce', $email, $header, $body, $reason, $subscriber ) );
			
		$this->dbo->query($sql);
	}




	public function dbDeleteBounce($bounceid) {
		$sql = $this->safesql->query("DELETE FROM %s WHERE bounce_id=%i ",
			array('pommomod_bounce', $bounceid) );
		$this->dbo->query($sql);
	}
	
} //BounceHandler

?>
