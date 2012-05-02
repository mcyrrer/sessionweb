<?php
session_start();

require_once('../../../include/validatesession.inc');

error_reporting(0);

require_once('../../../config/db.php.inc');
require_once ('../../../include/db.php');
require_once ('../../../include/apistatuscodes.inc');
require_once ('../../../classes/session.php');


$response = array();


if (isset($_REQUEST['sessionid'])) {
    $sessionid = mysql_real_escape_string($_REQUEST['sessionid']);
    $session = new session(3676);

    $response = $session->getSession();

    header("HTTP/1.0 200 Ok");
}
else
{
    header("HTTP/1.0 400 Bad Request");
    $response['code'] = ITEM_NOT_PROVIDED_IN_REQUEST;
    $response['text'] = "ITEM_NOT_PROVIDED_IN_REQUEST";
}

echo json_encode($response);
?>