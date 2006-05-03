l<?php 
/**
 * poMMo Configuration File:
 *   This file sets up your database, language, and debugging options.
 *
 *   IMPORTANT: File must be named "config.php" and saved in the
 * 	"root" directory of your poMMo installation (where bootstrap.php is)
 */
// DO NOT REMOVE OR CHANGE THE BELOW LINE
defined('_IS_VALID') or die('Move along...');

/************************************************************************
 * BEGIN CHANGING VALUES --> 
 *     (values are between quotes - do not remove them or semicolons)
 *     (only alter values after the = sign)
 * 
 * ::: MySQL Database Information :::
 *   in order to use poMMo, you must have access to a valid MySQL database.
 *   Contact your webhost for details if you are unsure of its details.
*/

// * Set your MySQL username
$bmdb['username'] = 'pommo';

// * Set your MySQL password
$bmdb['password'] = 'pommo';

// * Set your MySQL hostname ("localhost" if  your MySQL database is running on the webserver)
$bmdb['hostname'] = 'localhost';

// * Set the name of the MySQL database used by poMMo
$bmdb['database'] = 'pommo'; 

// * Set the table prefix  (change if you intend to have multiple poMMos running from the same database)
$bmdb['prefix'] = 'pommo_';

/************************************************************************
 * ::: Language Information :::
 *   Set this to your desired locale  -- this is a work in progress
 * 
 *	en - English
 *	fr - French
 *	de - German
*/
define('bm_lang','en');


/************************************************************************
 * ::: Debugging Information :::
 *   Only modify these values if you'd like to provide information
 *   to the developers.
*/

// enable (on) or disable (off) debug mode. Set this to 'on' to provide debugging information
//  to the developers. Make sure to set it to 'off' when you are finished collecting information.
// NOTE: when providing debugging information, please use the output from the HTML source by
//  by choosing "view source" in your web browser.
define('bm_debug','off');

// set the verbosity level of logging.
//  1: Debugging
//  2: Informational
//  3: Important (default)
define('bm_verbosity',3);


/************************************************************************
 * ::: Cache Directory :::
 * 
 *   poMMo uses this directory to cache templates. By default, it
 *   is set to the "cache" directory in the poMMo root, and can
 *   safely be left blank or commented out (default).
 * 
 *   Make sure the webserver can write to this directory! poMMo
 *   will NOT WORK without being able to write to this directory.
 * 
 *   If you change its location, it is recommended to set it to a path
 *   outside the web root (for security reasons). 
 *  
 *   DO NOT USE A RELATIVE PATH, USE THE FULL SERVER PATH: e.g.
 *   '/home/b/brice/pommoCache'
 * 
*/
//define('bm_workDir','/path/to/pommoCache');
?>