SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

ALTER TABLE `sessionwebos`.`members` ADD COLUMN `adaccount` TINYINT(1) NULL DEFAULT false  AFTER `password` , ADD COLUMN `deleted` TINYINT(1) NULL DEFAULT false  AFTER `adaccount` ;

ALTER TABLE `sessionwebos`.`mission` ADD COLUMN `project` VARCHAR(45) NULL  AFTER `lastupdatedby`
, ADD INDEX `fk_mission_members_idx` (`username` ASC) 
, ADD INDEX `fk_mission_sprintnames_idx` (`sprintname` ASC) 
, ADD INDEX `fk_mission_teamnames_idx` (`teamname` ASC) 
, ADD INDEX `fk_mission_teamsprintnames1_idx` (`teamsprintname` ASC) 
, ADD INDEX `fk_mission_testenvironment1_idx` (`testenvironment` ASC) 
, DROP INDEX `fk_mission_testenvironment1` 
, DROP INDEX `fk_mission_teamsprintnames1` 
, DROP INDEX `fk_mission_teamnames` 
, DROP INDEX `fk_mission_sprintnames` 
, DROP INDEX `fk_mission_members` ;

ALTER TABLE `sessionwebos`.`sprintnames` ADD COLUMN `project` VARCHAR(45) NULL  AFTER `updated` ;

ALTER TABLE `sessionwebos`.`teamnames` ADD COLUMN `project` VARCHAR(45)  NULL  AFTER `updated` ;

ALTER TABLE `sessionwebos`.`sessionid` 
ADD INDEX `fk_sessionid_members1_idx` (`createdby` ASC) 
, DROP INDEX `fk_sessionid_members1` ;

ALTER TABLE `sessionwebos`.`teamsprintnames` ADD COLUMN `project` VARCHAR(45)  NULL  AFTER `updated` ;

ALTER TABLE `sessionwebos`.`mission_debriefnotes` 
ADD INDEX `fk_debriefnotes_users1_idx` (`debriefedby` ASC) 
, ADD INDEX `fk_debriefnotes_mission1_idx` (`versionid` ASC) 
, DROP INDEX `fk_debriefnotes_mission1` 
, DROP INDEX `fk_debriefnotes_users1` ;

ALTER TABLE `sessionwebos`.`settings` ADD COLUMN `project` VARCHAR(45)  NULL  AFTER `custom3_multiselect` ;

ALTER TABLE `sessionwebos`.`areas` ADD COLUMN `project` VARCHAR(45) NULL  AFTER `updated` ;

ALTER TABLE `sessionwebos`.`mission_areas` 
ADD INDEX `fk_mission_debriefnotes_copy1_areas1_idx` (`areaname` ASC) 
, DROP INDEX `fk_mission_debriefnotes_copy1_areas1` ;

ALTER TABLE `sessionwebos`.`mission_sessionsconnections` 
ADD INDEX `fk_mission_sessionsconnections_mission1_idx` (`linked_from_versionid` ASC) 
, DROP INDEX `fk_mission_sessionsconnections_mission1` ;

ALTER TABLE `sessionwebos`.`user_settings` ADD COLUMN `project` VARCHAR(45) NULL AFTER `default_area`
, ADD INDEX `fk_user_settings_members1_idx` (`username` ASC) 
, ADD INDEX `fk_user_settings_teamnames1_idx` (`teamname` ASC) 
, DROP INDEX `fk_user_settings_teamnames1` 
, DROP INDEX `fk_user_settings_members1` ;

ALTER TABLE `sessionwebos`.`testenvironment` ADD COLUMN `project` VARCHAR(45)  NULL  AFTER `password` ;

ALTER TABLE `sessionwebos`.`mission_attachments` 
ADD INDEX `fk_attach_mission1_idx` (`mission_versionid` ASC) 
, DROP INDEX `fk_attach_mission1` ;

ALTER TABLE `sessionwebos`.`user_sessionsnotification` ADD COLUMN `project` VARCHAR(45) NULL  AFTER `acknowledge`
, ADD INDEX `fk_user_sessionsnotification_mission1_idx` (`versionid` ASC) 
, ADD INDEX `fk_user_sessionsnotification_members1_idx` (`username` ASC) 
, DROP INDEX `fk_user_sessionsnotification_members1` 
, DROP INDEX `fk_user_sessionsnotification_mission1` ;

ALTER TABLE `sessionwebos`.`mission_custom` 
ADD INDEX `fk_mission_debriefnotes_copy1_areas1_idx` (`customtablename` ASC) 
, DROP INDEX `fk_mission_debriefnotes_copy1_areas1` ;

ALTER TABLE `sessionwebos`.`custom_items` ADD COLUMN `project` VARCHAR(45) NULL  AFTER `updated` ;

ALTER TABLE `sessionwebos`.`mission_testers` 
ADD INDEX `fk_mission_debriefnotes_copy1_areas1_idx` (`tester` ASC) 
, DROP INDEX `fk_mission_debriefnotes_copy1_areas1` ;

DROP TABLE IF EXISTS `sessionwebos`.`settings_new` ;


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

UPDATE  `sessionwebos`.`version` SET  `versioninstalled` =  '23' WHERE  `version`.`id` =1;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
