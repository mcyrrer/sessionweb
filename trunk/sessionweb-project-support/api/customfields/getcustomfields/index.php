<?php
session_start();

require_once('../../../include/validatesession.inc');

error_reporting(0);

require_once('../../../config/db.php.inc');
require_once ('../../../include/db.php');
require_once ('../../../include/apistatuscodes.inc');


$response = array();

if (isset($_REQUEST['customid'])) {
    $con = getMySqlConnection();

    $tablename = mysql_real_escape_string($_REQUEST['customid']);

    $sql = "select * from custom_items WHERE tablename='$tablename' ORDER  BY name ASC;";

    $result = mysql_query($sql);

    if (!$result) {
        header("HTTP/1.0 500 Internal Server Error");
        $response['code'] = SQL_ERROR;
        $response['text'] = "SQL_ERROR";
        $logger->error($_SERVER["SCRIPT_NAME"].": SQL_ERROR: ".$sql);
    }
    else
    {
        $resultArray = array();

        while ($row = mysql_fetch_array($result))
        {
            $response[] = $row['name'];
        }
        header("HTTP/1.0 200 Ok");
    }

    mysql_close($con);
}
else
{
    header("HTTP/1.0 400 Bad Request");
    $response['code'] = ITEM_NOT_PROVIDED_IN_REQUEST;
    $response['text'] = "ITEM_NOT_PROVIDED_IN_REQUEST";
}
echo json_encode($response);
?>