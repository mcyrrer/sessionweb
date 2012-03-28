<?php
session_start();

require_once('../../../../include/validatesession.inc');

error_reporting(0);

require_once('../../../../config/db.php.inc');
require_once ('../../../../include/db.php');
require_once ('../../../../include/apistatuscodes.inc');



$response = array();
if ($_SESSION['useradmin'] == 1 || $_SESSION['superuser'] == 1) {

    if (isset($_REQUEST['area']) && strlen($_REQUEST['area']) > 0) {
        $areaName = $_REQUEST['area'];


        $con = getMySqlConnection();

        $areaName = mysql_real_escape_string($areaName);

        $sqlInsert = "";
        $sqlInsert .= "INSERT INTO areas ";
        $sqlInsert .= "            (`areaname`) ";
        $sqlInsert .= "VALUES      ('$areaName')";


        $result = mysql_query($sqlInsert);

        if (!$result) {
            if (mysql_errno() == 1062) {
                header("HTTP/1.0 409 Conflict");
                $response['code'] = ITEM_ALREADY_EXIST;
                $response['text'] = "ITEM_ALREADY_EXIST";

            }
            else
            {
                header("HTTP/1.0 500 Internal Server Error");
                $response['code'] = ITEM_NOT_ADDED;
                $response['text'] = "ITEM_NOT_ADDED";

            }
        }
        else
        {
            header("HTTP/1.0 201 Created");
            $response['code'] = ITEM_ADDED;
            $response['text'] = "ITEM_ADDED";

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