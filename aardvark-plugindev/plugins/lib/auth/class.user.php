<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * Created: Corinna Thoeni <corinn at gmx dot net> - 19.01.2007
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/

Pommo::requireOnce($this->_baseDir.'plugins/lib/auth/class.adminuser.php');
Pommo::requireOnce($this->_baseDir.'plugins/lib/auth/class.simpleuser.php');


/**
 * Interface for the User types used in this framework. For more
 * information see:
 * plugins/lib/auth/class.simpleuser.php
 * plugins/lib/auth/class.adminuser.php
 */
interface User {

	public function __construct($username, $md5pass);
	public function authenticate();
	public function isAuthenticated();

} //User

?>
