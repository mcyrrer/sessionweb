<?php
session_start();
require_once('../../../include/validatesession.inc');
require_once ('../../../include/db.php');
include_once('../../../config/db.php.inc');
include_once ('../../../include/commonFunctions.php.inc');

$con = getMySqlConnection();


$sessionDataToCopy = (getSessionData($_REQUEST["sessionid"]));

$sessionIdToCopy = $_REQUEST["sessionid"];

//Create a new random key
$sessionDataToCopy["publickey"] = md5(rand());

//Will create a new session id to map to a session
saveSession_CreateNewSessionId();

//Get the new session id for user x
$sessionid = saveSession_GetSessionIdForNewSession();
//Insert sessiondata to mission table

copySession_InsertSessionDataToDb($sessionid, $sessionDataToCopy);

//Get versionId from db
$versionid = saveSession_GetVersionIdForNewSession();

$versionIdToCopy = getSessionVersionId($sessionIdToCopy);

//Create missionstatus record in Db
$executed = false;
if ($_REQUEST["executed"] != "") {
    $executed = true;
}
saveSession_InsertSessionStatusToDb($versionid, $executed);

//Create metrics record for session
$metrics = array();
$metrics["setuppercent"] = $_REQUEST["setuppercent"];
$metrics["testpercent"] = $_REQUEST["testpercent"];
$metrics["bugpercent"] = $_REQUEST["bugpercent"];
$metrics["oppertunitypercent"] = $_REQUEST["oppertunitypercent"];
$metrics["duration"] = $_REQUEST["duration"];
saveSession_InsertSessionMetricsToDb($versionid, $metrics);


//Create areas for session

$areasFromOldSession = getSessionAreas($versionIdToCopy);

saveSession_InsertSessionAreaToDb($versionid, $areasFromOldSession);
$title = $sessionDataToCopy["title"];
echo "<center>";
echo "<img src='../../../pictures/edit-copy-9-medium.png' alt=''>";

echo "<h2>Copy session</h2>";
echo "<p>Title of copy:".$sessionDataToCopy["title"]."(Copy)</p>";
echo "<p>Copy created</p>";
echo "<div id='editCopy'><a href='session.php?sessionid=$sessionid&command=edit'>Edit session</a></div>";
echo "</center>";

?>