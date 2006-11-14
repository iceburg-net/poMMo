-- CONFIG <?php die('Move along...'); ?>

CREATE TABLE :::config::: (
  `config_name` varchar(64) NOT NULL default '',
  `config_value` text NOT NULL,
  `config_description` tinytext NOT NULL,
  `autoload` enum('on','off') NOT NULL default 'on',
  `user_change` enum('on','off') NOT NULL default 'on',
  PRIMARY KEY  (`config_name`)
);

INSERT INTO :::config::: VALUES ('admin_username', 'admin', 'Username', 'off', 'on');
INSERT INTO :::config::: VALUES ('admin_password', 'c40d70861d2b0e48a8ff2daa7ca39727', 'Password', 'off', 'on');
INSERT INTO :::config::: VALUES ('site_name', 'A', 'Website Name', 'on', 'on');
INSERT INTO :::config::: VALUES ('site_url', 'http://66.111.62.220/pommo', 'Website URL', 'on', 'on');
INSERT INTO :::config::: VALUES ('site_success', '', 'Signup Success URL', 'off', 'on');
INSERT INTO :::config::: VALUES ('list_name', 'A', 'List Name', 'on', 'on');
INSERT INTO :::config::: VALUES ('admin_email', 'nesta@iceburg.net', 'Administrator Email', 'on', 'on');
INSERT INTO :::config::: VALUES ('list_fromname', 'poMMo Administrative Team', 'From Name', 'off', 'on');
INSERT INTO :::config::: VALUES ('list_fromemail', 'pommo@yourdomain.com', 'From Email', 'off', 'on');
INSERT INTO :::config::: VALUES ('list_frombounce', 'bounces@yourdomain.com', 'Bounces', 'off', 'on');
INSERT INTO :::config::: VALUES ('list_exchanger', 'sendmail', 'List Exchanger', 'off', 'on');
INSERT INTO :::config::: VALUES ('list_confirm', 'on', 'Confirmation Messages', 'off', 'on');
INSERT INTO :::config::: VALUES ('demo_mode', 'on', 'Demonstration Mode', 'on', 'on');
INSERT INTO :::config::: VALUES ('site_confirm', '', '', 'off', 'on');
INSERT INTO :::config::: VALUES ('smtp_1', '', '', 'off', 'off');
INSERT INTO :::config::: VALUES ('smtp_2', '', '', 'off', 'off');
INSERT INTO :::config::: VALUES ('smtp_3', '', '', 'off', 'off');
INSERT INTO :::config::: VALUES ('smtp_4', '', '', 'off', 'off');
INSERT INTO :::config::: VALUES ('throttle_DBPP', '0', '', 'off', 'on');
INSERT INTO :::config::: VALUES ('throttle_DP', '10', '', 'off', 'on');
INSERT INTO :::config::: VALUES ('throttle_DMPP', '0', '', 'off', 'on');
INSERT INTO :::config::: VALUES ('throttle_BPS', '0', '', 'off', 'on');
INSERT INTO :::config::: VALUES ('throttle_MPS', '3', '', 'off', 'on');
INSERT INTO :::config::: VALUES ('throttle_SMTP', 'individual', '', 'off', 'on');
INSERT INTO :::config::: VALUES ('dos_processors', '0', '', 'on', 'off');
INSERT INTO :::config::: VALUES ('messages', '', '', 'off', 'off');
INSERT INTO :::config::: VALUES ('list_charset', 'UTF-8', '', 'off', 'on');
INSERT INTO :::config::: VALUES ('version', 'Aardvark SVN', 'poMMo Version', 'on', 'off');
INSERT INTO :::config::: VALUES ('revision', '26', 'Internal Revision', 'on', 'off');

-- DEMOGRAPHICS

CREATE TABLE :::fields::: (
  `field_id` smallint(5) unsigned NOT NULL auto_increment,
  `field_active` enum('on','off') NOT NULL default 'off',
  `field_ordering` smallint(5) unsigned NOT NULL default '0',
  `field_name` varchar(60) default NULL,
  `field_prompt` varchar(60) default NULL,
  `field_normally` varchar(60) default NULL,
  `field_array` text,
  `field_required` enum('on','off') NOT NULL default 'off',
  `field_type` enum('checkbox','multiple','text','date','number') default NULL,
  PRIMARY KEY  (`field_id`),
  KEY `active` (`field_active`,`field_ordering`)
);

-- GROUPS

