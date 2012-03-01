<?php
session_start();
require_once('../../../include/validatesession.inc');
require_once ('../../../include/db.php');
include_once('../../../../config/db.php.inc');
include_once ('../../../../include/commonFunctions.php.inc');

$con=getMySqlConnection();

$settings = getUserSettings();
if($settings['autosave']=="")
{
    echo "0";
}
else
{
    echo $settings['autosave'];

}
//print_r($settings);
?>