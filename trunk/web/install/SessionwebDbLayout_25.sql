SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

DROP SCHEMA IF EXISTS `sessionwebos` ;
CREATE SCHEMA IF NOT EXISTS `sessionwebos` DEFAULT CHARACTER SET latin1 ;
USE `sessionwebos` ;

-- -----------------------------------------------------
-- Table `sessionwebos`.`areas`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`areas` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`areas` (
  `areaname` VARCHAR(100) NOT NULL ,
  `updated` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `project` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`areaname`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sessionwebos`.`custom_items`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`custom_items` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`custom_items` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `tablename` VARCHAR(100) NOT NULL ,
  `name` VARCHAR(100) NOT NULL ,
  `updated` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `project` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
AUTO_INCREMENT = 29
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sessionwebos`.`members`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`members` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`members` (
  `username` VARCHAR(45) NOT NULL ,
  `fullname` VARCHAR(45) NULL DEFAULT NULL ,
  `active` TINYINT(1) NULL DEFAULT '1' ,
  `superuser` TINYINT(1) NOT NULL ,
  `admin` TINYINT(1) NOT NULL ,
  `updated` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP ,
  `password` VARCHAR(100) NOT NULL ,
  `adaccount` TINYINT(1) NULL DEFAULT '0' ,
  `deleted` TINYINT(1) NULL DEFAULT '0' ,
  PRIMARY KEY (`username`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sessionwebos`.`mission`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`mission` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`mission` (
  `versionid` INT(11) NOT NULL AUTO_INCREMENT ,
  `sessionid` INT(11) NOT NULL ,
  `title` VARCHAR(500) NULL DEFAULT NULL ,
  `charter` MEDIUMTEXT NULL DEFAULT NULL ,
  `notes` MEDIUMTEXT NULL DEFAULT NULL ,
  `username` VARCHAR(45) NOT NULL ,
  `teamname` VARCHAR(100) NULL DEFAULT NULL ,
  `sprintname` VARCHAR(100) NULL DEFAULT NULL ,
  `teamsprintname` VARCHAR(100) NULL DEFAULT NULL ,
  `depricated` TINYINT(1) NULL DEFAULT '0' ,
  `updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP ,
  `publickey` VARCHAR(100) NOT NULL ,
  `testenvironment` VARCHAR(45) NULL DEFAULT NULL ,
  `software` VARCHAR(1024) NULL DEFAULT NULL ,
  `lastupdatedby` VARCHAR(45) NULL DEFAULT NULL ,
  `project` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`versionid`) ,
  UNIQUE INDEX `sessionid_UNIQUE` (`sessionid` ASC) ,
  UNIQUE INDEX `versionid_UNIQUE` (`versionid` ASC) ,
  INDEX `fk_mission_members_idx` (`username` ASC) ,
  INDEX `fk_mission_sprintnames_idx` (`sprintname` ASC) ,
  INDEX `fk_mission_teamnames_idx` (`teamname` ASC) ,
  INDEX `fk_mission_teamsprintnames1_idx` (`teamsprintname` ASC) ,
  INDEX `fk_mission_testenvironment1_idx` (`testenvironment` ASC) ,
  FULLTEXT INDEX `notes` (`notes` ASC, `charter` ASC, `title` ASC, `software` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 6143
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sessionwebos`.`mission_areas`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`mission_areas` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`mission_areas` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `versionid` INT(11) NOT NULL ,
  `areaname` VARCHAR(100) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_debriefnotes_mission1` (`versionid` ASC) ,
  INDEX `fk_mission_debriefnotes_copy1_areas1_idx` (`areaname` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 230562
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sessionwebos`.`mission_attachments`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`mission_attachments` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`mission_attachments` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `mission_versionid` INT(11) NOT NULL ,
  `filename` VARCHAR(100) NOT NULL ,
  `mimetype` VARCHAR(45) NOT NULL ,
  `size` INT(11) NOT NULL ,
  `data` MEDIUMBLOB NOT NULL ,
  `thumbnail` MEDIUMBLOB NULL DEFAULT NULL ,
  `mission_attachmentscol` VARCHAR(45) NULL DEFAULT NULL ,
  `mission_attachmentscol1` VARCHAR(45) NULL DEFAULT NULL ,
  `mission_attachmentscol2` VARCHAR(45) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) ,
  INDEX `fk_attach_mission1_idx` (`mission_versionid` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 374
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sessionwebos`.`mission_bugs`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`mission_bugs` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`mission_bugs` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `versionid` INT(11) NOT NULL ,
  `bugid` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_debriefnotes_mission1` (`versionid` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 61128
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sessionwebos`.`mission_custom`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`mission_custom` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`mission_custom` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `versionid` INT(11) NOT NULL ,
  `customtablename` VARCHAR(100) NOT NULL ,
  `itemname` VARCHAR(100) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_debriefnotes_mission1` (`versionid` ASC) ,
  INDEX `fk_mission_debriefnotes_copy1_areas1_idx` (`customtablename` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 102054
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sessionwebos`.`mission_debriefnotes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`mission_debriefnotes` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`mission_debriefnotes` (
  `versionid` INT(11) NOT NULL ,
  `notes` TEXT NULL DEFAULT NULL ,
  `debriefedby` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`versionid`) ,
  INDEX `fk_debriefnotes_users1_idx` (`debriefedby` ASC) ,
  INDEX `fk_debriefnotes_mission1_idx` (`versionid` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sessionwebos`.`mission_mindmaps`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`mission_mindmaps` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`mission_mindmaps` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `versionid` INT(11) NOT NULL ,
  `map_id` INT(11) NOT NULL ,
  `map_title` VARCHAR(300) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `versionid` (`versionid` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 8
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sessionwebos`.`mission_requirements`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`mission_requirements` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`mission_requirements` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `versionid` INT(11) NOT NULL ,
  `requirementsid` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_missionreq_mission1` (`versionid` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 498727
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sessionwebos`.`mission_sessionmetrics`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`mission_sessionmetrics` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`mission_sessionmetrics` (
  `versionid` INT(11) NOT NULL ,
  `setup_percent` INT(11) NULL DEFAULT NULL ,
  `test_percent` INT(11) NULL DEFAULT NULL ,
  `bug_percent` INT(11) NULL DEFAULT NULL ,
  `opportunity_percent` INT(11) NULL DEFAULT NULL ,
  `duration_time` INT(11) NULL DEFAULT NULL ,
  `mood` INT(11) NULL DEFAULT NULL ,
  PRIMARY KEY (`versionid`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sessionwebos`.`mission_sessionsconnections`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`mission_sessionsconnections` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`mission_sessionsconnections` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `linked_from_versionid` INT(11) NOT NULL ,
  `linked_to_versionid` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_mission_sessionsconnections_mission1_idx` (`linked_from_versionid` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 42411
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sessionwebos`.`mission_status`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`mission_status` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`mission_status` (
  `versionid` INT(11) NOT NULL ,
  `executed` TINYINT(1) NOT NULL DEFAULT '0' ,
  `debriefed` TINYINT(1) NOT NULL DEFAULT '0' ,
  `masterdibriefed` TINYINT(1) NOT NULL DEFAULT '0' ,
  `executed_timestamp` TIMESTAMP NULL DEFAULT NULL ,
  `debriefed_timestamp` TIMESTAMP NULL DEFAULT NULL ,
  `closed` TINYINT(1) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`versionid`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sessionwebos`.`mission_testers`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`mission_testers` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`mission_testers` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `versionid` INT(11) NOT NULL ,
  `tester` VARCHAR(100) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_debriefnotes_mission1` (`versionid` ASC) ,
  INDEX `fk_mission_debriefnotes_copy1_areas1_idx` (`tester` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 7733
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sessionwebos`.`sessionid`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`sessionid` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`sessionid` (
  `sessionid` INT(11) NOT NULL AUTO_INCREMENT ,
  `updated` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `createdby` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`sessionid`, `createdby`) ,
  INDEX `fk_sessionid_members1_idx` (`createdby` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 6163
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sessionwebos`.`settings`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`settings` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`settings` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `normalized_session_time` INT(11) NULL DEFAULT NULL ,
  `team` TINYINT(1) NULL DEFAULT NULL ,
  `sprint` TINYINT(1) NULL DEFAULT NULL ,
  `teamsprint` TINYINT(1) NULL DEFAULT NULL ,
  `area` TINYINT(1) NULL DEFAULT NULL ,
  `testenvironment` TINYINT(1) NULL DEFAULT NULL ,
  `publicview` TINYINT(1) NULL DEFAULT NULL ,
  `analyticsid` VARCHAR(45) NULL DEFAULT NULL COMMENT 'google analytics id' ,
  `url_to_dms` VARCHAR(500) NULL DEFAULT NULL ,
  `url_to_rms` VARCHAR(500) NULL DEFAULT NULL ,
  `wordcloud` TINYINT(1) NULL DEFAULT '1' ,
  `custom1` TINYINT(1) NULL DEFAULT NULL ,
  `custom1_name` VARCHAR(100) NULL DEFAULT NULL ,
  `custom1_multiselect` TINYINT(1) NULL DEFAULT NULL ,
  `custom2` TINYINT(1) NULL DEFAULT NULL ,
  `custom2_name` VARCHAR(100) NULL DEFAULT NULL ,
  `custom2_multiselect` TINYINT(1) NULL DEFAULT NULL ,
  `custom3` TINYINT(1) NULL DEFAULT NULL ,
  `custom3_name` VARCHAR(100) NULL DEFAULT NULL ,
  `custom3_multiselect` TINYINT(1) NULL DEFAULT NULL ,
  `project` VARCHAR(45) NOT NULL ,
  `wisemapping_url` VARCHAR(45) NULL DEFAULT NULL ,
  `wisemapping_user` VARCHAR(45) NULL DEFAULT NULL ,
  `wisemapping_password` VARCHAR(45) NULL DEFAULT NULL ,
  `wisemapping` TINYINT(1) NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sessionwebos`.`softwareuseautofetched`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`softwareuseautofetched` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`softwareuseautofetched` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `versionid` INT(11) NOT NULL ,
  `versions` MEDIUMTEXT NOT NULL ,
  `missionstatus` VARCHAR(100) NULL DEFAULT NULL ,
  `updated` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `environment` VARCHAR(100) NULL DEFAULT NULL ,
  `softwareuseautofetchedcol` VARCHAR(45) NULL DEFAULT NULL ,
  `softwareuseautofetchedcol1` VARCHAR(45) NULL DEFAULT NULL ,
  `softwareuseautofetchedcol2` VARCHAR(45) NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `versionid` (`versionid` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 2119
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sessionwebos`.`sprintnames`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`sprintnames` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`sprintnames` (
  `sprintname` VARCHAR(100) NOT NULL ,
  `updated` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `project` VARCHAR(45) NOT NULL ,
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
  `project` VARCHAR(45) NOT NULL ,
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
  `project` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`teamsprintname`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sessionwebos`.`testenvironment`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`testenvironment` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`testenvironment` (
  `name` VARCHAR(45) NOT NULL ,
  `url` VARCHAR(500) NULL DEFAULT NULL ,
  `username` VARCHAR(100) NULL DEFAULT NULL ,
  `password` VARCHAR(100) NULL DEFAULT NULL ,
  `project` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`name`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sessionwebos`.`user_sessionsnotification`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`user_sessionsnotification` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`user_sessionsnotification` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `versionid` INT(11) NOT NULL ,
  `username` VARCHAR(45) NOT NULL ,
  `emailnotification` TINYINT(1) NULL DEFAULT NULL ,
  `emailsent` TINYINT(1) NULL DEFAULT '0' ,
  `acknowledge` TINYINT(1) NULL DEFAULT '0' ,
  `project` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `versionid_UNIQUE` (`versionid` ASC) ,
  INDEX `fk_user_sessionsnotification_mission1_idx` (`versionid` ASC) ,
  INDEX `fk_user_sessionsnotification_members1_idx` (`username` ASC) )
ENGINE = MyISAM
AUTO_INCREMENT = 7
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sessionwebos`.`user_settings`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`user_settings` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`user_settings` (
  `username` VARCHAR(45) NOT NULL ,
  `teamname` VARCHAR(100) NULL DEFAULT NULL ,
  `list_view` VARCHAR(45) NULL DEFAULT NULL ,
  `autosave` TINYINT(4) NULL DEFAULT '1' ,
  `default_team` VARCHAR(100) NULL DEFAULT NULL ,
  `default_sprint` VARCHAR(100) NULL DEFAULT NULL ,
  `default_teamsprint` VARCHAR(100) NULL DEFAULT NULL ,
  `default_area` VARCHAR(100) NULL DEFAULT NULL ,
  `project` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`username`) ,
  INDEX `fk_user_settings_members1_idx` (`username` ASC) ,
  INDEX `fk_user_settings_teamnames1_idx` (`teamname` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sessionwebos`.`version`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sessionwebos`.`version` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`version` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `versioninstalled` FLOAT NOT NULL DEFAULT '1.1' ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Placeholder table for view `sessionwebos`.`sessioninfo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sessionwebos`.`sessioninfo` (`sessionid` INT, `versionid` INT, `title` INT, `username` INT, `lastupdatedby` INT, `executed` INT, `debriefed` INT, `closed` INT, `publickey` INT, `updated` INT, `teamname` INT, `sprintname` INT, `charter` INT, `notes` INT, `executed_timestamp` INT, `debriefed_timestamp` INT, `setup_percent` INT, `test_percent` INT, `bug_percent` INT, `opportunity_percent` INT, `duration_time` INT, `project` INT);

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
CREATE  OR REPLACE ALGORITHM=UNDEFINED DEFINER=`sessionweb`@`localhost` SQL SECURITY DEFINER VIEW `sessionwebos`.`sessioninfo` AS select `m`.`sessionid` AS `sessionid`,`m`.`versionid` AS `versionid`,`m`.`title` AS `title`,`m`.`username` AS `username`,`m`.`lastupdatedby` AS `lastupdatedby`,`ms`.`executed` AS `executed`,`ms`.`debriefed` AS `debriefed`,`ms`.`closed` AS `closed`,`m`.`publickey` AS `publickey`,`m`.`updated` AS `updated`,`m`.`teamname` AS `teamname`,`m`.`sprintname` AS `sprintname`,`m`.`charter` AS `charter`,`m`.`notes` AS `notes`,`ms`.`executed_timestamp` AS `executed_timestamp`,`ms`.`debriefed_timestamp` AS `debriefed_timestamp`,`sm`.`setup_percent` AS `setup_percent`,`sm`.`test_percent` AS `test_percent`,`sm`.`bug_percent` AS `bug_percent`,`sm`.`opportunity_percent` AS `opportunity_percent`,`sm`.`duration_time` AS `duration_time`,`m`.`project` AS `project` from ((`sessionwebos`.`mission` `m` join `sessionwebos`.`mission_status` `ms`) join `sessionwebos`.`mission_sessionmetrics` `sm`) where ((`m`.`versionid` = `ms`.`versionid`) and (`m`.`versionid` = `sm`.`versionid`));

-- -----------------------------------------------------
-- View `sessionwebos`.`sessionview_with_areas`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `sessionwebos`.`sessionview_with_areas` ;
DROP TABLE IF EXISTS `sessionwebos`.`sessionview_with_areas`;
USE `sessionwebos`;
CREATE  OR REPLACE ALGORITHM=UNDEFINED DEFINER=`sessionweb`@`localhost` SQL SECURITY DEFINER VIEW `sessionwebos`.`sessionview_with_areas` AS select `sessionwebos`.`mission`.`versionid` AS `versionid`,`sessionwebos`.`mission`.`sessionid` AS `sessionid`,`sessionwebos`.`mission`.`title` AS `title`,`sessionwebos`.`mission`.`charter` AS `charter`,`sessionwebos`.`mission`.`notes` AS `notes`,`sessionwebos`.`mission`.`username` AS `username`,`sessionwebos`.`mission`.`teamname` AS `teamname`,`sessionwebos`.`mission`.`sprintname` AS `sprintname`,`sessionwebos`.`mission`.`teamsprintname` AS `teamsprintname`,`sessionwebos`.`mission`.`depricated` AS `depricated`,`sessionwebos`.`mission`.`updated` AS `updated`,`sessionwebos`.`mission`.`publickey` AS `publickey`,`sessionwebos`.`mission`.`testenvironment` AS `testenvironment`,`sessionwebos`.`mission`.`software` AS `software`,`sessionwebos`.`mission`.`lastupdatedby` AS `lastupdatedby`,`sessionwebos`.`mission_areas`.`areaname` AS `areaname`,`sessionwebos`.`mission`.`project` AS `project` from (`sessionwebos`.`mission` join `sessionwebos`.`mission_areas`) where (`sessionwebos`.`mission`.`versionid` = `sessionwebos`.`mission_areas`.`versionid`);


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `sessionwebos`.`members`
-- -----------------------------------------------------
START TRANSACTION;
USE `sessionwebos`;
INSERT INTO `sessionwebos`.`members` (`username`, `fullname`, `active`, `superuser`, `admin`, `updated`, `password`, `adaccount`, `deleted`) VALUES ('admin', 'Administrator', true, true, true, '2012-02-21 20:05:04', '60eaf2ab4fbb5654c481f52a5106171e', false, false);

COMMIT;

-- -----------------------------------------------------
-- Data for table `sessionwebos`.`settings`
-- -----------------------------------------------------
START TRANSACTION;
USE `sessionwebos`;
INSERT INTO `sessionwebos`.`settings` (`id`, `normalized_session_time`, `team`, `sprint`, `teamsprint`, `area`, `testenvironment`, `publicview`, `analyticsid`, `url_to_dms`, `url_to_rms`, `wordcloud`, `custom1`, `custom1_name`, `custom1_multiselect`, `custom2`, `custom2_name`, `custom2_multiselect`, `custom3`, `custom3_name`, `custom3_multiselect`, `project`, `wisemapping_url`, `wisemapping_user`, `wisemapping_password`, `wisemapping`) VALUES (1, 90, true, true, false, true, true, false, NULL, NULL, NULL, false, false, NULL, NULL, false, NULL, NULL, false, NULL, NULL, '0', NULL, NULL, NULL, false);

COMMIT;

-- -----------------------------------------------------
-- Data for table `sessionwebos`.`user_settings`
-- -----------------------------------------------------
START TRANSACTION;
USE `sessionwebos`;
INSERT INTO `sessionwebos`.`user_settings` (`username`, `teamname`, `list_view`, `autosave`, `default_team`, `default_sprint`, `default_teamsprint`, `default_area`, `project`) VALUES ('admin', '(null)', 'all', 1, '(null)', '(null)', '(null)', '(null)', '0');

COMMIT;

-- -----------------------------------------------------
-- Data for table `sessionwebos`.`version`
-- -----------------------------------------------------
START TRANSACTION;
USE `sessionwebos`;
INSERT INTO `sessionwebos`.`version` (`id`, `versioninstalled`) VALUES (1, 25);

COMMIT;
