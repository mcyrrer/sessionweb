<?php

require_once('../../../classes/autoloader.php');
require_once('../../../include/apistatuscodes.inc');

error_reporting(0);

$logger = new logging();


$response = array();


if (isset($_REQUEST['sessionid'])) {

    $dbm = new dbHelper();
    $con = $dbm->connectToLocalDb();

    $sessionid = $dbm->escape($con, $_REQUEST['sessionid']);
    $logger->debug("Api get session " . $sessionid, __FILE__, __LINE__);
    $session = new sessionObject($sessionid);
    $response = $session->toJson();
    header("HTTP/1.0 200 Ok");

} else {
    header("HTTP/1.0 400 Bad Request");
    $response['code'] = ITEM_NOT_PROVIDED_IN_REQUEST;
    $response['text'] = "ITEM_NOT_PROVIDED_IN_REQUEST";
}

echo $response;
?>