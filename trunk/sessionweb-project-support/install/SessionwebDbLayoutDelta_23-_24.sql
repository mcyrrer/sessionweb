ALTER TABLE `sessionwebos`.`softwareuseautofetched` ADD INDEX `versionid` (`versionid` ASC) ;

UPDATE `sessionwebos`.`areas` SET `project`='0';
UPDATE `sessionwebos`.`custom_items` SET `project`='0';
UPDATE `sessionwebos`.`mission` SET `project`='0', updated=updated;
UPDATE `sessionwebos`.`settings` SET `project`='0';
UPDATE `sessionwebos`.`sprintnames` SET `project`='0';
UPDATE `sessionwebos`.`teamnames` SET `project`='0';
UPDATE `sessionwebos`.`testenvironment` SET `project`='0';
UPDATE `sessionwebos`.`user_sessionsnotification` SET `project`='0';
UPDATE `sessionwebos`.`user_settings` SET `project`='0';
UPDATE `sessionwebos`.`testenvironment` SET `project`='0';

ALTER TABLE `sessionwebos`.`mission_attachments` ADD COLUMN `thumbnail` MEDIUMBLOB NULL  AFTER `data` ;

UPDATE  `sessionwebos`.`version` SET  `versioninstalled` =  '24' WHERE  `version`.`id` =1;
