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

// TODO -> page needs to be re-written. It has only been re-worked to fit new demo/subs system.

define('_IS_VALID', TRUE);

require('../../bootstrap.php');
require_once (bm_baseDir.'/inc/db_subscribers.php');
require_once (bm_baseDir.'/inc/db_demographics.php');
require_once (bm_baseDir.'/inc/lib.txt.php');
$bMail = & fireup("secure");
$dbo = & $bMail->openDB();

$appendUrl = "limit=".$_REQUEST['limit']."&order=".$_REQUEST['order']."&orderType=".$_REQUEST['orderType']."&group_id=".$_REQUEST['group_id']."&table=".$_REQUEST['table'];

if (!empty($_REQUEST['table']) && $_REQUEST['table'] != 'subscribers' && $_REQUEST['table'] != 'pending')
	die('<img src="'.bm_baseUrl.'/img/icons/alert.png" align="middle">subscriber_mod -> Unrecognized or empty table received.');
	$table = $_REQUEST['table'];


function refresh(& $appendUrl) {
	header('Location: '.bm_http.bm_baseUrl.'/admin/subscribers/subscribers_manage.php?'.$appendUrl);
}

// check to see if deleteion confirmation was made...
if (!empty ($_POST['deleteEmails'])) {

	if ($table == 'pending')
		dbPendingDel($dbo, $_POST['deleteEmails']);
	else
		dbSubscriberRemove($dbo, $_POST['deleteEmails']);

	refresh($appendUrl);
	die('<img src="'.bm_baseUrl.'/img/icons/alert.png" align="middle">Subscriber(s) have been deleted. Returning to <a href="subscribers_manage.php?'.$appendUrl.'">Subscriber Page</a>');
}
elseif (!empty ($_POST['addEmails'])) {
	foreach ($_REQUEST['addEmails'] as $email) {
		dbSubscriberAdd($dbo,$email);
	}
	refresh($appendUrl);
	die("Subscriber(s) have been updated. Returning to <a href=\"subscribers_manage.php?".$appendUrl."\">Subscriber Page</a>");
}
// ...or if an edit update was received...
elseif (!empty ($_REQUEST['editId'])) {

	$updates = array();

	// create dbGetSubscriber compatible array
	foreach ($_REQUEST['editId'] as $key) {
		
		// make sure email is valid.. TODO: employ all other validation rules here (as in subscribe process.php)
		if (!isEmail($_REQUEST['email'][$key]))
			$_REQUEST['email'][$key] = $_REQUEST['oldEmail'][$key];
				
		$a = array ('email' => $_REQUEST['email'][$key], 'date' => $_REQUEST['date'][$key], 'data' => array ());
		if ($a['email'] != $_REQUEST['oldEmail'][$key])
			$a['oldEmail'] = $_REQUEST['oldEmail'][$key];
		foreach (array_keys($_REQUEST['d'][$key]) as $demographic_id) {
			$subVal = & $_REQUEST['d'][$key][$demographic_id];
			if (!empty ($subVal))
				$a['data'][$demographic_id] = $subVal;
		}
		$updates[] = $a;		
	}

	foreach ($updates as $subscriber) {
		dbSubscriberUpdate($dbo,$subscriber);
	}

	refresh($appendUrl);
	die("Subscriber(s) have been updated. Returning to <a href=\"subscribers_manage.php?".$appendUrl."\">Subscriber Page</a>");

}

// ... or if this page was called by a sane person.
elseif (empty ($_REQUEST['sid']) || empty ($_REQUEST['action'])) {
	refresh($appendUrl);
	die("Invalid input. Being redirected to <a href=\"subscribers_manage.php?".$appendUrl."\">Subscriber Page</a>");
}

/** bMail templating system **/
// overide 'content' ID to allow for a WIDE page (auto adjusting width...)
$_head = '
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
$_menu[] = "<a href=\"".$bMail->_config['site_url']."\">".$bMail->_config['site_name']."</a>";

// right bar settings -->
$_nomenu = FALSE; // turn off main "admin menu" in right bar
$_nodemo = FALSE; // turn off display of bMail demonstration mode status

include (bm_baseDir.'/setup/top.php');
/** End templating system **/

// BEGIN MAIN PAGE
$demographics = dbGetDemographics($dbo);


echo "<form action=\"".$_SERVER['PHP_SELF']."\" method=\"POST\">\n";

