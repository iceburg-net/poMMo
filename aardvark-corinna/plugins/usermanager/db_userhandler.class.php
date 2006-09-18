<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * Created: Corinna Thoeni <corinn at gmx dot net> - 07.09.2006
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


class UserHandler {

	private $dbo;
	
	public function __construct($dbo) {
		$this->registerdbo($dbo);
	}
	
	private function registerdbo($dbo) {
		$this->dbo = $dbo;
	}
	
	public function dbFetchUser() {
		$safesql =& new SafeSQL_MySQL;
		$sql = $safesql->query("SELECT user_id, user_name, user_pass, user_group, user_rights " .
				"FROM %s",
			array('pommomod_user') );
		$i=0;
		while ($row = $this->dbo->getRows($sql)) {
			$user[$i] = array(
				'user_id' 		=> $row['user_id'],
				'user_name'		=> $row['user_name'],
				'user_pass'		=> $row['user_pass'],
				'user_group'	=> $row['user_group'],
				'user_rights'	=> $row['user_rights'],
				);
			$i++;
		}
		return $user;
	}

	public function dbFetchUserInfo($userid) {
		$safesql =& new SafeSQL_MySQL;
		$sql = $safesql->query("SELECT user_id, user_name, user_pass, user_group, user_rights " .
				"FROM %s WHERE user_id=%i",
			array('pommomod_user', $userid) );
		while ($row = $this->dbo->getRows($sql)) {
			$user = array(
				'user_id' 		=> $row['user_id'],
				'user_name'		=> $row['user_name'],
				'user_pass'		=> $row['user_pass'],
				'user_group'	=> $row['user_group'],
				'user_rights'	=> $row['user_rights'],
				);
		}
		return $user;
	}
	

	public function dbUpdateUserData($id, $column, $newval) {
		$safesql =& new SafeSQL_MySQL;
		$sql = $safesql->query("UPDATE %s SET %s='%s' WHERE user_id=%i",
			array('pommomod_user', $column, $newval, $id ) );
		$count = $this->dbo->query($sql);
		echo "<h1>User {$id}->{$column}:{$newval} changed.<br></h1>";
		return "User {$id}->{$column}:{$newval} changed.<br>";
	}

}
?>