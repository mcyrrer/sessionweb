<?php
require_once('../../../../classes/autoloader.php');
require_once('../../../../include/apistatuscodes.inc');




//error_reporting(0);

getTitle();

function getTitle()
{
    $logger = new logging();
    $sHelper = new sessionHelper();
    $dbm = new dbHelper();

    $sessionId = $_REQUEST["sessionid"];
    $sessionId = trim($sessionId);


    if ($sessionId == null || strcmp($sessionId, "") == 0) {
        header("HTTP/1.0 400 Bad Request");
        $responseArray['code'] = PARAMETER_NOT_PROVIDED_IN_REQUEST;
        $responseArray['text'] = "PARAMETER_NOT_PROVIDED_IN_REQUEST";
        echo json_encode($responseArray);
    } else {
        $con = $dbm->connectToLocalDb();

        $sessionId = dbHelper::escape($con, $sessionId);

        $sql = "SELECT title FROM mission WHERE sessionid=" . $sessionId . " AND project=" . $_SESSION['project'] . "";
        $result = $dbm->executeQuery($con, $sql);

        if (!$result) {
            $logger->error("Sql error", __FILE__, __LINE__);
            $logger->sql($sql, __FILE__, __LINE__);
            die('SQL ERROR');
        } else {
            if (mysqli_num_rows($result) == 1) {
                $row = $result->fetch_row();
                echo $row[0];
            } else {
                $logger->debug("Session title not found (sessionid:" . $sessionId . ")", __FILE__, __LINE__);
                header("HTTP/1.0 404 Not found");
                $responseArray['code'] = ITEM_DOES_NOT_EXIST;
                $responseArray['text'] = "ITEM_DOES_NOT_EXIST";
            }
        }
    }

}


?>