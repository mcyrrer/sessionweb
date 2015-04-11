<?php
ob_start();
header("HTTP/1.0 400 Bad Request");

require_once('../../../../../classes/autoloader.php');
require_once('../../../../../include/apistatuscodes.inc');

$logger = new logging();
$sHelper = new sessionHelper();
$dbm = new dbHelper();



$response = array();


if (isset($_REQUEST['changepasswordold']) && strlen($_REQUEST['changepasswordold']) > 0) {
    $changepasswordold = $_REQUEST['changepasswordold'];
    $changepassword1 = $_REQUEST['changepassword1'];
    $changepassword2 = $_REQUEST['changepassword2'];

    if (strcmp($changepassword1, $changepassword2) == 0) {


        $con = $dbm->connectToLocalDb();

        validateUser($changepasswordold, $response);

        $result = updatePassword($changepassword1);


        if (!$result) {
            if (mysqli_errno($con) == 1062) {
                header("HTTP/1.0 409 Conflict");
                $response['code'] = ITEM_ALREADY_EXIST;
                $response['text'] = "ITEM_ALREADY_EXIST";

            } else {
                header("HTTP/1.0 500 Internal Server Error");
                $response['code'] = SQL_ERROR;
                $response['text'] = "SQL_ERROR";

            }
        } else {
            $logger->debug($_SESSION['username'] . " changed his/her password");
            header("HTTP/1.0 201 Created");
            $response['code'] = ITEM_ADDED;
            $response['text'] = "ITEM_ADDED";

        }


    } else {
        header("HTTP/1.0 400 Bad Request");
        $response['code'] = PASSWORD_DOES_NOT_MATCH;
        $response['text'] = "PASSWORD_DOES_NOT_MATCH";
    }
} else {
    header("HTTP/1.0 400 Bad Request");
    $response['code'] = PARAMETER_NOT_PROVIDED_IN_REQUEST;
    $response['text'] = "PARAMETER_NOT_PROVIDED_IN_REQUEST";
    $response['other'] = $_REQUEST['changepasswordold'];
}

echo json_encode($response);
ob_end_flush();

function validateUser($changepasswordold, $response)
{
    $dbm = new dbHelper();
    $con = $dbm->connectToLocalDb();
    $myusername = $_SESSION['username'];
    $mypassword = stripslashes($changepasswordold);
    $myusername = mysqli_real_escape_string($con, $myusername);
    $mypassword = mysqli_real_escape_string($con, $mypassword);
    $mypassword = md5($mypassword);

    $sql = "";
    $sql .= "SELECT * ";
    $sql .= "FROM   members ";
    $sql .= "WHERE  username = '$myusername' ";
    $sql .= "       AND PASSWORD = '$mypassword' ";
    $sql .= "       AND active = 1 ";

    $result = $dbm->executeQuery($con,$sql);

    if ($result == FALSE || mysqli_num_rows($result) == 0) {
        header("HTTP/1.0 401 Unauthorized");
        $response['code'] = UNAUTHORIZED;
        $response['text'] = "UNAUTHORIZED";

        echo json_encode($response);

        die();
    }
    return $result;
}

function updatePassword($changepassword1)
{
    $dbm = new dbHelper();
    $con = $dbm->connectToLocalDb();
    $password1 = stripslashes($changepassword1);
    $username = $_SESSION['username'];

    $md5password = md5($password1);

    $username = urldecode($username);
    $sqlUpdate = "";
    $sqlUpdate .= "UPDATE `members` ";
    $sqlUpdate .= "SET    `password` ='$md5password' ";
    $sqlUpdate .= "WHERE  `members`.`username` = '$username' ";
    //echo $sqlUpdate;
    $result = $dbm->executeQuery($con,$sqlUpdate);
    return $result;
}


?>