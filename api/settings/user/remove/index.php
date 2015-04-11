<?php
require_once('../../../../classes/autoloader.php');
require_once('../../../../include/apistatuscodes.inc');

$logger = new logging();
$sHelper = new sessionHelper();
$dbm = new dbHelper();

$response = array();

if ($_SESSION['useradmin'] == 1) {
    if (isset($_REQUEST['username'])) {

        if (QueryHelper::doesUserExist($_REQUEST['username'])) {
            $response = removeUserFromDb($response);
        } else {
            $logger->debug("Tried to delete non-existing user " . $_REQUEST['username'], __FILE__, __LINE__);
            header("HTTP/1.0 400 Bad Request");
            $response['code'] = USER_DOES_NOT_EXIST;
            $response['text'] = "USER_DOES_NOT_EXIST";
        }
    } else {
        $logger->debug("CORRECT_PARAMETER_NOT_PROVIDED_IN_REQUEST", __FILE__, __LINE__);
        header("HTTP/1.0 400 Bad Request");
        $response['code'] = CORRECT_PARAMETER_NOT_PROVIDED_IN_REQUEST;
        $response['text'] = "CORRECT_PARAMETER_NOT_PROVIDED_IN_REQUEST";
    }


} else {
    $logger->debug("Unauthorized", __FILE__, __LINE__);
    header("HTTP/1.0 401 Unauthorized");
    $response['code'] = UNAUTHORIZED;
    $response['text'] = "UNAUTHORIZED";
}
echo json_encode($response);


function removeUserFromDb($response)
{
    $logger = new logging();
    $dbm = new dbHelper();
    $con = $dbm->connectToLocalDb();

    $username = mysqli_real_escape_string($con, $_REQUEST["username"]);


    $sqlDelete = "UPDATE members SET active=0, deleted=1 WHERE username='$username'";
    $result = $dbm->executeQuery($con, $sqlDelete);
    $logger->info(" user $username marked as deleted and inactive", __FILE__, __LINE__);

    header("HTTP/1.0 200 OK");

    $response['code'] = USER_DELETED;
    $response['text'] = "USER_DELETED";

    return $response;
}

?>