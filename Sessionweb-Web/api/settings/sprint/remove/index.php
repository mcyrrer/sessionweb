<?php
session_start();

require_once('../../../../include/validatesession.inc');

error_reporting(0);

require_once('../../../../config/db.php.inc');
require_once ('../../../../include/db.php');
require_once ('../../../../include/apistatuscodes.inc');


$response = array();
if ($_SESSION['useradmin'] == 1 || $_SESSION['superuser'] == 1) {

    if (isset($_REQUEST['sprint']) && strlen($_REQUEST['sprint']) > 0) {
        $sprintName = $_REQUEST['sprint'];

        $con = getMySqlConnection();

        $sprintName = mysql_real_escape_string($sprintName);

        $sql = "DELETE FROM sprintnames WHERE `sprintname`='$sprintName'";


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