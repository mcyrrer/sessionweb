<?php
session_start();

require_once('../../../include/validatesession.inc');

error_reporting(0);

require_once('../../../config/db.php.inc');
require_once ('../../../include/db.php');
require_once ('../../../include/apistatuscodes.inc');
require_once ('../../../include/loggingsetup.php');



$response = array();
if ($_SESSION['useradmin'] == 1) {

    $con = getMySqlConnection();


    $normlizedsessiontime = 90;

    if (is_int((int)$_REQUEST["normlizedsessiontime"]) && (int)$_REQUEST["normlizedsessiontime"] != 0) {
        $normlizedsessiontime = $_REQUEST["normlizedsessiontime"];
    }
    else
    {
        header("HTTP/1.0 400 Bad Request");
        $response['code'] = ITEM_NOT_PROVIDED_IN_REQUEST;
        $response['text'] = "ITEM_NOT_PROVIDED_IN_REQUEST";
        $response['message'] = "Normalized Sessions time is equal to 0 or not an integer, will use default value 90 min";
        echo json_encode($response);
        exit();
    }

    $team = 0;
    if (strcmp($_REQUEST["team"], "checked") == 0) {
        $team = 1;
    }
    else
    {
        $team = 0;
    }

    $sprint = 0;
    if (strcmp($_REQUEST["sprint"], "checked") == 0) {
        $sprint = 1;
    }
    else
    {
        $sprint = 0;
    }

//    $teamsprint = 0;
//    if (strcmp($_REQUEST["teamsprint"], "checked") == 0) {
//        $teamsprint = 1;
//    }
//    else
//    {
//        $teamsprint = 0;
//    }

    $area = 0;
    if (strcmp($_REQUEST["area"], "checked") == 0) {
        $area = 1;
    }
    else
    {
        $area = 0;
    }

    $env = 0;
    if (strcmp($_REQUEST["env"], "checked") == 0) {
        $env = 1;
    }
    else
    {
        $env = 0;
    }

    $publicview = 0;
    if (strcmp($_REQUEST["publicview"], "checked") == 0) {
        $publicview = 1;
    }
    else
    {
        $publicview = 0;
    }

    $wordcloud = 0;
    if (strcmp($_REQUEST["wordcloud"], "checked") == 0) {
        $wordcloud = 1;
    }
    else
    {
        $wordcloud = 0;
    }

    $url_to_dms = $_REQUEST["url_to_dms"];

    $url_to_rms = $_REQUEST["url_to_rms"];

    $sqlUpdate = "";
    $sqlUpdate .= "UPDATE settings ";
    $sqlUpdate .= "SET    `normalized_session_time` = $normlizedsessiontime, ";
    $sqlUpdate .= "       `team` = '$team', ";
    $sqlUpdate .= "       `sprint` = '$sprint', ";
    $sqlUpdate .= "       `area` = '$area', ";
    $sqlUpdate .= "       `url_to_dms` = '$url_to_dms', ";
    $sqlUpdate .= "       `url_to_rms` = '$url_to_rms', ";
    $sqlUpdate .= "       `testenvironment` = '$env', ";
    $sqlUpdate .= "       `publicview` = '$publicview', ";
//    $sqlUpdate .= "       `teamsprint` = '$teamsprint', ";
    $sqlUpdate .= "       `wordcloud` = '$wordcloud' ";
    //$sqlUpdate .= "WHERE  `id` = '1'" ;

    $result = mysql_query($sqlUpdate);


        $result = mysql_query($sqlInsert);

        if (!$result) {
            if (mysql_errno() == 1062) {
                header("HTTP/1.0 409 Conflict");
                $response['code'] = ITEM_ALREADY_EXIST;
                $response['text'] = "ITEM_ALREADY_EXIST";

            }
            else
            {
                header("HTTP/1.0 500 Internal Server Error");
                $response['code'] = ITEM_NOT_ADDED;
                $response['text'] = "ITEM_NOT_ADDED";

            }
        }
        else
        {
            $logger->info($_SESSION['username']." added environment $envName");

            header("HTTP/1.0 201 Created");
            $response['code'] = ITEM_ADDED;
            $response['text'] = "ITEM_ADDED";
            $_SESSION['settings'] = getSessionWebSettings();
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