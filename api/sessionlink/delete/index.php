<?php
/**
 * API to delete a connection between two sessions
 * api/sessionlinks/delete/index.php?from=[sessionid]&to=[sessionid]
 * from = sessionid to link from
 * to = sessionid to link to
 */

require_once('../../../classes/autoloader.php');
require_once('../../../include/apistatuscodes.inc');

$logger = new logging();
$sHelper = new sessionHelper();
$dbm = new dbHelper();

if (isset($_REQUEST['from']) && $_REQUEST['to']) {


    $con = $dbm->connectToLocalDb();

    $sessionidFrom = dbHelper::escape($con, $_REQUEST['from']);
    $sessionidTo = dbHelper::escape($con, $_REQUEST['to']);
    $soFrom = new sessionObject($sessionidFrom);
    $soTo = new sessionObject($sessionidTo);

    $versionidFrom = $soFrom->getVersionid();
    $versionidTo = $soTo->getVersionid();

    header("HTTP/1.0 501 Internal Server Error");


    if ($sHelper->isUserAllowedToEditSession($soFrom)) {
        if (in_array($sessionidTo, $soFrom->getLinked_to_session())) {

            $sql = "DELETE FROM mission_sessionsconnections ";
            $sql .= "WHERE  linked_from_versionid = $versionidFrom ";
            $sql .= "       AND linked_to_versionid = $versionidTo";
            $result = $dbm->executeQuery($con, $sql);

            $logger->debug("Removed session connection between sessions " . $sessionidFrom . "->" . $sessionidTo . " (versionid: $versionidFrom->$versionidTo)", __FILE__, __LINE__);
            header("HTTP/1.0 200 OK");
            $response['code'] = ITEM_REMOVED;
            $response['text'] = "ITEM_REMOVED";
        } else {
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
    $logger->debug("Tried to delete a session connection with bad 'from' or 'to' parameters", __FILE__, __LINE__);
    header("HTTP/1.0 400 Bad Request");
    $response['code'] = PARAMETER_NOT_PROVIDED_IN_REQUEST;
    $response['text'] = "PARAMETER_NOT_PROVIDED_IN_REQUEST";
}

echo json_encode($response);
