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

define('_IS_VALID', TRUE);

require('../../bootstrap.php');
require_once (bm_baseDir.'/inc/db_groups.php');
$poMMo = & fireup('secure');
$dbo = & $poMMo->openDB();

$group_id = str2db($_GET['group_id']);

// delete group if requested
if (!empty ($_GET['confirm'])) {
	if (dbGroupDelete($dbo, $group_id))
		header('Location: '.bm_http.bm_baseUrl.'/admin/subscribers/subscribers_groups.php');
	else
		$group_id = -1; // this will force an error below (invalid groupId)	
}

/** poMMo templating system **/
// header settings -->

$_nologo = FALSE;
$_menu = array ();
$_menu[] = '<a href="'.bm_baseUrl.'/index.php?logout=TRUE">Logout</a>';
$_menu[] = '<a href="subscribers_groups.php">Groups Page</a>';
$_menu[] = '<a href="'.$poMMo->_config['site_url'].'">'.$poMMo->_config['site_name'].'</a>';

// right bar settings -->
$_nomenu = FALSE; // turn off main "admin menu" in right bar
$_nodemo = FALSE; // turn off display of poMMo demonstration mode status

$_extmenu = array();
$_extmenu['name'] = "Subscriber Management";
$_extmenu['links'] = array();
$_extmenu['links'][] = "<a href=\"subscribers_manage.php\">Manage</a>";
$_extmenu['links'][] = "<a href=\"subscribers_import.php\">Import</a>";
$_extmenu['links'][] = "<a href=\"subscribers_groups.php\">Groups</a>";

include (bm_baseDir.'/setup/top.php');
/** End templating system **/
?>

<h1>Delete Mailing Group</h1>
<br>

<?php
if (empty ($group_id) || !is_numeric($group_id) || !dbGroupCheck($dbo, $group_id)) {
	// bad groupId / nonexistant group
	echo '
				<img src="'.bm_baseUrl.'/img/icons/error.png" align="middle">
					<b>ERROR</b>: Invalid Group.';
} else {
	
	$groups = & dbGetGroups($dbo, $group_id);
	$group_name = & $groups[$group_id];
	
	
	// confirm choice
	require_once (bm_baseDir.'/inc/printouts.php');
	$okUrl = $_SERVER['PHP_SELF'].'?group_id='.$group_id.'&confirm=TRUE';
	$note = 'Remove group <b>'.$group_name.'</b>?';
	printConfirm($okUrl, 'subscribers_groups.php', $note);
}

include (bm_baseDir.'/setup/footer.php');
?>