SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

ALTER SCHEMA `sessionwebos`  DEFAULT CHARACTER SET utf8  DEFAULT COLLATE utf8_general_ci ;

USE `sessionwebos`;

ALTER TABLE `sessionwebos`.`members` COLLATE = utf8_general_ci ;

ALTER TABLE `sessionwebos`.`mission` COLLATE = utf8_general_ci ;

ALTER TABLE `sessionwebos`.`mission_status` COLLATE = utf8_general_ci ;

ALTER TABLE `sessionwebos`.`sprintnames` COLLATE = utf8_general_ci ;

ALTER TABLE `sessionwebos`.`teamnames` COLLATE = utf8_general_ci ;

ALTER TABLE `sessionwebos`.`sessionid` COLLATE = utf8_general_ci ;

ALTER TABLE `sessionwebos`.`teamsprintnames` COLLATE = utf8_general_ci ;

ALTER TABLE `sessionwebos`.`mission_sessionmetrics` COLLATE = utf8_general_ci ;

ALTER TABLE `sessionwebos`.`mission_debriefnotes` COLLATE = utf8_general_ci ;

ALTER TABLE `sessionwebos`.`settings` COLLATE = utf8_general_ci , ADD COLUMN `custom1` TINYINT(1) NULL DEFAULT NULL  AFTER `wordcloud` , ADD COLUMN `custom1_name` VARCHAR(100) NULL DEFAULT NULL  AFTER `custom1` , ADD COLUMN `custom1_multiselect` TINYINT(1) NULL DEFAULT NULL  AFTER `custom1_name` , ADD COLUMN `custom2` TINYINT(1) NULL DEFAULT NULL  AFTER `custom1_multiselect` , ADD COLUMN `custom2_name` VARCHAR(100) NULL DEFAULT NULL  AFTER `custom2` , ADD COLUMN `custom2_multiselect` TINYINT(1) NULL DEFAULT NULL  AFTER `custom2_name` , ADD COLUMN `custom3` TINYINT(1) NULL DEFAULT NULL  AFTER `custom2_multiselect` , ADD COLUMN `custom3_name` VARCHAR(100) NULL DEFAULT NULL  AFTER `custom3` , ADD COLUMN `custom3_multiselect` TINYINT(1) NULL DEFAULT NULL  AFTER `custom3_name` ;

ALTER TABLE `sessionwebos`.`areas` COLLATE = utf8_general_ci ;

ALTER TABLE `sessionwebos`.`mission_areas` COLLATE = utf8_general_ci ;

ALTER TABLE `sessionwebos`.`mission_bugs` COLLATE = utf8_general_ci ;

ALTER TABLE `sessionwebos`.`mission_requirements` COLLATE = utf8_general_ci ;

ALTER TABLE `sessionwebos`.`mission_sessionsconnections` COLLATE = utf8_general_ci ;

ALTER TABLE `sessionwebos`.`user_settings` COLLATE = utf8_general_ci ;

ALTER TABLE `sessionwebos`.`testenvironment` COLLATE = utf8_general_ci ;

ALTER TABLE `sessionwebos`.`version` COLLATE = utf8_general_ci ;

ALTER TABLE `sessionwebos`.`mission_attachments` COLLATE = utf8_general_ci ;

ALTER TABLE `sessionwebos`.`user_sessionsnotification` COLLATE = utf8_general_ci ;

ALTER TABLE `sessionwebos`.`softwareuseautofetched` CHARACTER SET = utf8 , COLLATE = utf8_general_ci , ENGINE = InnoDB , CHANGE COLUMN `versions` `versions` MEDIUMTEXT NOT NULL  ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`mission_custom` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `versionid` INT(11) NOT NULL ,
  `customtablename` VARCHAR(100) NOT NULL ,
  `itemname` VARCHAR(100) NOT NULL ,
  INDEX `fk_debriefnotes_mission1` (`versionid` ASC) ,
  INDEX `fk_mission_debriefnotes_copy1_areas1` (`customtablename` ASC) ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`custom_items` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `tablename` VARCHAR(100) NOT NULL ,
  `name` VARCHAR(100) NOT NULL ,
  `updated` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  PRIMARY KEY (`id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;



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
UPDATE  `sessionwebos`.`version` SET  `versioninstalled` =  '1.7' WHERE  `version`.`id` =1;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
