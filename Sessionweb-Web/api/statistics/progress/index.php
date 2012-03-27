<?php
session_start();

require_once('../../../include/validatesession.inc');
include_once('../../../config/db.php.inc');
include_once ('../../../include/commonFunctions.php.inc');
require_once ('../../../include/db.php');

$valueName = $_REQUEST['name'];
$con = getMySqlConnection();

$sql = "SELECT DATE(executed_timestamp) AS date, COUNT(*) AS count, sessionid,unix_timestamp(executed_timestamp) as epoc  ";
$sql .= "FROM sessioninfo  ";
$sql .= "WHERE (executed = 1 OR debriefed = 1 OR closed = 1)  ";
if (isset($_REQUEST['tester'])) {
    if ($_SESSION['useradmin'] == 1) {
        $sql .= "AND username = '" . urldecode($_REQUEST['tester']) . "' ";
    }
}
if (strcmp($_REQUEST['team'],'') != 0) {
    if ($_SESSION['useradmin'] == 1) {
        $sql .= "AND teamname = '" . urldecode($_REQUEST['team']) . "' ";
    }
}
if (strcmp($_REQUEST['sprint'],'') != 0) {
    $sql .= "AND sprintname = '" . urldecode($_REQUEST['sprint']) . "'  ";
}

$sql .= "GROUP BY DATE(executed_timestamp)  ";
$sql .= "ORDER BY date;";
//TODO: FIX THAT PARAMETERS IS REALY POPULATED.!!!
//print_r($_SESSION);
//echo $sql;

$result = mysql_query($sql);

$result = mysql_query($sql);
$callback = $_REQUEST['callback'];
$returnString = $callback."([\n";
$notfirsttime = false;
$count = 0;
while ($row = mysql_fetch_array($result))
{
    $count = $count + $row['count'];
    if ($notfirsttime) {
        $returnString = $returnString . ",\n";
    }
    //" . $row['value'] . "
    $returnString = $returnString . "[" . $row['epoc'] . "000,$count]";
    $notfirsttime = true;
}
$returnString = $returnString . "\n]);";

echo  $returnString;


?>