CREATE TABLE :::groups::: (
  `group_id` smallint(5) unsigned NOT NULL auto_increment,
  `group_name` tinytext  NOT NULL,
  PRIMARY KEY  (`group_id`)
);

-- GROUPS_CRITERIA

CREATE TABLE :::groups_criteria::: (
  `criteria_id` int(10) unsigned NOT NULL auto_increment,
  `group_id` int(10) unsigned NOT NULL default '0',
  `field_id` tinyint(3) unsigned NOT NULL default '0',
  `logic` enum('is_in','not_in','is_equal','not_equal','is_more','is_less','is_true','not_true') NOT NULL default 'is_in',
  `value` text,
  PRIMARY KEY  (`criteria_id`),
  KEY `group_id` (`group_id`)
);


-- MAILING_CURRENT

CREATE TABLE :::mailing_current::: (
  `current_id` int(10) unsigned NOT NULL,
  `command` enum('none','restart','stop') NOT NULL default 'none',
  `serial` varchar(20) default NULL,
  `securityCode` varchar(35) default NULL,
  `notices` longtext default NULL,
  `current_status` enum('started','stopped') NOT NULL default 'stopped',
  PRIMARY KEY  (`current_id`)
);


-- MAILINGS

CREATE TABLE :::mailings::: (
  `mailing_id` int(10) unsigned NOT NULL auto_increment,
  `fromname` varchar(60) NOT NULL default '',
  `fromemail` varchar(60) NOT NULL default '',
  `frombounce` varchar(60) NOT NULL default '',
  `subject` varchar(60) NOT NULL default '',
  `body` mediumtext NOT NULL,
  `altbody` mediumtext default NULL,
  `ishtml` enum('on','off') NOT NULL default 'off',
  `mailgroup` varchar(60) NOT NULL default 'Unknown',
  `subscriberCount` int(10) unsigned NOT NULL default '0',
  `started` datetime NOT NULL,
  `finished` datetime NOT NULL,
  `sent` int(10) unsigned NOT NULL default '0',
  `charset` varchar(15) NOT NULL default 'UTF-8',
  PRIMARY KEY  (`mailing_id`)
);


-- QUEUE

CREATE TABLE :::queue::: (
  `email` varchar(60) NOT NULL default '',
  `smtp_id` enum('0','1','2','3','4') NOT NULL default '0',
  UNIQUE KEY `email` (`email`),
  KEY `smtp_id` (`smtp_id`)
);


-- SUBSCRIBER_DATA

CREATE TABLE :::subscriber_data::: (
  `data_id` bigint(20) unsigned NOT NULL auto_increment,
  `field_id` int(10) unsigned NOT NULL default '0',
  `subscriber_id` int(10) unsigned NOT NULL default '0',
  `value` varchar(60) NOT NULL default '',
  PRIMARY KEY  (`data_id`),
  KEY `s_plus_demo_id` (`field_id`,`subscriber_id`),
  KEY `val_plus_demo` (`value`,`field_id`),
  KEY `subscriber_id` (`subscriber_id`),
  KEY `subscriber_id_2` (`subscriber_id`,`value`)
);


-- SUBSCRIBER_PENDING

CREATE TABLE :::subscriber_pending::: (
  `pending_id` int(10) unsigned NOT NULL auto_increment,
  `subscriber_id` int(10) unsigned NOT NULL default '0',
  `pending_code` varchar(35) NOT NULL default '',
  `pending_type` enum('add','del','change','password') default NULL,
  `pending_email` varchar(60) NULL default NULL,
  PRIMARY KEY  (`pending_id`),
  KEY `code` (`pending_code`),
  KEY `subscriber_id` (`subscriber_id`)
);


--  SUBSCRIBERS

CREATE TABLE :::subscribers::: (
  `subscriber_id` int(10) unsigned NOT NULL auto_increment,
  `email` varchar(60) NOT NULL default '',
  `time_touched` timestamp(14) NOT NULL,
  `time_registered` datetime NOT NULL,
  `flag` enum('update') default NULL,
  `ip` varchar(60) default NULL,
  `status` enum('active','inactive','pending') NOT NULL default 'pending',
  PRIMARY KEY  (`subscriber_id`),
  KEY `email` (`email`(30)),
  KEY `flag` (`flag`),
  KEY `status` (`status`)
);


-- UPDATES

CREATE TABLE :::updates::: (
  `update_id` int(10) unsigned NOT NULL auto_increment,
  `update_serial` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`update_id`),
  KEY `update_serial` (`update_serial`)
);
