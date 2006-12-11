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


require_once ($pommo->_baseDir.'plugins/lib/interfaces/interface.dbhandler.php');

// Cool DB Query Wrapper from Monte Ohrt
require_once ($pommo->_baseDir.'inc/lib/safesql/SafeSQL.class.php');


class UserDBHandler implements iDbHandler {

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
		return $this->dbo->query($sql, 0);
	}
	
	
	
	/* ---------- Custom DB fetch functions ---------- */
	
	/**
	 *  Get User Matrix with Permission Group Information
	 */
	public function dbFetchUserMatrix() {
		$sql = $this->safesql->query("SELECT u.user_id, u.user_name, u.user_pass, p.perm_name, u.user_created, u.user_lastlogin, u.user_logintries, u.user_lastedit, u.user_active " .
				"FROM %s AS u LEFT JOIN %s AS p ON u.perm_id=p.perm_id ORDER BY u.user_id",
			array( 'pommomod_user', 'pommomod_perm' ) );
		$i=0;
		while ($row = $this->dbo->getRows($sql)) {
			
			//TODO if lastlogin VOR created -> KOMISCH?????
			if ($row['user_lastlogin'] == "0000-00-00") {
				$login = "-";
			} else {
				$login = $row['user_lastlogin'];
			}
			$user[$i] = array(
				'id' 	=> $row['user_id'],
				'name'	=> $row['user_name'],
				'pass'	=> $row['user_pass'],
				'perm'	=> $row['perm_name'],
				'created'	=> $row['user_created'],
				'lastlogin'=> $login,
				'logintries' => $row['user_logintries'],
				'lastedit' => $row['user_lastedit'],
				'active' => $row['user_active'],
				);
			$i++;
		}
		return $user;
	}
	
	/**
	 * Get Permissions Matrix
	 * Every Permission Set is stored in a GROUP, you can add a User to a Group, depending on his/her rights
	 */
	public function dbFetchPermissionMatrix() {
		$sql = $this->safesql->query("SELECT perm_id, perm_name, perm_perm, perm_desc FROM %s ",
			array('pommomod_perm') );
		$i=0;
		while ($row = $this->dbo->getRows($sql)) {
			$group[$i] = array(
				'id' 	=> $row['perm_id'],
				'name'	=> $row['perm_name'],
				'perm'	=> $row['perm_perm'],
				'desc'	=> $row['perm_desc'],
				);
			$i++;
		}
		return $group;
	}	

	/** 
	 * Fetch a Array that contains only ID / Permission Group Info
	 */
	public function dbFetchPermNames() {
		$sql = $this->safesql->query("SELECT perm_id, perm_name FROM %s ",
			array('pommomod_perm') );
		$i=0;
		while ($row = $this->dbo->getRows($sql)) {
			$group[$i] = array(
				'id'	=> $row['perm_id'],
				'name'	=> $row['perm_name'],
				);
			$i++;
		}
		return $group;
	}
	
	
	
	
	// Problem: wenn ein user keine GRP hat dann ist diese NULL und kann hier nicht mehr selected werden.
	/**
	 * Fetch User info for a single user ID
	 */
	public function dbFetchUserInfo($userid) {
		$sql = $this->safesql->query("SELECT u.user_id, u.user_name, u.user_pass,  p.perm_id, u.user_created, u.user_lastlogin, u.user_logintries, u.user_lastedit, u.user_active " .
				"FROM %s AS u LEFT JOIN %s AS p ON u.perm_id=p.perm_id WHERE u.user_id=%i", 
			array('pommomod_user', 'pommomod_perm', $userid) );
		while ($row = $this->dbo->getRows($sql)) {
			
			//TODO if lastlogin VOR created -> KOMISCH?????
			if ($row['user_lastlogin'] == "0000-00-00 00:00:00") {	//TIMESTAMP, not DATETIME
				$login = "-";
			} else {
				$login = $row['user_lastlogin'];
			}
			$user = array(
				'id' 		=> $row['user_id'],
				'name'		=> $row['user_name'],
				'pass'		=> $row['user_pass'],
				'perm'		=> $row['perm_name'],
				'created'	=> $row['user_created'],
				'lastlogin' => $login,
				'logintries' => $row['user_login_tries'],
			);
		}
		return $user;
	}
	/* TODO Permission_name unique??? */
	public function dbFetchPermInfo($groupid) {
		$sql = $this->safesql->query("SELECT perm_id, perm_name, perm_perm, perm_desc " .
				"FROM %s WHERE perm_id=%i",
			array('pommomod_perm', $groupid) );
		while ($row = $this->dbo->getRows($sql)) {
			$group = array(
				'id' 	=> $row['perm_id'],
				'name'	=> $row['perm_name'],
				'perm'	=> $row['perm_perm'],
				'desc'	=> $row['perm_desc'],
			);
		}
		return $group;
	}


	/* -------------------- [user] USE CASES -------------------- */

	/**
	 * Add a new User to Database.
	 * No username can be double TODO: make some duplicate check for "User already exists."
	 * Table row is unique, check not needed? only logger binding!
	 */
	public function dbAddUser($user, $pass, $perm) {
		if ($this->dbCheckUserName($user) == 0) {
			
			// We insert in DB only when the username does not exist
			$sql = $this->safesql->query("INSERT INTO %s (user_name, user_pass, perm_id, user_created, user_lastlogin, user_logintries, user_lastedit, user_active ) " .
					"VALUES ('%s', '%s', '%s',  NOW(), '0000-00-00', 0, NOW(), TRUE)",
				array('pommomod_user', $user, $pass, $perm ) );
			// If query fails return the Error
			if (!$this->dbo->query($sql)) {
				return  $this->_dbo->getError();
			} else {
				$affected = $this->dbo->affected();
				return ($affected == 1) ? 1 : FALSE;
			}
		} else {
			return "User already in DB.";	//errorstring
		}
	}
	
	public function dbDeleteUser($userid) {
		$sql = $this->safesql->query("DELETE FROM %s WHERE user_id=%i LIMIT 1",
			array('pommomod_user', $userid ) );
		// If query fails return the error.
		if (!$this->dbo->query($sql)) {
			return  $this->_dbo->getError();
		} else {
			$affected = $this->dbo->affected();
			return ($affected == 0) ? FALSE : $affected;
		}
	}
	
	public function dbEditUser($id, $user, $pass, $perm) {
		 $active = TRUE; // in titel
		// We could change only one column but i prefer the atomic transaction idea of this
		// If we change 4 dates in a loop and the loop is somehow aborted/fails, then there is data 
		// changed and some unchanged.
		$sql = $this->safesql->query("UPDATE %s SET user_name='%s', user_pass='%s', perm_id='%s', user_lastedit=NOW(), user_active=%s 
				WHERE user_id=%i",
			array('pommomod_user', $user, $pass, $perm, $active, $id) );
		//$count = $this->dbo->query($sql);
		//TODO	//return "User {$id}->{$column}:{$newval} changed.<br>";
		if (!$this->dbo->query($sql)) {
			return  $this->_dbo->getError();
		} else {
			$affected = $this->dbo->affected();
			return ($affected == 0) ? FALSE : $affected;
		}
	}
	/*	OLDfunction 
	public function dbUpdateUserData($id, $column, $newval) {
		$sql = $this->safesql->query("UPDATE %s SET %s='%s' WHERE user_id=%i",
			array('pommomod_user', $column, $newval, $id ) );
		$count = $this->dbo->query($sql);
		//TODO
		echo "<h1>User {$id}->{$column}:{$newval} changed.<br></h1>";
		return "User {$id}->{$column}:{$newval} changed.<br>";
	}*/	
	
	private function dbCheckUserName($user) {
		$sql = $this->safesql->query("SELECT user_name FROM %s WHERE user_name='%s'",
			array('pommomod_user', $user ) );
		$this->dbo->query($sql);
		$count = $this->dbo->affected();
		return $count;
	}
	
	

	/* -------------------- [permission groups] USE CASES -------------------- */

	/* public function dbGetGroupId($permname) {
		$sql = $this->safesql->query("SELECT perm_id " .
				"FROM %s WHERE perm_name=%s",
			array('pommomod_perm', $permname) );
		return $this->dbo->getRows($sql);
	}*/


	public function dbAddPermGroup($name, $perm, $desc) {
		/*if ($this->dbCheckUserName($user) == 0) {*/
			
			// We insert in DB only when the username does not exist
			$sql = $this->safesql->query("INSERT INTO %s (perm_name, perm_perm, perm_desc) VALUES ('%s', '%s', '%s')",
				array('pommomod_perm', $name, $perm, $desc ) );
			// If query fails return the Error
			if (!$this->dbo->query($sql)) {
				return  $this->_dbo->getError();
			} else {
				$affected = $this->dbo->affected();
				return ($affected == 1) ? 1 : FALSE;
			}
		/*} else {
			return "User already in DB.";
		}*/
	}
	
	public function dbEditPermGroup($permid, $name, $perm, $desc) {
		$sql = $this->safesql->query("UPDATE %s SET perm_name='%s', perm_perm='%s', perm_desc='%s'  
				WHERE perm_id=%i",
			array('pommomod_perm', $name, $perm, $desc, $permid ) );
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
	
	public function dbDeletePermGroup($permid) {
		$sql = $this->safesql->query("DELETE FROM %s WHERE perm_id=%i",
			array('pommomod_perm', $permid ) );
		$sql2 = $this->safesql->query("UPDATE %s SET user_perm=NULL WHERE user_perm=%i",		// UPDATE `pommomod_user` SET `user_perm` = NULL WHERE `user_id` =44 LIMIT 1 ;
			array('pommomod_user', $permid ) );
		// If query fails return the error.
		if (!$this->dbo->query($sql) OR !$this->dbo->query($sql2)) {
			return  $this->_dbo->getError();
		} else {
			$affected = $this->dbo->affected();
			return ($affected == 0) ? FALSE : $affected;
		}
	}
	

} //UserDBHandler


?>
