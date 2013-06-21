


SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

UPDATE `sessionwebos`.`areas` SET `project`='0';
UPDATE `sessionwebos`.`custom_items` SET `project`='0';
UPDATE `sessionwebos`.`mission` SET `project`='0', updated=updated;
UPDATE `sessionwebos`.`settings` SET `project`='0';
UPDATE `sessionwebos`.`sprintnames` SET `project`='0';
UPDATE `sessionwebos`.`teamsprintnames` SET `project`='0';
UPDATE `sessionwebos`.`teamnames` SET `project`='0';
UPDATE `sessionwebos`.`testenvironment` SET `project`='0';
UPDATE `sessionwebos`.`user_sessionsnotification` SET `project`='0';
UPDATE `sessionwebos`.`user_settings` SET `project`='0';
UPDATE `sessionwebos`.`testenvironment` SET `project`='0';

ALTER TABLE `sessionwebos`.`mission` CHANGE COLUMN `project` `project` VARCHAR(45) NOT NULL  ;

ALTER TABLE `sessionwebos`.`sprintnames` CHANGE COLUMN `project` `project` VARCHAR(45) NOT NULL  ;

ALTER TABLE `sessionwebos`.`teamnames` CHANGE COLUMN `project` `project` VARCHAR(45) NOT NULL  ;

ALTER TABLE `sessionwebos`.`teamsprintnames` CHANGE COLUMN `project` `project` VARCHAR(45) NOT NULL  ;

ALTER TABLE `sessionwebos`.`settings` CHANGE COLUMN `project` `project` VARCHAR(45) NOT NULL  , ADD COLUMN `wisemapping_url` VARCHAR(45) NULL DEFAULT NULL  AFTER `project` , ADD COLUMN `wisemapping_user` VARCHAR(45) NULL DEFAULT NULL  AFTER `wisemapping_url` , ADD COLUMN `wisemapping_password` VARCHAR(45) NULL DEFAULT NULL  AFTER `wisemapping_user` , ADD COLUMN `wisemapping` TINYINT(1) NULL DEFAULT 0  AFTER `wisemapping_password` ;

ALTER TABLE `sessionwebos`.`areas` CHANGE COLUMN `project` `project` VARCHAR(45) NOT NULL  ;

ALTER TABLE `sessionwebos`.`user_settings` CHANGE COLUMN `project` `project` VARCHAR(45) NOT NULL  ;

ALTER TABLE `sessionwebos`.`testenvironment` CHANGE COLUMN `project` `project` VARCHAR(45) NOT NULL  ;

ALTER TABLE `sessionwebos`.`version` ADD COLUMN `versioncol` VARCHAR(45) NULL DEFAULT NULL  AFTER `versioninstalled` ;

ALTER TABLE `sessionwebos`.`mission_attachments` ADD COLUMN `thumbnail` MEDIUMBLOB NULL DEFAULT NULL  AFTER `data` , ADD COLUMN `mission_attachmentscol` VARCHAR(45) NULL DEFAULT NULL  AFTER `thumbnail` , ADD COLUMN `mission_attachmentscol1` VARCHAR(45) NULL DEFAULT NULL  AFTER `mission_attachmentscol` , ADD COLUMN `mission_attachmentscol2` VARCHAR(45) NULL DEFAULT NULL  AFTER `mission_attachmentscol1` ;

ALTER TABLE `sessionwebos`.`user_sessionsnotification` CHANGE COLUMN `project` `project` VARCHAR(45) NOT NULL  ;

ALTER TABLE `sessionwebos`.`custom_items` CHANGE COLUMN `project` `project` VARCHAR(45) NOT NULL  ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`mission_mindmaps` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `versionid` INT(11) NOT NULL ,
  `map_id` INT(11) NOT NULL ,
  `map_title` VARCHAR(300) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `versionid` (`versionid` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

USE `sessionwebos`;


-- -----------------------------------------------------
-- Placeholder table for view `sessionwebos`.`sessioninfo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sessionwebos`.`sessioninfo` (`sessionid` INT, `versionid` INT, `title` INT, `username` INT, `lastupdatedby` INT, `executed` INT, `debriefed` INT, `closed` INT, `publickey` INT, `updated` INT, `teamname` INT, `sprintname` INT, `charter` INT, `notes` INT, `executed_timestamp` INT, `debriefed_timestamp` INT, `setup_percent` INT, `test_percent` INT, `bug_percent` INT, `opportunity_percent` INT, `duration_time` INT, `project` INT);

-- -----------------------------------------------------
-- Placeholder table for view `sessionwebos`.`sessionview_with_areas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sessionwebos`.`sessionview_with_areas` (`versionid` INT, `sessionid` INT, `title` INT, `charter` INT, `notes` INT, `username` INT, `teamname` INT, `sprintname` INT, `teamsprintname` INT, `depricated` INT, `updated` INT, `publickey` INT, `testenvironment` INT, `software` INT, `lastupdatedby` INT, `areaname` INT, `project` INT);


USE `sessionwebos`;

-- -----------------------------------------------------
-- View `sessionwebos`.`sessioninfo`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`sessioninfo`;
USE `sessionwebos`;
CREATE  OR REPLACE VIEW `sessionwebos`.`sessioninfo` AS SELECT
        m.sessionid,
        m.versionid,
        m.title,
        m.username,
        m.lastupdatedby,
        ms.executed,
        ms.debriefed,
        ms.closed,
        m.publickey,
        m.updated,
        m.teamname,
        m.sprintname,
        m.charter,
        m.notes,
        ms.executed_timestamp,
        ms.debriefed_timestamp,
        sm.setup_percent,
        sm.test_percent,
        sm.bug_percent,
        sm.opportunity_percent,
        sm.duration_time,
        m.project
    from
        mission m,
        mission_status ms,
        mission_sessionmetrics sm
    WHERE
        m.versionid = ms.versionid
        AND
        m.versionid = sm.versionid
;


USE `sessionwebos`;

-- -----------------------------------------------------
-- View `sessionwebos`.`sessionview_with_areas`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`sessionview_with_areas`;
USE `sessionwebos`;
CREATE  OR REPLACE view `sessionwebos`.`sessionview_with_areas` AS SELECT
`mission`.`versionid` AS `versionid`, `mission`.`sessionid` AS `sessionid`, `mission`.`title`
AS `title`, `mission`.`charter` AS `charter`, `mission`.`notes` AS `notes`,
`mission`.`username` AS `username`, `mission`.`teamname` AS `teamname`,
`mission`.`sprintname` AS `sprintname`, `mission`.`teamsprintname` AS
`teamsprintname`, `mission`.`depricated` AS `depricated`, `mission`.`updated` AS
`updated`, `mission`.`publickey` AS `publickey`, `mission`.`testenvironment` AS
`testenvironment`, `mission`.`software` AS `software`, `mission`.`lastupdatedby`
AS `lastupdatedby`, `mission_areas`.`areaname` AS `areaname`, `mission`.`project` AS `project`FROM (`mission`
JOIN `mission_areas`)
WHERE (`mission`.`versionid` =
`mission_areas`.`versionid`) ;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;


UPDATE  `sessionwebos`.`version` SET  `versioninstalled` =  '25' WHERE  `version`.`id` =1;
