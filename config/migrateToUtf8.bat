mysqldump -u root -pnot2easy sessionwebos > backupBeforeUpgradeToSW.1.2.txt
mysql -u root -pnot2easy  sessionwebos < SessionwebDbLayoutDelta_1.0-_1.2.sql
mysqldump --no-create-info -u root -pnot2easy sessionwebos > backupOfSW.1.2.OnlyDataNoTables.txt
mysql -u root -pnot2easy  sessionwebos < SessionwebDbLayout_1.2.sql
mysql -uroot -pnot2easy -e "TRUNCATE TABLE  sessionwebos.`members`"
mysql -uroot -pnot2easy -e "TRUNCATE TABLE  sessionwebos.`settings`;"
mysql -uroot -pnot2easy -e "TRUNCATE TABLE  sessionwebos.`user_settings`;"
mysql -uroot -pnot2easy sessionwebos < backupOfSW.1.2.OnlyDataNoTables.txt
del backupOfSW.1.2.OnlyDataNoTables.txt
del backupBeforeUpgradeToSW.1.2.txt