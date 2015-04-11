ALTER TABLE `sessionwebos`.`settings`
ADD COLUMN `chartertext` VARCHAR(1000) NULL DEFAULT '<h2 style=\"text-align: left;\"><strong>Background:</strong></h2>  <p style=\"text-align:left\">&nbsp;</p>  <h2 style=\"text-align: left;\"><strong>Mission</strong>:</h2>  <p style=\"text-align:left\">&nbsp;</p>  <h2 style=\"text-align: left;\"><strong>Strategy/Heuristic</strong>s</h2>  <p style=\"text-align:left\">&nbsp;</p> ' AFTER `wisemapping`;

UPDATE  `sessionwebos`.`version` SET  `versioninstalled` =  '28' WHERE  `version`.`id` =1;
