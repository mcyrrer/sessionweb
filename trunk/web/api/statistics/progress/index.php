<?php
session_start();

require_once('../../../include/validatesession.inc');
include_once('../../../config/db.php.inc');
include_once('../../../include/commonFunctions.php.inc');
require_once('../../../include/db.php');
require_once('../../../classes/logging.php');

$logger = new logging();
if (isset($_REQUEST['name'])) {
    $valueName = $_REQUEST['name'];
} else {
    $valueName = null;
}
$con = getMySqlConnection();
if (isset($_REQUEST['sql'])) {
    $sqlPosted = urldecode($_REQUEST['sql']);
}

//TODO: refactor, remove view in favor of
$sql = "SELECT
    DATE(ms.executed_timestamp) AS vDATE,
    COUNT(*)                    AS vCOUNT,
    m.sessionid,
    unix_timestamp(ms.executed_timestamp) AS epoc
FROM
((mission m
JOIN
    mission_status ms)
JOIN
    mission_sessionmetrics sm)
WHERE
    (ms.executed = 1 OR ms.debriefed = 1 OR ms.closed = 1)
    AND
    m.versionid = ms.versionid
    AND m.versionid = sm.versionid";
if (isset($_REQUEST['tester'])) {
    if ($_SESSION['useradmin'] == 1) {
        $sql .= " AND username = '" . urldecode($_REQUEST['tester']) . "' ";
    }
}
if (isset($_REQUEST['team']) && strcmp($_REQUEST['team'], '') != 0) {
    if ($_SESSION['useradmin'] == 1) {
        $sql .= " AND teamname = '" . urldecode($_REQUEST['team']) . "' ";
    }
}
if (isset($_REQUEST['sprint']) && strcmp($_REQUEST['sprint'], '') != 0) {
    $sql .= " AND sprintname = '" . urldecode($_REQUEST['sprint']) . "'  ";
}

$sql .= " GROUP BY
    DATE(executed_timestamp)";

//
//$sql = "SELECT DATE(executed_timestamp) AS date, COUNT(*) AS count, sessionid,unix_timestamp(executed_timestamp) as epoc  ";
//$sql .= "FROM sessioninfo  ";
//$sql .= "WHERE (executed = 1 OR debriefed = 1 OR closed = 1)  ";
//if (isset($_REQUEST['tester'])) {
//    if ($_SESSION['useradmin'] == 1) {
//        $sql .= "AND username = '" . urldecode($_REQUEST['tester']) . "' ";
//    }
//}
//if (isset($_REQUEST['team']) && strcmp($_REQUEST['team'], '') != 0) {
//    if ($_SESSION['useradmin'] == 1) {
//        $sql .= "AND teamname = '" . urldecode($_REQUEST['team']) . "' ";
//    }
//}
//if (isset($_REQUEST['sprint']) && strcmp($_REQUEST['sprint'], '') != 0) {
//    $sql .= "AND sprintname = '" . urldecode($_REQUEST['sprint']) . "'  ";
//}
//if (isset($_REQUEST['sql'])) {
//    $sql .= " AND " . $sqlPosted;
//}
//
//$sql .= " GROUP BY DATE(executed_timestamp)  ";
//$sql .= "ORDER BY date;";

$logger->sql($sql, __FILE__, __LINE__);

$result = mysql_query($sql);

$result = mysql_query($sql);
$callback = $_REQUEST['callback'];
$returnString = $callback . "([\n";
$notfirsttime = false;
$count = 0;
while ($row = mysql_fetch_array($result)) {
    $count = $count + $row['vCOUNT'];
    if ($notfirsttime) {
        $returnString = $returnString . ",\n";
    }
    //" . $row['value'] . "
    $returnString = $returnString . "[" . $row['epoc'] . "000,$count]";
    $notfirsttime = true;
}
$returnString = $returnString . "\n]);";

echo $returnString;


?>