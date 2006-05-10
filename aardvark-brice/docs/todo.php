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

   
SHORT TERM:

  (API) - override PHPMailers error handling to use logger -- see extending PHPMailer Example @ website
  (API) Better mailing send debugging ->
    Change queue table to include "status" field --> ie. ENUM ('unsent','sent','failed') + error catching... (including PHP fatal errors) 
  (API) Merge validator's is_email rules with lib.txt.php's isEmail
  (API) Add validation schemes to subscription form (process.php)
  (API) when inserting into subscribers_flagged, watch for duplicate keys (either add IGNORE or explicity check for flag_type...)
  (API) Allow fetching of field id, names, + types -- NOT OPTIONS, etc... too much data being passed around manage/groups/import/etc.
  
  (feature) Add ability to view emails in queue (from mailing status)
  (feature) Mail hanging prevention --  if command recieves > 20 seconds, prompt to restart/cancel.
  
  (feature) Add test "suite" to check httpspawn, create temporary tables, etc. etc.
  
  (feature) add mailing history
  (feature) add message templating
  (feature) Add Date + Numeric types  [[[{html_select_date}]]]
  
  (feature) Enhanced subscriber management
  (feature) Display flagged subscribers...
  
  

MEDIUM TERM:

  (API) Get rid of pending table. Add pending flag to subscribers, as well as "code" & action...
		+ Enforce non duplicate subscribers on the DB level!
  (API) Seperate lang files for "admin" & "user" directories --> total of 3: user, admin, install ??
  
  
  (API) Use smartyvalidator + custom validation rules for subscription/subscriber update forms!
     + get rid of isEmail()?
		

  
  (feature) add ability to send "comments" to list administrator upon successfull subscription
  (feature) add personalization to messages
  (feature) Add search capability to subscriber management
  (feature) Add OR to group filtering
  (feature) Enhanced subscriber import

  
LONG TERM:

  (API) include some kind of bandwith throttling / DOS detection / firewalling to drop pages from offending IPs / halt system for 3 mins if too many page requests ??
  
  (API) create embeddable friendly namespace/objects - published API (externally accessible)
    	+ work on Wordpress, gallery, OsCommerce/ZenCart Modules
    	

  (design) New default theme
  (design) New Installer & Upgrade script - Realtime flushing of output.
  (design) AJAX forms processing
  
  (feature) Allow seperate language selection for admin and user section. Include "auto" language selection based from client browser
  (feature) Bounced mail reading
 