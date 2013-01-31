<?php
session_start();

require_once('../../../../include/validatesession.inc');

error_reporting(0);

$response = "!!";

require_once ('../../../../include/apistatuscodes.inc');

if (file_exists("../../../../include/customfunctions.php.inc")) ;
{
    $bugId = $_REQUEST['$bugId'];

    require_once('../../../../include/customfunctions.php.inc');
    $reqName = getBugNameFromServer($bugId);

    header("HTTP/1.0 200 Ok");
    echo $bugId;
    return;
}

header("HTTP/1.0 500 Internal Server Error");
$responseArray['code'] = SQL_ERROR;
$responseArray['text'] = "SQL_ERROR";
echo json_encode($responseArray);




?>