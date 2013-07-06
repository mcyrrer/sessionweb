<?php
session_start();

require_once('../../../../include/validatesession.inc');

error_reporting(0);

$response = "!!";

require_once ('../../../../include/apistatuscodes.inc');

if (file_exists("../../../../include/customfunctions.php.inc")) ;
{
    $reqId = $_REQUEST['reqId'];
    $reqId = trim($reqId);

    require_once('../../../../include/customfunctions.php.inc');
    $reqName = getRequirementNameFromServer($reqId);
//ToDO: fix if title not found with reponse....
    header("HTTP/1.0 200 Ok");
    echo $reqName;
    return;
}

header("HTTP/1.0 500 Internal Server Error");
$responseArray['code'] = SQL_ERROR;
$responseArray['text'] = "SQL_ERROR";
echo json_encode($responseArray);
?>