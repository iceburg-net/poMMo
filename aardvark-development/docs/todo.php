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
die();
?>

Mailing notices -- no dupes are displayed

Test mailing exchanger from setup > configure page

Toggle gettext emulation (phpgettext)

----
"Hello. I have upload a file with about 35.000 emails.

All ok. But, after, when i need to select field i have this error:

Fatal error: Maximum execution time of 30 seconds exceeded

/inc/lib/safesql/SafeSQL.class.php on line 106"
----

Add http://www.pommo.org/community/viewtopic.php?pid=1609#p1609  JP Charset

[LEGACY POMMO]
  Port config parser + config.php/sample

[BEFORE Aardvark Final]
	+ Message Templating
	+ Remove language from default distribution, seperate download.
	+ Remove all unused JS/CSS/TPL/PHP/PNG/GIF/ETC Files
	+ Rewrite validate.js for scoping by table row
	

[THEME]
	ENHANCED DEFAULT SUBSCRIPTION FORM? -- THERE'S ALWAYS "PLAIN TEXT
	ADD MESSAGE OUTPUT/DETECTION TO EVERY PAGE (logger messages -- esp. debugging stuff)
	Use TableSorter/Table layout for field, group, and group filter display
	Layout Fixes for IE -- see http://www.flickr.com/photos/26392873@N00/322986007/
	ReStripe rows on delete/tableSort
	ELEMENTS with TITLE="??" : Title needs to be translated -- use SAME text as INNERHTML/LINK text
	
[BRICE -- "Feel free to inherit any of these ;)" ]

	NOTES:
		MAKE BETTER USE OF PommoValidate::FUNCTIONS  (move more stuff to this file!)

	SHORT TERM: 
	  (API) Replace all prototype/scriptaculous/lightbox with jQuery equivelent
	  
	  (API) Maintenance : clean out old/not utilized activations from subscriber_update
	  
	  (feature) add message templating
	  
	  (feature) Add Admin Notifications (assignable email addresse(s)) of a) new subscriptions b) subscription updates c) unsubscriptions & d) newsletter sent.
	  (enhancement) Setup > Config tabbed layout
	  
	  (feature) Add OR to group filtering
	  	+ Utilize subquery method. Requires MySQL 4.1 .. GOOD!
	  	+ Use http://interface.eyecon.ro/demos/sort_example.html  to move between && or ||
	  	
	  	----
			SELECT count(subscriber_id)
			from subscribers 
			where 
			status ='1' 
			AND (
			subscriber_id in 
				(select subscriber_id from subscriber_data  where  field_id =3 and value IN ('on'))
			AND subscriber_id in 
				(select subscriber_id from subscriber_data  where  field_id =4 and value NOT IN ('lemur'))
			OR subscriber_id in
				(select subscriber_id from subscriber_data  where  field_id =5 and value NOT IN ('on'))
			);
	  	----
	  	
	  ADD Support Page (next to admin page in main menu bar)
		+ Enhanced support library
		+ PHPInfo()  (or specifically mysql, php, gettext, safemode, webserver, etc. versions)
		+ Database dump (allow selection of tables.. provide a dump of them)
		+ Link to README.HTML  +  local documentation
		+ Link to WIKI documentation
			+ Make a user-contributed open WIKI documentation system
			+ When support page is clicked, show specific support topics for that page
	  	
	MEDIUM TERM:
	
	  (API) SWITCH "phase1" dialogs of subscriber add/delete/search/export to INLINE DISPLAY vs. AJAX POLL 
 		 + Requires unobtrusive modal window (thickbox destroys event bindings). Keep eye on Gavin's plugin
	  (API) Rewrite PommoMailer()  [ currently depricated with PR13 functionality ]
	  (API) Rewrite PommoThrottler() [ currently depricated with PR13 functionality ]
	  (API) Better Organize inc/helpers/messages & validate... underutilized!
	  (API) - override PHPMailers error handling to use logger -- see extending PHPMailer Example @ website
	  
	  (API) - Rewrite admin reset password request!  -- get rid of PommoPending::getBySubID()!!
	  
	  
	  (feature) Implement drag & drop between AND and OR filters (via table row handles)
	  (feature) Add 'comment' type to subscriber field which outputs a text area configured to certain # of chars & whose styling is handled via theme template
	  (feature) Add specific emails to a group
	  (feature) Include "first page" which encourages "testing" and loading of sample data -- detect via maintenance routine.
	  (feature) Display flagged subscribers...
	  (feature) Support SSL+TLS SMTP Connections
	  
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

	SHORT TERM
		
		(API) 		get rid of appendURL problem!
					+ convert to $pommo->_state + save there
					
			====>	BB: Corinna, See the new state handling in subscribers_manage & mailings_history
	
		(arch)		module integration architecture
					how hook in the modules?
		
		
		(module) 	User Administration (3 tier achitecture)
		
		(module)	LDAP Support, ADS
		
		(module)	Bounce management would be cool, as module
					Filter incoming Mails, if there is a mailer-daemon replied to 1 of 
					our mails report it to the administrator
		
		
	MIDDLE TERM
	
		(UI)		Manual, FAQ, User Doku
	
	

	LONG TERM



  -----------------------------------------------
 	[DONE]

