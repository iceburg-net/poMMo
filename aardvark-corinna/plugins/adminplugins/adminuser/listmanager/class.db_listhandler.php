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


class ListDBHandler implements iDbHandler {

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
	/*public function dbFetchUser() {

		$sql = $this->safesql->query("SELECT user_id, user_name,  group_name " .	//user_pass,
				"FROM %s LEFT JOIN %s ON user_group=group_id ORDER BY user_id",
			array( 'pommomod_user', 'pommomod_permgroups' ) );
		$i=0;
		while ($row = $this->dbo->getRows($sql)) {
			$user[$i] = array(
				'user_id' 		=> $row['user_id'],
				'user_name'		=> $row['user_name'],
				//'user_pass'		=> $row['user_pass'],
				'user_group'	=> $row['group_name'],
				);
			$i++;
		}
		return $user;
	}*/


	/*public function dbFetchUserLists() {
		$sql = $this->safesql->query("SELECT u.user_name, g.group_name, l.list_name " .	// count(l.list_id) AS numlist
				"FROM %s AS u LEFT JOIN %s AS lu ON u.user_id=lu.user_id " .
				"LEFT JOIN %s AS l ON lu.list_id=l.list_id " .
				"LEFT JOIN %s AS g ON u.user_group=g.group_id " .
				"ORDER BY u.user_id ",	//GROUP BY u.user_id 
			array( 'pommomod_user', 'pommomod_list_user', 'pommomod_list', 'pommomod_permgroups' ) );	
			
		$i=0;
		while ($row = $this->dbo->getRows($sql)) {
			$user[$i] = array(
				//'user_id' 		=> $row['user_id'],
				'user_name'		=> $row['user_name'],
				'user_group'	=> $row['group_name'],
				'numlist'		=> $row['numlist'],
				'list_name'		=> $row['list_name'],
				//'list_user_data'	=> $row['list_user_data'],
			);
			$i++;
		}
		
		echo "<div style='background-color:red;'>"; echo $this->dbo->affected(); echo "</div>";
		return $user;
	}*/
	public function dbFetchUserLists() {
		$sql = $this->safesql->query("SELECT u.user_id, u.user_name, g.group_name, count(lu.list_id) AS numlist " .	
				"FROM %s AS u LEFT JOIN %s AS lu ON u.user_id=lu.user_id " .
				//"LEFT JOIN %s AS l ON lu.list_id=l.list_id " .
				"LEFT JOIN %s AS g ON u.user_group=g.group_id " .
				"GROUP BY u.user_id ORDER BY u.user_id ",
			array( 'pommomod_user', 'pommomod_list_user', 'pommomod_permgroups' ) );	// als 3. 'pommomod_list'
		$i=0;
		while ($row = $this->dbo->getRows($sql)) {
			$user[$i] = array(
				'user_id'		=> $row['user_id'],
				'user_name'		=> $row['user_name'],
				'user_group'	=> $row['group_name'],
				'numlist'		=> $row['numlist'],
			);
			$i++;
		}
		
		for ($i=0; $i < count($user); $i++) {
			if ($user[$i]['numlist'] > 0 ) {
				$user[$i]['lists'] = $this->dbGetListsForUser($user[$i]['user_id']); //$row['list_id']
			}
		}
		
		return $user;
	}
	
	public function dbGetListsForUser($userid) {
		$sql1 = $this->safesql->query("SELECT lu.user_id, l.list_id, l.list_name, l.list_desc " .
				"FROM %s AS l, %s AS lu WHERE l.list_id=lu.list_id AND lu.user_id=%i",	//
			array('pommomod_list', 'pommomod_list_user', $userid) );
		$i=0;
		while ($row1 = $this->dbo->getRows($sql1)) {
			$lists[$i] = array(
				'user_id'		=> $row1['user_id'],
				'list_id'		=> $row1['list_id'],
				'list_name'		=> $row1['list_name'],
				'list_desc'		=> $row1['list_desc'],
			);
			$i++;
		}
		return $lists;
	}
	
	
	public function dbAddList($name, $desc, $userid) {
			$sql = $this->safesql->query("INSERT INTO %s (list_name, list_desc) VALUES ('%s', '%s'); ",
				array('pommomod_list', $name, $desc ) );
			$sql2 = $this->safesql->query("INSERT INTO %s (list_id, user_id, list_user_data) VALUES (LAST_INSERT_ID(), '%s', 'k.a.')", 
				array('pommomod_list_user', $userid) );
				
			if (!$this->dbo->query($sql) OR !$this->dbo->query($sql2)) {
				return  $this->_dbo->getError();
			} else {
				return TRUE;
				/*$affected = $this->dbo->affected();
				return ($affected == 2) ? 1 : FALSE;*/
			}
	}
	
	public function dbDeleteList($listid, $userid) {
		$sql = $this->safesql->query("DELETE FROM %s WHERE list_id=%i",
			array('pommomod_list', $listid ) );
		$sql2 = $this->safesql->query("DELETE FROM %s WHERE list_id=%i AND user_id=%i",
			array('pommomod_list_user', $listid, $userid) );
		if (!$this->dbo->query($sql) OR !$this->dbo->query($sql2)) {
			return  $this->_dbo->getError();
		} else {
			return TRUE;
			/*$affected = $this->dbo->affected();
			return ($affected == 0) ? FALSE : $affected;*/
		}
	}
	public function dbEditList($name, $desc) {
		$sql = $this->safesql->query("UPDATE %s SET list_name='%s', list_desc='%s'  
				WHERE list_id=%i",
			array('pommomod_list', $name, $desc ) );
		if (!$this->dbo->query($sql)) {
			return  $this->_dbo->getError();
		} else {
			$affected = $this->dbo->affected();
			return ($affected == 0) ? FALSE : $affected;
		}
	}
	
	public function dbGetListInfo($listid, $userid) {
		$sql = $this->safesql->query("SELECT l.list_id, l.list_name, lu.user_id " .
				"FROM %s AS lu, %s AS l WHERE l.list_id=%i AND lu.user_id=%i", 
			array('pommomod_list_user', 'pommomod_list', $listid, $userid) );
		$i=0;
		while ($row = $this->dbo->getRows($sql)) {
			$listinfo[$i] = array(
				'list_id'		=> $row1['list_id'],
				'list_name'		=> $row1['list_name'],
				'user_id'		=> $row1['user_id'],
			);
			$i++;
		}
		return $listinfo;
	}

} //ListDBHandler

?>

