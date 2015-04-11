<?php
require_once('../../../../classes/autoloader.php');
require_once('../../../../include/apistatuscodes.inc');

$logger = new logging();

//error_reporting(0);

$bugId = $_REQUEST['id'];
$bugId = trim($bugId);

if (file_exists("../../../../include/customfunctions.php.inc")) {
    require_once('../../../../include/customfunctions.php.inc');
    $bugName = getBugNameFromServer($bugId);
    $logger->debug("Unlinked bug id ".$bugId);
    header("HTTP/1.0 200 Ok");
    echo $bugName;
    return;
} else {
    echo $bugId;
    return;
}

header("HTTP/1.0 500 Internal Server Error");
$responseArray['code'] = SQL_ERROR;
$responseArray['text'] = "SQL_ERROR";
echo json_encode($responseArray);




?>