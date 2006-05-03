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
require_once (bm_baseDir.'/inc/db_demographics.php');
require_once (bm_baseDir.'/inc/lib.txt.php');
$bMail = & fireup('secure');
$dbo = & $bMail->openDB();

// setup $criteria_id. Make OK for DB...
$criteria_id = '';
$criteria_id = str2db($_REQUEST['criteria_id']);

// setup $criteria_id. Make OK for DB...
$group_id = '';
$group_id = str2db($_REQUEST['group_id']);

// update criteria if requested, save
$updated = FALSE;
if (!empty ($_POST['value']) && is_array($_POST['value'])) {
	$updated = bm_baseUrl.'/img/icons/nok.png';
	if (dbGroupFilterUpdate($dbo, $criteria_id, str2db(array2csv($_POST['value']))))
		$updated = bm_baseUrl.'/img/icons/ok.png';
}

/** bMail templating system **/
// header settings -->
$_head = '<script src="inc/js/bform.js" type="text/javascript"></script>';

$_nologo = FALSE;
$_menu = array ();
$_menu[] = '<a href="'.bm_baseUrl.'/index.php?logout=TRUE">Logout</a>';
$_menu[] = '<a href="subscribers_groups.php">Groups Page</a>';
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

// get criteria
$filters = dbGetGroupFilter($dbo, $group_id, $criteria_id);
$criteria = & $filters[$criteria_id];

// validate criteria_id
if (empty ($criteria)) {
	// bad / non existant criteria_id
	echo '
												<img src="'.bm_baseUrl.'/img/icons/error.png" align="middle">
													<b>ERROR</b>: Invalid Criteria ID.';
	include (bm_baseDir.'/setup/footer.php');
	die();
}

// get array of demographics
$demoArray = & dbGetDemographics($dbo);
?>

<h1>Edit Filtering Criteria</h1>

<p><a href="groups_edit.php?group_id=<?php echo $group_id; ?>">
	<img src="<?php echo bm_baseUrl; ?>/img/icons/undo.png" class="navimage" align="middle">
	Return</a> to Group Edit.
</p><br>

<h2>Match Subscribers Where;</h2>	  
<?php

// load information about the involved demographic into the $demographic[] array
$demographic_id = & $criteria['demographic_id'];
$demographic = & $demoArray[$demographic_id];

$fontStr = '<font size="+1" color="red">';
$matchStr = '';
switch ($criteria['logic']) {
	case "is_in" :
		$matchStr = 'they also belong to group';
		$demographic['type'] = 'group';
		break;
	case "not_in" :
		$matchStr = 'they do not belong to group';
		$demographic['type'] = 'group';

		break;
	case "is_equal" :
		$matchStr = $fontStr.$demographic['name'].'</font> is';
		break;
	case "not_equal" :
		$matchStr = $fontStr.$demographic['name'].'</font> is something other than';
		break;
	case "is_more" :
		$matchStr = $fontStr.$demographic['name'].'</font> is greater than';
		break;
	case "is_less" :
		$matchStr = $fontStr.$demographic['name'].'</font> is less than';
		break;
	case "is_true" :
		$matchStr = $fontStr.$demographic['name'].'</font> is checked';
		break;
	case "not_true" :
		$matchStr = $fontStr.$demographic['name'].'</font> is not checked';
		break;
}

if ($updated)
	$updated = '
		<script type="text/javascript"><!--
		// fade an image out after 2 seconds 
		window.onload = setTimeout("opacity(\'okImage\', 100, 0, 500)",2000);
		--></script>
	
		<script type="text/javascript" src="'.bm_baseUrl.'/inc/js/fades.js"></script>
	
		&nbsp; &nbsp; &nbsp; <img src="'.$updated.'" width="32" height="32" align="top" id="okImage">';

