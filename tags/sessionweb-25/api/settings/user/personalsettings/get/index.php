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

$sql = "select * from user_settings WHERE username = '$username'";

$result = mysql_query($sql);


if (!$result) {
    header("HTTP/1.0 500 Internal Server Error");
    $response['code'] = SQL_ERROR;
    $response['text'] = "SQL_ERROR";
}
else
{
    header("HTTP/1.0 200 Ok");
    $response = mysql_fetch_array($result);

}

mysql_close($con);


echo json_encode($response);
ob_end_flush();

?>