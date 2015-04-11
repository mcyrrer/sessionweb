<?php
/**
 * API to delete a session
 * api/session/delete/index.php?sessionid=[sessionId]
 */

define('NUMBER_OF_INC_RECORDS_LIMIT', 1);

require_once('../../../classes/autoloader.php');
require_once('../../../include/apistatuscodes.inc');

$logger = new logging();
$sHelper = new sessionHelper();
$dbm = new dbHelper();

if (isset($_REQUEST['sessionid']) && $_REQUEST['sessionid'] != null) {

    $con = $dbm->connectToLocalDb();
    $sessionid = dbHelper::escape($con, $_REQUEST['sessionid']);
    $so = new sessionObject($sessionid);

    header("HTTP/1.0 501 Internal Server Error");


    if ($so->getSessionExist()) {
        $versionid = $so->getVersionid();
        if ($sHelper->isUserAllowedToEditSession($so)) {

            incremental_save_sessions($so, $con, $logger, $sessionid);
            $bugs = $so->getBugs();
            $req = $so->getRequirements();
            foreach ($bugs as $aBug) {
                $sHelper->updateRemoteStatusForCharterSetDeleted($so, $aBug);
            }
            foreach ($req as $aReq) {
                $sHelper->updateRemoteStatusForCharterSetDeleted($so, $aReq);
            }
            $so->deleteFromDatabase();
            header("HTTP/1.0 200 OK");
            $response['code'] = ITEM_REMOVED;
            $response['text'] = "ITEM_REMOVED";
        } else {
            header("HTTP/1.0 401 Unauthorized");
            $response['code'] = UNAUTHORIZED;
            $response['text'] = "UNAUTHORIZED";
        }
    } else {
        $logger->debug("Tried to delete session $sessionid but it does not exist", __FILE__, __LINE__);
        header("HTTP/1.0 404 Not found");
        $response['code'] = ITEM_DOES_NOT_EXIST;
        $response['text'] = "ITEM_DOES_NOT_EXIST";
    }

} else {
    $logger->debug("Tried to delete a session but one of the parameters is bad", __FILE__, __LINE__);
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
function incremental_save_sessions(sessionObject $so, $con, logging $logger, $sessionid)
{
    $dbm = new dbHelper();
    $con = $dbm->connectToLocalDb();
    $sqlIncSave = "INSERT INTO mission_incremental_save (versionid, title, charter, notes) VALUES ('" . $so->getVersionid() . "', '" . $so->getTitle() . "', '" . $so->getCharter() . "', '" . $so->getNotes() . "')";
    $dbm->executeQuery($con, $sqlIncSave);
    $logger->debug("Saved backup of $sessionid (title,charter and notes) since it will be deleted", __FILE__, __LINE__);

    $sqlNbrOfRow = "SELECT count(*) as NbrOfRows FROM mission_incremental_save WHERE versionid=" . $so->getVersionid() . " AND notes NOT LIKE ''";
    $nbrOfRowResult = $dbm->executeQuery($con, $sqlNbrOfRow);
    $nbrOfRowResultRow = mysqli_fetch_row($nbrOfRowResult);
    $nbrOfRow = $nbrOfRowResultRow[0];

    if ($nbrOfRow > NUMBER_OF_INC_RECORDS_LIMIT) {
        $nbrOfRowToCleanUp = $nbrOfRow - NUMBER_OF_INC_RECORDS_LIMIT;
        $sqlDeleteIncSaves = "DELETE FROM mission_incremental_save WHERE versionid=" . $so->getVersionid() . " AND notes NOT LIKE '' ORDER BY id ASC LIMIT " . $nbrOfRowToCleanUp . "";
        $nbrOfRowResult = $dbm->executeQuery($con, $sqlDeleteIncSaves);
        $logger->debug("Saved backup of $sessionid, and cleaned " . $nbrOfRowToCleanUp . " rows", __FILE__, __LINE__);
    }
}