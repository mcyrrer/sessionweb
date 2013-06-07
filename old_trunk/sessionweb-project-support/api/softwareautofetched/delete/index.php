<?php
session_start();
require_once('../../../include/validatesession.inc');
require_once('../../../include/apistatuscodes.inc');
require_once('../../../classes/dbHelper.php');
require_once('../../../classes/sessionHelper.php');
require_once('../../../classes/sessionObject.php');
require_once('../../../classes/logging.php');
require_once('../../../config/db.php.inc');

$logger = new logging();
$sHelper = new sessionHelper();
$dbManager = new dbHelper();
$con = $dbManager->db_getMySqliConnection();
$id = dbHelper::escape($con, $_REQUEST['id']);

$sqlGetVersionID = 'SELECT versionid FROM softwareuseautofetched WHERE id = ' . $id;
$sqlDeleteSwRecord = 'DELETE FROM softwareuseautofetched WHERE id=' . $id;

$sessionIdResult = dbHelper::sw_mysqli_execute($con, $sqlGetVersionID, __FILE__, __LINE__);
if (mysqli_num_rows($sessionIdResult) > 0) {
    $sRow = mysqli_fetch_row($sessionIdResult);
    $sessionId = $sRow[0];
    $so = new sessionObject($sessionId);

    if ($sHelper->isUserAllowedToEditSession($so)) {

        dbHelper::sw_mysqli_execute($con, $sqlDeleteSwRecord, __FILE__, __LINE__);
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
}
else
{
    header("HTTP/1.0 200 OK");
    $response['code'] = ITEM_DOES_NOT_EXIST;
    $response['text'] = "ITEM_DOES_NOT_EXIST";
}
echo json_encode($response);
mysqli_close($con);
