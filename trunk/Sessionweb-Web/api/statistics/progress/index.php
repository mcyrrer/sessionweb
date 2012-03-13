<?php
include_once('../../../config/db.php.inc');
include_once ('../../../include/commonFunctions.php.inc');
require_once ('../../../include/db.php');

$valueName = $_REQUEST['name'];
$con = getMySqlConnection();

$sql = "SELECT DATE(executed_timestamp) AS date, COUNT(*) AS count, sessionid,unix_timestamp(executed_timestamp) as epoc  ";
$sql .= "FROM sessioninfo  ";
$sql .= "WHERE executed = 1 OR debriefed = 1 OR closed = 1  ";
if ($_REQUEST['tester'] != null) {
    if ($_SESSION['useradmin'] == 1) {
        $sql .= "AND username = '" . urldecode($_REQUEST['tester']) . "' ";
    }
}
if ($_REQUEST['team'] != null) {
    if ($_SESSION['useradmin'] == 1) {
        $sql .= "AND teamname = '" . urldecode($_REQUEST['team']) . "' ";
    }
}
if ($_REQUEST['sprint'] != null) {
    $sql .= "AND sprintname = '" . urldecode($_REQUEST['sprint']) . "' ";
}

$sql .= "GROUP BY DATE(executed_timestamp)  ";
$sql .= "ORDER BY date;";
//    echo "    <script type='text/javascript'>\n";

//    echo "    </script>\n";
$result = mysql_query($sql);

//$sql = "SELECT value,unix_timestamp(timestamp) as epoc  FROM sdigraph.graphdata WHERE name = '".$valueName."' ORDER by timestamp ASC";

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