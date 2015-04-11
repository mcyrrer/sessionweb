<?php
require_once('../../../../classes/autoloader.php');
require_once('../../../../include/apistatuscodes.inc');

$logger = new logging();
$sHelper = new sessionHelper();
$dbm = new dbHelper();


$response = array();
if ($_SESSION['useradmin'] == 1 || $_SESSION['superuser'] == 1) {

    if (isset($_REQUEST['customid']) && strlen($_REQUEST['itemname']) > 0) {

        $con = $dbm->connectToLocalDb();

        $customid = mysqli_real_escape_string($con, $_REQUEST['customid']);
        $itemname = mysqli_real_escape_string($con, $_REQUEST['itemname']);

        $sql = "DELETE FROM custom_items WHERE tablename='$customid' AND name='$itemname';";
        $response['data'] = $sql;


        $result = $dbm->executeQuery($con,$sql);

        if (!$result) {

            header("HTTP/1.0 500 Internal Server Error");
            $response['code'] = ITEM_NOT_REMOVED;
            $response['text'] = "ITEM_NOT_REMOVED";
        } else {
            $logger->info($_SESSION['username'] . " removed area $areaName");
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