<?php
require_once('../../classes/autoloader.php');
require_once('../../include/apistatuscodes.inc');

$logger = new logging();
$sHelper = new sessionHelper();
$dbm = new dbHelper();
$wm = new WisemappingManager();

echo 'result:' . $wm->createMap(rand(123, 1231231231) . ":asasdf", "DESFADF") . ';';
echo $wm->listMaps();
//$wm->login();


