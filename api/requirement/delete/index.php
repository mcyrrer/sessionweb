<?php
/**
 * API to create a add a requirement to a session
 * api/sessionlinks/set/index.php?id=[requirementId]&sessionid=[sessionId]

 */

require_once('../../../classes/autoloader.php');
require_once('../../../include/apistatuscodes.inc');

$logger = new logging();
$sHelper = new sessionHelper();
$dbm = new dbHelper();

if (isset($_REQUEST['id']) && isset($_REQUEST['sessionid']) && $_REQUEST['id'] != null) {

    $con = $dbm->connectToLocalDb();

    $sessionid = dbHelper::escape($con, $_REQUEST['sessionid']);
    $requirementId = dbHelper::escape($con, $_REQUEST['id']);
    $so = new sessionObject($sessionid);

    header("HTTP/1.0 501 Internal Server Error");
    // $logger->debug("soFrom exist:".$soFrom->getSessionExist(), __FILE__, __LINE__);
    //$logger->debug("soTo exist:".$soTo->getSessionExist(), __FILE__, __LINE__);

    if ($so->getSessionExist()) {
        $versionid = $so->getVersionid();
        if ($sHelper->isUserAllowedToEditSession($so)) {
            if (in_array($requirementId, $so->getRequirements())) {
                $sql = "";
                $sql .= "DELETE FROM mission_requirements ";
                $sql .= "WHERE  versionid = $versionid ";
                $sql .= "       AND requirementsid = '$requirementId' ";

                $result = $dbm->executeQuery($con, $sql);

                $logger->debug("Deleted requirement $requirementId from session $sessionid", __FILE__, __LINE__);

                header("HTTP/1.0 200 OK");
                $response['code'] = ITEM_REMOVED;
                $response['text'] = "ITEM_REMOVED";

                $sHelper->updateRemoteStatusForCharterSetDeleted($so, $requirementId);

            } else {
                $logger->debug("Tried to delete a requirement $requirementId but is not mapped to sessionid $sessionid", __FILE__, __LINE__);
                header("HTTP/1.0 404 Not found");
                $response['code'] = ITEM_DOES_NOT_EXIST;
                $response['text'] = "ITEM_DOES_NOT_EXIST";
            }
        } else {
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
