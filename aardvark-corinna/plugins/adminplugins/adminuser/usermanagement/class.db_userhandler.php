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


require_once (bm_baseDir.'/plugins/pluginregistry/interfaces/interface.dbhandler.php');

// Cool DB Query Wrapper from Monte Ohrt
require_once (bm_baseDir.'/inc/safesql/SafeSQL.class.php');


class UserDBHandler implements iDbHandler {

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
	
	
	
	/* Custom DB fetch functions */
	
	public function dbFetchUser() {
		$sql = $this->safesql->query("SELECT user_id, user_name, user_pass, user_group, user_rights " .
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
	
	public function dbAddUser($user, $pass, $group, $perm) {
		$sql = $this->safesql->query("INSERT INTO %s (user_name, user_pass, user_group, user_rights) VALUES ('%s', '%s', '%s', '%s')",
			array('pommomod_user', $user, $pass, $group, $pass ) );
		$ret = $this->dbo->query($sql);
		echo "<b>ADDUSER: {$ret}</b>";
	}
	public function dbDeleteUser($userid) {
		$sql = $this->safesql->query("DELETE FROM %s WHERE user_id=%i",
			array('pommomod_user', $userid ) );
		$ret = $this->dbo->query($sql);
		echo "<b>DELETEUSER: {$ret}</b>";
	}
	

	public function dbFetchUserInfo($userid) {
		$sql = $this->safesql->query("SELECT user_id, user_name, user_pass, user_group, user_rights " .
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
		$sql = $this->safesql->query("UPDATE %s SET %s='%s' WHERE user_id=%i",
			array('pommomod_user', $column, $newval, $id ) );
		$count = $this->dbo->query($sql);
		echo "<h1>User {$id}->{$column}:{$newval} changed.<br></h1>";
		return "User {$id}->{$column}:{$newval} changed.<br>";
	}

} //UserDBHandler

?>
