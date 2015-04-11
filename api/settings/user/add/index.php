<?php
require_once('../../../../classes/autoloader.php');
require_once('../../../../include/apistatuscodes.inc');

$logger = new logging();
$sHelper = new sessionHelper();
$dbm = new dbHelper();


$response = array();
if ($_SESSION['useradmin'] == 1) {

    if (isset($_REQUEST['username']) &&
        isset($_REQUEST['fullname']) &&
        isset($_REQUEST['pw1']) &&
        isset($_REQUEST['pw2'])
    ) {
        $con = $dbm->connectToLocalDb();

        $username = mysqli_real_escape_string($con, $_REQUEST["username"]);
        $fullname = mysqli_real_escape_string($con, $_REQUEST["fullname"]);
        $teamName = mysqli_real_escape_string($con, $_REQUEST["team"]);
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

            $result = $dbm->executeQuery($con,$sqlInsert);
            if (!$result) {
                if (mysqli_errno($con) == 1062) {
                    header("HTTP/1.0 409 Conflict");
                    $response['code'] = USER_ALREADY_EXIST;
                    $response['text'] = "USER_ALREADY_EXIST";
                } else {
                    header("HTTP/1.0 500 Internal Server Error");
                    $response['code'] = ITEM_NOT_ADDED;
                    $response['text'] = "ITEM_NOT_ADDED";
                    $logger->error($_SERVER["SCRIPT_NAME"] . ": SQL_ERROR: " . $sqlInsert);
                    $logger->error(mysqli_error($con));
                }
            } else {
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

            $result = $dbm->executeQuery($con,$sqlInsert);
            if (!$result) {
                if (mysqli_errno($con) == 1062) {
                    header("HTTP/1.0 409 Conflict");
                    $response['code'] = ITEM_ALREADY_EXIST;
                    $response['text'] = "ITEM_ALREADY_EXIST";
                } else {
                    header("HTTP/1.0 500 Internal Server Error");
                    $response['code'] = ITEM_NOT_ADDED;
                    $response['text'] = "ITEM_NOT_ADDED";
                    $logger->error($_SERVER["SCRIPT_NAME"] . ": SQL_ERROR: " . $sqlInsert);
                    $logger->error(mysqli_error($con));
                }
            } else {
                $addedusersuccess = true;
            }

            if ($addedusersuccess == true) {
                $logger->info($_SESSION['username'] . " added user $fullname ($username)");
                header("HTTP/1.0 201 Created");
                $response['code'] = ITEM_ADDED;
                $response['text'] = "ITEM_ADDED";
            }

        } else {
            header("HTTP/1.0 400 Bad Request");
            $response['code'] = CORRECT_PARAMETER_NOT_PROVIDED_IN_REQUEST;
            $response['text'] = strlen($username) . "CORRECT_PARAMETER_NOT_PROVIDED_IN_REQUEST";
        }
    } else {
        header("HTTP/1.0 400 Bad Request");
        $response['code'] = ITEM_NOT_PROVIDED_IN_REQUEST;
        $response['text'] = "ITEM_NOT_PROVIDED_IN_REQUEST";
    }
} else {
    header("HTTP/1.0 401 Unauthorized");
    $response['code'] = UNAUTHORIZED;
    $response['text'] = "UNAUTHORIZED";
}
echo json_encode($response);
?>