<?php
session_start();

require_once('../../../../include/validatesession.inc');

error_reporting(0);

require_once('../../../../config/db.php.inc');
require_once ('../../../../include/db.php');
require_once ('../../../../include/apistatuscodes.inc');


$response = array();
if ($_SESSION['useradmin'] == 1) {

    if (isset($_REQUEST['team']) && strlen($_REQUEST['team']) > 0) {
        $teamName = $_REQUEST['team'];

        $con = getMySqlConnection();

        $teamName = mysql_real_escape_string($teamName);

        $sql = "DELETE FROM teamnames WHERE `teamname`='$teamName'";


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