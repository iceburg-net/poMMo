<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/licenses/gpl.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. Notify the above author of modifications to contents within.
 * 
 *  WHY? Because this is a community project -- purposely released under the GPL.
 *    We'd love to have the possiblity to include your derivative works! 
 *    We'd love to coordinate around your development efforts!
 *    We'd love to assist you with your changes!
 *    DON'T BE A STRANGER!
 * 
 ** [END HEADER]**/

/**********************************
	INITIALIZATION METHODS
*********************************/
require ('../../../bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/groups.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/fields.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/filters.php');

$pommo->init(array('noDebug' => TRUE));
$dbo = & $pommo->_dbo;

switch($_POST['logic']) {
	case 'is_in':
	case 'not_in':
		PommoFilter::addGroupFilter($_POST['group'], $_POST['match'], $_POST['logic']);
		break;
	case 'true':
	case 'false':
		PommoFilter::addBoolFilter($_POST['group'], $_POST['match'], $_POST['logic']);
		break;
	case 'is':
	case 'not':
	case 'less':
	case 'greater':
		// unserialize the values -- they are given as 'v=123&v=abdv=defsd'
		$values = preg_split('/&?v=/',substr($_POST['value'],2));
		
		// urldecode string, remove if empty
		foreach($values as $key => $val) {
			if (!empty($val))
				$values[$key] = urldecode($val);
			else
				unset($values[$key]);
		}
		$values = array_unique($values);
		
		PommoFilter::addFieldFilter($_POST['group'], $_POST['match'], $_POST['logic'], $values);
		break;
}
?>