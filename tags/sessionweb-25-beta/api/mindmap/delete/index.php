<?php
/**
 * API to delete a MindMap
 * api/mindmap/delete/index.php?sessionid=[sessionId]&id=[mind_map_id]

 */

session_start();

require_once('../../../include/validatesession.inc');

require_once('../../../config/db.php.inc');
require_once ('../../../include/commonFunctions.php.inc');
require_once ('../../../include/db.php');
require_once('../../../classes/sessionHelper.php');
require_once('../../../classes/logging.php');
require_once('../../../classes/dbHelper.php');

$logger = new logging();
$sHelper = new sessionHelper();
$dbManager = new dbHelper();

if (isset($_REQUEST['sessionid']) && isset($_REQUEST['id']) && $_REQUEST['id'] != null) {

    $con = $dbManager->db_getMySqliConnection();
    $map_id = dbHelper::escape($con, $_REQUEST['id']);
    $so = new sessionObject($sessionid);

    header("HTTP/1.0 501 Internal Server Error");

    if ($so->getSessionExist()) {
        $versionid = $so->getVersionid();
        if ($sHelper->isUserAllowedToEditSession($so)) {
            if (in_array($bugId, $so->getBugs())) {
                $sql = "";
                $sql .= "DELETE FROM mission_mindmaps ";
                $sql .= "WHERE  versionid = $versionid ";
                $sql .= "       AND map_id = '$map_id' ";

                $result = dbHelper::sw_mysqli_execute($con, $sql, __FILE__, __LINE__);
                $logger->debug("Deleted MindMap $map_id from session $sessionid", __FILE__, __LINE__);

                header("HTTP/1.0 200 OK");
                $response['code'] = ITEM_REMOVED;
                $response['text'] = "ITEM_REMOVED";
            } else {
                $logger->debug("Tried to delete a MindMap $map_id but is not mapped to sessionid $sessionid", __FILE__, __LINE__);
                header("HTTP/1.0 404 Not found");
                $response['code'] = ITEM_DOES_NOT_EXIST;
                $response['text'] = "ITEM_DOES_NOT_EXIST";
            }
        }
        else
        {
            header("HTTP/1.0 401 Unauthorized");
            $response['code'] = UNAUTHORIZED;
            $response['text'] = "UNAUTHORIZED";
        }
    } else {
        $logger->debug("Tried to delete a MindMap $map_id but sessionid $sessionid does not exist", __FILE__, __LINE__);
        header("HTTP/1.0 404 Not found");
        $response['code'] = ITEM_DOES_NOT_EXIST;
        $response['text'] = "ITEM_DOES_NOT_EXIST";
    }

} else {
    $logger->debug("Tried to delete a MindMap but one of the parameters is bad", __FILE__, __LINE__);
    header("HTTP/1.0 400 Bad Request");
    $response['code'] = PARAMETER_NOT_PROVIDED_IN_REQUEST;
    $response['text'] = "PARAMETER_NOT_PROVIDED_IN_REQUEST";
}

echo json_encode($response);
