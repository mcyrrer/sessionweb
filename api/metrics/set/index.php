<?php
/**
 * API to update the metrics for a session
 * api/metrics/set/index.php?mood=[1-4]&sessionid=[sessionId]
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

$logger = new logging();
$sHelper = new sessionHelper();
$dbManager = new dbHelper();

if (isset($_REQUEST['setup']) &&
    isset($_REQUEST['test']) &&
    isset($_REQUEST['bug']) &&
    isset($_REQUEST['opp']) &&
    isset($_REQUEST['sessionid'])
) {

    $con = $dbManager->db_getMySqliConnection();
    $sessionid = dbHelper::escape($con, $_REQUEST['sessionid']);
    $setup = dbHelper::escape($con, $_REQUEST['setup']);
    $test = dbHelper::escape($con, $_REQUEST['test']);
    $bug = dbHelper::escape($con, $_REQUEST['bug']);
    $opp = dbHelper::escape($con, $_REQUEST['opp']);

    $so = new sessionObject($sessionid);

    header("HTTP/1.0 501 Internal Server Error");

    if ($so->getSessionExist()) {
        $versionid = $so->getVersionid();
        if ($sHelper->isUserAllowedToEditSession($so)) {

            $sql = "update mission_sessionmetrics set setup_percent = $setup, test_percent = $test, bug_percent = $bug, opportunity_percent = $opp where versionid = " . $versionid;

            $result = dbHelper::sw_mysqli_execute($con, $sql, __FILE__, __LINE__);
            $logger->debug("Updated session metrics for session $sessionid", __FILE__, __LINE__);
            header("HTTP/1.0 200 OK");
            $response['code'] = ITEM_UPDATED;
            $response['text'] = "ITEM_UPDATED";
        } else {
            header("HTTP/1.0 401 Unauthorized");
            $response['code'] = UNAUTHORIZED;
            $response['text'] = "UNAUTHORIZED";
        }
    } else {
        $logger->debug("Tried to updated session metrics for session $sessionid but $sessionid does not exist", __FILE__, __LINE__);
        header("HTTP/1.0 404 Not found");
        $response['code'] = ITEM_DOES_NOT_EXIST;
        $response['text'] = "ITEM_DOES_NOT_EXIST";
    }

} else {
    $logger->debug("Tried to updated session metrics but one of the parameters is bad", __FILE__, __LINE__);
    header("HTTP/1.0 400 Bad Request");
    $response['code'] = PARAMETER_NOT_PROVIDED_IN_REQUEST;
    $response['text'] = "PARAMETER_NOT_PROVIDED_IN_REQUEST";
}

echo json_encode($response);
