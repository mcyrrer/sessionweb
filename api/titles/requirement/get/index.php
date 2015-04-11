<?php
require_once('../../../../classes/autoloader.php');
require_once('../../../../include/apistatuscodes.inc');

$logger = new logging();

//error_reporting(0);

$reqId = $_REQUEST['reqId'];
$reqId = trim($reqId);

if (file_exists("../../../../include/customfunctions.php.inc")) {
    require_once('../../../../include/customfunctions.php.inc');
    $reqName = getRequirementNameFromServer($reqId);
    $logger->debug("Unlinked req id ".$reqId);
    header("HTTP/1.0 200 Ok");
    echo $reqName;
    return;
} else {
    echo $reqId;
    return;
}

header("HTTP/1.0 500 Internal Server Error");
$responseArray['code'] = SQL_ERROR;
$responseArray['text'] = "SQL_ERROR";
echo json_encode($responseArray);
?>