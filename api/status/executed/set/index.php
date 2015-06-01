<?php
/**
 * API to set a status for a session
 * api/status/executed/set/index.php
 * [GET]
 * sessionid=[sessionId]
 */

require_once('../../../../classes/autoloader.php');
require_once('../../../../include/apistatuscodes.inc');

$logger = new logging();
$sHelper = new sessionHelper();
$dbm = new dbHelper();


if (isset($_REQUEST['sessionid'])) {

    $con = $dbm->connectToLocalDb();

    $sessionid = dbHelper::escape($con, $_REQUEST['sessionid']);
    $so = new sessionObject($sessionid);

    header("HTTP/1.0 501 Internal Server Error");

    if ($so->getSessionExist()) {
        $versionid = $so->getVersionid();
        if ($sHelper->isUserAllowedToEditSession($so)) {
            $sql = "UPDATE mission_status SET executed=1,executed_timestamp=NOW() WHERE versionid='" . $so->getVersionid() . "'";
            $result = $dbm->executeQuery($con, $sql);
            $logger->debug("Changed status for executed to 1 for session $sessionid", __FILE__, __LINE__);
            header("HTTP/1.0 200 OK");
            $response['code'] = ITEM_UPDATED;
            $response['text'] = "ITEM_UPDATED";

            //Reloading SO since it is updated
            unset($so);
            $so = new sessionObject($sessionid);
            $sHelper->updateRemoteStatusForCharter($so);

        } else {
            header("HTTP/1.0 401 Unauthorized");
            $response['code'] = UNAUTHORIZED;
            $response['text'] = "UNAUTHORIZED";
        }
    } else {
        $logger->debug("Tried to change status for executed to 1 for but sessionid $sessionid does not exist", __FILE__, __LINE__);
        header("HTTP/1.0 404 Not found");
        $response['code'] = ITEM_DOES_NOT_EXIST;
        $response['text'] = "ITEM_DOES_NOT_EXIST";
    }

} else {
    $logger->debug("Tried to change status for executed to 1 for but one of the parameters is bad", __FILE__, __LINE__);
    header("HTTP/1.0 400 Bad Request");
    $response['code'] = PARAMETER_NOT_PROVIDED_IN_REQUEST;
    $response['text'] = "PARAMETER_NOT_PROVIDED_IN_REQUEST";
}

echo json_encode($response);