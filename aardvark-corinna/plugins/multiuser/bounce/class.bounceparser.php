<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * Created: Corinna Thoeni <corinn at gmx dot net> - 21.11.2006
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/

include('Net_POP3-1.3.6/POP3.php');

require_once (bm_baseDir . '/plugins/multiuser/bounce/class.db_bouncehandler.php'); 


class BounceParser {		//implements iDbHandler {

	//private $dbo;
	
	private $bouncedbhandler;
	
	
	// STORE AND GET FROM DB
private $user='corinna-pommo@gmx.net';
private $pass='A6Q00VAAS';
private $host='pop.gmx.net';
private $port="110";
	
	private $pop3;
	
	
	public function __construct($dbo) {
		//$this->dbo = $dbo;
		//$this->safesql =& new SafeSQL_MySQL;
		$this->pop3 =& new Net_POP3();
		$this->bouncedbhandler = new BounceDBHandler($dbo);
	}
	
	
	

	
	
	
	public function parseEmail($input) {
	
		// PARS!
		//TU was mitm input
		echo "<div style=' margin: 20px; background-color: #efefef; border:1px solid red'>PARS!"; 
			print_r($input); 
		echo "</div>";
		return $input;
		
		
		
		
	}
	
	
	
	public function execute() {
		
	
		//$this->pop3->setDebug();
	
		if(PEAR::isError( $ret= $this->pop3->connect($this->host , $this->port ) )){
		    echo "ERROR: " . $ret->getMessage() . "\n";
		    exit();
		}
		if(PEAR::isError( $ret= $this->pop3->login($this->user , $this->pass,'USER' ) )){
		    echo "ERROR: " . $ret->getMessage() . "\n";
		    exit();
		}
		
		
		
		$mailse = $this->pop3->numMsg();
		echo "<br>In se mailbox gibtse " . $mailse . "mailse.<br><br>";
		
		
		
		//$this->
		for ($i=1; $i <= $mailse; $i++) {

			// In BUFFER LADEN!
			// und dann nachher parsen evtl? dann ist connection schneller und herunterladen usw
						
			echo "Parsing Mail nr. " . $i . " <br>";
			$in = NULL;
			$in = $this->pop3->getMsg($i);
			
			$parseout = $this->parseEmail($in);
			$this->bouncedbhandler->dbInsertParsedBounce($parseout['email'], $parseout['header'], 
					$parseout['body'], $parseout['reason'], $parseout['subscribers']);
		
		} 
	
	
		$this->pop3->disconnect();
		
	} //ececute
	
/*
 * $pop3->getRawHeaders(1)
$pop3->getParsedHeaders(1)

$pop3->getBody(1)
$pop3->numMsg()
$pop3->getSize()
$pop3->getMsg($i)
$pop3->getBody(1
$pop3->getListing()
*/
	
} // BounceParser

?>
