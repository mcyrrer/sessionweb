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
    $bugId = dbHelper::escape($con, $_REQUEST['id']);
    $bugId = trim($bugId);

    $so = new sessionObject($sessionid);

    header("HTTP/1.0 501 Internal Server Error");

    if ($so->getSessionExist()) {
        $versionid = $so->getVersionid();
        if ($sHelper->isUserAllowedToEditSession($so)) {
            if (!(in_array($bugId, $so->getBugs()))) {
                $sql = "";
                $sql .= "INSERT INTO mission_bugs ";
                $sql .= "            (versionid, ";
                $sql .= "             bugid) ";
                $sql .= "VALUES      ( $versionid, ";
                $sql .= "              '$bugId' ) ";

                $result = $dbm->executeQuery($con, $sql);

                $logger->debug("Added bug $bugId to session $sessionid", __FILE__, __LINE__);
                header("HTTP/1.0 201 Created");
                $response['code'] = ITEM_ADDED;
                $response['text'] = "ITEM_ADDED";
            } else {
                header("HTTP/1.0 409 Conflict");
                $response['code'] = ITEM_ALREADY_EXIST;
                $response['text'] = "ITEM_ALREADY_EXIST";
            }
        } else {
            header("HTTP/1.0 401 Unauthorized");
            $response['code'] = UNAUTHORIZED;
            $response['text'] = "UNAUTHORIZED";
        }
    } else {
        $logger->debug("Tried to add a requirement $bugId but sessionid $sessionid does not exist", __FILE__, __LINE__);
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
