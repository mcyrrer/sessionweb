<?php
require_once('../../../classes/autoloader.php');
require_once('../../../include/apistatuscodes.inc');

$logger = new logging();
$sHelper = new sessionHelper();
$dbm = new dbHelper();

$con = $dbm->connectToLocalDb();

$id = dbHelper::escape($con, $_REQUEST['id']);

$sqlGetVersionID = 'SELECT versionid FROM softwareuseautofetched WHERE id = ' . $id;
$sqlDeleteSwRecord = 'DELETE FROM softwareuseautofetched WHERE id=' . $id;

$sessionIdResult = $dbm->executeQuery($con, $sqlGetVersionID);
if (mysqli_num_rows($sessionIdResult) > 0) {
    $sRow = mysqli_fetch_row($sessionIdResult);
    $sessionId = $sRow[0];
    $so = new sessionObject($sessionId);

    if ($sHelper->isUserAllowedToEditSession($so)) {

        $result = $dbm->executeQuery($con, $sqlDeleteSwRecord);
        $logger->debug($_SESSION['username'] . " removed autofetched sw record with id ," . $id, __FILE__, __LINE__);
        header("HTTP/1.0 200 OK");
        $response['code'] = ITEM_REMOVED;
        $response['text'] = "ITEM_REMOVED";
    } else {
        $logger->warn($_SESSION['username'] . " tried to remove autofetched sw record with id ," . $id, __FILE__, __LINE__);
        header("HTTP/1.0 401 Unauthorized");
        $response['code'] = UNAUTHORIZED;
        $response['text'] = "UNAUTHORIZED";
    }
} else {
    header("HTTP/1.0 200 OK");
    $response['code'] = ITEM_DOES_NOT_EXIST;
    $response['text'] = "ITEM_DOES_NOT_EXIST";
}
echo json_encode($response);
mysqli_close($con);
