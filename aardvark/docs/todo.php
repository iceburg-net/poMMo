<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/
 
/** 
 * Don't allow direct access to this file. Must be called from
elsewhere
*/
defined('_IS_VALID') or die('Move along...');
?>

IMMEDIATE (for next release):

  (goals for PR11.1)
  * Smarty template scheme throughout whole program
		+ Remove /img, /inc/css|js, /setup directory
		+ Remove class.bform.php
  * Rid all poMMo getmessages in favor of Logger....
  * Add test "suite" to check httpspawn, create temporary tables, etc. etc.
  * Rename "demographics" to .... ?
  * instatiate ob_start() @ call to common.php ... call end method (flush) @ end of template display / redirection
   + Requires all pages to be under smarty templating architecutre
  * Fix embedded forms
     + Theme URLS should resolve to FULL http location ()
     +  better (proper) detection of poMMo root?
  
SHORT TERM:
  
  (API) Better mailing send debugging ->
    Change queue table to include "status" field --> ie. ENUM ('unsent','sent','failed') + error catching... (including PHP fatal errors) 
  (API) Merge validator's is_email rules with lib.txt.php's isEmail
  (API) Add validation schemes to subscription form (process.php)
  (API) when inserting into subscribers_flagged, watch for duplicate keys (either add IGNORE or explicity check for flag_type...)
  
  
  (feature) add mailing history
  (feature) add message templating
  (feature) Add Date + Numeric types
  

MEDIUM TERM:

  (API) Get rid of pending table. Add pending flag to subscribers, as well as "code" & action...
		+ Enforce non duplicate subscribers on the DB level!
  (API) Seperate lang files for "admin" & "user" directories --> total of 3: user, admin, install ??
  (API) Use smartyvalidator + custom validation rules for subscription/subscriber update forms!
     + get rid of isEmail()?
		
  (design) New default theme
  (design) New Installer & Upgrade script - Realtime flushing of output.
  
  (feature) add ability to send "comments" to list administrator upon successfull subscription
  (feature) add personalization to messages
  (feature) Add search capability to subscriber management
  (feature) Add OR to group filtering

  
LONG TERM:

  (API) include some kind of bandwith throttling / DOS detection / firewalling to drop pages from offending IPs / halt system for 3 mins if too many page requests ??
  (API) create embeddable friendly namespace/objects
    	+ work on Wordpress, gallery, OsCommerce/ZenCart Modules
    	
  (design) AJAX forms processing
  (feature) Allow seperate language selection for admin and user section. Include "auto" language selection based from client browser
