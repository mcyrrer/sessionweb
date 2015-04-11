<?php

require_once('../../../classes/autoloader.php');

$dbm= new dbHelper();
$con = $dbm->connectToLocalDb();

$us = new UserSettings();

echo json_encode($us->getUserSettings());

?>