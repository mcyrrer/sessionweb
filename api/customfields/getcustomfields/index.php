<?php
require_once('../../../classes/autoloader.php');
require_once('../../../include/apistatuscodes.inc');

$logger = new logging();
$sHelper = new sessionHelper();
$dbm = new dbHelper();


$response = array();

if (isset($_REQUEST['customid'])) {
    $con = $dbm->connectToLocalDb();

    $tablename = mysqli_real_escape_string($con, $_REQUEST['customid']);

    $sql = "select * from custom_items WHERE tablename='$tablename' ORDER  BY name ASC;";

    $result = $dbm->executeQuery($con,$sql);
    $result = $dbm->executeQuery($con,$sql);

    if (!$result) {
        header("HTTP/1.0 500 Internal Server Error");
        $response['code'] = SQL_ERROR;
        $response['text'] = "SQL_ERROR";
        $logger->error($_SERVER["SCRIPT_NAME"] . ": SQL_ERROR: " . $sql);
    } else {
        $resultArray = array();

        while ($row = mysqli_fetch_array($result)) {
            $response[] = $row['name'];
        }
        header("HTTP/1.0 200 Ok");
    }


} else {
    header("HTTP/1.0 400 Bad Request");
    $response['code'] = ITEM_NOT_PROVIDED_IN_REQUEST;
    $response['text'] = "ITEM_NOT_PROVIDED_IN_REQUEST";
}
echo json_encode($response);
?>