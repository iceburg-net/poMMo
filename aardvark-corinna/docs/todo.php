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

+++ Implement Passwords on user information (login.php). Include customizable question/answer pair.

+++ Merge subscribers/pending table.


I'd like to add a "comment" to the field types which would output a text area on the subscriber form that could be limited to a certain number of characters, and who's styling would be defined within themes/default/subscribe/form.subscribe.tpl (of course). Perhaps there's a better word for "comment" as a field type?

----
(18:17:49) comporder1: hey man
(18:18:01) bricecubed: hey
(18:18:03) comporder1: i had to resume one time this afternoon.
(18:18:21) comporder1: i found this in the error log - [23-Aug-2006 16:04:57] PHP Fatal error:  Maximum execution time of 10 seconds exceeded in D:\webs\ebcmain\pt\inc\phpmailer\class.smtp.php on line 1018

(18:18:40) bricecubed: excellent!
(18:19:00) comporder1: i take it that is good?
(18:19:14) bricecubed: what's your SMTP server?
(18:19:28) comporder1: cable isp
(18:19:42) bricecubed: from what I can tell; the SMTP server is not responding to the call... thus causing the script to timeout
----

1. when i try to import some subscribers from a CSV file, the script does inject them into the database alright, but the success page doesn't come up (subscribers_import2.php ),
in firefox it's a blank page and in IE it's a "server not found page". there are 650 subscribers in the csv file if that matters.

2.In the subscribers admin section, the default subscribers number per page is 50. When i try to select a bigger number, it also leads to a blank page.

3. mailings_send.php redirects me to /admin/admin/mailings_send2.php which doesn't exist the same with mailings_send3.php

4. do not forget to put a blank index in all the directories that do not have an index of their own


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
	  				
	  //(feature) add mailing history
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
	
	-> if it was this, it was a nasty problem :)
	
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

		(feature)	add + refactor http://www.phpinsider.com/php/code/SafeSQL/
			 	-> all but the Strings with escaped ''.
			 	
			 	$whereStr = ' WHERE group_id=\''.$where.'\'';
			 	[..]
			 	$safesql =& new SafeSQL_MySQL;
				$sql = $safesql->query("SELECT group_id, group_name FROM %s %s ORDER BY group_name",
					array($dbo->table['groups'], $whereStr) );
					
				Brice, what do you want for standard? SafeSQL wants "QUERY STRING in double QUOTES and the parameters in 'this quotes'"
				Also should i use always %s oder %i for the ids? Because you used 'id', but i think numbers can be without ''
				Can i convert all to this format? $stringvar = "abc'de'fgh"
				See his README in inc/safesql
				
				
		DB Scheme for Mailings current/history(ideas?) -- 
				* Eventually I think they should be merged into one table as we discussed. 
				At this time, lets focus elsewhere as there are bigger fish to fry ;). Mark this as long/medium term? 

				-> OK!
				-> You requested this in the poMMo forum if the execution times will be longer..
				With little data it is no problem and when one has a lot of data he can always 
				make a index on them (Indices).




	SHORT TERM
		
		(API) 		get rid of appendURL problem!
					+ convert to $poMMo->_state + save there
	
		(feature)	alter database design -> merge tables mailings &mailings_history and refactor
					EDIT: after finishing mailing ... database entry in mailing_current would not 
					switch to mailing_history
	
		(arch)		module integration architecture
					how hook in the modules?
		
		
		(module) 	User Administration (3 tier achitecture)
		
		(module)	LDAP Support, ADS
		
		(module)	Bounce management would be cool, as module
					Filter incoming Mails, if there is a mailer-daemon replied to 1 of 
					our mails report it to the administrator
		
		(feature)	Numeric types/sets for Demographics
		
	MIDDLE TERM
	
		(UI)		Manual, FAQ, User Doku
	
	

	LONG TERM



  -----------------------------------------------
 	[DONE]


 
  	(API) - Fix pager class. See Corinna's comments @ admin/mailings/mailings_history.php + 
	// This seems to not handle the case, that when we are on the last page of multiple pages,
	// and then choose to increase the diplay number then the start value is too great
	// eg. limit=5, 3 pages, go to page 3 -> then choose limit=10 
	// -> no mailings found because of start = 20 
	// its doing right, but less user friendly it it says no mailing, but its only that there are no mailings in this range
	// $pagelist : echo to print page navigation. -- 
	// TODO: adding appendURL to every link gets VERY LONG!!! come up w/ new plan!
	-> i started from the beginning in the case of $start geater then number of mails -> simple :/

