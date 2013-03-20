<?php
/**
 * API to create a add a requirement to a session
 * api/sessionlinks/set/index.php?id=[requirementId]&sessionid=[sessionId]

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

if (isset($_REQUEST['id']) && isset($_REQUEST['sessionid']) && $_REQUEST['id']!=null) {

    $con = $dbManager->db_getMySqliConnection();
    $sessionid = dbHelper::escape($con, $_REQUEST['sessionid']);
    $requirementId = dbHelper::escape($con, $_REQUEST['id']);
    $so = new sessionObject($sessionid);

    header("HTTP/1.0 501 Internal Server Error");
    // $logger->debug("soFrom exist:".$soFrom->getSessionExist(), __FILE__, __LINE__);
    //$logger->debug("soTo exist:".$soTo->getSessionExist(), __FILE__, __LINE__);

    if ($so->getSessionExist()) {
        $versionid = $so->getVersionid();
        if ($sHelper->isUserAllowedToEditSession($so)) {
            if (!(in_array($requirementId, $so->getRequirements()))) {
                //print_($soFrom->getLinked_to_session());
                // if ($sHelper->isUserAllowedToEditSession($versionidFrom)) {
                $sql = "";
                $sql .= "INSERT INTO mission_requirements ";
                $sql .= "            (versionid, ";
                $sql .= "             requirementsid) ";
                $sql .= "VALUES      ( $versionid, ";
                $sql .= "              '$requirementId' ) " ;

                $result = dbHelper::sw_mysqli_execute($con, $sql, __FILE__, __LINE__);
                $logger->debug("Added requirement $requirementId to session $sessionid",__FILE__, __LINE__);
                header("HTTP/1.0 201 Created");
                $response['code'] = ITEM_ADDED;
                $response['text'] = "ITEM_ADDED";
            } else {
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
        $logger->debug("Tried to add a requirement $requirementId but sessionid $sessionid does not exist", __FILE__, __LINE__);
        header("HTTP/1.0 404 Not found");
        $response['code'] = ITEM_DOES_NOT_EXIST;
        $response['text'] = "ITEM_DOES_NOT_EXIST";
    }

} else {
    $logger->debug("Tried to create a requirement but one of the parameters is bad", __FILE__, __LINE__);
    header("HTTP/1.0 400 Bad Request");
    $response['code'] = PARAMETER_NOT_PROVIDED_IN_REQUEST;
    $response['text'] = "PARAMETER_NOT_PROVIDED_IN_REQUEST";
}

echo json_encode($response);
