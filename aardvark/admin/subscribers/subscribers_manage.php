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
 
 /* TODO --> make cool w/ a) no subscribers,  b) no demographics   ,   c) blank demographics [edit demographic creation to auto fill prompt, etc.] */
 
define('_IS_VALID', TRUE);

require('../../bootstrap.php');
require_once (bm_baseDir.'/inc/db_subscribers.php');
require_once (bm_baseDir.'/inc/db_groups.php');
require_once (bm_baseDir.'/inc/db_sqlgen.php');
require_once (bm_baseDir.'/inc/db_demographics.php');
require_once (bm_baseDir.'/inc/class.pager.php');
$poMMo = & fireup("secure", "dataSave");
$dbo = & $poMMo->openDB();

if (bm_debug == 'on' && isset($dbo) && is_object($dbo))
	$dbo->debug(TRUE);


/** Setup Variables
 * 
 * demographics = array of all demographics (key is demographic_id)
 * groups = array of all groups (key is group_id)
 * table = table to perform lookup on. Either 'subscribers' or 'pending''
 * group_id = The ID of the group being viewed. If none set to "all" for all subscribers
 * limit = The Maximum # of subscribers to show per page
 * order = The demographic (demographic_id) to order subscribers by
 * orderType = type of ordering (ascending - ASC /descending - DESC)
 * appendUrl = all the values strung together in HTTP_GET form
 */
$demographics = dbGetDemographics($dbo);
$groups = dbGetGroups($dbo);
$table = (empty ($_REQUEST['table'])) ? 'subscribers' : str2db($_REQUEST['table']);
$group_id = (empty ($_REQUEST['group_id'])) ? 'all' : str2db($_REQUEST['group_id']);
$limit = (empty ($_REQUEST['limit'])) ? '50' : str2db($_REQUEST['limit']);
$order = (empty ($_REQUEST['order'])) ? 'email' : str2db($_REQUEST['order']);
$orderType = (empty ($_REQUEST['orderType'])) ? 'ASC' : str2db($_REQUEST['orderType']);
$appendUrl = '&table='.$table.'&limit='.$limit."&order=".$order."&orderType=".$orderType."&group_id=".$group_id;

// Get a count -- TODO implement group object so this could be made into a 'list',
//   and then a partial list of subscribers_ids fed to the 'detailed' query based on start/limit
//    TODO -> cache this count somehow (group object...)
$groupCount =  dbGetGroupSubscribers($dbo, $table, $group_id, 'count');

// Instantiate Pager class (Using modified template from author)
$p = new Pager($appendUrl);
$start = $p->findStart($limit);
$pages = $p->findPages($groupCount, $limit);
// pagelist : echo to print page navigation.
$pagelist = $p->pageList($_GET['page'], $pages);

// get the subscribers array
if ($groupCount)
	$subscribers = & dbGetSubscriber($dbo, dbGetGroupSubscribers($dbo, $table, $group_id,'list', $order, $orderType, $limit, $start),'detailed', $table);

// isSelected
function isSelected($x, $y) {
	if ($x == $y)
		return 'SELECTED';
	return '';
}

/** poMMo templating system **/
$_head = '<script src="'.bm_baseUrl.'/inc/js/bform.js" type="text/javascript"></script>';
// overide 'content' ID to allow for a WIDE page (auto adjusting width...)
$_head .= '
	<style type="text/css" media="all">
		#content {
			width: auto;
			margin: 10 10 10 10;
			text-align:left;
		}</style>';
$_nosidebar = TRUE;

//$_header = "Send a Mailing";

$_nologo = FALSE;
$_menu = array ();
$_menu[] = "<a href=\"".bm_baseUrl."/index.php?logout=TRUE\">Logout</a>";
$_menu[] = "<a href=\"admin_subscribers.php\">Subscribers Page</a>";
$_menu[] = "<a href=\"".$poMMo->_config['site_url']."\">".$poMMo->_config['site_name']."</a>";

// right bar settings -->
$_nomenu = FALSE; // turn off main "admin menu" in right bar
$_nodemo = FALSE; // turn off display of poMMo demonstration mode status

include (bm_baseDir.'/setup/top.php');

/** End templating system **/

echo "<div style=\"width:90%\" align=\"right\">";
if ($table == "subscribers") {
	echo "<a href=\"subscribers_manage.php?table=pending\">View Pending</a> &nbsp;&nbsp;&nbsp; &nbsp;";
	echo "<a href=\"subscribers_export.php?table=".$table."&group_id=".$group_id."\">Export to CSV</a> &nbsp;&nbsp;&nbsp; &nbsp;";
	echo "<a href=\"admin_subscribers.php\">Back to Subscribers Page</a>";
} else
	echo "<a href=\"subscribers_manage.php\">View Subscribed Subscribers</a>&nbsp;&nbsp;";

echo "</div>\n<hr>";

echo "<div align=\"center\">";

echo "<form name=\"bForm\" id=\"bForm\" method=\"POST\" action=\"".$_SERVER['PHP_SELF']."\">\n";

echo "Subscribers per Page: ";
echo "<SELECT name=\"limit\" onChange=\"document.bForm.submit()\">";
echo "<option value=10 ".isSelected($limit, 10).">10</option>";
echo "<option value=50 ".isSelected($limit, 50).">50</option>";
echo "<option value=150 ".isSelected($limit, 150).">150</option>";
echo "<option value=300 ".isSelected($limit, 300).">300</option>";
echo "<option value=500 ".isSelected($limit, 500).">500</option>";
echo "</SELECT>\n&nbsp;&nbsp;&nbsp;";

