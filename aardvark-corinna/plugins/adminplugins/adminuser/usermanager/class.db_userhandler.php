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


require_once (bm_baseDir.'/plugins/adminplugins/adminuser/interfaces/interface.dbhandler.php');

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

		$sql = $this->safesql->query("SELECT user_id, user_name, user_pass, group_name " .
				"FROM %s LEFT JOIN %s ON user_group=group_id ORDER BY user_id",
			array( 'pommomod_user', 'pommomod_permgroups' ) );
		$i=0;
		while ($row = $this->dbo->getRows($sql)) {
			$user[$i] = array(
				'user_id' 		=> $row['user_id'],
				'user_name'		=> $row['user_name'],
				'user_pass'		=> $row['user_pass'],
				'user_group'	=> $row['group_name'],
				);
			$i++;
		}
		return $user;
	}
	
	/**
	 * Add a new User to Database.
	 * No username can be double TODO: make some duplicate check for "User already exists."
	 */
	public function dbAddUser($user, $pass, $group) {
		if ($this->dbCheckUserName($user) == 0) {
			
			// We insert in DB only when the username does not exist
			$sql = $this->safesql->query("INSERT INTO %s (user_name, user_pass, user_group ) VALUES ('%s', '%s', '%s')",
				array('pommomod_user', $user, $pass, $group ) );
			//$this->dbo->query($sql);
			// If query fails return the Error
			if (!$this->dbo->query($sql)) {
				return  $this->_dbo->getError();
			} else {
				$affected = $this->dbo->affected();
				return ($affected == 1) ? 1 : FALSE;
			}
			
		} else {
			return "User already in DB.";
		}
	}
	
	
	public function dbDeleteUser($userid) {
		$sql = $this->safesql->query("DELETE FROM %s WHERE user_id=%i",
			array('pommomod_user', $userid ) );
		// If query fails return the error.
		if (!$this->dbo->query($sql)) {
			return  $this->_dbo->getError();
		} else {
			$affected = $this->dbo->affected();
			return ($affected == 0) ? FALSE : $affected;
		}
	}
	
	public function dbEditUser($id, $user, $pass, $group) {
		// We could change only one column but i prefer the atomic transaction idea of this
		// If we change 4 dates in a loop and the loop is somehow aborted/fails, then there is data 
		// changed and some unchanged.
		$sql = $this->safesql->query("UPDATE %s SET user_name='%s', user_pass='%s', user_group='%s' 
				WHERE user_id=%i",
			array('pommomod_user', $user, $pass, $group, $id ) );
		//$count = $this->dbo->query($sql);
		//TODO
		//return "User {$id}->{$column}:{$newval} changed.<br>";
		if (!$this->dbo->query($sql)) {
			return  $this->_dbo->getError();
		} else {
			$affected = $this->dbo->affected();
			return ($affected == 0) ? FALSE : $affected;
		}
	}
	

	public function dbFetchUserInfo($userid) {
		$sql = $this->safesql->query("SELECT u.user_id, u.user_name, u.user_pass, g.group_name " .
				"FROM %s AS u, %s AS g WHERE u.user_id=%i AND u.user_group=g.group_id ",
			array('pommomod_user', 'pommomod_permgroups', $userid) );
		while ($row = $this->dbo->getRows($sql)) {
			$user = array(
				'user_id' 		=> $row['user_id'],
				'user_name'		=> $row['user_name'],
				'user_pass'		=> $row['user_pass'],
				'user_group'	=> $row['group_name'],
				);
		}
		return $user;
	}
	
	
	private function dbCheckUserName($user) {
		$sql = $this->safesql->query("SELECT user_name FROM %s WHERE user_name='%s'",
			array('pommomod_user', $user ) );
		$this->dbo->query($sql);
		$count = $this->dbo->affected();
		return $count;
	}
	