switch ($demographic['type']) {
	case 'group' :
		echo '<h3>'.$matchStr.' -> '.dbGroupName($dbo, $criteria['value']).'</h3>';

		// fetch groups, create options for SELECT
		$groups = dbGetGroups($dbo);
		$groupStr = '';
		foreach (array_keys($groups) as $gid) {
			$gname = & $groups[$gid];
			// don't include current group in the select'
			if (!($gid == $group_id)) {
				if ($gid == $criteria['value'])
					$groupStr .= '<option SELECTED value="'.$gid.'">'.$gname.'</option>';
				else
					$groupStr .= '<option value="'.$gid.'">'.$gname.'</option>';
			}
		}
		echo '
							<form id="gCriteria" action="'.$_SERVER['PHP_SELF'].'" method="POST">
							<input type="hidden" name="group_id" value="'.$group_id.'">
							<input type="hidden" name="criteria_id" value="'.$criteria_id.'">
							<div class="field">
									<select name="value[]" id="value[]">'.$groupStr.'</select>
									&raquo;
									<input class="button" id="gCriteria-submit" name="gCriteria-submit" type="submit" value="Set as new Value" />
							'.$updated.'
							</div>
							</form>';
		break;
	case 'checkbox' :
		echo '<h3>'.$matchStr.'</h3>';
		echo '(no possible values)';
		break;
	case 'text' :
	case 'date' :
	case 'numberr' :
		echo '<h3>'.$matchStr.' -> '.$criteria['value'].'</h3>';
		echo '
							<form id="gCriteria" action="'.$_SERVER['PHP_SELF'].'" method="POST">
							<input type="hidden" name="group_id" value="'.$group_id.'">
							<input type="hidden" name="criteria_id" value="'.$criteria_id.'">
							<div class="field"> 
									<input type="text" class="text" name="value[]" id="value[]" title="enter value" value="'.$criteria['value'].'" maxlength="60" size="32" />
									&raquo;
									<input class="button" id="gCriteria-submit" name="gCriteria-submit" type="submit" value="Set as new Value" />
							'.$updated.'
							</div>
							</form>';
		break;
	case 'multiple' :

		// load selected values into an array
		$values = quotesplit($criteria['value']);

		// load list of values to choose from
		$list = $demographic['options'];

		// create option string (for select), selecting values in the $values array
		$optStr = '';
		foreach (array_keys($list) as $key) {
			$option = & $list[$key];
			// if this option is one of the user selected values, select it
			if (in_array($option, $values))
				$optStr .= '<option SELECTED>'.$option.'</option>';
			else
				$optStr .= '<option>'.$option.'</option>';
		}
		$sizeOfSelect = 7;
		if (count($list) >= 30)
			$sizeOfSelect = 25;
		elseif (count($list) >= 15) $sizeOfSelect = 12;

		// create selectedValStr
		$selectedValStr = '';
		foreach (array_keys($values) as $key) {
			$option = & $values[$key];
			$selectedValStr .= '<option>'.$option.'</option>';
		}

		echo '<h3>'.$matchStr.' -> <select name="selectedVals">'.$selectedValStr.'</select></h3>';

		echo '
							<form id="gCriteria" action="'.$_SERVER['PHP_SELF'].'" method="POST">
							<input type="hidden" name="group_id" value="'.$group_id.'">
							<input type="hidden" name="criteria_id" value="'.$criteria_id.'">
							<table cellpadding="0" border="0" cellspacing="5"><tr>
								<td valign="top">Select Value(s)<br><br><b>Note:</b> You can select multiple values by clicking using the <em>ctrl</em> and <em>shift</em> keys.</td>
								<td><select name="value[]" id="value[]" size="'.$sizeOfSelect.'" MULTIPLE>'.$optStr.'</select></td>
								<td valign="top"><input class="button" id="gCriteria-submit" name="gCriteria-submit" type="submit" value="Set as new Value(s)" />
									'.$updated.'</td></tr></table>
							</form>';
		break;
	default :
		echo '(unknown demographic type)';
		break;
}

include (bm_baseDir.'/setup/footer.php');
?>