// hidden values to keep appendStr consistent
echo "\t<input type=\"hidden\" name=\"order\" value=\"".$_REQUEST['order']."\">\n";
echo "\t<input type=\"hidden\" name=\"orderType\" value=\"".$_REQUEST['orderType']."\">\n";
echo "\t<input type=\"hidden\" name=\"limit\" value=\"".$_REQUEST['limit']."\">\n";
echo "\t<input type=\"hidden\" name=\"table\" value=\"".$_REQUEST['table']."\">\n";
echo "\t<input type=\"hidden\" name=\"group_id\" value=\"".$_REQUEST['group_id']."\">\n";

echo "<div style=\"width:90%\" align=\"right\">";
echo "<a href=\"subscribers_manage.php?".$appendUrl."\">Back to Subscribers Page</a>";
echo "</div><hr>";

switch ($_REQUEST['action']) {
	case "edit" :
	
			echo "<div align=\"center\">";
			
$subCount = 0;
if (is_array($_REQUEST['sid'])) {
	$subCount = count($_REQUEST['sid']);
	if ($subCount > 15) {
		$_REQUEST['sid'] = array_slice($_REQUEST['sid'], 0, 15);
	echo "<b>Note</b>: You can only edit 15 subscribers at a time.<br>";	
	}
}
$subscribers = dbGetSubscriber($dbo, $_REQUEST['sid'], 'detailed', $table);


		echo "<table cellspacing=\"5\"><tr><td>email</td>\n";
		foreach (array_keys($demographics) as $demographic_id) {
			echo "<td>".$demographics[$demographic_id]['name']."</td>";
		}
		echo "</tr>";

		$count = 0;
		foreach (array_keys($subscribers) as $subscriber_id) {
			$count ++;

			$subscriber = & $subscribers[$subscriber_id];
			echo "<tr>";

			echo '<td>
							<input type="hidden" name="editId[]" value="'.$subscriber_id.'">
							<input type="hidden" name="date['.$subscriber_id.']" value="'.$subscriber['date'].'">
							<input type="hidden" name="oldEmail['.$subscriber_id.']" value="'.$subscriber['email'].'">';
			echo "<input type=\"text\" name=\"email[".$subscriber_id."]\" value=\"".$subscriber['email']."\" maxlength=\"60\"></td>\n";

			foreach (array_keys($demographics) as $demographic_id) {
				$demographic = & $demographics[$demographic_id];

				// set subscribers value for this demographic (if any)
				$subVal = '';
				if (isset ($subscriber['data'][$demographic_id]))
					$subVal = & $subscriber['data'][$demographic_id];

				switch ($demographic['type']) {
					case "checkbox" :
						if ($subVal == 'on')
							echo "<td><input type=\"checkbox\" name=\"d[".$subscriber_id."][".$demographic_id."]\" checked></td>";
						else
							echo "<td><input type=\"checkbox\" name=\"d[".$subscriber_id."][".$demographic_id."]\"></td>";
						break;

					case "multiple" :
						echo "<td><SELECT name=\"d[".$subscriber_id."][".$demographic_id."]\">\n";
						$options = $demographic['options'];

						echo "<option value=\"\"></option>";
						foreach ($options as $option) {
							if ($option == $subVal)
								echo "\t  <option value=\"".db2str($option)."\" selected> ".db2str($option)."\n";
							else
								echo "\t  <option value=\"".db2str($option)."\"> ".db2str($option)."\n";
						}
						echo "</SELECT></td>";

						break;

					case "text" :
						echo "<td><input type=\"text\" name=\"d[".$subscriber_id."][".$demographic_id."]\" maxlength=\"60\" value=\"".db2str($subVal)."\"></td>";
						break;
					case "date" : // select
						echo '<td></td>';
						break;

					case "number" : // select
						echo '<td></td>';
						break;

					default :
						break;
				}

			}
			echo "</tr>\n";

		}
		echo "</table>";

		echo "\n<br><input type=\"submit\" name=\"submit\" value=\"Update\"><br>";

		break;

	case "delete" :
	
	$emails = dbGetSubscriber($dbo, $_REQUEST['sid'], 'email', $table);

		echo "\nClick <input type=\"submit\" name=\"submit\" value=\"ok\"> to delete the following --><br>";
		foreach ($emails as $email) {
			echo "<input type=\"hidden\" name=\"deleteEmails[]\" value=\"".$email."\">\n";
			echo $email."<br>";
		}
		break;

	case "add" :
	
	$emails = dbGetSubscriber($dbo, $_REQUEST['sid'], 'email', $table);

		echo "\nClick <input type=\"submit\" name=\"submit\" value=\"ok\"> to add the following addresses to the subscriber table --><br>";
		foreach ($emails as $email) {
			echo "<input type=\"hidden\" name=\"addEmails[]\" value=\"".$email."\">\n";
			echo $email."<br>";
		}
		break;
	default :
		break;
}

echo "\n</form>";
?>