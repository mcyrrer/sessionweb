SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`mission_testers` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `versionid` INT(11) NOT NULL ,
  `tester` VARCHAR(100) NOT NULL ,
  INDEX `fk_debriefnotes_mission1` (`versionid` ASC) ,
  INDEX `fk_mission_debriefnotes_copy1_areas1` (`tester` ASC) ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

-- CREATE  TABLE IF NOT EXISTS `sessionwebos`.`settings_new` (
--   `id` INT(11) NOT NULL AUTO_INCREMENT ,
--   `name` INT(11) NOT NULL ,
--   `value` VARCHAR(1000) NOT NULL ,
--   `type` VARCHAR(45) NOT NULL ,
--   `updated` TIMESTAMP NULL DEFAULT NOW() ,
--   INDEX `fk_debriefnotes_mission1` (`name` ASC) ,
--   INDEX `fk_mission_debriefnotes_copy1_areas1` (`value` ASC) ,
--   PRIMARY KEY (`id`) )
-- ENGINE = MyISAM
-- DEFAULT CHARACTER SET = utf8
-- COLLATE = utf8_general_ci;

ALTER TABLE `sessionwebos`.`mission` ENGINE = MyISAM ;
ALTER TABLE `sessionwebos`.`mission` ADD FULLTEXT(notes, charter, title,software);

-- -----------------------------------------------------
-- Placeholder table for view `sessionwebos`.`sessioninfo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sessionwebos`.`sessioninfo` (`sessionid` INT, `versionid` INT, `title` INT, `username` INT, `lastupdatedby` INT, `executed` INT, `debriefed` INT, `closed` INT, `publickey` INT, `updated` INT, `teamname` INT, `sprintname` INT, `charter` INT, `notes` INT, `executed_timestamp` INT, `debriefed_timestamp` INT, `setup_percent` INT, `test_percent` INT, `bug_percent` INT, `opportunity_percent` INT, `duration_time` INT);

-- -----------------------------------------------------
-- Placeholder table for view `sessionwebos`.`sessionview_with_areas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sessionwebos`.`sessionview_with_areas` (`versionid` INT, `sessionid` INT, `title` INT, `charter` INT, `notes` INT, `username` INT, `teamname` INT, `sprintname` INT, `teamsprintname` INT, `depricated` INT, `updated` INT, `publickey` INT, `testenvironment` INT, `software` INT, `lastupdatedby` INT, `areaname` INT);


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
        sm.duration_time
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
CREATE  OR REPLACE VIEW `sessionwebos`.`sessionview_with_areas` AS select `mission`.`versionid` AS `versionid`,`mission`.`sessionid` AS `sessionid`,`mission`.`title` AS `title`,`mission`.`charter` AS `charter`,`mission`.`notes` AS `notes`,`mission`.`username` AS `username`,`mission`.`teamname` AS `teamname`,`mission`.`sprintname` AS `sprintname`,`mission`.`teamsprintname` AS `teamsprintname`,`mission`.`depricated` AS `depricated`,`mission`.`updated` AS `updated`,`mission`.`publickey` AS `publickey`,`mission`.`testenvironment` AS `testenvironment`,`mission`.`software` AS `software`,`mission`.`lastupdatedby` AS `lastupdatedby`,`mission_areas`.`areaname` AS `areaname` from (`mission` join `mission_areas`) where (`mission`.`versionid` = `mission_areas`.`versionid`)
;

UPDATE  `sessionwebos`.`version` SET  `versioninstalled` =  '18' WHERE  `version`.`id` =1;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
