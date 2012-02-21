<?php
session_start();
if (!session_is_registered(myusername)) {
    header("HTTP/1.0 403 Forbidden");
    echo "No valid user session is active";
}

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