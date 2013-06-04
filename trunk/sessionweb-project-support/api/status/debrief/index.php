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

session_start();

require_once('../../../include/validatesession.inc');

//error_reporting(0);

require_once('../../../config/db.php.inc');
require_once ('../../../include/commonFunctions.php.inc');
require_once ('../../../include/db.php');
require_once('../../../classes/sessionHelper.php');
require_once('../../../classes/logging.php');
require_once('../../../classes/dbHelper.php');
require_once('../../../classes/AccessManagement.php');

$logger = new logging();
$sHelper = new sessionHelper();
$dbManager = new dbHelper();
$accessManager = new AccessManagement();

if (isset($_REQUEST['sessionid'])) {

    $con = $dbManager->db_getMySqliConnection();
    $sessionid = dbHelper::escape($con, $_REQUEST['sessionid']);
    $debriefed = dbHelper::escape($con, $_REQUEST['debriefed']);
    $closed = dbHelper::escape($con, $_REQUEST['closed']);
    $so = new sessionObject($sessionid);

    header("HTTP/1.0 501 Internal Server Error");

    if ($so->getSessionExist()) {
        $versionid = $so->getVersionid();

        if ($accessManager->IsCurrentUserAllowedToDebiref()) {
            if ($so->getExecuted()) {
                if (strcmp($debriefed, 'true') == 0) {
                    $sql = "UPDATE mission_status SET debriefed=1, debriefed_timestamp=NOW(), closed=0 WHERE versionid='" . $so->getVersionid() . "'";

                } elseif (strcmp($closed, 'true') == 0) {
                    $sql = "UPDATE mission_status SET debriefed=0, debriefed_timestamp=NOW(), closed=1 WHERE versionid='" . $so->getVersionid() . "'";

                } else {
                    $sql = "UPDATE mission_status SET debriefed=0, debriefed_timestamp=null, closed=0 WHERE versionid='" . $so->getVersionid() . "'";
                }

                $result = dbHelper::sw_mysqli_execute($con, $sql, __FILE__, __LINE__);
                $logger->debug("Changed debrief status for for session $sessionid", __FILE__, __LINE__);
                header("HTTP/1.0 200 OK");
                $response['code'] = ITEM_UPDATED;
                $response['text'] = "ITEM_UPDATED";
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
