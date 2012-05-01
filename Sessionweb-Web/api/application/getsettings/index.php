<?php
session_start();

require_once('../../../include/validatesession.inc');

//error_reporting(0);

require_once('../../../config/db.php.inc');
require_once ('../../../include/db.php');
require_once ('../../../include/commonFunctions.php.inc');

require_once ('../../../include/apistatuscodes.inc');
require_once ('../../../include/loggingsetup.php');


$response = array();
if ($_SESSION['useradmin'] == 1) {

    $con = getMySqlConnection();


    $settings = getSessionWebSettings();


    if (!$settings) {
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
        header("HTTP/1.0 200 Ok");
        $response = $settings;
    }

    mysql_close($con);
}
else
{
    header("HTTP/1.0 401 Unauthorized");
    $response['code'] = UNAUTHORIZED;
    $response['text'] = "UNAUTHORIZED";
}
echo json_encode($response);
?>