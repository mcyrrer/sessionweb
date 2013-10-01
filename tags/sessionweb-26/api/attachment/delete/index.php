<?php
/**
 * API to delete an attachment a add a requirement to a session
 * api/attachment/delete/index.php?id=[attachmentId]
 */

session_start();

require_once('../../../include/validatesession.inc');

//error_reporting(0);

require_once('../../../config/db.php.inc');
//require_once ('../../../include/commonFunctions.php.inc');
require_once ('../../../include/db.php');
require_once('../../../classes/sessionHelper.php');
require_once('../../../classes/logging.php');
require_once('../../../classes/dbHelper.php');

$logger = new logging();
$sHelper = new sessionHelper();
$dbManager = new dbHelper();

if (isset($_REQUEST['id']) && $_REQUEST['id'] != null) {

    $con = $dbManager->db_getMySqliConnection();
    $attachmentId = dbHelper::escape($con, $_REQUEST['id']);
    $versionid = getVersionIdFromAttachment($con, $attachmentId,$logger);
    $sessionid = $sHelper->getSessionIdFromVersionId($versionid,$con);
    $so = new sessionObject($sessionid);

    header("HTTP/1.0 501 Internal Server Error");


    if ($so->getSessionExist()) {
        $versionid = $so->getVersionid();
        if ($sHelper->isUserAllowedToEditSession($so)) {
            $attachments=$so->getAttachments();
            //print_r($so->getAttachments());
            if (in_array($attachmentId, array_keys($attachments))) {
                $sql = "";
                $sql .= "DELETE FROM mission_attachments ";
                $sql .= "WHERE  id = $attachmentId ";

                $result = dbHelper::sw_mysqli_execute($con, $sql, __FILE__, __LINE__);
                $logger->debug("Deleted attachment $attachmentId from session $sessionid", __FILE__, __LINE__);

                header("HTTP/1.0 200 OK");
                $response['code'] = ITEM_REMOVED;
                $response['text'] = "ITEM_REMOVED";
            } else {
                $logger->debug("Tried to delete a attachment id $attachmentId but it is not mapped to sessionid $sessionid", __FILE__, __LINE__);
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
        $logger->debug("Tried to delete a attachment $attachmentId but sessionid $sessionid does not exist", __FILE__, __LINE__);
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

function getVersionIdFromAttachment($con, $id,$logger)
{
    $sql = "select mission_versionid from `mission_attachments` WHERE id = $id";
    $result = dbHelper::sw_mysqli_execute($con,$sql,__FILE__,__LINE__);
    $row = mysqli_fetch_row($result);
    return $row[0];
}

