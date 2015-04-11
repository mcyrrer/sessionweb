<?php
require_once('../../../../classes/autoloader.php');
require_once('../../../../include/apistatuscodes.inc');

$logger = new logging();
$sHelper = new sessionHelper();
$dbm = new dbHelper();



$response = array();
if ($_SESSION['useradmin'] == 1) {

    if (isset($_REQUEST['team']) && strlen($_REQUEST['team']) > 0) {
        $teamName = $_REQUEST['team'];

        $con = $dbm->connectToLocalDb();

        $teamName = mysqli_real_escape_string($con, $teamName);

        $sql = "DELETE FROM teamnames WHERE `teamname`='$teamName'";


        $result = $dbm->executeQuery($con,$sql);

        if (!$result) {

            header("HTTP/1.0 500 Internal Server Error");
            $response['code'] = ITEM_NOT_REMOVED;
            $response['text'] = "ITEM_NOT_REMOVED";
        } else {
            $logger->info($_SESSION['username'] . " removed team $teamName");

            header("HTTP/1.0 200 OK");
            $response['code'] = ITEM_REMOVED;
            $response['text'] = "ITEM_REMOVED";

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