echo "Belonging to: ";
echo "<SELECT name=\"group_id\" onChange=\"document.bForm.submit()\">";
echo "<option value=all ".isSelected($group_id, 'all').">All Subscribers</option>";
echo "<option value=all>-- (Mailing Groups) --</option>";
foreach (array_keys($groups) as $gid) {
	echo "<option value=\"".$gid."\" ".isSelected($group_id, $gid).">".db2str($groups[$gid]);
}
echo "</SELECT>\n&nbsp;&nbsp;&nbsp;";

echo "Order by: ";
echo "<SELECT name=\"order\" onChange=\"document.bForm.submit()\">";
echo '<option value="email">email</option>';
foreach (array_keys($demographics) as $demographic_id) {
	$demographic = & $demographics[$demographic_id];
	echo "<option value=\"".$demographic_id."\" ".isSelected($order, $demographic_id).">".$demographic['name']."</option>";
}
echo "</SELECT>\n&nbsp;&nbsp;&nbsp;";

echo "<SELECT name=\"orderType\" onChange=\"document.bForm.submit()\">";
echo "<option value=\"ASC\" ".isSelected($orderType, 'ASC').">ascending</option>";
echo "<option value=\"DESC\" ".isSelected($orderType, 'DESC').">descending</option>";
echo "</SELECT><br><br>\n\n";

echo "</FORM>";

echo "(<em>".$groupCount." subscribers</em>)";

if ($groupCount > 0) {
	echo "<form name=\"oForm\" id=\"oForm\" method=\"POST\" action=\"subscribers_mod.php\">\n";

	// hidden values to keep appendStr consistent
	echo "\t<input type=\"hidden\" name=\"order\" value=\"".$order."\">\n";
	echo "\t<input type=\"hidden\" name=\"orderType\" value=\"".$orderType."\">\n";
	echo "\t<input type=\"hidden\" name=\"limit\" value=\"".$limit."\">\n";
	echo "\t<input type=\"hidden\" name=\"table\" value=\"".$table."\">\n";
	echo "\t<input type=\"hidden\" name=\"group_id\" value=\"".$group_id."\">\n";

	// if the active table is pending, allow option for admin to "add" the subscriber to the regular subscribers table
	$addStr = "";
	if ($table == "pending")
		$addStr = "<td>add&nbsp;&nbsp;</td>";

	// ** PRINT SUBSCRIBER VIEW HEADER **
	echo "<table cellspacing=\"5\">";

	if ($table == 'pending')
		echo "<tr><td nowrap>select</td>".$addStr."<td nowrap>delete</td><td nowrap>email</td>\n";
	else
		echo "<tr><td nowrap>select</td>".$addStr."<td nowrap>edit</td><td>delete</td><td nowrap>email</td>\n";

	foreach (array_keys($demographics) as $demographic_id) {
		echo "<td nowrap>".$demographics[$demographic_id]['name']."</td>";
	}
	echo "<td nowrap>Subscribed</td></tr>\n";

	foreach (array_keys($subscribers) as $subscriber_id) {
		$subscriber = & $subscribers[$subscriber_id];
		echo "<tr>\n";
		echo "\t<td nowrap><input type=\"checkbox\" name=\"sid[]\" value=".$subscriber['email']."></td>\n";

		if ($table == "pending") {
			echo "\t<td nowrap><a href=\"subscribers_mod.php?sid=".$subscriber['email']."&action=add".$appendUrl."\">add</a></td>\n";
			echo "\t<td nowrap><a href=\"subscribers_mod.php?sid=".$subscriber['email']."&action=delete".$appendUrl."\">delete</a></td>\n";
		} else {
			echo "\t<td nowrap><a href=\"subscribers_mod.php?sid=".$subscriber['email']."&action=edit".$appendUrl."\">edit</a></td>\n";
			echo "\t<td nowrap><a href=\"subscribers_mod.php?sid=".$subscriber['email']."&action=delete".$appendUrl."\">delete</a></td>\n";
		}

		echo "\t<td nowrap><strong>".$subscriber['email']."</strong></td>";

		foreach (array_keys($demographics) as $demographic_id) {
			if (isset ($subscriber['data'][$demographic_id]))
				echo "\t<td nowrap>".$subscriber['data'][$demographic_id]."</td>\n";
			else
				echo "\t<td></td>\n";
		}
		echo "\t<td nowrap>".$subscriber['date']."</td>\n";
		echo "</tr>\n";
	}

	echo '<tr><td colspan="4"><b><a href="javascript:SetChecked(1,\'sid[]\')">Check All</a> &nbsp;&nbsp; || &nbsp;&nbsp; <a href="javascript:SetChecked(0,\'sid[]\')">Clear All</a></b></td></tr>';
	echo "</table>";

	echo "<SELECT name=\"action\"><option value=\"\" SELECTED>Ignore checked subscribers</option>\n";
	if ($table == "pending")
		echo "<option value=\"add\">Add checked subscribers</option>\n";
	echo "<option value=\"delete\">Delete checked subscribers</option>\n";
	echo "<option value=\"edit\">Edit checked subscribers</option>\n\n";
	echo "</SELECT>&nbsp;&nbsp;&nbsp; \n<input type=\"submit\" name=\"send\" value=\"go\">\n";

	echo "\n\n<br><br>\n".$pagelist."\n\n</div>";

	echo "</form>\n";
}

include (bm_baseDir.'/setup/footer.php');
?>