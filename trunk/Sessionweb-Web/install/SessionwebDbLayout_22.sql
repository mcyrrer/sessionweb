SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

DROP SCHEMA IF EXISTS `sessionwebos` ;
CREATE SCHEMA IF NOT EXISTS `sessionwebos` DEFAULT CHARACTER SET utf8 ;
USE `sessionwebos` ;

-- -----------------------------------------------------
-- Table `sessionwebos`.`members`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`members` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`members` (
  `username` VARCHAR(45) NOT NULL ,
  `fullname` VARCHAR(45) NULL ,
  `active` TINYINT(1) NULL DEFAULT TRUE ,
  `superuser` TINYINT(1) NOT NULL ,
  `admin` TINYINT(1) NOT NULL ,
  `updated` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
  `password` VARCHAR(100) NOT NULL ,
  PRIMARY KEY (`username`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sessionwebos`.`sprintnames`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`sprintnames` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`sprintnames` (
  `sprintname` VARCHAR(100) NOT NULL ,
  `updated` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `projects` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`sprintname`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sessionwebos`.`teamnames`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`teamnames` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`teamnames` (
  `teamname` VARCHAR(100) NOT NULL ,
  `updated` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `projects` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`teamname`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sessionwebos`.`teamsprintnames`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`teamsprintnames` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`teamsprintnames` (
  `teamsprintname` VARCHAR(100) NOT NULL ,
  `updated` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `projects` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`teamsprintname`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sessionwebos`.`sessionid`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`sessionid` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`sessionid` (
  `sessionid` INT NOT NULL AUTO_INCREMENT ,
  `updated` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `createdby` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`sessionid`, `createdby`) ,
  INDEX `fk_sessionid_members1` (`createdby` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sessionwebos`.`testenvironment`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`testenvironment` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`testenvironment` (
  `name` VARCHAR(45) NOT NULL ,
  `url` VARCHAR(500) NULL ,
  `username` VARCHAR(100) NULL ,
  `password` VARCHAR(100) NULL ,
  `projects` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`name`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sessionwebos`.`mission`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`mission` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`mission` (
  `versionid` INT NOT NULL AUTO_INCREMENT ,
  `sessionid` INT NOT NULL ,
  `title` VARCHAR(500) NULL ,
  `charter` MEDIUMTEXT NULL ,
  `notes` MEDIUMTEXT NULL ,
  `username` VARCHAR(45) NOT NULL ,
  `teamname` VARCHAR(100) NULL ,
  `sprintname` VARCHAR(100) NULL ,
  `teamsprintname` VARCHAR(100) NULL ,
  `depricated` TINYINT(1) NULL DEFAULT 0 ,
  `updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
  `publickey` VARCHAR(100) NOT NULL ,
  `testenvironment` VARCHAR(45) NULL ,
  `software` VARCHAR(1024) NULL ,
  `lastupdatedby` VARCHAR(45) NULL ,
  `projects` VARCHAR(45) NOT NULL ,
  INDEX `fk_mission_members` (`username` ASC) ,
  INDEX `fk_mission_sprintnames` (`sprintname` ASC) ,
  INDEX `fk_mission_teamnames` (`teamname` ASC) ,
  INDEX `fk_mission_teamsprintnames1` (`teamsprintname` ASC) ,
  UNIQUE INDEX `sessionid_UNIQUE` (`sessionid` ASC) ,
  UNIQUE INDEX `versionid_UNIQUE` (`versionid` ASC) ,
  PRIMARY KEY (`versionid`) ,
  INDEX `fk_mission_testenvironment1` (`testenvironment` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sessionwebos`.`mission_status`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`mission_status` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`mission_status` (
  `versionid` INT NOT NULL ,
  `executed` TINYINT(1) NOT NULL DEFAULT false ,
  `debriefed` TINYINT(1) NOT NULL DEFAULT false ,
  `masterdibriefed` TINYINT(1) NOT NULL DEFAULT false ,
  `executed_timestamp` TIMESTAMP NULL ,
  `debriefed_timestamp` TIMESTAMP NULL ,
  `closed` TINYINT(1) NOT NULL DEFAULT false ,
  `projects` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`versionid`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sessionwebos`.`mission_sessionmetrics`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`mission_sessionmetrics` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`mission_sessionmetrics` (
  `versionid` INT NOT NULL ,
  `setup_percent` INT NULL ,
  `test_percent` INT NULL ,
  `bug_percent` INT NULL ,
  `opportunity_percent` INT NULL ,
  `duration_time` INT NULL ,
  `mood` INT NULL ,
  `projects` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`versionid`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sessionwebos`.`mission_debriefnotes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`mission_debriefnotes` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`mission_debriefnotes` (
  `versionid` INT NOT NULL ,
  `notes` TEXT NULL ,
  `debriefedby` VARCHAR(45) NOT NULL ,
  `projects` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`versionid`) ,
  INDEX `fk_debriefnotes_users1` (`debriefedby` ASC) ,
  INDEX `fk_debriefnotes_mission1` (`versionid` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sessionwebos`.`settings`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`settings` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`settings` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `normalized_session_time` INT NULL ,
  `team` TINYINT(1) NULL ,
  `sprint` TINYINT(1) NULL ,
  `teamsprint` TINYINT(1) NULL ,
  `area` TINYINT(1) NULL ,
  `testenvironment` TINYINT(1) NULL ,
  `publicview` TINYINT(1) NULL ,
  `analyticsid` VARCHAR(45) NULL COMMENT 'google analytics id' ,
  `url_to_dms` VARCHAR(500) NULL ,
  `url_to_rms` VARCHAR(500) NULL ,
  `wordcloud` TINYINT(1) NULL DEFAULT 1 ,
  `custom1` TINYINT(1) NULL ,
  `custom1_name` VARCHAR(100) NULL ,
  `custom1_multiselect` TINYINT(1) NULL ,
  `custom2` TINYINT(1) NULL ,
  `custom2_name` VARCHAR(100) NULL ,
  `custom2_multiselect` TINYINT(1) NULL ,
  `custom3` TINYINT(1) NULL ,
  `custom3_name` VARCHAR(100) NULL ,
  `custom3_multiselect` TINYINT(1) NULL ,
  `projects` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sessionwebos`.`areas`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`areas` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`areas` (
  `areaname` VARCHAR(100) NOT NULL ,
  `updated` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `projects` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`areaname`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sessionwebos`.`mission_areas`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`mission_areas` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`mission_areas` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `versionid` INT NOT NULL ,
  `areaname` VARCHAR(100) NOT NULL ,
  `projects` VARCHAR(45) NOT NULL ,
  INDEX `fk_debriefnotes_mission1` (`versionid` ASC) ,
  INDEX `fk_mission_debriefnotes_copy1_areas1` (`areaname` ASC) ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sessionwebos`.`mission_bugs`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`mission_bugs` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`mission_bugs` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `versionid` INT NOT NULL ,
  `bugid` VARCHAR(45) NOT NULL ,
  `projects` VARCHAR(45) NOT NULL ,
  INDEX `fk_debriefnotes_mission1` (`versionid` ASC) ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sessionwebos`.`mission_requirements`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`mission_requirements` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`mission_requirements` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `versionid` INT NOT NULL ,
  `requirementsid` VARCHAR(45) NOT NULL ,
  `projects` VARCHAR(45) NOT NULL ,
  INDEX `fk_missionreq_mission1` (`versionid` ASC) ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sessionwebos`.`mission_sessionsconnections`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`mission_sessionsconnections` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`mission_sessionsconnections` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `linked_from_versionid` INT NOT NULL ,
  `linked_to_versionid` INT NOT NULL ,
  `projects` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_mission_sessionsconnections_mission1` (`linked_from_versionid` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sessionwebos`.`user_settings`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`user_settings` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`user_settings` (
  `username` VARCHAR(45) NOT NULL ,
  `teamname` VARCHAR(100) NULL ,
  `list_view` VARCHAR(45) NULL ,
  `autosave` TINYINT NULL DEFAULT 1 ,
  `default_team` VARCHAR(100) NULL ,
  `default_sprint` VARCHAR(100) NULL ,
  `default_teamsprint` VARCHAR(100) NULL ,
  `default_area` VARCHAR(100) NULL ,
  `projects` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`username`) ,
  INDEX `fk_user_settings_members1` (`username` ASC) ,
  INDEX `fk_user_settings_teamnames1` (`teamname` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sessionwebos`.`version`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`version` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`version` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `versioninstalled` FLOAT NOT NULL DEFAULT 1.1 ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sessionwebos`.`mission_attachments`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`mission_attachments` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`mission_attachments` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `mission_versionid` INT NOT NULL ,
  `filename` VARCHAR(100) NOT NULL ,
  `mimetype` VARCHAR(45) NOT NULL ,
  `size` INT NOT NULL ,
  `data` MEDIUMBLOB NOT NULL ,
  `projects` VARCHAR(45) NOT NULL ,
  INDEX `fk_attach_mission1` (`mission_versionid` ASC) ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `sessionwebos`.`user_sessionsnotification`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`user_sessionsnotification` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`user_sessionsnotification` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `versionid` INT NOT NULL ,
  `username` VARCHAR(45) NOT NULL ,
  `emailnotification` TINYINT(1) NULL ,
  `emailsent` TINYINT(1) NULL DEFAULT false ,
  `acknowledge` TINYINT(1) NULL DEFAULT false ,
  `projects` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_user_sessionsnotification_mission1` (`versionid` ASC) ,
  INDEX `fk_user_sessionsnotification_members1` (`username` ASC) ,
  UNIQUE INDEX `versionid_UNIQUE` (`versionid` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `sessionwebos`.`softwareuseautofetched`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`softwareuseautofetched` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`softwareuseautofetched` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `versionid` INT NOT NULL ,
  `versions` MEDIUMTEXT NOT NULL ,
  `missionstatus` VARCHAR(100) NULL ,
  `updated` TIMESTAMP NULL DEFAULT NOW() ,
  `environment` VARCHAR(100) NULL ,
  `projects` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sessionwebos`.`mission_custom`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`mission_custom` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`mission_custom` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `versionid` INT NOT NULL ,
  `customtablename` VARCHAR(100) NOT NULL ,
  `itemname` VARCHAR(100) NOT NULL ,
  `projects` VARCHAR(45) NOT NULL ,
  INDEX `fk_debriefnotes_mission1` (`versionid` ASC) ,
  INDEX `fk_mission_debriefnotes_copy1_areas1` (`customtablename` ASC) ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sessionwebos`.`custom_items`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`custom_items` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`custom_items` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `tablename` VARCHAR(100) NOT NULL ,
  `name` VARCHAR(100) NOT NULL ,
  `updated` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `projects` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sessionwebos`.`mission_testers`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`mission_testers` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`mission_testers` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `versionid` INT NOT NULL ,
  `tester` VARCHAR(100) NOT NULL ,
  `projects` VARCHAR(45) NOT NULL ,
  INDEX `fk_debriefnotes_mission1` (`versionid` ASC) ,
  INDEX `fk_mission_debriefnotes_copy1_areas1` (`tester` ASC) ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Placeholder table for view `sessionwebos`.`sessioninfo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sessionwebos`.`sessioninfo` (`sessionid` INT, `versionid` INT, `title` INT, `username` INT, `lastupdatedby` INT, `executed` INT, `debriefed` INT, `closed` INT, `publickey` INT, `updated` INT, `teamname` INT, `sprintname` INT, `charter` INT, `notes` INT, `executed_timestamp` INT, `debriefed_timestamp` INT, `setup_percent` INT, `test_percent` INT, `bug_percent` INT, `opportunity_percent` INT, `duration_time` INT, `projects` INT);

-- -----------------------------------------------------
-- Placeholder table for view `sessionwebos`.`sessionview_with_areas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sessionwebos`.`sessionview_with_areas` (`versionid` INT, `sessionid` INT, `title` INT, `charter` INT, `notes` INT, `username` INT, `teamname` INT, `sprintname` INT, `teamsprintname` INT, `depricated` INT, `updated` INT, `publickey` INT, `testenvironment` INT, `software` INT, `lastupdatedby` INT, `areaname` INT, `project` INT);

-- -----------------------------------------------------
-- View `sessionwebos`.`sessioninfo`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `sessionwebos`.`sessioninfo` ;
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
        m.projects
    from
        mission m,
        mission_status ms,
        mission_sessionmetrics sm
    WHERE
        m.versionid = ms.versionid
        AND
        m.versionid = sm.versionid
;

-- -----------------------------------------------------
-- View `sessionwebos`.`sessionview_with_areas`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `sessionwebos`.`sessionview_with_areas` ;
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
AS `lastupdatedby`, `mission_areas`.`areaname` AS `areaname`, `mission`.`projects` AS `project`FROM (`mission`
JOIN `mission_areas`) 
WHERE (`mission`.`versionid` =
`mission_areas`.`versionid`) ;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `sessionwebos`.`members`
-- -----------------------------------------------------
START TRANSACTION;
USE `sessionwebos`;
INSERT INTO `sessionwebos`.`members` (`username`, `fullname`, `active`, `superuser`, `admin`, `updated`, `password`) VALUES ('admin', 'Administrator', 1, 1, 1, NULL, '21232f297a57a5a743894a0e4a801fc3');

COMMIT;

-- -----------------------------------------------------
-- Data for table `sessionwebos`.`settings`
-- -----------------------------------------------------
START TRANSACTION;
USE `sessionwebos`;
INSERT INTO `sessionwebos`.`settings` (`id`, `normalized_session_time`, `team`, `sprint`, `teamsprint`, `area`, `testenvironment`, `publicview`, `analyticsid`, `url_to_dms`, `url_to_rms`, `wordcloud`, `custom1`, `custom1_name`, `custom1_multiselect`, `custom2`, `custom2_name`, `custom2_multiselect`, `custom3`, `custom3_name`, `custom3_multiselect`, `projects`) VALUES (NULL, 90, 1, 1, 1, 1, 1, 1, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0');

COMMIT;

-- -----------------------------------------------------
-- Data for table `sessionwebos`.`user_settings`
-- -----------------------------------------------------
START TRANSACTION;
USE `sessionwebos`;
INSERT INTO `sessionwebos`.`user_settings` (`username`, `teamname`, `list_view`, `autosave`, `default_team`, `default_sprint`, `default_teamsprint`, `default_area`, `projects`) VALUES ('admin', NULL, 'all', NULL, NULL, NULL, NULL, NULL, '0');

COMMIT;

-- -----------------------------------------------------
-- Data for table `sessionwebos`.`version`
-- -----------------------------------------------------
START TRANSACTION;
USE `sessionwebos`;
INSERT INTO `sessionwebos`.`version` (`id`, `versioninstalled`) VALUES (NULL, 19);

COMMIT;
