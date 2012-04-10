<?php
session_start();

require_once('../../../../include/validatesession.inc');

error_reporting(0);

require_once('../../../../config/db.php.inc');
require_once ('../../../../include/db.php');
require_once ('../../../../include/apistatuscodes.inc');


$response = array();
if ($_SESSION['useradmin'] == 1) {

    if (isset($_REQUEST['environment']) && strlen($_REQUEST['environment']) > 0) {

        $con = getMySqlConnection();

        $environmentName = mysql_real_escape_string($_REQUEST['environment']);

        $sql = "DELETE FROM testenvironment WHERE `name`='$environmentName'";


        $result = mysql_query($sql);

        if (!$result) {

            header("HTTP/1.0 500 Internal Server Error");
            $response['code'] = ITEM_NOT_REMOVED;
            $response['text'] = "ITEM_NOT_REMOVED";
        }
        else
        {
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