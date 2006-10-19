<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * Created: Corinna Thoeni <corinn at gmx dot net> - 31.08.2006
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/
 
/**
 * PLUGIN /MODULE Interface PROTOTYPE
 * The hooks and connections to the modules are this functions
 */
 
 
 //REDO
interface iPlugin {
   
	/**
	 * PHP Standard "magic" methods
	 */ 
/*	public function __construct();			//overload Constructor
	public function __destruct();			//overload Destructor
	public function __toString();			//overload print/echo routine with custom printout
*/


   	/**
	 * Register the database ion the object
	 */
	public function registerdbo($dbo);
	public function registerlogger($logger);
   
	/**
	 * Standard execution method of the plugin -> the HOOK
	 */
	public function execute();

	/**
	 * TODO Should print, handle Messages and error messages 
	 * write them to the logger, Exception whatever, decide it later and alter only one function
	 */
	public function handleMessage($msg);
	public function handleError($msg);
   
	public function setVariable($name, $var);
	public function getVariable();
	

	/* --------Decide here---------------------------------------------------  */

	//public function getIdentity();
	//public function getHtml($template);
  	//public function getName();
   
   
} //iPlugin

?>
