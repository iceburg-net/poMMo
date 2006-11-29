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
	
	private $poMMo;
	private $dbo;
	private $logger;
	
	
	public function __construct($poMMo) {
		$this->poMMo = $poMMo;
		$this->dbo = $poMMo->_dbo;
		$this->logger = $poMMo->logger;
	}
	
	
	public function display() {
		
		//echo "<h3>MULTIUSER ok</h3>";
		
		$smarty = & bmSmartyInit();
		//Perm
		
		$loggeduser = & $this->poMMo->_loggeduser;
			$user = $loggeduser['user'];
			$perm = $loggeduser['perm'];
		/*echo "<h1 style='color:red;'>"; print_r($user); echo "<br>"; print_r($perm);
		echo "</h1>";*/
		
		if (stristr($perm, 'compose')) {
			$smarty->assign("compose", TRUE);
			$smarty->assign("options", TRUE);
		}
		if (stristr($perm, 'send')) {
			$smarty->assign("send", TRUE);
			$smarty->assign("options", TRUE);
		}
		if (stristr($perm, 'history')) {
			$smarty->assign("history", TRUE);
			$smarty->assign("options", TRUE);
		}
		
		if (stristr($perm, 'maillists')) {
			$smarty->assign("maillists", TRUE);
			$smarty->assign("admin", TRUE);
		}
		if (stristr($perm, 'bounce')) {
			$smarty->assign("bounce", TRUE);
			$smarty->assign("admin", TRUE);
		}
		if (stristr($perm, 'useradmin')) {
			$smarty->assign("useradmin", TRUE);
			$smarty->assign("admin", TRUE);
		}
		if (stristr($perm, 'subscribers')) {
			$smarty->assign("subscribers", TRUE);
			$smarty->assign("admin", TRUE);
		}
		if (stristr($perm, 'groups')) {
			$smarty->assign("groups", TRUE);
			$smarty->assign("admin", TRUE);
		}


		if (stristr($perm, 'blah')) {
			$smarty->assign("blah", TRUE);
		}	
		
		
		$smarty->assign("user", $user);
		$smarty->assign("perm", $perm);
		
		$smarty->display('plugins/multiuser/multiuser.tpl');
		bmKill();
		
	}
	
	
}

?>
