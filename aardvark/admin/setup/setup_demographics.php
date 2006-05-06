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

/**********************************
	INITIALIZATION METHODS
*********************************/
define('_IS_VALID', TRUE);

require ('../../bootstrap.php');
require_once (bm_baseDir . '/inc/db_demographics.php');

$poMMo = & fireup('secure');
$logger = & $poMMo->logger;
$dbo = & $poMMo->openDB();

/**********************************
	SETUP TEMPLATE, PAGE
 *********************************/
$smarty = & bmSmartyInit();
$smarty->prepareForForm();

$smarty->assign('intro', _T('Demographics are pieces of information you choose to collect from subscribers (other than email address). Any number of demographics can be assigned to the subscription form.  Each demographic is categorized as either <em>TEXT</em>, <em>NUMBER</em>, <em>MULTIPLE CHOICE</em>, <em>CHECK BOX</em>, or <em>DATE</em> depending on kind of information it collects.'));

// add demographic if requested, redirect to its edit page on success
if (!empty ($_POST['demographic_name'])) {
	if (dbDemographicAdd($dbo, str2db($_POST['demographic_name']), str2db($_POST['demographic_type'])))
		bmRedirect('demographics_edit.php?demographic_id=' .
		$dbo->lastId());
	else
		$logger->addMsg('{t}Unable to add demographic?{/t}');
}

// check for a deletion request
if (!empty ($_GET['delete'])) {

	// make sure it is a valid demographic
	if (!dbDemographicCheck($dbo, $_GET['demographic_id'])) {
		$logger->addMsg(_T('Demographic cannot be deleted.'));
	} else {
		// See if this change will affect any subscribers, if so, confirm the change.
		$sql = 'SELECT COUNT(data_id) FROM ' . $dbo->table['subscribers_data'] . ' WHERE demographic_id=\'' . $_GET['demographic_id'] . '\'';
		$affected = $dbo->query($sql, 0);

		if ($affected && empty ($_GET['dVal-force'])) {
			$smarty->assign('confirm', array (
				'title' => _T('Delete Demographic'
			), 'nourl' => $_SERVER['PHP_SELF'] . '?demographic_id=' . $_GET['demographic_id'],
			 'yesurl' => $_SERVER['PHP_SELF'] . '?demographic_id=' . $_GET['demographic_id'] . '&delete=TRUE&dVal-force=TRUE',
			  'msg' => sprintf(_T('Currently, %1$s subscribers have a non empty value for this demographic. All Subscriber data relating to this demographic will be lost. Are you sure you want to remove demographic %2$s?'), '<b>' . $affected . '</b>','<b>' . $_GET['demographic_name'] . '</b>')));
			$smarty->display('admin/confirm.tpl');
			bmKill();
		} else {
			// delete demographic
			if (dbDemographicDelete($dbo, $_REQUEST['demographic_id']))
				bmRedirect($_SERVER['PHP_SELF']);
			$logger->addMsg(_T('Demographic cannot be deleted.'));
		}
	}
}

// Get array of demographics. Key is ID, value is an array of the demo's info
$demographics = dbGetDemographics($dbo);
if (!empty($demographics))
	$smarty->assign('demographics', $demographics);
	
$smarty->display('admin/setup/setup_demographics.tpl');
bmKill();
?>