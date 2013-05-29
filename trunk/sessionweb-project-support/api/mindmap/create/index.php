<?php
/**
 * API to create a add a MindMap to a session
 * api/mindmap/create/index.php?sessionid=[sessionId]&title=[tile]&description=[description]

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
require_once('../../../classes/Wisemapping.php');

$logger = new logging();
$sHelper = new sessionHelper();
$dbManager = new dbHelper();
$wiseMapping = new WisemappingManager();

if (isset($_REQUEST['sessionid']) && isset($_REQUEST['title'])) {

    $con = $dbManager->db_getMySqliConnection();
    $sessionid = dbHelper::escape($con, $_REQUEST['sessionid']);
    $so = new sessionObject($sessionid);

    header("HTTP/1.0 501 Internal Server Error");

    if ($so->getSessionExist()) {
        $versionid = $so->getVersionid();
        if ($sHelper->isUserAllowedToEditSession($so)) {

            $title = $dbManager->escape($con, $_REQUEST['title']);

            if (isset($_REQUEST['description'])) {
                $description = $dbManager->escape($con, $_REQUEST['description']);
            } else {
                $description = "";
            }

            $responseData = $wiseMapping->createMap($title, $description);
            if ($responseData == false) {
                $logger->error("Could not create MindMap ", __FILE__, __LINE__);
                $logger->info("Trying again with a random int as title prefix ", __FILE__, __LINE__);
                $randInt = rand(1000,9999);
                $title = $title . "-" . $randInt;
                $responseData = $wiseMapping->createMap($title, $description);
                if ($responseData == false) {

                    header("HTTP/1.0 400 Internal Server Error");
                    $response['code'] = UNKNOWN_ERROR;
                    $response['text'] = "UNKNOWN_ERROR";
                }
            }
            $mapId=$responseData['mapid'];

            if ($mapId != false) {
                $sql = "";
                $sql .= "INSERT INTO mission_mindmaps ";
                $sql .= "            (versionid, ";
                $sql .= "             map_id, ";
                $sql .= "             map_title )";
                $sql .= "VALUES      ( $versionid, ";
                $sql .= "              '$mapId',  ";
                $sql .= "              '$title' ) ";

                $result = dbHelper::sw_mysqli_execute($con, $sql, __FILE__, __LINE__);
                $logger->debug("Added MindMap with map_id $mapId to session $sessionid", __FILE__, __LINE__);
                header("HTTP/1.0 201 Created");
                $response['code'] = ITEM_ADDED;
                $response['text'] = "ITEM_ADDED";
                $response['data'] = $mapId;
                $response['title'] = $title;
                $response['url'] = $responseData['url'];
                ;
            }
        } else {
            header("HTTP/1.0 401 Unauthorized");
            $response['code'] = UNAUTHORIZED;
            $response['text'] = "UNAUTHORIZED";
        }
    } else {
        $logger->debug("Tried to add a MindMap but sessionid $sessionid does not exist", __FILE__, __LINE__);
        header("HTTP/1.0 404 Not found");
        $response['code'] = ITEM_DOES_NOT_EXIST;
        $response['text'] = "ITEM_DOES_NOT_EXIST";
    }

} else {
    $logger->debug("Tried to create a MindMap but one of the parameters is bad", __FILE__, __LINE__);
    header("HTTP/1.0 400 Bad Request");
    $response['code'] = PARAMETER_NOT_PROVIDED_IN_REQUEST;
    $response['text'] = "PARAMETER_NOT_PROVIDED_IN_REQUEST";
}

echo json_encode($response);
