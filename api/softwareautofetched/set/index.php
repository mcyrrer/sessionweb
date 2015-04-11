<?php
require_once('../../../classes/autoloader.php');
require_once('../../../include/apistatuscodes.inc');

$logger = new logging();
$sHelper = new sessionHelper();
$dbm = new dbHelper();

$con = $dbm->connectToLocalDb();

$environment = dbHelper::escape($con, $_REQUEST['env']);
$sessionid = dbHelper::escape($con, $_REQUEST['sessionid']);
$so = new sessionObject($sessionid);
$versionid = $so->getVersionid();
$sessionStatusArray = $sHelper->getSessionStatus($versionid, $con);

if ($sHelper->isUserAllowedToEditSession($so)) {
    $status = '';
    if ($sessionStatusArray['debriefed'] == 1)
        $status = 'debriefed';
    elseif ($sessionStatusArray['executed'] == 1)
        $status = 'executed';
    else
        $status = 'not executed';

    $row = TestEnvironmentHelper::getTestEnvironmentInformation($environment);
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
        $content = null;
        $errorlevel = error_reporting();
        try {
            $content = file_get_contents($url, false, $context);
            if ($content == false) {
                throw new Exception('No route to ' . $url);
            }
        } catch (Exception $e) {
            $logger->error($e, __FILE__, __LINE__);
        }
        error_reporting($errorlevel);

        if ($content != false) {

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
            $dbm->executeQuery($con, $var1);

            $sql = "SELECT id, updated,environment FROM softwareuseautofetched ORDER BY id DESC LIMIT 0,1";
            $result = $dbm->executeQuery($con, $sql);
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);


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
} else {
    $logger->warn($_SESSION['username'] . " tried to remove autofetched sw record with id ," . $id, __FILE__, __LINE__);
    header("HTTP/1.0 401 Unauthorized");
    $response['code'] = UNAUTHORIZED;
    $response['text'] = "UNAUTHORIZED";
}

echo json_encode($response);
//Get id for added software
//echo it to div tag (implement new api called viewswrunnung....

?>