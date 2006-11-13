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


class MultiUser {
	
	public function __construct() {
		
	}
	
	public function display() {
		
		echo "<h3>MULTIUSER ok</h3>";
		$smarty = & bmSmartyInit();
		
		
		
		
		$smarty->display('plugins/multiuser/multiuser.tpl');
		bmKill();
		
	}
	
	
}

?>
