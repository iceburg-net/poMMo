/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * Created: Corinna Thoeni <corinn at gmx dot net> - 16.10.2006
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/
 
 VERY BETA!!!
 
 
 
 
 In order to enable plugins:
 
 -	Enable plugins in the config.php file in the poMMo root.
 	Set the value $useplugins = TRUE;
 
 -	Generate TABLES in the database with the script:
 	installplugins.php
 	It contains some basic configurations. To edit the configurations use the pluginmanager plugin.
 	All configs should be editeble through the database.
 	
 
 There are some constraints -> e.g. the authentication plugins
 Maybe some constraints will be added in the future.
