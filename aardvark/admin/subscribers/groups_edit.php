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
require_once (bm_baseDir.'/inc/db_demographics.php');
require_once (bm_baseDir.'/inc/lib.txt.php');
$poMMo = & fireup('secure');
$dbo = & $poMMo->openDB();

// delete criteria if requested
if (!empty($_GET['delete']))  
	dbGroupFilterDel($dbo, str2db($_GET['criteria_id']));

// setup $group_id. Make OK for DB...
$group_id = '';
$group_id = str2db($_REQUEST['group_id']);

// change group name  if requested
if (!empty ($_POST['group_name']))
	dbGroupUpdateName($dbo, $group_id, str2db($_POST['group_name']));
	
// get group
$groups = & dbGetGroups($dbo, $group_id);
$group_name = db2str($groups[$group_id]);


// get array of demographics
$demoArray = & dbGetDemographics($dbo);

$errorStr = '';
// check if a filter is requested to be add
if (isset ($_POST['demographic_id'])) {
	// validate that the user selected combination is legal. 

	if ($_POST['demographic_id'] != '')
		$demographic = & $demoArray[$_POST['demographic_id']];

	switch ($_POST['logic']) {
		case '' :
			$errorStr = 'A filter must be supplied';
			break;
		case 'is_in' :
		case 'not_in' :
			// Not legal if demographic_id is set. 
			if ($_POST['demographic_id'] != '')
				$errorStr = 'Leave criteria empty if filter is include another mail group';
			break;
		case 'is_equal' :
		case 'not_equal' :
			// Not legal if  demographic_id is empty, or type is check.
			if ($_POST['demographic_id'] == '')
				$errorStr = 'Select a criteria if filter is to enforce value';
			elseif ($demographic['type'] == 'checkbox') $errorStr = 'Checkbox criteria cannot be used when enforcing value(s).';
			break;
		case 'is_more' :
		case 'is_less' :
			// Not legal if  demographic_id is empty, or type is check or select.
			if ($_POST['demographic_id'] == '')
				$errorStr = 'Select a criteria if filter is to enforce value';
			elseif (($demographic['type'] == 'checkbox') || ($demographic['type'] == 'multiple')) $errorStr = 'Only text, date, or year criteria can be compared.';
			break;
		case 'is_true' :
		case 'not_true' :
			// Not legal if demographic_id is empty, type must be a checkbox
			if ($_POST['demographic_id'] == '')
				$errorStr = 'Select a criteria if filter is to examine selected status';
			elseif ($demographic['type'] != 'checkbox') 
				$errorStr = 'Only checkbox criteria status can be determined.';
			break;
		default :
			$errorStr = 'Unknown filter received.';
			break;
	}

	// no errors were found in users selection, add to DB with a bunk value
	if (empty ($errorStr)) {
		if (dbGroupFilterAdd($dbo, $group_id, str2db($_POST['demographic_id']), str2db($_POST['logic']), 0))
			header('Location: '.bm_http.bm_baseUrl.'/admin/subscribers/groups_filter.php?group_id='.$group_id.'&criteria_id='.$dbo->lastId());
		else
			$errorStr = 'Could not add filter. Perhaps it\'s a duplicate or negates an existing filter?';
	}
	elseif (!empty ($demographic)) $errorStr .= '<br>&nbsp;&nbsp; '.$demographic['name'].' type is: '.$demographic['type'];

}

/** poMMo templating system **/
// header settings -->
$_head = '<script src="inc/js/bform.js" type="text/javascript"></script>';

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

// validate group_id
if (empty ($group_id) || !is_numeric($group_id) || !dbGroupCheck($dbo, $group_id)) {
	// bad groupId / nonexistant group
	echo '
									<img src="'.bm_baseUrl.'/img/icons/error.png" align="middle">
										<b>ERROR</b>: Invalid Group.';
	include (bm_baseDir.'/setup/footer.php');
	die();
}
?>

<h1>Edit Mailing Group</h1>

<img src="<?php echo bm_baseUrl; ?>/img/icons/groups.png" class="articleimg">

<p>
Groups are made of filtering criteria. Available criteria is based on
<a href="setup_demographics.php">demographics</a> you collect . You can match subscribers 
against any demographic's value(s). For instance, if you collect 'age', you can create a  group to match 
subscribers over 21. To do this, create a filter that matches 'age' to the value of '21', and another 
that matches 'age' to greater than '21'.
</p>	  
<?php 

echo '<a href="subscribers_groups.php"><img src="'.bm_baseUrl.'/img/icons/back.png" align="middle" class="navimage" border=\'0\'>Return to groups page</a>';

echo '<h2>'.$group_name.' &raquo;</h2><br>';

echo '
	<form id="gName" action="'.$_SERVER['PHP_SELF'].'" method="POST">
	<input type="hidden" name="group_id" value="'.$group_id.'">
	<div class="field">
			<input type="text" class="text" name="group_name" id="group_name" title="enter group name" value="'.$group_name.'" maxlength="60" size="32" />
			<input class="button" id="gName-submit" name="gName-submit" type="submit" value="Change Name" />
	</div>
	</form>';

// fetch this group filters to an array. array_key == filter/criteria_id
$filters = dbGetGroupFilter($dbo, $group_id);
$filterCount = count($filters);

if ($filterCount < 1)
	echo '<br><b>No filtering criteria has been supplied.</b>';
