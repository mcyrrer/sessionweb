SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

ALTER TABLE `sessionwebos`.`user_settings` ADD COLUMN `default_team` VARCHAR(100) NULL DEFAULT NULL  AFTER `autosave` , ADD COLUMN `default_sprint` VARCHAR(100) NULL DEFAULT NULL  AFTER `default_team` , ADD COLUMN `default_teamsprint` VARCHAR(100) NULL DEFAULT NULL  AFTER `default_sprint` , ADD COLUMN `default_area` VARCHAR(100) NULL DEFAULT NULL  AFTER `default_teamsprint` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`mission_attachments` (
  `id` INT(11) NULL DEFAULT NULL AUTO_INCREMENT ,
  `mission_versionid` INT(11) NOT NULL ,
  `filename` VARCHAR(100) NOT NULL ,
  `mimetype` VARCHAR(45) NOT NULL ,
  `size` INT(11) NOT NULL ,
  `data` MEDIUMBLOB NOT NULL ,
  INDEX `fk_attach_mission1` (`mission_versionid` ASC) ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) ,
  CONSTRAINT `fk_attach_mission1`
    FOREIGN KEY (`mission_versionid` )
    REFERENCES `sessionwebos`.`mission` (`versionid` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Placeholder table for view `sessionwebos`.`sessioninfo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sessionwebos`.`sessioninfo` (`sessionid` INT, `versionid` INT, `title` INT, `username` INT, `executed` INT, `debriefed` INT, `closed` INT, `publickey` INT, `updated` INT, `teamname` INT, `sprintname` INT, `executed_timestamp` INT, `debriefed_timestamp` INT, `setup_percent` INT, `test_percent` INT, `bug_percent` INT, `opportunity_percent` INT, `duration_time` INT);


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
        ms.executed,
        ms.debriefed,
        ms.closed,
        m.publickey,
        m.updated,
        m.teamname,
        m.sprintname,
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

UPDATE  `sessionwebos`.`version` SET  `versioninstalled` =  '1.3' WHERE  `version`.`id` =1;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
