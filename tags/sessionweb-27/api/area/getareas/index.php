<?php
session_start();

require_once('../../../include/validatesession.inc');

error_reporting(0);

require_once('../../../config/db.php.inc');
require_once ('../../../include/db.php');
require_once ('../../../include/apistatuscodes.inc');


$response = array();
$con = getMySqlConnection();

$areaName = mysql_real_escape_string($areaName);

$sql = "SELECT areaname FROM areas WHERE project = ".$_SESSION['project']." ORDER BY areaname ASC;";

$result = mysql_query($sql);

if (!$result) {
    header("HTTP/1.0 500 Internal Server Error");
    $response['code'] = SQL_ERROR;
    $response['text'] = "SQL_ERROR";
}
else
{
    $resultArray = array();

    while ($row = mysql_fetch_array($result))
    {
        $response[] = $row['areaname'];
    }
    header("HTTP/1.0 200 Ok");
}

mysql_close($con);


echo json_encode($response);
?>