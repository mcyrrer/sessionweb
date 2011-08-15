SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

ALTER TABLE `sessionwebos`.`mission` CHANGE COLUMN `software` `software` VARCHAR(1024) NULL DEFAULT NULL  ;

ALTER TABLE `sessionwebos`.`settings` ADD COLUMN `wordcloud` TINYINT(1) NULL DEFAULT 1  AFTER `url_to_rms` ;

ALTER TABLE `sessionwebos`.`user_settings` ADD COLUMN `autosave` TINYINT(4) NULL DEFAULT 1  AFTER `list_view` ;

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`version` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `versioninstalled` FLOAT NOT NULL DEFAULT 1.1 ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_swedish_ci;


-- -----------------------------------------------------
-- Placeholder table for view `sessionwebos`.`sessioninfo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sessionwebos`.`sessioninfo` (`sessionid` INT, `versionid` INT, `title` INT, `username` INT, `executed` INT, `debriefed` INT, `publickey` INT, `updated` INT, `teamname` INT, `sprintname` INT, `executed_timestamp` INT, `debriefed_timestamp` INT, `setup_percent` INT, `test_percent` INT, `bug_percent` INT, `opportunity_percent` INT, `duration_time` INT);


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

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
