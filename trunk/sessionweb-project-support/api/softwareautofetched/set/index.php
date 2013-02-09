<?php
session_start();

require_once('../../../include/validatesession.inc');

error_reporting(0);

require_once('../../../config/db.php.inc');
require_once ('../../../include/commonFunctions.php.inc');
require_once ('../../../include/db.php');
require_once('../../../classes/sessionHelper.php');
require_once('../../../classes/logging.php');
require_once('../../../classes/dbHelper.php');

$logger = new logging();
$sHelper = new sessionHelper();
$dbManager = new dbHelper();

$con = $dbManager->db_getMySqliConnection();

$environment = dbHelper::escape($con, $_REQUEST['env']);
$sessionid = dbHelper::escape($con, $_REQUEST['sessionid']);
$so = new sessionObject($sessionid);
$versionid = $so->getVersionid();
$sessionStatusArray = $sHelper->getSessionStatus($versionid,$con);

if ($sHelper->isUserAllowedToEditSession($so)) {
    $status = '';
    if ($sessionStatusArray['debriefed'] == 1)
        $status = 'debriefed';
    elseif ($sessionStatusArray['executed'] == 1)
        $status = 'executed'; else
        $status = 'not executed';

    $row = getTestEnvironmentInformation($environment);
    $envName = $row[0];
    $url = $row[1];
    $username = $row[2];
    $password = $row[3];

    if ($url != null && $url != "") {
        if ($username != null && $password != null) {
            $context = stream_context_create(array(
                'http' => array(
                    'header' => "Authorization: Basic " . base64_encode("$username:$password")
                )
            ));
        }

        $content = file_get_contents($url, false, $context);
        if ($content != null) {

            $content = dbHelper::escape($con, $content);
            $var1 = "";
            $var1 .= "INSERT INTO softwareuseautofetched ";
            $var1 .= "            (versionid, ";
            $var1 .= "             versions, ";
            $var1 .= "             missionstatus, ";
            $var1 .= "             environment) ";

            $var1 .= "VALUES      ($versionid, ";
            $var1 .= "             '$content', ";
            $var1 .= "             '$status', ";
            $var1 .= "             '$envName')";

            dbHelper::sw_mysqli_execute($con, $var1, __FILE__, __LINE__);

            $sql = "SELECT id, updated,environment FROM softwareuseautofetched ORDER BY id DESC LIMIT 0,1";
            $result = dbHelper::sw_mysqli_execute($con, $sql, __FILE__, __LINE__);
            //$row = mysqli_fetch_row($con, $result);
            $row= mysqli_fetch_array($result,MYSQLI_ASSOC);


            $response = $row;
            mysqli_close($con);
        } else {
            header("HTTP/1.0 401 Unauthorized");
            $response['code'] = EXTERNAL_WEB_UNAUTHORIZED;
            $response['text'] = "EXTERNAL_WEB_UNAUTHORIZED";
        }

    } else {
        header("HTTP/1.0 404 Not Found");
        $response['code'] = EXTERNAL_WEB_404;
        $response['text'] = "EXTERNAL_WEB_404";
    }
}
else {
    $logger->warn($_SESSION['username'] . " tried to remove autofetched sw record with id ," . $id, __FILE__, __LINE__);
    header("HTTP/1.0 401 Unauthorized");
    $response['code'] = UNAUTHORIZED;
    $response['text'] = "UNAUTHORIZED";
}

echo json_encode($response);
//Get id for added software
//echo it to div tag (implement new api called viewswrunnung....

?>