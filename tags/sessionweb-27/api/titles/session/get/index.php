<?php
session_start();

require_once('../../../../include/validatesession.inc');
require_once('../../../../include/apistatuscodes.inc');
require_once('../../../../classes/dbHelper.php');
require_once('../../../../classes/logging.php');


error_reporting(0);

require_once ('../../../../include/apistatuscodes.inc');

//TODO ADD a select for and return the title..."SELECT title from mission WHERE sessionid=110;"
getTitle();

function getTitle()
{

    $logger = new logging();
    $sessionId = $_REQUEST["sessionid"];
    $sessionId = trim($sessionId);


    if ($sessionId == null || strcmp($sessionId, "") == 0) {
        header("HTTP/1.0 400 Bad Request");
        $responseArray['code'] = PARAMETER_NOT_PROVIDED_IN_REQUEST;
        $responseArray['text'] = "PARAMETER_NOT_PROVIDED_IN_REQUEST";
        echo json_encode($responseArray);
    } else {
        $con = getMySqliConnection();
        $sessionId = dbHelper::escape($con, $sessionId);

        $sql = "SELECT title FROM mission WHERE sessionid=" . $sessionId . " AND project=" . $_SESSION['project'] . "";

        $result = dbHelper::sw_mysqli_execute($con, $sql, __FILE__, __LINE__);

        if (!$result) {
            $logger->error("Sql error", __FILE__, __LINE__);
            $logger->sql($sql, __FILE__, __LINE__);
            die('SQL ERROR');
        } else {
            if (mysqli_num_rows($result) == 1) {
                $row = $result->fetch_row();
                echo $row[0];
            }
            else
                {
                    $logger->debug("Session title not found (sessionid:".$sessionId.")", __FILE__, __LINE__);
                    header("HTTP/1.0 404 Not found");
                    $responseArray['code'] = ITEM_DOES_NOT_EXIST;
                    $responseArray['text'] = "ITEM_DOES_NOT_EXIST";
                }
        }
    }

}


?>