/*
	public function dbUpdateUserData($id, $column, $newval) {
		$sql = $this->safesql->query("UPDATE %s SET %s='%s' WHERE user_id=%i",
			array('pommomod_user', $column, $newval, $id ) );
		$count = $this->dbo->query($sql);
		//TODO
		echo "<h1>User {$id}->{$column}:{$newval} changed.<br></h1>";
		return "User {$id}->{$column}:{$newval} changed.<br>";
	}*/



	public function dbGetGroups() {
		$sql = $this->safesql->query("SELECT group_id, group_name, group_perm, group_desc FROM %s ",
			array('pommomod_permgroups') );
		$i=0;
		while ($row = $this->dbo->getRows($sql)) {
			$group[$i] = array(
				'group_id' 		=> $row['group_id'],
				'group_name'	=> $row['group_name'],
				'group_perm'	=> $row['group_perm'],
				'group_desc'	=> $row['group_desc'],
				);
			$i++;
		}
		return $group;
	}
	public function dbFetchGroupInfo($groupid) {
		$sql = $this->safesql->query("SELECT group_id, group_name, group_perm, group_desc " .
				"FROM %s WHERE group_id=%i",
			array('pommomod_permgroups', $groupid) );
		while ($row = $this->dbo->getRows($sql)) {
			$group = array(
				'group_id' 		=> $row['group_id'],
				'group_name'	=> $row['group_name'],
				'group_perm'	=> $row['group_perm'],
				'group_desc'	=> $row['group_desc'],
				);
		}
		return $group;
	}
	public function dbGetGroupId($groupname) {
		$sql = $this->safesql->query("SELECT group_id " .
				"FROM %s WHERE group_name=%s",
			array('pommomod_permgroups', $groupname) );
		return $this->dbo->getRows($sql);
	}

	public function dbAddGroup($name, $perm, $desc) {
		//if ($this->dbCheckUserName($user) == 0) {
			
			// We insert in DB only when the username does not exist
			$sql = $this->safesql->query("INSERT INTO %s (group_name, group_perm, group_desc) VALUES ('%s', '%s', '%s')",
				array('pommomod_permgroups', $name, $perm, $desc ) );
			//$this->dbo->query($sql);
			// If query fails return the Error
			if (!$this->dbo->query($sql)) {
				return  $this->_dbo->getError();
			} else {
				$affected = $this->dbo->affected();
				return ($affected == 1) ? 1 : FALSE;
			}
		//} else {
		//	return "User already in DB.";
		//}
	}
	
	public function dbEditGroup($groupid, $name, $perm, $desc) {
		$sql = $this->safesql->query("UPDATE %s SET group_name='%s', group_perm='%s', group_desc='%s'  
				WHERE group_id=%i",
			array('pommomod_permgroups', $name, $perm, $desc, $groupid ) );
		//$count = $this->dbo->query($sql);
		//TODO
		//return "User {$id}->{$column}:{$newval} changed.<br>";
		if (!$this->dbo->query($sql)) {
			return  $this->_dbo->getError();
		} else {
			$affected = $this->dbo->affected();
			return ($affected == 0) ? FALSE : $affected;
		}
	}
	public function dbDeleteGroup($groupid) {
		$sql = $this->safesql->query("DELETE FROM %s WHERE group_id=%i",
			array('pommomod_permgroups', $groupid ) );
		// If query fails return the error.
		if (!$this->dbo->query($sql)) {
			return  $this->_dbo->getError();
		} else {
			$affected = $this->dbo->affected();
			return ($affected == 0) ? FALSE : $affected;
		}
	}
	
	public function dbFetchGroupNames() {
		$sql = $this->safesql->query("SELECT group_id, group_name FROM %s ",
			array('pommomod_permgroups') );
		$i=0;
		while ($row = $this->dbo->getRows($sql)) {
			$group[$i] = array(
				'group_id'	=> $row['group_id'],
				'group_name'	=> $row['group_name'],
				);
			$i++;
		}
		return $group;
	}

} //UserDBHandler

?>
