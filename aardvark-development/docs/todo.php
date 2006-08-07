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

Personally I would also like to see a "chain" in place for the unsubscribe, eg. it calls the unsuscribe as it does now but then continies onto another php file, by default empty. But this would allow users (admins) to implement any further processing that they wanted to do, i would imagine that the persons email address should be "posted" to this php chainer. This could make the integration of this to any other installation of anything else, eg site registration removal, so much easier.

Add validation of subscriber field name (via AJAX?) with personalization -- check on form submit of mailings_send2.php 
  + Ensure that server side algorithim during mailing_send4.php will not choke on invalid subscriber field name
  
  
[BRICE]
	
	IMMEDIATE (for next release):
		+ fix prepareForForm() in SMARTY TEMPLATE (must load proper JS, CSS!)
			-  test w/ group adding ("type group name" not cleared on focus)

	[BEFORE 1.0]
		+ Personalization
		+ Rewritten Import
		+ Rewritten Subscriber Manage
		+ Message Templating
		+
	
	SHORT TERM:
	
	
	  (API) - Fix pager class. See Corinna's comments @ admin/mailings/mailings_history.php + get rid of appendURL problem!
	
	  (API) - secure "included" files under cache -- don't include them.. rather run them through specialized parser? e.g. for embed.forms & httpSpawn tester
	  
	  (API) - override PHPMailers error handling to use logger -- see extending PHPMailer Example @ website
	  (API) Better mailing send debugging ->
	    Change queue table to include "status" field --> ie. ENUM ('unsent','sent','failed') + error catching... (including PHP fatal errors) 
	  (API) Merge validator's is_email rules with lib.txt.php's isEmail
	  (API) Add validation schemes to subscription form (process.php)
	  (API) when inserting into subscribers_flagged, watch for duplicate keys (either add IGNORE or explicity check for flag_type...)
	  (API) Allow fetching of field id, names, + types -- NOT OPTIONS, etc... too much data being passed around manage/groups/import/etc.
	  
	  (feature) Add ability to view emails in queue (from mailing status)

	  (feature) add personalization to messages
	  	+ personalization algorithm ->
	  		[queue is grabbed per page] (TODO defaults to 100, make easier to set @ file header)
	  			+ adjust queue to also return subscriberID, stored in $queue variable w/ email
	  		[body is loaded into session] 
	  			+ if body contains personalization, ENABLE personalization
	  				+ get position of personalizations & assosiated demographic_id & "default" into PERSONALIZATIONS array
	  				
	  				
	  		[mail leavs throttler to send]
	  			+ IF _SESSION[PERSONALIZATION] PERSONALIZATION
	  					+ personalizeBody(subId)
	  			SEND MAIL
	  		
	  		[personalizeBody]	
	  		personalizeBody(subId)
	  			IF subId !IN SESSION_BUFFER {
	  				add to buffer(queue,email)	
	  			}
	  			
	  			alter mailer body... original stored in session, posistions are cached
	  			
	  		
	  		[ADD TO BUFFER]
	  			+ GRAB FIRST 15 (set in header) sub IDs from subscriber_values -- make sure to include subscriber ID passed
	  			ADD VALUES to SESSION as ARRAY [subID] [demoID => value || default]
	  			
	  			
	  			}
	  				
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
	  
	  (module) Visual Verrification / CAPTCHA @ subscribe form
	  
	  (feature) Allow seperate language selection for admin and user section. Include "auto" language selection based from client browser
	  (feature) Bounced mail reading
  

		 -----
	  
	  UNCAT
	  
	  when we want to set up a mailing in ISO-8859-15 encoding, this mailing is sent as ISO-8859-1 ...
	I got the reason : the xxx_mailing_current table is created with a column charset varchar(10) and ISO-8859-15 is 11 char's long...
	
	For me it's now corrected with this SQL line :
	- alter table pommo_mailing_current change charset charset varchar(30) not null;
	
	  REGEX group filtering
	  Admin notification on subscriber changes/unsubscribes/additions/etc.
	 
	 
	 
	 ----- 
	  P.S. : I also get another issue - when users type any special character (umlaut, accent, etc.) in the name field (I added it to the form), the name appears buggy in the admin interface. It is correct in DB but it's a bit annoying because all colums are scrambled in the interface. Any idea???
	  
	  corinna: Somtimes i get pages with little "?" for umlaut also.
	  corinna: when i switch to some ISO 8859 or so then it works
	  corinna: maybe it is a firefox browser problem or has to do with this (?) ->
	  corinna: http://www.w3.org/International/O-HTTP-charset
	  			-> For PHP, use the header() function before generating any content, e.g.: header("Content-type: text/html; charset=utf-8");
	 

[CORINNA]

	(feature)	fix paging class 
	
	(feature)	add + refactor http://www.phpinsider.com/php/code/SafeSQL/
	(feature)	alter database design -> merge tables mailings &mailings_history and refactor

				EDIT: after finishing mailing ... database entry in mailing_current would not switch to mailing_history

	(feature)	Mailing History 		Mailing History -> Database insertion of Mailings
	(feature)	Numeric types/sets for Demographics

	(feature)	Change Radio Button Labels
				<label for="r1"><input type="radio" name="group1" id="r1" value="1" /> button one</label> 
				so that a click on a label activates the radio button and not a click on the mini-button
				e.g we have "O on" and want it to activate with a click on "on" and not only the "O"
				
				-> problem because fields_edit.php LABEL is the Description of the entire field
				-> css alterations with inner labels

	(module) 	User Administration (3 tier achitecture)
	(module)	LDAP Support, ADS


	
	LONG TERM:
	DB Scheme for Mailings current/history(ideas?) -- 
		* Eventually I think they should be merged into one table as we discussed. At this time,
			lets focus elsewhere as there are bigger fish to fry ;). Mark this as long/medium term? 
			~ Brice
		-> OK! 


	DONE: 
	(done) 	(feature) View Page (mailings_mod): Ability to "load" message -- copy body, group, subject, from, 
			etc. to a new Mailing.	
  
 
