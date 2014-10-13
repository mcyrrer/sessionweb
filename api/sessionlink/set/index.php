<?php
/**
 * API to create a connection between two sessions
 * api/sessionlinks/set/index.php?from=[sessionid]&to=[sessionid]
 * from = sessionid to link from
 * to = sessionid to link to
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

if (isset($_REQUEST['from']) && $_REQUEST['to']) {


    $con = $dbManager->db_getMySqliConnection();

    $sessionidFrom = dbHelper::escape($con, $_REQUEST['from']);
    $sessionidFrom = trim($sessionidFrom);
    $sessionidTo = dbHelper::escape($con, $_REQUEST['to']);
    $sessionidTo = trim($sessionidTo);

    $soFrom = new sessionObject($sessionidFrom);
    $soTo = new sessionObject($sessionidTo);

    $versionidFrom = $soFrom->getVersionid();
    $versionidTo = $soTo->getVersionid();

    header("HTTP/1.0 501 Internal Server Error");
    // $logger->debug("soFrom exist:".$soFrom->getSessionExist(), __FILE__, __LINE__);
    //$logger->debug("soTo exist:".$soTo->getSessionExist(), __FILE__, __LINE__);

    if ($soFrom->getSessionExist() && $soTo->getSessionExist()) {
        if ($sHelper->isUserAllowedToEditSession($soFrom)) {
            if (!(in_array($sessionidTo, $soFrom->getLinked_to_session()))) {
                //print_($soFrom->getLinked_to_session());
                // if ($sHelper->isUserAllowedToEditSession($versionidFrom)) {
                $sql = "INSERT INTO mission_sessionsconnections (linked_from_versionid, linked_to_versionid) VALUES ('" . $versionidFrom . "', '" . $versionidTo . "')";
                $result = dbHelper::sw_mysqli_execute($con, $sql, __FILE__, __LINE__);
                $logger->debug("Created session connection between sessions " . $sessionidFrom . "->" . $sessionidTo . " (versionid: $versionidFrom->$versionidTo)", __FILE__, __LINE__);
                header("HTTP/1.0 201 Created");
                $response['code'] = ITEM_ADDED;
                $response['text'] = "ITEM_ADDED";
            }
            else
            {
                header("HTTP/1.0 409 Conflict");
                $response['code'] = ITEM_ALREADY_EXIST;
                $response['text'] = "ITEM_ALREADY_EXIST";
            }

        }
        else
        {
            header("HTTP/1.0 401 Unauthorized");
            $response['code'] = UNAUTHORIZED;
            $response['text'] = "UNAUTHORIZED";
        }
    } else {
        $logger->debug("Tried to create a session connection between sessions " . $sessionidFrom . "->" . $sessionidTo . " but one of the session does not exist", __FILE__, __LINE__);
        header("HTTP/1.0 404 Not found");
        $response['code'] = ITEM_DOES_NOT_EXIST;
        $response['text'] = "ITEM_DOES_NOT_EXIST";
    }

} else {
    $logger->debug("Tried to create a session connection with bad 'from' or 'to' parameters", __FILE__, __LINE__);
    header("HTTP/1.0 400 Bad Request");
    $response['code'] = PARAMETER_NOT_PROVIDED_IN_REQUEST;
    $response['text'] = "PARAMETER_NOT_PROVIDED_IN_REQUEST";
}

echo json_encode($response);
