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
?>

[BEFORE Aardvark Final]
	+ Rewritten Import/Export
	+ Message Templating
	+ Rewritten Default Theme (CSS, JS, TEMPLATES)
	
	+ Remove language from default distribution, seperate download.
	+ Remove all unused JS/CSS/TPL/PHP/PNG/GIF/ETC Files
	+ Rewrite validate.js for scoping by table row
	

[THEME]
	USE TABLES IN SUBSCRIPTION FORM? -- DATEPICKER BREAKS FORMATTING
	ENHANCED DEFAULT SUBSCRIPTION FORM? -- THERE'S ALWAYS "PLAIN TEXT
		
  
[BRICE -- "Feel free to inherit any of these ;)" ]

	NOTES:
		CHECK subscriber update (self + admin) of UNCHECKING/CHECKING a check field
		MAKE BETTER USE OF PommoValidate::FUNCTIONS  (move more stuff to this file!)
		SECURITY ISSUE W/ SESSIONS -- e.g. If you login to demo & then acess pommo elsewhere on SAME DOMAIN -- you bypass login.
	
	SHORT TERM:
	
	  (API) - override PHPMailers error handling to use logger -- see extending PHPMailer Example @ website
	  (API) Better mailing send debugging ->
	    Change queue table to include "status" field --> ie. ENUM ('unsent','sent','failed') + error catching... (including PHP fatal errors) 
	    
	  (feature) add message templating
	  (feature) Add Admin Notifications (assignable email addresse(s)) of a) new subscriptions b) subscription updates c) unsubscriptions & d) newsletter sent.
	  (feature) Add OR to group filtering
	  	+ Utilize subquery method. Requires MySQL 4.1 .. GOOD!
	  (feature) Rewritten Pager -- current one is very ugly when > 25 pages are available. Use "google" like paging.
	  (feature) Ability to download a sent + unsent list during the processing of a mailing
	  
	  ADD Support Page (next to admin page in main menu bar)
		+ Enhanced support library
		+ PHPInfo()  (or specifically mysql, php, gettext, safemode, webserver, etc. versions)
		+ Database dump (allow selection of tables.. provide a dump of them)
		+ Link to README.HTML  +  local documentation
		+ Link to WIKI documentation
			+ Make a user-contributed open WIKI documentation system
			+ When support page is clicked, show specific support topics for that page
		+ Clear All Subscribers
		+ Reset Database
		+ Backup Database

	  "Test Mailing" enhancements
		+ send via httpSpawn
		+ popup dialog to fill in personalization values
	
	  	
	MEDIUM TERM:
	
	  (API) SWITCH "phase1" dialogs of subscriber add/delete/search/export to INLINE DISPLAY vs. AJAX POLL 
 		 + Requires unobtrusive modal window (thickbox destroys event bindings). Keep eye on Gavin's plugin
			
	  (feature) Add 'comment' type to subscriber field which outputs a text area configured to certain # of chars & whose styling is handled via theme template
	  (feature) Add specific emails to a group
	  (feature) Include "first page" which encourages "testing" and loading of sample data -- detect via maintenance routine.
	  (feature) Display flagged subscribers...
	  (feature) Support SSL+TLS SMTP Connections
	  
	  (security) Add a index.php to every directory (or use a .htaccess?)
	  
	LONG TERM:
	
	  (API) include some kind of bandwith throttling / DOS detection / firewalling to drop pages from offending IPs / halt system for 3 mins if too many page requests ??
	  (API) Plugin architecture -- allow handler & manipulation injections/replacements to API functions
	  	+ Can be used to chain the subscription process (process.php) through custom functions, add an extended authentication layer, etc.
	  	
	  (design) client side validation of subscribe form (use validation.js), potential AJAX processing
	  (module) Visual Verrification / CAPTCHA @ subscribe form
	  (feature) Bounced mail reading
	  (feature) Add search capability to subscriber management
	  
	  (security) Implement Passwords on user information (login.php). Include customizable question/answer pair.


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
				
				// bb: Corinna, PR14 wraps about every query through safeSQL. Look at functions in inc/helpers/*
				 as examples to the new standard. Let me know if you have any ?s ;)
				
				
		DB Scheme for Mailings current/history(ideas?) -- 
				* Eventually I think they should be merged into one table as we discussed. 
				At this time, lets focus elsewhere as there are bigger fish to fry ;). Mark this as long/medium term? 

				-> OK!
				-> You requested this in the poMMo forum if the execution times will be longer..
				With little data it is no problem and when one has a lot of data he can always 
				make a index on them (Indices).
				
				// bb : Corinna, I've merged subscribers and mailings tables for PR14

	SHORT TERM
		
		(API) 		get rid of appendURL problem!
					+ convert to $pommo->_state + save there
	
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
	
	bb: I'll be replacing the pager class with a more Google like one -- current one displays WAY too many pages
	with large amounts of subscribers (50,000+)

