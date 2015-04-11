<?php
require_once('../../../../../classes/autoloader.php');
require_once('../../../../../include/apistatuscodes.inc');

$logger = new logging();
$sHelper = new sessionHelper();
$dbm = new dbHelper();

$response = array();

if ($_SESSION['useradmin'] == 1) {
    $con = $dbm->connectToLocalDb();

    $publicview = mysqli_real_escape_string($con,$_REQUEST['publicview']);
    $env = mysqli_real_escape_string($con,$_REQUEST['env']);
    $area = mysqli_real_escape_string($con,$_REQUEST['area']);
    $sprint = mysqli_real_escape_string($con,$_REQUEST['sprint']);
    $team = mysqli_real_escape_string($con,$_REQUEST['team']);
    $url_to_rms = mysqli_real_escape_string($con,$_REQUEST['url_to_rms']);
    $url_to_dms = mysqli_real_escape_string($con,$_REQUEST['url_to_dms']);
    $teamsprint = mysqli_real_escape_string($con,$_REQUEST['teamsprint']);
    $wordcloud = mysqli_real_escape_string($con,$_REQUEST["wordcloud"]);


    $normlizedsessiontime = 90;

    if (is_int((int)$_REQUEST["normlizedsessiontime"]) && (int)$_REQUEST["normlizedsessiontime"] != 0) {
        $normlizedsessiontime = $_REQUEST["normlizedsessiontime"];
    } else {
        header("HTTP/1.0 400 Bad Request");
        $response['code'] = CORRECT_PARAMETER_NOT_PROVIDED_IN_REQUEST;
        $response['text'] = "normlizedsessiontime need to be an integer";
    }

    if (strcmp($team, "checked") == 0) {
        $team = 1;
    } else {
        $team = 0;
    }

    if (strcmp($sprint, "checked") == 0) {
        $sprint = 1;
    } else {
        $sprint = 0;
    }

    if (strcmp($teamsprint, "checked") == 0) {
        $teamsprint = 1;
    } else {
        $teamsprint = 0;
    }

    if (strcmp($area, "checked") == 0) {
        $area = 1;
    } else {
        $area = 0;
    }

    if (strcmp($env, "checked") == 0) {
        $env = 1;
    } else {
        $env = 0;
    }

    if (strcmp($publicview, "checked") == 0) {
        $publicview = 1;
    } else {
        $publicview = 0;
    }

    if (strcmp($wordcloud, "checked") == 0) {
        $wordcloud = 1;
    } else {
        $wordcloud = 0;
    }

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
    $sqlUpdate .= "       `teamsprint` = '$teamsprint', ";
    $sqlUpdate .= "       `wordcloud` = '$wordcloud' ";

    $result = $dbm->executeQuery($con,$sqlUpdate);


    $result = $dbm->executeQuery($con,$sqlUpdate);

    if (!$result) {
        if (mysqli_errno($con) == 1062) {
            header("HTTP/1.0 409 Conflict");
            $response['code'] = ITEM_ALREADY_EXIST;
            $response['text'] = "ITEM_ALREADY_EXIST";
        } else {
            header("HTTP/1.0 500 Internal Server Error");
            $response['code'] = SQL_ERROR;
            $response['text'] = "SQL_ERROR";
            $logger->error($_SERVER["SCRIPT_NAME"] . ": SQL_ERROR: " . $sqlUpdate);
        }
    } else {
        $logger->info($_SESSION['username'] . " Updated app settings");
        header("HTTP/1.0 201 Created");
        $response['code'] = ITEM_ADDED;
        $response['text'] = "ITEM_ADDED";
        $_SESSION['settings'] = getSessionWebSettings();
    }


} else {
    header("HTTP/1.0 401 Unauthorized");
    $response['code'] = UNAUTHORIZED;
    $response['text'] = "UNAUTHORIZED";
}
echo json_encode($response);
?>