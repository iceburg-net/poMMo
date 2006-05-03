<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * SOURCE: http://bmail.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/

define('_IS_VALID', TRUE);

require('../../bootstrap.php');
require_once (bm_baseDir.'/inc/db_groups.php');
$bMail = & fireup('secure');
$dbo = & $bMail->openDB();

// add group if requested
if (!empty ($_POST['group_name']))
	dbGroupAdd($dbo, str2db($_POST['group_name']));

/** bMail templating system **/
// header settings -->
$_head = '<script src="inc/js/bform.js" type="text/javascript"></script>';

$_nologo = FALSE;
$_menu = array ();
$_menu[] = '<a href="'.bm_baseUrl.'/index.php?logout=TRUE">Logout</a>';
$_menu[] = '<a href="admin_subscribers.php">Subscribers Page</a>';
$_menu[] = '<a href="'.$bMail->_config['site_url'].'">'.$bMail->_config['site_name'].'</a>';

// right bar settings -->
$_nomenu = FALSE; // turn off main "admin menu" in right bar
$_nodemo = FALSE; // turn off display of bMail demonstration mode status

$_extmenu = array ();
$_extmenu['name'] = "Subscriber Management";
$_extmenu['links'] = array ();
$_extmenu['links'][] = "<a href=\"subscribers_manage.php\">Manage</a>";
$_extmenu['links'][] = "<a href=\"subscribers_import.php\">Import</a>";
$_extmenu['links'][] = "<a href=\"subscribers_groups.php\">Groups</a>";

include (bm_baseDir.'/setup/top.php');
/** End templating system **/
?>

<h1>Subscriber Groups</h1>

<img src="<?php echo bm_baseUrl; ?>/img/icons/groups.png" class="articleimg">

<p>
Mailing groups allow you to create subsets of your subscribers. This is useful if you'd like to mail a portion
of your subscribers, and not the entire list. You create groups of subscribers by providing filtering criteria
based from what you've setup in the <a href="setup_demographics.php">Demographics Page</a>. For
instance, if you collect a "city" from subscribers, you can create a group that matches only subscribers from "Milwaukee".
</p>

<h2>Mailing Groups:</h2>

<?php


// Get array of mailing groups. Key is ID, value is name
$groups = dbGetGroups($dbo);

// # of groups == size of $groups array
$numGroups = count($groups);

if (!$numGroups)
	echo 'You have not created any mailing groups. <br><br>';
	
echo '
	<form id="bForm" action="'.$_SERVER['PHP_SELF'].'" method="POST">
	<div class="field">
			<b>NEW &raquo;</b>
			<input type="text" class="text" name="group_name" id="group_name" title="enter group name" value="enter group name" maxlength="60" size="32" />
			<input class="button" id="bForm-submit" name="bForm-submit" type="submit" value="Add" />
	</div>
	</form>';
	

if ($numGroups) { // mailing groups exist, print table of them
?>

	<div width="100%" align="center"><table border="0" cellspacing="4" width="97%">
	<tr align="center"><td width="30">&nbsp;</td><td width="30">Delete</td><td width="30">Edit</td><td align="left"> &nbsp; &nbsp; &nbsp;Group Name</td></tr>
<?php

	$i = 0;
	foreach (array_keys($groups) as $group_id) {
		$group_name = & $groups[$group_id];
		$i ++;
		echo '
							<tr>
								<td align="right"> '.$i.'. </td>
								<td align="center"><a href="groups_delete.php?group_id='.$group_id.'"><img src="'.bm_baseUrl.'/img/icons/delete.png" border="0"></a></td>
								<td align="center"><a href="groups_edit.php?group_id='.$group_id.'"><img src="'.bm_baseUrl.'/img/icons/edit.png" border="0"></a></td>
								<td align="left"> &nbsp; &nbsp; <b>'.$group_name.'</b></td>
							</tr>';
	}
	echo '
					</table></div><br><br>';
}

include (bm_baseDir.'/setup/footer.php');
?>