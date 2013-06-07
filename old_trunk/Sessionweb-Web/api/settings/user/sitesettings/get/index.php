<?php
ob_start();
header("HTTP/1.0 400 Bad Request");
session_start();

require_once('../../../../../include/validatesession.inc');

error_reporting(0);

require_once('../../../../../config/db.php.inc');
require_once ('../../../../../include/db.php');
require_once ('../../../../../include/apistatuscodes.inc');
require_once ('../../../../../include/loggingsetup.php');


$response = array();

$con = getMySqlConnection();
$username = $_SESSION['username'];

$sql = "select * from members WHERE username = '$username'";

$result = mysql_query($sql);


if (!$result) {
    header("HTTP/1.0 500 Internal Server Error");
    $response['code'] = SQL_ERROR;
    $response['text'] = "SQL_ERROR";
}
else
{
    header("HTTP/1.0 200 Ok");
    $responseArray = mysql_fetch_array($result);
    $response['username']=$responseArray['username'];
    $response['fullname']=$responseArray['fullname'];
    $response['superuser']=$responseArray['superuser'];
    $response['admin']=$responseArray['admin'];
    $response['updated']=$responseArray['updated'];
}

mysql_close($con);


echo json_encode($response);
ob_end_flush();

?>