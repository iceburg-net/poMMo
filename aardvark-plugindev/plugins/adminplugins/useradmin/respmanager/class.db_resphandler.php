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


require_once ($pommo->_baseDir.'plugins/lib/interfaces/interface.dbhandler.php');

// Cool DB Query Wrapper from Monte Ohrt
require_once ($pommo->_baseDir.'inc/lib/safesql/SafeSQL.class.php');


class RespDBHandler implements iDbHandler {

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
	public function dbFetchRespMatrix() {
		$sql = $this->safesql->query("SELECT u.user_id, u.user_name, r.rp_realname, r.rp_bounceemail, r.rp_sonst " .	//user_pass,
				//"FROM %s AS u LEFT JOIN %s AS p ON u.user_perm=p.perm_id ORDER BY u.user_id",
				"FROM %s AS u RIGHT JOIN %s as r ON u.user_id=r.user_id ", 
			array( 'pommomod_user',  'pommomod_responsibleperson' ) );
		$i=0;
		while ($row = $this->dbo->getRows($sql)) {
			$user[$i] = array(
				'uid' 		=> $row['user_id'],
				'name'		=> $row['user_name'],
				'realname'	=> $row['rp_realname'],
				//'surname'	=> $row['user_realsurname'],
				'bounceemail'	=> $row['rp_bounceemail'],
				//'realname'	=> $row['user_realname'],
				);
			$i++;
		}
		return $user;
	}
	
	public function dbFetchUser() {
		$sql = $this->safesql->query("SELECT u.user_id, u.user_name " .	//user_pass,
				"FROM %s AS u ORDER BY u.user_id",
				//"FROM %s AS u RIGHT JOIN %s as r ON u.user_id=r.user_id ", 
			array( 'pommomod_user' ) );
		$i=0;
		while ($row = $this->dbo->getRows($sql)) {
			$user[$i] = array(
				'uid' 		=> $row['user_id'],
				'name'		=> $row['user_name'],
				);
			$i++;
		}
		return $user;
	}
	
	
	public function dbFetchUserData($userid) {
		$sql = $this->safesql->query("SELECT r.user_id, u.user_name, r.rp_realname, r.rp_bounceemail " .	//user_pass,
				"FROM %s AS u RIGHT JOIN %s as r ON u.user_id=r.user_id WHERE u.user_id=%i LIMIT 1", 
			array( 'pommomod_user',  'pommomod_responsibleperson', $userid ) );

		while ($row = $this->dbo->getRows($sql)) {
			$user = array(
				'id' 		=> $row['user_id'],
				'username'	=> $row['user_name'],
				'realname'	=> $row['rp_realname'],
				'bounceemail'	=> $row['rp_bounceemail'],
				//'realname'	=> $row['user_realname'],
				);
		}
		return $user;
	}
	
	
	
	public function dbAddResponsiblePerson( $uid, $realname, $bounce ) {
			$sql = $this->safesql->query("INSERT INTO %s (user_id, rp_realname, rp_bounceemail, rp_sonst) VALUES ('%s', '%s', '%s', '%s', 'blah'); ",
				array('pommomod_responsibleperson', $uid, $realname, $bounce ) );
				
			if (!$this->dbo->query($sql)) {
				return  $this->_dbo->getError();
			} else {
				return TRUE;
			}
	}
	
	// zB für neu zu weisungen?
	public function dbEditResponsiblePerson( $uid, $realname, $bounce ) {
		$sql = $this->safesql->query("UPDATE %s SET rp_realname='%s', rp_bounceemail='%s'
				WHERE user_id=%i",
			array('pommomod_responsibleperson', $realname, $bounce, $uid ) );
		if (!$this->dbo->query($sql)) {
			return  $this->_dbo->getError();
		} else {
			$affected = $this->dbo->affected();
			return ($affected == 0) ? FALSE : $affected;
		}
	}
	
	public function dbDeleteResponsiblePerson( $uid ) {
			$sql = $this->safesql->query("DELETE FROM %s WHERE user_id=%i LIMIT 1",
				array('pommomod_responsibleperson', $uid) );
			if (!$this->dbo->query($sql)) {
				return  $this->_dbo->getError();
			} else {
				return TRUE;
			}
	}
	
	
	public function dbGetGroups() {
		$groups = array ();
		$sql = $this->safesql->query("SELECT group_id, group_name FROM %s ORDER BY group_name",
			array($this->dbo->table['groups']) );
		while ($row = $this->dbo->getRows($sql, TRUE)) {
			$groups[$row[0]] = $row[1];
		}
		return $groups;
	}

} //ListDBHandler

?>


