ALTER TABLE `sessionwebos`.`settings`
ADD COLUMN `chartertext` VARCHAR(1000) NULL DEFAULT '' AFTER `wisemapping`;

UPDATE  `sessionwebos`.`version` SET  `versioninstalled` =  '28' WHERE  `version`.`id` =1;
