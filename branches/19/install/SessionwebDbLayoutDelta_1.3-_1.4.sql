
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE  TABLE IF NOT EXISTS `sessionwebos`.`user_sessionsnotification` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `versionid` INT(11) NOT NULL ,
  `username` VARCHAR(45) NOT NULL ,
  `emailnotification` TINYINT(1) NULL DEFAULT NULL ,
  `emailsent` TINYINT(1) NULL DEFAULT false ,
  `acknowledge` TINYINT(1) NULL DEFAULT false ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_user_sessionsnotification_mission1` (`versionid` ASC) ,
  INDEX `fk_user_sessionsnotification_members1` (`username` ASC) ,
  UNIQUE INDEX `versionid_UNIQUE` (`versionid` ASC) ,
  CONSTRAINT `fk_user_sessionsnotification_mission1`
    FOREIGN KEY (`versionid` )
    REFERENCES `sessionwebos`.`mission` (`versionid` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_sessionsnotification_members1`
    FOREIGN KEY (`username` )
    REFERENCES `sessionwebos`.`members` (`username` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;

UPDATE  `sessionwebos`.`version` SET  `versioninstalled` =  '1.4' WHERE  `version`.`id` =1;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
