<?php
require_once('../../../../classes/autoloader.php');
require_once('../../../../include/apistatuscodes.inc');

$logger = new logging();
$sHelper = new sessionHelper();
$dbm = new dbHelper();



$response = array();
if ($_SESSION['useradmin'] == 1) {

    if (isset($_REQUEST['environment']) && strlen($_REQUEST['environment']) > 0) {

        $con = $dbm->connectToLocalDb();

        $envName = mysqli_real_escape_string($con, $_REQUEST['environment']);
        $envautofetchurl = mysqli_real_escape_string($con, $_REQUEST["url"]);
        $envusername = mysqli_real_escape_string($con, $_REQUEST["username"]);
        $envpassword = mysqli_real_escape_string($con, $_REQUEST["password"]);

        $sqlInsert = "";
        $sqlInsert .= "INSERT INTO testenvironment ";
        $sqlInsert .= "            (name, ";
        $sqlInsert .= "             url, ";
        $sqlInsert .= "             username, ";
        $sqlInsert .= "             PASSWORD,  ";
        $sqlInsert .= "             project) ";
        $sqlInsert .= "VALUES      ('$envName', ";
        $sqlInsert .= "             '$envautofetchurl', ";
        $sqlInsert .= "             '$envusername', ";
        $sqlInsert .= "             '$envpassword', ";
        $sqlInsert .= "             '" . $_SESSION['project'] . "') ";


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

            }
        } else {
            $logger->info($_SESSION['username'] . " added environment $envName");

            header("HTTP/1.0 201 Created");
            $response['code'] = ITEM_ADDED;
            $response['text'] = "ITEM_ADDED";

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