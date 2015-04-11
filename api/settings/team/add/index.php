<?php
require_once('../../../../classes/autoloader.php');
require_once('../../../../include/apistatuscodes.inc');

$logger = new logging();
$sHelper = new sessionHelper();
$dbm = new dbHelper();



$response = array();
if ($_SESSION['useradmin'] == 1) {

    if (isset($_REQUEST['team']) && strlen($_REQUEST['team']) > 0) {
        $teamName = $_REQUEST['team'];


        $con = $dbm->connectToLocalDb();

        $teamName = mysqli_real_escape_string($con, $teamName);
        $project = $_SESSION['project'];
        $sqlInsert = "INSERT INTO teamnames (teamname,project) VALUES ('$teamName','$project');
";


        $result = $dbm->executeQuery($con,$sqlInsert);

        if (!$result) {
            if (mysqli_errno($con) == 1062) {
                header("HTTP/1.0 409 Conflict");
                $response['code'] = ITEM_ALREADY_EXIST;
                $response['text'] = "ITEM_ALREADY_EXIST";

            } else {
                header("HTTP/1.0 500 Internal Server Error");
                $response['code'] = ITEM_NOT_ADDED;
                $response['text'] = "ITEM_NOT_ADDED";

            }
        } else {
            $logger->info($_SESSION['username'] . " added team $teamName");

            header("HTTP/1.0 201 Created");
            $response['code'] = ITEM_ADDED;
            $response['text'] = "ITEM_ADDED";

        }


    } else {
        header("HTTP/1.0 400 Bad Request");
        $response['code'] = ITEM_NOT_PROVIDED_IN_REQUEST;
        $response['text'] = "ITEM_NOT_PROVIDED_IN_REQUEST";
    }
} else {
    header("HTTP/1.0 401 Unauthorized");
    $response['code'] = UNAUTHORIZED;
    $response['text'] = "UNAUTHORIZED";
}
echo json_encode($response);
?>