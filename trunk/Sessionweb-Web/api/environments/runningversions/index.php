<?php
session_start();
if (!session_is_registered(myusername)) {
    header("HTTP/1.0 403 Forbidden");
    echo "No valid user session is active";
}

include_once('../../../config/db.php.inc');
include_once ('../../../include/commonFunctions.php.inc');

$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("cannot connect");
mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

$environment = $_REQUEST['env'];
$sessionid = $_REQUEST['sessionid'];
$versionid = getSessionVersionId($sessionid);
$sessionStatusArray = getSessionStatus($versionid);

$status = '';
if($sessionStatusArray['debriefed']==1)
   $status = 'debriefed';
elseif($sessionStatusArray['executed']==1)
    $status = 'executed';
else
    $status = 'not executed';

$row = getTestEnvironmentInformation($environment);
$envName = $row[0];
$url = $row[1];
$username = $row[2];
$password = $row[3];

if ($url != null) {
    if ($username != null && $password != null) {
        $context = stream_context_create(array(
            'http' => array(
                'header' => "Authorization: Basic " . base64_encode("$username:$password")
            )
        ));
    }
    $content = file_get_contents($url);

    $con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("cannot connect");
    mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");
    $content=mysql_real_escape_string($content);
    $var1 = "";
    $var1 .= "INSERT INTO softwareuseautofetched ";
    $var1 .= "            (versionid, ";
    $var1 .= "             versions, ";
    $var1 .= "             missionstatus) ";
    $var1 .= "VALUES      ($versionid, ";
    $var1 .= "             '$content', ";
    $var1 .= "             '$status')" ;
    mysql_query($var1);
    echo $var1;
    mysql_close($con);
}
else
{
    echo "No valid url found for this environment, please update config.";
}

//Get id for added software
//echo it to div tag (implement new api called viewswrunnung....

?>