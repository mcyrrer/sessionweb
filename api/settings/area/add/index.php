<?php
require_once('../../../../classes/autoloader.php');
require_once('../../../../include/apistatuscodes.inc');

$logger = new logging();
$sHelper = new sessionHelper();
$dbm = new dbHelper();


$logger = new logging();
$response = array();
if ($_SESSION['useradmin'] == 1 || $_SESSION['superuser'] == 1) {

    if (isset($_REQUEST['area']) && strlen($_REQUEST['area']) > 0) {
        $areaName = $_REQUEST['area'];


        $con = $dbm->connectToLocalDb();

        $areaName = mysqli_real_escape_string($con, $areaName);

        $sqlInsert = "";
        $sqlInsert .= "INSERT INTO areas ";
        $sqlInsert .= "            (`areaname`,`project`) ";
        $sqlInsert .= "VALUES      ('$areaName','" . $_SESSION['project'] . "')";


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
            $logger->info("Created area $areaName for project " . $_SESSION['project'], __FILE__, __LINE__);
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