CREATE TABLE IF NOT EXISTS `accounts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `company` varchar(100) NOT NULL,
  `domain` varchar(20) NOT NULL,
  `currency` varchar(5) NOT NULL DEFAULT 'EUR',
  `registered` int(10) unsigned NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `domain` (`domain`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

CREATE TABLE IF NOT EXISTS `accounts_plans` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `for_account` int(10) unsigned NOT NULL,
  `plan` varchar(20) NOT NULL,
  `sponsored` int(11) NOT NULL DEFAULT '0',
  `payed` int(11) NOT NULL DEFAULT '0',
  `price` varchar(10) NOT NULL COMMENT 'price in $',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `from_date` int(10) unsigned NOT NULL,
  `to_date` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `for_account` int(10) unsigned NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `comment` longtext NOT NULL,
  `user` int(11) NOT NULL COMMENT 'id',
  `time` int(11) NOT NULL COMMENT 'published',
  `timeupdated` int(11) NOT NULL COMMENT 'updated',
  `sub_for` int(11) NOT NULL COMMENT 'id of parent',
  `message_uid` int(10) unsigned NOT NULL DEFAULT '0',
  `send` tinyint(1) NOT NULL,
  `message_attachments` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `connected_tickets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `for_account` int(11) NOT NULL,
  `ticket_1` int(10) unsigned NOT NULL COMMENT 'ticket id ',
  `ticket_2` int(10) unsigned NOT NULL COMMENT 'ticket id ',
  `type` varchar(30) NOT NULL COMMENT 'the type is ticket 1 to ticket 2 !!',
  PRIMARY KEY (`id`),
  UNIQUE KEY `tripleUnique` (`ticket_1`,`ticket_2`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `currencies` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `country` varchar(50) NOT NULL,
  `currency` varchar(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=116 ;

CREATE TABLE IF NOT EXISTS `custom_accounts_plans` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `for_id` int(10) unsigned NOT NULL COMMENT 'for id in - accounts_plans',
  `custom_domain` tinyint(1) NOT NULL,
  `projects` int(11) NOT NULL,
  `users` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `default_language` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `for_account` int(11) NOT NULL,
  `abbr` varchar(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

INSERT INTO `default_language` (`id`, `for_account`, `abbr`) VALUES
(1, 1, 'EN');

CREATE TABLE IF NOT EXISTS `login_image` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `for_account` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `log_tickets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `for_account` int(11) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `project_id` int(11) unsigned NOT NULL,
  `time` int(10) unsigned NOT NULL,
  `event` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `ticket_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='logs only for tickets' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `log_wiki` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `for_account` int(11) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `project_id` int(11) unsigned NOT NULL,
  `time` int(10) unsigned NOT NULL,
  `event` varchar(255) NOT NULL,
  `page_id` int(10) unsigned NOT NULL,
  `page_update_id` int(10) unsigned NOT NULL,
  `space_key` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='logs only for tickets' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `for_account` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'watcher id',
  `ticket_log_id` int(10) unsigned NOT NULL,
  `wiki_log_id` int(10) unsigned NOT NULL,
  `previewed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pass_resets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `for_account` int(11) NOT NULL,
  `reset_code` varchar(52) NOT NULL,
  `email` varchar(150) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reset_code` (`reset_code`,`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `paused_trackings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `for_account` int(11) NOT NULL,
  `for_id` int(10) unsigned NOT NULL,
  `from_time` int(10) unsigned NOT NULL,
  `to_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `priority_colors` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `for_id` int(10) unsigned NOT NULL,
  `color` char(7) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

INSERT INTO `priority_colors` (`id`, `for_id`, `color`) VALUES
(1, 2, '#009933'),
(2, 3, '#e60000'),
(3, 1, '#e0e0d1');

CREATE TABLE IF NOT EXISTS `professions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `for_account` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

CREATE TABLE IF NOT EXISTS `projects` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `for_account` int(11) NOT NULL,
  `abbr` varchar(3) NOT NULL COMMENT 'tickets abbreviation',
  `name` varchar(30) NOT NULL,
  `sync` int(11) NOT NULL DEFAULT '0' COMMENT 'synced with connection id',
  `timestamp` int(15) unsigned NOT NULL COMMENT 'creation time',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

CREATE TABLE IF NOT EXISTS `saved_tracktimes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `for_account` int(11) NOT NULL,
  `worked_seconds` int(6) NOT NULL COMMENT 'days, hours,seconds to seconds',
  `user_id` tinyint(4) NOT NULL COMMENT 'id of user',
  `project_id` tinyint(4) NOT NULL COMMENT 'id of project',
  `ticket_id` int(11) NOT NULL,
  `date_tracked` int(11) NOT NULL COMMENT 'date added',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `started_track_times` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `for_account` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `started` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `paused_on` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='currently working trackers for time' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `sync_connections` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `for_account` int(11) NOT NULL,
  `hostname` varchar(255) NOT NULL COMMENT 'for imap/pop3',
  `smtp_hostname` varchar(255) NOT NULL,
  `protocol` varchar(5) NOT NULL,
  `port` smallint(4) NOT NULL DEFAULT '0',
  `smtp_port` smallint(4) NOT NULL,
  `_ssl` tinyint(1) NOT NULL,
  `smtp_ssl` tinyint(1) NOT NULL,
  `self_signed` tinyint(1) NOT NULL DEFAULT '0',
  `folder` varchar(50) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

CREATE TABLE IF NOT EXISTS `tickets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `for_account` int(11) NOT NULL,
  `ticket_id` int(10) unsigned NOT NULL,
  `project` tinyint(3) unsigned NOT NULL,
  `type` varchar(20) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `status` tinyint(4) unsigned NOT NULL,
  `priority` tinyint(4) unsigned NOT NULL,
  `assignee` tinyint(4) unsigned NOT NULL,
  `addedby` tinyint(4) NOT NULL,
  `message_uid` int(11) NOT NULL DEFAULT '0' COMMENT 'uid of message',
  `message_id` varchar(255) DEFAULT NULL,
  `message_from_email` varchar(255) DEFAULT NULL,
  `message_from_name` varchar(255) DEFAULT NULL,
  `message_attachments` varchar(255) DEFAULT NULL,
  `message_to_email` varchar(255) DEFAULT NULL,
  `send` tinyint(1) NOT NULL DEFAULT '0',
  `duedate` int(12) unsigned NOT NULL,
  `pph` varchar(10) DEFAULT NULL,
  `pph_c` varchar(5) DEFAULT NULL,
  `estimated_seconds` int(10) unsigned NOT NULL COMMENT 'estimated time in seconds',
  `timecreated` int(12) unsigned NOT NULL,
  `timeclosed` int(10) unsigned NOT NULL,
  `lastupdate` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `ticket_priority` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `power` tinyint(2) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

INSERT INTO `ticket_priority` (`id`, `name`, `power`) VALUES
(1, 'Low', 3),
(2, 'Normal', 2),
(3, 'High', 1);

CREATE TABLE IF NOT EXISTS `ticket_statuses` (
  `id` tinyint(4) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

INSERT INTO `ticket_statuses` (`id`, `name`) VALUES
(4, 'Closed'),
(3, 'In Progress'),
(1, 'New'),
(5, 'Rejected'),
(2, 'To Do');

CREATE TABLE IF NOT EXISTS `ticket_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

INSERT INTO `ticket_types` (`id`, `name`) VALUES
(1, 'Bug'),
(2, 'Feature'),
(3, 'Support'),
(4, 'Task');

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `for_account` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(32) NOT NULL,
  `fullname` varchar(30) NOT NULL,
  `email` varchar(150) NOT NULL,
  `image` varchar(255) DEFAULT NULL COMMENT 'profile picture',
  `prof` tinyint(11) unsigned NOT NULL,
  `registered` int(10) unsigned NOT NULL,
  `last_login` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `last_active` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `dash_filter` varchar(255) DEFAULT NULL COMMENT 'serialize of default filter for dashboard',
  `social` varchar(255) NOT NULL COMMENT 'serialize of social media links',
  `projects` varchar(255) NOT NULL COMMENT 'id of projects that can work on',
  `email_notif` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Receive email when have notification',
  `lang` varchar(5) DEFAULT NULL,
  `privileges` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`,`for_account`),
  UNIQUE KEY `email` (`email`,`for_account`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

INSERT INTO `users` (`id`, `for_account`, `username`, `password`, `fullname`, `email`, `image`, `prof`, `registered`, `last_login`, `last_active`, `dash_filter`, `social`, `projects`, `email_notif`, `lang`, `privileges`) VALUES
(1, 1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'Demonstration Account', 'admin@pmticket.com', NULL, 1, 1467721962, 1477637142, 1477599995, '', '', ',3', 0, NULL, '1,2,3,4,5,6,7,8,9'); 

CREATE TABLE IF NOT EXISTS `watchers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `for_account` int(11) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `ticket_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `page_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `start_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'time that is started watching',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `wiki_pages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `for_account` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext DEFAULT NULL,
  `sub_for` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'This page is sub page for [page_id]',
  `for_space` int(10) unsigned NOT NULL COMMENT 'Id of space for this page',
  `category` int(10) unsigned NOT NULL COMMENT 'template id',
  `created` int(10) unsigned NOT NULL,
  `created_from` int(11) NOT NULL COMMENT 'Created from user',
  `hash` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hash` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `wiki_pages_updates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `for_account` int(11) NOT NULL,
  `num` int(10) unsigned NOT NULL,
  `content` longtext NOT NULL,
  `update_time` int(10) unsigned NOT NULL COMMENT 'Time updated',
  `page_id` int(10) unsigned NOT NULL COMMENT 'Update for [page_id]',
  `update_from` int(10) unsigned NOT NULL COMMENT 'Update from [user_id]',
  `first` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `wiki_page_templates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `for_account` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `default_r` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nameAndID` (`name`,`for_account`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

INSERT INTO `wiki_page_templates` (`id`, `for_account`, `name`, `content`, `default_r`) VALUES
(9, 1, 'Blank Page', '', 0),
(10, 1, 'How-to article', '<ol><li></li></ol>', 0);

CREATE TABLE IF NOT EXISTS `wiki_spaces` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `for_account` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `key_space` varchar(255) NOT NULL,
  `project_id` int(10) unsigned NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `timestamp` int(11) unsigned NOT NULL COMMENT 'time created',
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key_space`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;
