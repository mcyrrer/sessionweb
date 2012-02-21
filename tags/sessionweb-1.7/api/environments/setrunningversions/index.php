<?php
session_start();
if (!session_is_registered(myusername)) {
    header("HTTP/1.0 403 Forbidden");
    echo "No valid user session is active";
}

error_reporting(0);

include_once('../../../config/db.php.inc');
include_once ('../../../include/commonFunctions.php.inc');

$con=getMySqlConnection();


$environment = $_REQUEST['env'];
$sessionid = $_REQUEST['sessionid'];
$versionid = getSessionVersionId($sessionid);
$sessionStatusArray = getSessionStatus($versionid);

$status = '';
if ($sessionStatusArray['debriefed'] == 1)
    $status = 'debriefed';
elseif ($sessionStatusArray['executed'] == 1)
    $status = 'executed';
else
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
        $con=getMySqlConnection();
        $content = mysql_real_escape_string($content);
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

        mysql_query($var1);
        //echo $var1;

        $sql = "SELECT id, updated FROM softwareuseautofetched ORDER BY id DESC LIMIT 0,1";
        $result = mysql_query($sql);
        $row = mysql_fetch_row($result);
        $returnArray = array();
        $returnArray['id'] = $row[0];
        $returnArray['date'] = $row[1];
        echo json_encode($returnArray);
        mysql_close($con);
    }
    else
    {
        echo json_encode("1"); //Authorization failed, please check settings for test environment.";
    }

}
else
{
    echo json_encode("2"); //No valid url found for this environment, please update config.";
}

//Get id for added software
//echo it to div tag (implement new api called viewswrunnung....

?>