else {
?>
	<div width="100%" align="center"><table border="0" cellspacing="4" width="97%">
	<tr align="center"><td width="30">&nbsp;</td><td width="30">Delete</td><td width="30">Edit</td><td align="left"> &nbsp; &nbsp; &nbsp;Match Subscribers Who:</td><td width="30" nowrap></td></tr>
<?php 
	$i = 0;
	foreach (array_keys($filters) as $criteria_id) {
		$row = & $filters[$criteria_id];
		$i ++;

		$andStr = '';
		if ($i < $filterCount)
			$andStr = 'AND';

		// make an array holding the demographic info (ie. 'name','prompt','active','required',etc)
		if (is_numeric($row['demographic_id']) && ($row['demographic_id'] >= 0)) // make sure demo_id is not 0 [ 0 is set for is_in or not_in logic]...
			$demographic = & $demoArray[$row['demographic_id']];

		$matchStr = '';
		switch ($row['logic']) {
			case "is_in" :
				$matchStr = 'Belong to mailing group <b>'.dbGroupName($dbo, $row['value']).'</b>';
				break;
			case "not_in" :
				$matchStr = 'Do not belong to mailing group <b>'.dbGroupName($dbo, $row['value']).'</b>';
				break;
			case "is_equal" :
				// determine criteria matches a single or multiple values
				$values = quotesplit($row['value']);
				if (count($values) > 1)
					$matchStr =  'Have <em>'.$demographic['name'].'</em> set to one of these <a href="groups_filter.php?group_id='.$group_id.'&criteria_id='.$criteria_id.'">values</a>';
				else
					$matchStr =  'Have <em>'.$demographic['name'].'</em> set to <b>'.$values[0].'</b>';
				break;
			case "not_equal" :
				// determine criteria matches a single or multiple values
				$values = quotesplit($row['value']);
				if (count($values) > 1)
					$matchStr =  'Did not set <em>'.$demographic['name'].'</em> to one of these <a href="groups_filter.php?group_id='.$group_id.'&criteria_id='.$criteria_id.'">values</a>';
				else
					$matchStr =  'Did not set <em>'.$demographic['name'].'</em> to <b>'.$values[0].'</b>';
				break;
			case "is_more" :
				$matchStr =  'Have <em>'.$demographic['name'].'</em> greater than <b>'.$row['value'].'</b>';
				break;
			case "is_less" :
				$matchStr = 'Have <em>'.$demographic['name'].'</em> less than <b>'.$row['value'].'</b>';
				break;
			case "is_true" :
				$matchStr =  'Checked <em>'.$demographic['name'].'</em>';
				break;
			case "not_true" :
				$matchStr =  'Did not check <em>'.$demographic['name'].'</em>';
				break;

		}

		echo '
										<tr>
											<td align="right"> '.$i.'. </td>
											<td align="center"><a href="'.$_SERVER['PHP_SELF'].'?group_id='.$group_id.'&criteria_id='.$criteria_id.'&delete=TRUE" onclick="javascript:return confirm(\'Are you sure you want to delete filter criteria #'.$i.'?\')"><img src="'.bm_baseUrl.'/img/icons/delete.png" border="0"></a></td>
											<td align="center"><a href="groups_filter.php?group_id='.$group_id.'&criteria_id='.$criteria_id.'"><img src="'.bm_baseUrl.'/img/icons/edit.png" border="0"></a></td>
											<td align="left"> &nbsp; &nbsp; <b>'.$matchStr.'</b></td>
											<td align="center">'.$andStr.'</td>
										</tr>
										';
	}
	echo '
					<tr><td colspan="2"></td><td colspan="3">Filters match <b>'.dbGroupTally($dbo,$group_id).'</b> total subscribers <a href="'.bm_baseUrl.'/subscribers_manage.php?group_id='.$group_id.'">(view)</a></td>			
				</table></div><br><br>';
}

$demoStr = '<option value="">Choose Criteria</option>';
foreach (array_keys($demoArray) as $key) {
	$selected = '';
	$demoId = & $key;
	if (!empty ($_POST['demographic_id']) && ($demoId == $_POST['demographic_id']))
		$selected = 'SELECTED';
	$demographic = & $demoArray[$key];
		$demoStr .= '<option value="'.$demoId.'" '.$selected.' />'.$demographic['name'].'</option>';
}

$logicStr = '<option value="">Choose Filter</option>';
$logicStr .= '<option value="is_equal">Has value (=)</option>';
$logicStr .= '<option value="not_equal">Not value (!=)</option>';
$logicStr .= '<option value="is_more">Is more than (>)</option>';
$logicStr .= '<option value="is_less">Is less than (<)</option>';
$logicStr .= '<option value="is_true">Is checked</option>';
$logicStr .= '<option value="not_true">Is not checked</option>';
$logicStr .= '<option value="is_in">Also In mail group</option>';
$logicStr .= '<option value="not_in">Not in mail group</option>';

if (!empty ($errorStr))
	$errorStr = '<font color="red"><b>ERROR:</b> '.$errorStr.'</font>';

echo '
	<form id="gCriteria" action="'.$_SERVER['PHP_SELF'].'" method="POST">
	<input type="hidden" name="group_id" value="'.$group_id.'">
	<fieldset>
		<legend>Add filtering criteria</legend>
	<br>'.$errorStr.'
	<div class="field"> &nbsp;
			<select name="demographic_id">'.$demoStr.'</select>
			<select name="logic">'.$logicStr.'</select>
			&raquo;
			<input class="button" id="gCriteria-submit" name="gCriteria-submit" type="submit" value="Select Value(s)" />
	</div>
	<br>
	</fieldset>
	</form>';
	
include (bm_baseDir.'/setup/footer.php');
?>