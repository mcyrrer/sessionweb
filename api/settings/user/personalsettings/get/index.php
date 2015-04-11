<?php
ob_start();
header("HTTP/1.0 400 Bad Request");

require_once('../../../../../classes/autoloader.php');
require_once('../../../../../include/apistatuscodes.inc');

$logger = new logging();
$sHelper = new sessionHelper();
$dbm = new dbHelper();



$response = array();

$con = $dbm->connectToLocalDb();
$username = $_SESSION['username'];

$sql = "select * from user_settings WHERE username = '$username'";

$result = $dbm->executeQuery($con,$sql);


if (!$result) {
    header("HTTP/1.0 500 Internal Server Error");
    $response['code'] = SQL_ERROR;
    $response['text'] = "SQL_ERROR";
} else {
    header("HTTP/1.0 200 Ok");
    $response = mysqli_fetch_array($result);

}




echo json_encode($response);
ob_end_flush();

?>