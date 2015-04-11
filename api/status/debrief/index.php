<?php
/**
 * API to set debrief status for a session
 * If debried is set to true closed will be omitted.
 * api/status/debrief/index.php
 * [POST]
 * sessionid=[sessionId]
 * debriefed=[true/false]
 * closed=[true/false]
 */

require_once('../../../classes/autoloader.php');
require_once('../../../include/apistatuscodes.inc');

$logger = new logging();
$sHelper = new sessionHelper();
$dbm = new dbHelper();

$accessManager = new AccessManagement();

if (isset($_REQUEST['sessionid'])) {

    $con = $dbm->connectToLocalDb();

    $sessionid = dbHelper::escape($con, $_REQUEST['sessionid']);
    $debriefed = dbHelper::escape($con, $_REQUEST['debriefed']);
    $closed = dbHelper::escape($con, $_REQUEST['closed']);
    $so = new sessionObject($sessionid);

    header("HTTP/1.0 501 Internal Server Error");

    if ($so->getSessionExist()) {
        $versionid = $so->getVersionid();

        if ($accessManager->isCurrentUserAllowedToDebiref()) {
            if ($so->getExecuted()) {
                if (strcmp($debriefed, 'true') == 0) {
                    $sql = "UPDATE mission_status SET debriefed=1, debriefed_timestamp=NOW(), closed=0 WHERE versionid='" . $so->getVersionid() . "'";
                    incremental_save_delete($so, $con, $dbm, $logger, $sessionid);

                } elseif (strcmp($closed, 'true') == 0) {
                    $sql = "UPDATE mission_status SET debriefed=0, debriefed_timestamp=NOW(), closed=1 WHERE versionid='" . $so->getVersionid() . "'";
                    incremental_save_delete($so, $con, $dbm, $logger, $sessionid);

                } else {
                    $sql = "UPDATE mission_status SET debriefed=0, debriefed_timestamp=null, closed=0 WHERE versionid='" . $so->getVersionid() . "'";
                }

                $result = $dbm->executeQuery($con, $sql);

                $logger->debug("Changed debrief status for for session $sessionid", __FILE__, __LINE__);
                header("HTTP/1.0 200 OK");
                $response['code'] = ITEM_UPDATED;
                $response['text'] = "ITEM_UPDATED";

                //Reloading SO since it is updated
                unset($so);
                $so = new sessionObject($sessionid);
                $sHelper->updateRemoteStatusForCharter($so);

            } else {
                header("HTTP/1.0 409 Conflict");
                $response['code'] = SESSION_NOT_EXECUTED;
                $response['text'] = "SESSION_NOT_EXECUTED";
            }
        } else {
            header("HTTP/1.0 401 Unauthorized");
            $response['code'] = UNAUTHORIZED;
            $response['text'] = "UNAUTHORIZED";
        }
    } else {
        $logger->debug("Tried to change debrief status but sessionid $sessionid does not exist", __FILE__, __LINE__);
        header("HTTP/1.0 404 Not found");
        $response['code'] = ITEM_DOES_NOT_EXIST;
        $response['text'] = "ITEM_DOES_NOT_EXIST";
    }

} else {
    $logger->debug("Tried to change debrief status but one of the parameters is bad", __FILE__, __LINE__);
    header("HTTP/1.0 400 Bad Request");
    $response['code'] = PARAMETER_NOT_PROVIDED_IN_REQUEST;
    $response['text'] = "PARAMETER_NOT_PROVIDED_IN_REQUEST";
}

echo json_encode($response);

function incremental_save_delete(sessionObject $so, $con, $dbm, logging $logger, $sessionid)
{
    $sqlDeleteIncSaves = "DELETE FROM mission_incremental_save WHERE versionid=" . $so->getVersionid();
    $nbrOfRowResult = $dbm->executeQuery($con, $sqlDeleteIncSaves);

    $logger->debug("Incremental save table clean up for session $sessionid executed, cleaned all rows since it is closed/debriefed", __FILE__, __LINE__);

}
