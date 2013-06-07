<?php
session_start();

require_once('../../../include/validatesession.inc');

error_reporting(0);

require_once ('../../../config/db.php.inc');
require_once ('../../../include/db.php');
require_once ('../../../include/apistatuscodes.inc');
require_once ('../../../include/loggingsetup.php');


$response = array();
if ($_SESSION['useradmin'] == 1) {

    if (isset($_REQUEST['from'])) {
        $from = $_REQUEST['from'];

        $re1='((?:(?:[1]{1}\\d{1}\\d{1}\\d{1})|(?:[2]{1}\\d{3}))[-:\\/.](?:[0]?[1-9]|[1][012])[-:\\/.](?:(?:[0-2]?\\d{1})|(?:[3][01]{1})))(?![\\d])';	# YYYYMMDD 1

        if (!($c = preg_match_all("/" . $re1 . "/is", $from, $matches))) {
            header("HTTP/1.0 400 Bad Request");
            $response['code'] = CORRECT_PARAMETER_NOT_PROVIDED_IN_REQUEST;
            $response['text'] = "CORRECT_PARAMETER_NOT_PROVIDED_IN_REQUEST";
            echo json_encode($response);
            exit();
        }
        $con = getMySqlConnection();

        $from = mysql_real_escape_string($from);

        $sql = 'UPDATE mission_status SET debriefed_timestamp=NOW(), closed=1 WHERE executed=1 AND debriefed=0 AND executed_timestamp <= "' . $from . ' 23:59:59"';

        $result = mysql_query($sql);

        if (!$result) {
            header("HTTP/1.0 500 Internal Server Error");
            $response['code'] = ITEM_NOT_ADDED;
            $response['text'] = "ITEM_NOT_ADDED";
            $logger->error($_SERVER["SCRIPT_NAME"] . ": SQL_ERROR: " . $sql);
        }
        else
        {
            header("HTTP/1.0 200 Ok");
            $response['code'] = Ok;
            $response['text'] = "Ok";
            $logger->info($_SESSION['username'] . " made a bulk close of sessions end date was $from");
        }

        mysql_close($con);
    }
    else
    {
        header("HTTP/1.0 400 Bad Request");
        $response['code'] = ITEM_NOT_PROVIDED_IN_REQUEST;
        $response['text'] = "ITEM_NOT_PROVIDED_IN_REQUEST";
    }
}
else
{
    header("HTTP/1.0 401 Unauthorized");
    $response['code'] = UNAUTHORIZED;
    $response['text'] = "UNAUTHORIZED";
}
echo json_encode($response);
?>
