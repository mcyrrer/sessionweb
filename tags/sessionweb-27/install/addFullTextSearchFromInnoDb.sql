ALTER TABLE  `sessionwebos`.`mission` DROP FOREIGN KEY `fk_mission_members`;
ALTER TABLE  `sessionwebos`.`mission` DROP FOREIGN KEY `fk_mission_sessionid1`;
ALTER TABLE  `sessionwebos`.`mission` DROP FOREIGN KEY `fk_mission_sprintnames`;
ALTER TABLE  `sessionwebos`.`mission` DROP FOREIGN KEY `fk_mission_teamnames`;
ALTER TABLE  `sessionwebos`.`mission` DROP FOREIGN KEY `fk_mission_teamsprintnames1`;
ALTER TABLE  `sessionwebos`.`mission` DROP FOREIGN KEY `fk_mission_testenvironment1`;
ALTER TABLE  `sessionwebos`.`mission` ADD FULLTEXT(notes, charter, title,software);