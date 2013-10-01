<?php
/**
 * API to create a set areas
 * api/area/set/index.php?names[]=[areaNamesAsArray]&sessionid=[sessionId]

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

if (isset($_REQUEST['names']) && isset($_REQUEST['sessionid']) && $_REQUEST['names'] != null) {

    $con = $dbManager->db_getMySqliConnection();
    $sessionid = dbHelper::escape($con, $_REQUEST['sessionid']);
    foreach ($_REQUEST['names'] as $name) {
        $testers[] = dbHelper::escape($con, $name);
    }
    $so = new sessionObject($sessionid);

    header("HTTP/1.0 501 Internal Server Error");

    if ($so->getSessionExist()) {
        $versionid = $so->getVersionid();
        if ($sHelper->isUserAllowedToEditSession($so)) {
            $sqlDelete = "DELETE FROM mission_areas WHERE versionid='" . $so->getVersionid() . "'";
            $result = dbHelper::sw_mysqli_execute($con, $sqlDelete, __FILE__, __LINE__);
            $logger->debug("Deleted all areas for session $sessionid", __FILE__, __LINE__);

            foreach ($testers as $name) {
                $sql = "INSERT INTO mission_areas (versionid, areaname) VALUES ('" . $so->getVersionid() . "', '$name');";
                $result = dbHelper::sw_mysqli_execute($con, $sql, __FILE__, __LINE__);
                $logger->debug("Added area $name to session $sessionid", __FILE__, __LINE__);
            }
            header("HTTP/1.0 200 OK");
            $response['code'] = ITEM_ADDED;
            $response['text'] = "ITEM_ADDED";
        } else {
            header("HTTP/1.0 401 Unauthorized");
            $response['code'] = UNAUTHORIZED;
            $response['text'] = "UNAUTHORIZED";
        }
    } else {
        $logger->debug("Tried to change areas but sessionid $sessionid does not exist", __FILE__, __LINE__);
        header("HTTP/1.0 404 Not found");
        $response['code'] = ITEM_DOES_NOT_EXIST;
        $response['text'] = "ITEM_DOES_NOT_EXIST";
    }

} else {
    $logger->debug("Tried to change areas but one of the parameters is bad", __FILE__, __LINE__);
    header("HTTP/1.0 400 Bad Request");
    $response['code'] = PARAMETER_NOT_PROVIDED_IN_REQUEST;
    $response['text'] = "PARAMETER_NOT_PROVIDED_IN_REQUEST";
}

echo json_encode($response);
