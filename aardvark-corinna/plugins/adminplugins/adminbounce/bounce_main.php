<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * Created: Corinna Thoeni <corinn at gmx dot net> - 18.10.2006
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/

define('_IS_VALID', TRUE);

require ('../../../bootstrap.php');
require_once (bm_baseDir . '/plugins/adminplugins/adminbounce/class.bounceplugin.php');

$poMMo = & fireup("secure");	//$logger	= & $poMMo->_logger; //$dbo	= & $poMMo->_dbo;

$data = NULL;

//GETPOST data


$bounceplugin = new BouncePlugin($poMMo);
$bounceplugin->execute($data);


?>
