<?php
session_start();

require_once('../../../../include/validatesession.inc');

error_reporting(0);

require_once('../../../../config/db.php.inc');
require_once ('../../../../include/db.php');
require_once ('../../../../include/apistatuscodes.inc');
require_once ('../../../../include/loggingsetup.php');
require_once ('../../../../include/commonFunctions.php.inc');



$response = array();
if ($_SESSION['useradmin'] == 1) {

    if (isset($_REQUEST['cf1']) &&
        isset($_REQUEST['cf2']) &&
        isset($_REQUEST['cf3']) &&
        isset($_REQUEST['cf1multiselect']) &&
        isset($_REQUEST['cf2multiselect']) &&
        isset($_REQUEST['cf3multiselect']) &&
        isset($_REQUEST['cf1enabled']) &&
        isset($_REQUEST['cf2enabled']) &&
        isset($_REQUEST['cf3enabled'])) {

        $con = getMySqlConnection();

        $cf1 = mysql_real_escape_string($_REQUEST['cf1']);
        $cf2 = mysql_real_escape_string($_REQUEST['cf2']);
        $cf3 = mysql_real_escape_string($_REQUEST['cf3']);
        $cf1multiselect = mysql_real_escape_string($_REQUEST['cf1multiselect']);
        $cf2multiselect = mysql_real_escape_string($_REQUEST['cf2multiselect']);
        $cf3multiselect = mysql_real_escape_string($_REQUEST['cf3multiselect']);
        $cf1enabled = mysql_real_escape_string($_REQUEST['cf1enabled']);
        $cf2enabled = mysql_real_escape_string($_REQUEST['cf2enabled']);
        $cf3enabled = mysql_real_escape_string($_REQUEST['cf3enabled']);




        $sqlUpdate = "
        UPDATE settings
        SET    custom1 = $cf1enabled,
               custom1_name = '$cf1',
               custom1_multiselect = $cf1multiselect,
               custom2 = $cf2enabled,
               custom2_name = '$cf2',
               custom2_multiselect = $cf2multiselect,
               custom3 = $cf3enabled,
               custom3_name = '$cf3',
               custom3_multiselect = $cf3multiselect
        WHERE  id = '1'";


        $result = mysql_query($sqlUpdate);

        if (!$result) {
            if (mysql_errno() == 1062) {
                header("HTTP/1.0 409 Conflict");
                $response['code'] = ITEM_ALREADY_EXIST;
                $response['text'] = "ITEM_ALREADY_EXIST";
            }
            else
            {
                header("HTTP/1.0 500 Internal Server Error");
                $response['code'] = SQL_ERROR;
                $response['text'] = "SQL_ERROR";
                $logger->error($_SERVER["SCRIPT_NAME"].": SQL_ERROR: ".$sqlUpdate);
            }
        }
        else
        {
            $logger->info($_SESSION['username']." Updated custom fields");
            header("HTTP/1.0 201 Created");
            $response['code'] = ITEM_ADDED;
            $response['text'] = "ITEM_ADDED";
            $_SESSION['settings'] = getSessionWebSettings();
        }

        mysql_close($con);
    }
    else
    {
        header("HTTP/1.0 400 Bad Request");
        $response['code'] = ITEM_NOT_PROVIDED_IN_REQUEST;
        $response['text'] = "ITEM_NOT_PROVIDED_IN_REQUEST";
        print_r($_REQUEST);

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