<?php
require_once('../../../../classes/autoloader.php');
require_once('../../../../include/apistatuscodes.inc');

$logger = new logging();
$sHelper = new sessionHelper();
$dbm = new dbHelper();

$con = $dbm->connectToLocalDb();

$settings = UserSettings::getUserSettings();
if ($settings['autosave'] == "") {
    echo "0";
} else {
    echo $settings['autosave'];

}
//print_r($settings);
?>