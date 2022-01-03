CREATE TABLE IF NOT EXISTS `user_log` (
  `id` int(11) NOT NULL auto_increment,
  `id_user` int(11) NOT NULL ,
  `method` set('1','0') NOT NULL default '0',
  `time` int(11) NOT NULL ,
  `ip` bigint(20) NOT NULL default '0',
  `ua` varchar(32) default NULL,
  PRIMARY KEY  (`id`),
  KEY `id_user` (`id_user`),
  KEY `time` (`time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;