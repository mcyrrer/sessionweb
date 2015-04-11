<?php
/**
 * API to create a add a custom fields to a session, this api will delete all old values before adding the new once.
 * api/customfields/set/index.php?sessionid=[sessionid]&customfield=[custom field name]&(value=[value to add to db]||value[]=[value to add to db])

 */

require_once('../../../classes/autoloader.php');
require_once('../../../include/apistatuscodes.inc');

$logger = new logging();
$sHelper = new sessionHelper();
$dbm = new dbHelper();

$response['code'] = UNKNOWN_ERROR;
$response['text'] = "UNKNOWN_ERROR";

if (isset($_REQUEST['sessionid']) && isset($_REQUEST['customfield']) && isset($_REQUEST['value'])) {

    $con = $dbm->connectToLocalDb();

    $sessionid = dbHelper::escape($con, $_REQUEST['sessionid']);
    $customfield = dbHelper::escape($con, $_REQUEST['customfield']);

    if (is_array($_REQUEST['value'])) {
        $tmpArray = $_REQUEST['value'];
        $value = array();
        $i = 0;
        foreach ($_REQUEST['value'] as $aValue) {
            $value[$i] = dbHelper::escape($con, $tmpArray[$i]);
            $i++;
        }
    } else {
        $value = array();
        $value[0] = dbHelper::escape($con, $_REQUEST['value']);
    }

    if (StringHelper::str_startsWith($customfield, "id")) {
        $customfield = substr($customfield, 2);
        $logger->debug("Removed id from custom table name id provided in request", __FILE__, __LINE__);
    }

    $so = new sessionObject($sessionid);

    header("HTTP/1.0 501 Internal Server Error");

    if ($so->getSessionExist()) {
        $versionid = $so->getVersionid();
        if ($sHelper->isUserAllowedToEditSession($so)) {
            $sqlDelete = "DELETE FROM mission_custom WHERE customtablename='" . $customfield . "' AND versionid='" . $so->getVersionid() . "'";
            $result = $dbm->executeQuery($con, $sqlDelete);
            $logger->debug("Deleted all custom values (" . $customfield . ") for session $sessionid", __FILE__, __LINE__);
            foreach ($value as $aValue) {
                if (strcmp($aValue, "") != 0) {
                    $sql = "";
                    $sql .= "INSERT INTO mission_custom ";
                    $sql .= "            (versionid, ";
                    $sql .= "             customtablename, ";
                    $sql .= "             itemname) ";
                    $sql .= "VALUES      ( $versionid, ";
                    $sql .= "              '$customfield', ";
                    $sql .= "              '$aValue' ) ";

                    $result = $dbm->executeQuery($con, $sql);
                    $logger->debug("Added custom field $customfield-$aValue to session $sessionid", __FILE__, __LINE__);
                } else {
                    $logger->debug("Empty value in request, will not add it to db.", __FILE__, __LINE__);
                }
                header("HTTP/1.0 201 Created");
                $response['code'] = ITEM_ADDED;
                $response['text'] = "ITEM_ADDED";
            }
        } else {
            header("HTTP/1.0 401 Unauthorized");
            $response['code'] = UNAUTHORIZED;
            $response['text'] = "UNAUTHORIZED";
        }
    } else {
        $logger->debug("Tried to add custom field $customfield-$value but sessionid $sessionid does not exist", __FILE__, __LINE__);
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

