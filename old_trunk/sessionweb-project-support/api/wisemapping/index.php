<?php
session_start();

require_once('../../include/validatesession.inc');
require_once('../../classes/Wisemapping.php');

$wm = new WisemappingManager();

echo 'result:'.$wm->createMap(rand(123,1231231231).":asasdf","DESFADF").';';
echo $wm->listMaps();
//$wm->login();


