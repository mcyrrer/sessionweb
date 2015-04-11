<?php
/**
 * API to update the mood for a session
 * api/mood/set/index.php?mood=[1-4]&sessionid=[sessionId]
 */

require_once('../../../classes/autoloader.php');
require_once('../../../include/apistatuscodes.inc');

$logger = new logging();
$sHelper = new sessionHelper();
$dbm = new dbHelper();

if (isset($_REQUEST['mood']) && isset($_REQUEST['sessionid']) && $_REQUEST['mood'] != null) {

    $con = $dbm->connectToLocalDb();

    $sessionid = dbHelper::escape($con, $_REQUEST['sessionid']);
    $mood = dbHelper::escape($con, $_REQUEST['mood']);
    $so = new sessionObject($sessionid);

    header("HTTP/1.0 501 Internal Server Error");

    if ($so->getSessionExist()) {
        $versionid = $so->getVersionid();
        if ($sHelper->isUserAllowedToEditSession($so)) {
            $sql = "update mission_sessionmetrics set mood = " . $mood . " where versionid = " . $versionid;

            $result = $dbm->executeQuery($con, $sql);

            $logger->debug("Updated mood to $mood for session $sessionid", __FILE__, __LINE__);
            header("HTTP/1.0 200 OK");
            $response['code'] = ITEM_UPDATED;
            $response['text'] = "ITEM_UPDATED";
        } else {
            header("HTTP/1.0 401 Unauthorized");
            $response['code'] = UNAUTHORIZED;
            $response['text'] = "UNAUTHORIZED";
        }
    } else {
        $logger->debug("Tried to updated mood to $mood for session $sessionid but $sessionid does not exist", __FILE__, __LINE__);
        header("HTTP/1.0 404 Not found");
        $response['code'] = ITEM_DOES_NOT_EXIST;
        $response['text'] = "ITEM_DOES_NOT_EXIST";
    }

} else {
    $logger->debug("Tried to updated mood but one of the parameters is bad", __FILE__, __LINE__);
    header("HTTP/1.0 400 Bad Request");
    $response['code'] = PARAMETER_NOT_PROVIDED_IN_REQUEST;
    $response['text'] = "PARAMETER_NOT_PROVIDED_IN_REQUEST";
}

echo json_encode($response);
