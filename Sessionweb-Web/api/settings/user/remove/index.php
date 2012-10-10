<?php
session_start();

require_once('../../../../include/validatesession.inc');

error_reporting(1);

require_once('../../../../config/db.php.inc');
require_once ('../../../../include/db.php');
require_once ('../../../../include/apistatuscodes.inc');
require_once ('../../../../include/loggingsetup.php');
require_once ('../../../../include/commonFunctions.php.inc');

$response = array();


if ($_SESSION['useradmin'] == 1) {
    if (isset($_REQUEST['username'])) {

        if (doesUserExist($_REQUEST['username'])) {
            $response = removeUserFromDb($logger, $response);
        }
        else
        {
            $logger->debug($_SESSION['username'] . " tried to delete non-existing user ".$_REQUEST['username']);
            header("HTTP/1.0 400 Bad Request");
            $response['code'] = USER_DOES_NOT_EXIST;
            $response['text'] = "USER_DOES_NOT_EXIST";
        }
    } else {
        header("HTTP/1.0 400 Bad Request");
        $response['code'] = CORRECT_PARAMETER_NOT_PROVIDED_IN_REQUEST;
        $response['text'] = "CORRECT_PARAMETER_NOT_PROVIDED_IN_REQUEST";
    }
} else {
    header("HTTP/1.0 401 Unauthorized");
    $response['code'] = UNAUTHORIZED;
    $response['text'] = "UNAUTHORIZED";
}
echo json_encode($response);


function removeUserFromDb($logger, $response)
{
    $username = mysql_real_escape_string($_REQUEST["username"]);

    $con = getMySqlConnection();

    $sqlDelete = "UPDATE members SET active=0, deleted=1 WHERE username='$username'";
    $result = mysql_query($sqlDelete);
    $logger->info($_SESSION['username'] . " marked user $username as deleted and inactive");

    header("HTTP/1.0 200 OK");

    $response['code'] = USER_DELETED;
    $response['text'] = "USER_DELETED";

    mysql_close($con);
    return $response;
}

?>