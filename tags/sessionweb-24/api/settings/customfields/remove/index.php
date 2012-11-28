<?php
session_start();

require_once('../../../../include/validatesession.inc');

error_reporting(0);

require_once('../../../../config/db.php.inc');
require_once ('../../../../include/db.php');
require_once ('../../../../include/apistatuscodes.inc');
require_once ('../../../../include/loggingsetup.php');



$response = array();
if ($_SESSION['useradmin'] == 1 || $_SESSION['superuser'] == 1) {

    if (isset($_REQUEST['customid']) && strlen($_REQUEST['itemname']) > 0) {

        $con = getMySqlConnection();

        $customid = mysql_real_escape_string($_REQUEST['customid']);
        $itemname = mysql_real_escape_string($_REQUEST['itemname']);

        $sql = "DELETE FROM custom_items WHERE tablename='$customid' AND name='$itemname';";
        $response['data'] = $sql;


        $result = mysql_query($sql);

        if (!$result) {

            header("HTTP/1.0 500 Internal Server Error");
            $response['code'] = ITEM_NOT_REMOVED;
            $response['text'] = "ITEM_NOT_REMOVED";
        }
        else
        {
            $logger->info($_SESSION['username']." removed area $areaName");
            header("HTTP/1.0 200 OK");
            $response['code'] = ITEM_REMOVED;
            $response['text'] = "ITEM_REMOVED";

        }

        mysql_close($con);
    }
    else
    {
        header("HTTP/1.0 400 Bad Request");
        $response['code'] = ITEM_NOT_PROVIDED_IN_REQUEST;
        $response['text'] = "ITEM_NOT_PROVIDED_IN_REQUEST";
    }
}
else
{
    header("HTTP/1.0 401 Unauthorized");
    $response['code'] = UNAUTHORIZED;
    $response['text'] = "UNAUTHORIZED";
}
echo json_encode($response);
?>