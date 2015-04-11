<?php
require_once('../../../classes/autoloader.php');
require_once('../../../include/apistatuscodes.inc');

$logger = new logging();
$sHelper = new sessionHelper();
$dbm = new dbHelper();


$response = array();
if ($_SESSION['useradmin'] == 1) {

    $con = $dbm->connectToLocalDb();


    $settings = ApplicationSettings::getSettings();


    if (!$settings) {
        if (mysqli_errno($con) == 1062) {
            header("HTTP/1.0 409 Conflict");
            $response['code'] = ITEM_ALREADY_EXIST;
            $response['text'] = "ITEM_ALREADY_EXIST";

        } else {
            header("HTTP/1.0 500 Internal Server Error");
            $response['code'] = ITEM_NOT_ADDED;
            $response['text'] = "ITEM_NOT_ADDED";

        }
    } else {
        header("HTTP/1.0 200 Ok");
        $response = $settings;
    }


} else {
    header("HTTP/1.0 401 Unauthorized");
    $response['code'] = UNAUTHORIZED;
    $response['text'] = "UNAUTHORIZED";
}
echo json_encode($response);
?>