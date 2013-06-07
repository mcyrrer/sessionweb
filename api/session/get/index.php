<?php
session_start();

require_once('../../../include/validatesession.inc');

error_reporting(0);

require_once('../../../config/db.php.inc');
require_once ('../../../include/db.php');
require_once ('../../../include/apistatuscodes.inc');
require_once ('../../../classes/sessionObject.php');
require_once ('../../../classes/logging.php');

$logger = new logging();


$response = array();


if (isset($_REQUEST['sessionid'])) {

    $dbManager = new dbHelper();
    $con = $dbManager->db_getMySqliConnection();

    $sessionid = $dbManager->escape($con,$_REQUEST['sessionid']);
    $logger->debug("Api get session ".$sessionid,__FILE__,__LINE__);
    $session = new sessionObject($sessionid);
    $response = $session->toJson();
    header("HTTP/1.0 200 Ok");

}
else
{
    header("HTTP/1.0 400 Bad Request");
    $response['code'] = ITEM_NOT_PROVIDED_IN_REQUEST;
    $response['text'] = "ITEM_NOT_PROVIDED_IN_REQUEST";
}

echo $response;
?>