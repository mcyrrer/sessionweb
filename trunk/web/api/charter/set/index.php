<?php
/**
 * API to set a charter content to a session
 * api/charter/set/index.php
 * [POST]
 * text=[sprintName]
 * sessionid=[sessionId]
 */
define('NUMBER_OF_INC_RECORDS_LIMIT', 3);
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

if (isset($_REQUEST['text']) && isset($_REQUEST['sessionid'])) {

    $con = $dbManager->db_getMySqliConnection();
    $sessionid = dbHelper::escape($con, $_REQUEST['sessionid']);
    $charter = dbHelper::escape($con, $_REQUEST['text']);
    $so = new sessionObject($sessionid);

    header("HTTP/1.0 501 Internal Server Error");

    if ($so->getSessionExist()) {
        $versionid = $so->getVersionid();
        if ($sHelper->isUserAllowedToEditSession($so)) {
            incremental_save_charter($so, $con, $logger, $sessionid);

            $sql = "UPDATE mission SET charter='$charter' WHERE versionid='".$so->getVersionid()."'" ;
                $result = dbHelper::sw_mysqli_execute($con, $sql, __FILE__, __LINE__);
                $logger->debug("Changed charter content in session $sessionid",__FILE__, __LINE__);
                header("HTTP/1.0 200 OK");
                $response['code'] = ITEM_UPDATED;
                $response['text'] = "ITEM_UPDATED";
        }
        else
        {
            header("HTTP/1.0 401 Unauthorized");
            $response['code'] = UNAUTHORIZED;
            $response['text'] = "UNAUTHORIZED";
        }
    } else {
        $logger->debug("Tried to change charter content but sessionid $sessionid does not exist", __FILE__, __LINE__);
        header("HTTP/1.0 404 Not found");
        $response['code'] = ITEM_DOES_NOT_EXIST;
        $response['text'] = "ITEM_DOES_NOT_EXIST";
    }

} else {
    $logger->debug("Tried to change charter content but one of the parameters is bad", __FILE__, __LINE__);
    header("HTTP/1.0 400 Bad Request");
    $response['code'] = PARAMETER_NOT_PROVIDED_IN_REQUEST;
    $response['text'] = "PARAMETER_NOT_PROVIDED_IN_REQUEST";
}

echo json_encode($response);

/**
 * @param $so
 * @param $con
 * @param $logger
 * @param $sessionid
 */
function incremental_save_charter(sessionObject $so, $con, logging $logger, $sessionid)
{
    $sqlIncSave = "INSERT INTO mission_incremental_save (versionid, title, charter, notes) VALUES ('" . $so->getVersionid() . "', '', '" . $so->getCharter() . "', '')";
    dbHelper::sw_mysqli_execute($con, $sqlIncSave, __FILE__, __LINE__);
    $logger->debug("Incremental saved  $sessionid (charters)", __FILE__, __LINE__);

    $sqlNbrOfRow="SELECT count(*) as NbrOfRows FROM mission_incremental_save WHERE versionid=" . $so->getVersionid() . " AND charter NOT LIKE ''";
    $nbrOfRowResult = dbHelper::sw_mysqli_execute($con, $sqlNbrOfRow, __FILE__, __LINE__);
    $nbrOfRowResultRow = mysqli_fetch_row($nbrOfRowResult);
    $nbrOfRow=$nbrOfRowResultRow[0];

    if($nbrOfRow> NUMBER_OF_INC_RECORDS_LIMIT)
    {
        $nbrOfRowToCleanUp = $nbrOfRow- NUMBER_OF_INC_RECORDS_LIMIT;
        $sqlDeleteIncSaves="DELETE FROM mission_incremental_save WHERE versionid=" . $so->getVersionid() . " AND charter NOT LIKE '' ORDER BY id ASC LIMIT ".$nbrOfRowToCleanUp."";
        $nbrOfRowResult = dbHelper::sw_mysqli_execute($con, $sqlDeleteIncSaves, __FILE__, __LINE__);
        $logger->debug("Incremental save table clean up (charters) for session $sessionid executed, cleaned ".$nbrOfRowToCleanUp." rows", __FILE__, __LINE__);
    }
}