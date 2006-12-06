<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * Created: Corinna Thoeni <corinn at gmx dot net> - 13.11.2006
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/

require_once (bm_baseDir . '/plugins/multiuser/bounce/class.db_bouncehandler.php');

require_once (bm_baseDir . '/plugins/multiuser/bounce/class.bounceparser.php');

//require_once (bm_baseDir . '/inc/class.pager.php');


/*
 *  http://en.wikipedia.org/wiki/Bounce_message
 * http://tools.ietf.org/html/rfc3463
 */



class BouncePlugin {
	

	// UNIQUE Name of the Plugin i decided to do this so some can select his plugins configuration
	// from the database through this name.
	private $pluginname = "bouncemanager";	
	
	private $dbo;
	private $logger;
	private $poMMo;
	
	private $bouncedbhandler;
	
	private $bounceparser;

	
	

	public function __construct($poMMo) {
		$this->dbo = $poMMo->_dbo;
		$this->logger = $poMMo->_logger;
		$this->poMMo = $poMMo;
		
		$this->bouncedbhandler = new BounceDBHandler($this->dbo);
		
	}
	public function __destruct() {
	}

	public function isActive() {
		// Parameter 'PLUGINNAME' is the uniquename of the plugin
		//return $this->userdbhandler->dbPluginIsActive($this->pluginname);
	}
	
	public function getPermission($user) {
		//TODO select the permissions from DB 
		return TRUE;
	}
	
	
	public function execute($data) {

		$smarty = & bmSmartyInit();
		
		if ($data['action'] == "store") {
			// Speichern in DB ausführen
			// PARSE FUNKTION
			// evtl ein thread????? der beim einloggen im hintergrund parst?
			//Parser Objekt machen!!!!
			
			
			$this->bounceparser = new BounceParser($this->dbo);
			$this->bounceparser->execute();
			
			// VIS BAR!!! -> fortschrittsbalken.
			
		} elseif ($data['action'] == "visualize") {
			
			
			$bounces = $this->bouncedbhandler->dbGetBounceMatrix();
			$smarty->assign('bounces', $bounces);		
			$smarty->assign("showbounces", TRUE);
	
			//$smarty->assign('data', $bounces[0]);	//array(1,2,3,4,5,6,7,8,9)
			//$smarty->assign('tr',array('bgcolor="#eeeeee"','bgcolor="#dddddd"'));


		} elseif ($data['action'] == "delete") {
			
			$this->bouncedbhandler->dbDeleteBounce($data['delid']);
		}
		
		
		
		/*
		 * //Settings
		$bounce_protocol = 'POP';	//pop
		$bounce_mailbox_host = 'pop.gmx.net';
		$bounce_mailbox_user = 'corinna-pommo@gmx.net';
		$bounce_mailbox_password = 'A6Q00VAAS';
		$bounce_mailbox_port = "110/pop3/notls";*/
		
		
		
		//$smarty->assign('user' , $user);
		
		
		$smarty->assign($_POST);

		$smarty->display('plugins/multiuser/bounce/bounce_main.tpl');
		bmKill();
	}
	
	
	
	
	
}



?>
