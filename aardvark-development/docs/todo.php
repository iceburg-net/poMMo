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

[BEFORE Aardvark Final]
	+ Message Templating
	+ Rewritten Default Theme (CSS, JS, TEMPLATES)
	
	+ Remove language from default distribution, seperate download.
	+ Remove all unused JS/CSS/TPL/PHP/PNG/GIF/ETC Files
	+ Rewrite validate.js for scoping by table row
	

[THEME]
	ENHANCED DEFAULT SUBSCRIPTION FORM? -- THERE'S ALWAYS "PLAIN TEXT
	ADD MESSAGE OUTPUT/DETECTION TO EVERY PAGE (logger messages -- esp. debugging stuff)
	Use TableSorter/Table layout for field, group, and group filter display
	Fix table styling/striping -- don't rely on #subs! make a generic architecture + modular CSS include!
	  Seen in subscriber_manage, import_csv, and mailings_history SO FAR
	Layout Fixes for IE -- see http://www.flickr.com/photos/26392873@N00/322986007/
	  
[BRICE -- "Feel free to inherit any of these ;)" ]

	NOTES:
		MAKE BETTER USE OF PommoValidate::FUNCTIONS  (move more stuff to this file!)

	SHORT TERM: 
	  (API) Replace all prototype/scriptaculous/lightbox with jQuery equivelent
	  
	  (feature) add message templating
	  (feature) Add Admin Notifications (assignable email addresse(s)) of a) new subscriptions b) subscription updates c) unsubscriptions & d) newsletter sent.
	  (feature) Add OR to group filtering
	  	+ Utilize subquery method. Requires MySQL 4.1 .. GOOD!
	  	
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
	  (feature) Rewritten Pager -- current one is very ugly when > 25 pages are available. Use "google" like paging.

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
		+ Ensure max run time is 30 seconds if safe mode is enabled

	  	
	MEDIUM TERM:
	
	  (API) SWITCH "phase1" dialogs of subscriber add/delete/search/export to INLINE DISPLAY vs. AJAX POLL 
 		 + Requires unobtrusive modal window (thickbox destroys event bindings). Keep eye on Gavin's plugin
	  (API) Rewrite PommoMailer()  [ currently depricated with PR13 functionality ]
	  (API) Rewrite PommoThrottler() [ currently depricated with PR13 functionality ]
	  (API) Better Organize inc/helpers/messages & validate... underutilized!
	  (API) - override PHPMailers error handling to use logger -- see extending PHPMailer Example @ website
	  
	  
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

