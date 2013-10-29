CREATE TABLE `sessionwebos`.`mission_incremental_save` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `versionid` int(11) NOT NULL,
  `title` varchar(500) NOT NULL,
  `charter` mediumtext NOT NULL,
  `notes` mediumtext NOT NULL,
  `timestamp_saved` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `versionid` (`versionid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='Table to store incremental  save of titles, note and charters'


UPDATE  `sessionwebos`.`version` SET  `versioninstalled` =  '27' WHERE  `version`.`id` =1;
