<?php
session_start();

require_once('../../../../include/validatesession.inc');

error_reporting(0);

require_once ('../../../../include/apistatuscodes.inc');

if (file_exists("../../../../include/customfunctions.php.inc")) ;
{
    $bugId = $_REQUEST['id'];
    $bugId = trim($bugId);

    require_once('../../../../include/customfunctions.php.inc');
    $bugName = getBugNameFromServer($bugId);

    header("HTTP/1.0 200 Ok");
    echo $bugName;
    return;
}

header("HTTP/1.0 500 Internal Server Error");
$responseArray['code'] = SQL_ERROR;
$responseArray['text'] = "SQL_ERROR";
echo json_encode($responseArray);




?>