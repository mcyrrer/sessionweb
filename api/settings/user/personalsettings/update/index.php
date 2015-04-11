<?php
require_once('../../../../../classes/autoloader.php');
require_once('../../../../../include/apistatuscodes.inc');

$logger = new logging();
$sHelper = new sessionHelper();
$dbm = new dbHelper();



$response = array();

if (isset($_REQUEST['listsettings'])) {
    $con = $dbm->connectToLocalDb();

    $envName = mysqli_real_escape_string($con, $_REQUEST['environment']);
    $envautofetchurl = mysqli_real_escape_string($con, $_REQUEST["url"]);
    $envusername = mysqli_real_escape_string($con, $_REQUEST["username"]);
    $envpassword = mysqli_real_escape_string($con, $_REQUEST["password"]);

    $sqlUpdate = "";
    $sqlUpdate .= "UPDATE `user_settings` ";
    $sqlUpdate .= "SET    `list_view` ='" . mysqli_real_escape_string($con, $_REQUEST['listsettings']) . "' ,";
    if ($_REQUEST['team'] != '')
        $sqlUpdate .= "       `default_team` ='" . $_REQUEST['team'] . "' , ";
    else
        $sqlUpdate .= "       `default_team` =null , ";

    if ($_REQUEST['sprint'] != '')
        $sqlUpdate .= "       `default_sprint` ='" . $_REQUEST['sprint'] . "' , ";
    else
        $sqlUpdate .= "       `default_sprint` =null , ";

    if ($_REQUEST['teamsprint'] != '')
        $sqlUpdate .= "       `default_teamsprint` ='" . $_REQUEST['teamsprint'] . "' , ";
    else
        $sqlUpdate .= "       `default_teamsprint` =null , ";

    if ($_REQUEST['area'] != '')
        $sqlUpdate .= "       `default_area` ='" . $_REQUEST['area'] . "' , ";
    else
        $sqlUpdate .= "       `default_area` =null , ";

    if ($_REQUEST['autosave'] == 'true')
        $sqlUpdate .= "       `autosave` ='1' ";
    else
        $sqlUpdate .= "       `autosave` ='0' ";
    $sqlUpdate .= "WHERE  `user_settings`.`username` = '" . $_SESSION['username'] . "' ";

    $result = $dbm->executeQuery($con,$sqlUpdate);

    if (!$result) {
        if (mysqli_errno($con) == 1062) {
            header("HTTP/1.0 409 Conflict");
            $response['code'] = ITEM_ALREADY_EXIST;
            $response['text'] = "ITEM_ALREADY_EXIST";

        } else {
            header("HTTP/1.0 500 Internal Server Error");
            $response['code'] = ITEM_NOT_UPDATED;
            $response['text'] = "ITEM_NOT_UPDATED";
        }
    } else {
        $logger->info($_SESSION['username'] . " change his/her settings");

        header("HTTP/1.0 201 Created");
        $response['code'] = ITEM_UPDATED;
        $response['text'] = "ITEM_UPDATED";
        $_SESSION['settings'] = getSessionWebSettings();


    }


} else {
    header("HTTP/1.0 400 Bad Request");
    $response['code'] = ITEM_NOT_PROVIDED_IN_REQUEST;
    $response['text'] = "ITEM_NOT_PROVIDED_IN_REQUEST";
}

echo json_encode($response);
?>