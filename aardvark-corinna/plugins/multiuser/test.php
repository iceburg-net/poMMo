<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * Created: Corinna Thoeni <corinn at gmx dot net> - 14.11.2006
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/
 
defined('_IS_VALID') or die('Move along...');

// Cool DB Query Wrapper from Monte Ohrt
require_once (bm_baseDir.'/inc/safesql/SafeSQL.class.php');

function getPerm($dbo, $username) {
	$safesql =& new SafeSQL_MySQL;
	$sql = $safesql->query("SELECT p.perm_perm FROM %s AS p, %s AS u " .
			"WHERE p.perm_id=u.perm_id AND u.user_name='%s' LIMIT 1 ",
		array('pommomod_perm', 'pommomod_user', $username) );
		$count = $dbo->query($sql,0);	// -> row 0
		return ($count) ? $count : "blah";
}

?>
