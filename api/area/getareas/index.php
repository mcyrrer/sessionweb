<?php
require_once('../../../classes/autoloader.php');
require_once('../../../include/apistatuscodes.inc');

$logger = new logging();
$sHelper = new sessionHelper();
$dbm = new dbHelper();


$response = array();
$con = $dbm->connectToLocalDb();


$sql = "SELECT areaname FROM areas WHERE project = " . $_SESSION['project'] . " ORDER BY areaname ASC;";

$result = $dbm->executeQuery($con,$sql);

if (!$result) {
    header("HTTP/1.0 500 Internal Server Error");
    $response['code'] = SQL_ERROR;
    $response['text'] = "SQL_ERROR";
} else {
    $resultArray = array();

    while ($row = mysqli_fetch_array($result)) {
        $response[] = $row['areaname'];
    }
    header("HTTP/1.0 200 Ok");
}




echo json_encode($response);
?>