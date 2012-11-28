ALTER TABLE `sessionwebos`.`softwareuseautofetched` ADD INDEX `versionid` (`versionid` ASC) ;

UPDATE  `sessionwebos`.`version` SET  `versioninstalled` =  '24' WHERE  `version`.`id` =1;
