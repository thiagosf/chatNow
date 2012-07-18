CREATE TABLE `chat_admin` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `user` varchar(30) default NULL,
  `password` char(32) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
CREATE TABLE `chat_ip_block` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `ip` varchar(20) default NULL,
  `permanent` tinyint(3) unsigned NOT NULL default '0',
  `timestamp` timestamp NULL default NULL,
  `end` timestamp NULL default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `chat_messages` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `user` varchar(20) default NULL,
  `id_user` int(11) unsigned default NULL,
  `message` tinytext,
  `to_user` int(11) unsigned default NULL,
  `id_room` int(11) unsigned default NULL,
  `reserved` tinyint(1) unsigned NOT NULL default '0',
  `type` varchar(10) default NULL COMMENT 'entry, exit',
  `timestamp` timestamp NULL default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `chat_rooms` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `room` varchar(50) default NULL,
  `description` tinytext,
  `capacity` int(11) unsigned default NULL,
  `capacity_exclusive` int(11) unsigned default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
CREATE TABLE `chat_users` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `user` varchar(20) NOT NULL default '',
  `id_room` int(11) unsigned NOT NULL default '0',
  `ip` varchar(20) NOT NULL default '',
  `timestamp` timestamp NULL default NULL,
  `active` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO chat_admin (user, password) VALUES ('root', '202cb962ac59075b964b07152d234b70');
INSERT INTO chat_rooms (room, description, capacity, capacity_exclusive) VALUES ('Room test', 'Test', 50, 80);