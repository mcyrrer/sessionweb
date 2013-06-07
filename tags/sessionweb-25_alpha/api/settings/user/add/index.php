<?php
session_start();

require_once('../../../../include/validatesession.inc');

error_reporting(1);

require_once('../../../../config/db.php.inc');
require_once ('../../../../include/db.php');
require_once ('../../../../include/apistatuscodes.inc');
require_once ('../../../../include/loggingsetup.php');

$response = array();
if ($_SESSION['useradmin'] == 1) {

    if (isset($_REQUEST['username']) &&
        isset($_REQUEST['fullname']) &&
        isset($_REQUEST['pw1']) &&
        isset($_REQUEST['pw2'])
    ) {
        $con = getMySqlConnection();

        $username = mysql_real_escape_string($_REQUEST["username"]);
        $fullname = mysql_real_escape_string($_REQUEST["fullname"]);
        $teamName = mysql_real_escape_string($_REQUEST["team"]);
        $password1 = $_REQUEST["pw1"];
        $password2 = $_REQUEST["pw2"];
        $md5password = md5($password1);
        $active = 1;
        $admin = 0;
        $superuser = 0;

        if (strcmp($_REQUEST["admin"], "yes") == 0) {
            $admin = 1;
        }

        $superuser = 0;
        if (strcmp($_REQUEST["superuser"], "yes") == 0) {
            $superuser = 1;
        }
        if (strcmp($password1, $password2) == 0 && strlen($username) > 3) {

            $sqlInsert = "";
            $sqlInsert .= "INSERT INTO `members` ";
            $sqlInsert .= "            (`username`, ";
            $sqlInsert .= "             `password`, ";
            $sqlInsert .= "             `fullname`, ";
            $sqlInsert .= "             `active`, ";
            $sqlInsert .= "             `admin`, ";
            $sqlInsert .= "             `superuser`) ";
            $sqlInsert .= "VALUES      ('$username', ";
            $sqlInsert .= "             '$md5password', ";
            $sqlInsert .= "             '$fullname', ";
            $sqlInsert .= "             '$active', ";
            $sqlInsert .= "             '$admin', ";
            $sqlInsert .= "             '$superuser')";

            $result = mysql_query($sqlInsert);
            if (!$result) {
                if (mysql_errno() == 1062) {
                    header("HTTP/1.0 409 Conflict");
                    $response['code'] = USER_ALREADY_EXIST;
                    $response['text'] = "USER_ALREADY_EXIST";
                }
                else {
                    header("HTTP/1.0 500 Internal Server Error");
                    $response['code'] = ITEM_NOT_ADDED;
                    $response['text'] = "ITEM_NOT_ADDED";
                    $logger->error($_SERVER["SCRIPT_NAME"] . ": SQL_ERROR: " . $sqlInsert);
                    $logger->error(mysql_error());
                }
            }
            else {
                $addedusersuccess = true;
            }

            $sqlInsert = "";
            $sqlInsert .= "INSERT INTO `user_settings` ";
            $sqlInsert .= "            (`username`, ";
            $sqlInsert .= "             `teamname`, ";
            $sqlInsert .= "             `default_team`, ";
            $sqlInsert .= "             `list_view`) ";
            $sqlInsert .= "VALUES      ('$username', ";
            $sqlInsert .= "             '$teamName', ";
            $sqlInsert .= "             '$teamName', ";
            $sqlInsert .= "             'all')";

            $result = mysql_query($sqlInsert);
            if (!$result) {
                if (mysql_errno() == 1062) {
                    header("HTTP/1.0 409 Conflict");
                    $response['code'] = ITEM_ALREADY_EXIST;
                    $response['text'] = "ITEM_ALREADY_EXIST";
                }
                else {
                    header("HTTP/1.0 500 Internal Server Error");
                    $response['code'] = ITEM_NOT_ADDED;
                    $response['text'] = "ITEM_NOT_ADDED";
                    $logger->error($_SERVER["SCRIPT_NAME"] . ": SQL_ERROR: " . $sqlInsert);
                    $logger->error(mysql_error());
                }
            }
            else {
                $addedusersuccess = true;
            }

            if ($addedusersuccess == true) {
                $logger->info($_SESSION['username'] . " added user $fullname ($username)");
                header("HTTP/1.0 201 Created");
                $response['code'] = ITEM_ADDED;
                $response['text'] = "ITEM_ADDED";
            }
            mysql_close($con);
        }
        else {
            header("HTTP/1.0 400 Bad Request");
            $response['code'] = CORRECT_PARAMETER_NOT_PROVIDED_IN_REQUEST;
            $response['text'] = strlen($username)."CORRECT_PARAMETER_NOT_PROVIDED_IN_REQUEST";
        }
    }
    else {
        header("HTTP/1.0 400 Bad Request");
        $response['code'] = ITEM_NOT_PROVIDED_IN_REQUEST;
        $response['text'] = "ITEM_NOT_PROVIDED_IN_REQUEST";
    }
}
else {
    header("HTTP/1.0 401 Unauthorized");
    $response['code'] = UNAUTHORIZED;
    $response['text'] = "UNAUTHORIZED";
}
echo json_encode($response);
?>