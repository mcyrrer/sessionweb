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

$sql = "select * from members WHERE username = '$username'";

$result = $dbm->executeQuery($con,$sql);


if (!$result) {
    header("HTTP/1.0 500 Internal Server Error");
    $response['code'] = SQL_ERROR;
    $response['text'] = "SQL_ERROR";
} else {
    header("HTTP/1.0 200 Ok");
    $responseArray = mysqli_fetch_array($result);
    $response['username'] = $responseArray['username'];
    $response['fullname'] = $responseArray['fullname'];
    $response['superuser'] = $responseArray['superuser'];
    $response['admin'] = $responseArray['admin'];
    $response['updated'] = $responseArray['updated'];
}




echo json_encode($response);
ob_end_flush();

?>