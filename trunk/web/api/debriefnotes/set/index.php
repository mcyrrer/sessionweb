<?php
/**
 * API to set debrief notes content to a session
 * api/debriefnotes/set/index.php
 * [POST]
 * text=[text]
 * final=[true, adds users full name to debrief notes]
 */

session_start();

require_once('../../../include/validatesession.inc');

//error_reporting(0);

require_once('../../../config/db.php.inc');
require_once('../../../include/commonFunctions.php.inc');
require_once('../../../include/db.php');
require_once('../../../classes/AccessManagement.php');
require_once('../../../classes/sessionHelper.php');
require_once('../../../classes/logging.php');
require_once('../../../classes/dbHelper.php');

$logger = new logging();
$sHelper = new sessionHelper();
$dbManager = new dbHelper();

if (isset($_REQUEST['text']) && isset($_REQUEST['sessionid'])) {

    $con = $dbManager->db_getMySqliConnection();
    $sessionid = dbHelper::escape($con, $_REQUEST['sessionid']);
    $notes = dbHelper::escape($con, $_REQUEST['text']);

    $so = new sessionObject($sessionid);

    header("HTTP/1.0 501 Internal Server Error");

    if ($so->getSessionExist()) {
        $versionid = $so->getVersionid();
        if (strcmp("",$notes)!=0) {
            if (isset($_REQUEST['final']) && $_REQUEST['final'] == true) {
                $notes = 'Notes added by: ' . $sHelper->getUserFullName() . '<br>' . $notes;
            }
            if (AccessManagement::isCurrentUserAllowedToDebiref()) {

                $sql = "INSERT IGNORE
                        INTO
                            mission_debriefnotes
                            (
                                versionid,
                                notes,
                                debriefedby
                            )
                            VALUES
                            (
                                " . $versionid . ",
                                '" . $notes . "',
                                '" . $sHelper->getUserName() . "'
                            ) ON DUPLICATE KEY UPDATE notes='" . $notes . "', debriefedby='" . $sHelper->getUserName() . "'";

                $result = dbHelper::sw_mysqli_execute($con, $sql, __FILE__, __LINE__);
                $logger->debug("Added/updated debrief notes content for session $sessionid", __FILE__, __LINE__);
                header("HTTP/1.0 200 OK");
                $response['code'] = ITEM_UPDATED;
                $response['text'] = "ITEM_UPDATED";
            } else {
                header("HTTP/1.0 401 Unauthorized");
                $response['code'] = UNAUTHORIZED;
                $response['text'] = "UNAUTHORIZED";
            }
        }
        else
        {
            $logger->debug($sessionid.' has no notes to save', __FILE__, __LINE__);
            header("HTTP/1.0 200 OK");
            $response['code'] = ITEM_UPDATED;
            $response['text'] = "ITEM_UPDATED";
        }
    } else {
        $logger->debug("Tried to change debrief notes content but sessionid $sessionid does not exist", __FILE__, __LINE__);
        header("HTTP/1.0 404 Not found");
        $response['code'] = ITEM_DOES_NOT_EXIST;
        $response['text'] = "ITEM_DOES_NOT_EXIST";
    }

} else {
    $logger->debug("Tried to change debrief notes content but one of the parameters is bad", __FILE__, __LINE__);
    header("HTTP/1.0 400 Bad Request");
    $response['code'] = PARAMETER_NOT_PROVIDED_IN_REQUEST;
    $response['text'] = "PARAMETER_NOT_PROVIDED_IN_REQUEST";
}

echo json_encode($response);
