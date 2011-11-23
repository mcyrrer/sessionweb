<?php
function getSessionMetricsPieChartUrl($versionid)
{

    $sqlSelect = "";
    $sqlSelect .= "SELECT * ";
    $sqlSelect .= "FROM   mission_sessionmetrics ";
    $sqlSelect .= "WHERE  versionid = $versionid";

    $result = mysql_query($sqlSelect);

    if (!$result) {
        echo "getSessionMetrics: " . mysql_error() . "<br/>";
    }
    else
    {
        $row = mysql_fetch_array($result);

        $setup_percent = $row["setup_percent"];
        $test_percent = $row["test_percent"];
        $bug_percent = $row["bug_percent"];
        $opportunity_percent = $row["opportunity_percent"];

        return createPercentPieChart($setup_percent, $test_percent, $bug_percent, $opportunity_percent);
    }
}

function saveSession_CreateNewSessionId()
{
    $sqlInsert = "";
    $sqlInsert .= "INSERT INTO sessionid ";
    $sqlInsert .= "            (`createdby`) ";
    $sqlInsert .= "VALUES      ('" . $_SESSION['username'] . "') ";

    $result = mysql_query($sqlInsert);

    if (!$result) {
        echo "saveSession_CreateNewSessionId: " . mysql_error() . "<br/>";
    }
}

function saveSession_GetSessionIdForNewSession()
{
    $sqlSelect = "";
    $sqlSelect .= "SELECT * ";
    $sqlSelect .= "FROM   sessionid ";
    $sqlSelect .= "WHERE  createdby = '" . $_SESSION['username'] . "' ";
    $sqlSelect .= "ORDER  BY sessionid DESC ";
    $sqlSelect .= "LIMIT  1";

    $result = mysql_query($sqlSelect);

    if ($result) {
        $row = mysql_fetch_array($result);
        $sessionid = $row["sessionid"];
    }
    else
    {
        echo "saveSession_GetSessionIdForNewSession: " . mysql_error() . "<br/>";
    }

    return $sessionid;
}


function saveSession_InsertSessionDataToDb($sessionid)
{


    if ($_REQUEST["title"] == "") {
        $_REQUEST["title"] = "Unnamed Session";
        echo "<b>Warning:</b> Session has no title, it will be named \"Unnamed Session\"<br/>\n";
    }

    $sqlInsert = "";
    $sqlInsert .= "INSERT INTO mission ";
    $sqlInsert .= "            (`sessionid`, ";
    $sqlInsert .= "             `title`, ";
    $sqlInsert .= "             `charter`, ";
    $sqlInsert .= "             `notes`, ";
    $sqlInsert .= "             `username`, ";
    $sqlInsert .= "             `sprintname`, ";
    $sqlInsert .= "             `teamsprintname`, ";
    $sqlInsert .= "             `testenvironment`, ";
    $sqlInsert .= "             `software`, ";
    $sqlInsert .= "             `teamname`, ";
    $sqlInsert .= "             `publickey`) ";
    $sqlInsert .= "VALUES      ('$sessionid', ";
    $sqlInsert .= "             '" . mysql_real_escape_string($_REQUEST["title"]) . "', ";
    $sqlInsert .= "             '" . mysql_real_escape_string($_REQUEST["charter"]) . "', ";
    $sqlInsert .= "             '" . mysql_real_escape_string($_REQUEST["notes"]) . "', ";
    $sqlInsert .= "             '" . $_SESSION['username'] . "', ";
    if ($_REQUEST['sprint'] == "") {
        $sqlInsert .= "             null, ";
    }
    else
    {
        $sqlInsert .= "             '" . mysql_real_escape_string($_REQUEST['sprint']) . "', ";
    }
    if ($_REQUEST['teamsprint'] == "") {
        $sqlInsert .= "             null, ";
    }
    else
    {

        $sqlInsert .= "             '" . mysql_real_escape_string($_REQUEST['teamsprint']) . "', ";
    }
    if ($_REQUEST['testenv'] == "") {
        $sqlInsert .= "             null, ";
    }
    else
    {
        $sqlInsert .= "             '" . mysql_real_escape_string($_REQUEST['testenv']) . "', ";
    }
    if ($_REQUEST['textareaswundertest'] == "") {
        $sqlInsert .= "             null, ";
    }
    else
    {
        $sqlInsert .= "             '" . mysql_real_escape_string($_REQUEST['textareaswundertest']) . "', ";
    }
    if ($_REQUEST['team'] == "") {
        $sqlInsert .= "             null, ";
    }
    else
    {
        $sqlInsert .= "             '" . mysql_real_escape_string($_REQUEST['team']) . "', ";
    }
    $sqlInsert .= "             '" . $_REQUEST["publickey"] . "' ";
    $sqlInsert .= ") ";

    //  echo $sqlInsert;

    $result = mysql_query($sqlInsert);

    if (!$result) {
        echo "saveSession_InsertSessionDataToDb: " . mysql_error() . "<br/>";
    }
}

function copySession_InsertSessionDataToDb($sessionid, $sessionToCopy)
{
    // print_r($sessionToCopy);

    $sqlInsert = "";
    $sqlInsert .= "INSERT INTO mission ";
    $sqlInsert .= "            (`sessionid`, ";
    $sqlInsert .= "             `title`, ";
    $sqlInsert .= "             `charter`, ";
    $sqlInsert .= "             `username`, ";
    $sqlInsert .= "             `sprintname`, ";
    $sqlInsert .= "             `teamsprintname`, ";
    $sqlInsert .= "             `testenvironment`, ";
    $sqlInsert .= "             `teamname`, ";
    $sqlInsert .= "             `publickey`) ";
    $sqlInsert .= "VALUES      ('$sessionid', ";
    $sqlInsert .= "             '" . $sessionToCopy["title"] . "(COPY) ', ";
    $sqlInsert .= "             '" . $sessionToCopy["charter"] . "', ";
    $sqlInsert .= "             '" . $_SESSION['username'] . "', ";
    if ($sessionToCopy['sprintname'] == "") {
        $sqlInsert .= "             null, ";
    }
    else
    {
        $sqlInsert .= "             '" . $sessionToCopy['sprintname'] . "', ";
    }
    if ($sessionToCopy['teamsprintname'] == "") {
        $sqlInsert .= "             null, ";
    }
    else
    {

        $sqlInsert .= "             '" . $sessionToCopy['teamsprintname'] . "', ";
    }
    if ($sessionToCopy['testenvironment'] == "") {
        $sqlInsert .= "             null, ";
    }
    else
    {
        $sqlInsert .= "             '" . $sessionToCopy['testenvironment'] . "', ";
    }
    if ($sessionToCopy['teamname'] == "") {
        $sqlInsert .= "             null, ";
    }
    else
    {
        $sqlInsert .= "             '" . $sessionToCopy['teamname'] . "', ";
    }
    $sqlInsert .= "             '" . $sessionToCopy["publickey"] . "' ";
    $sqlInsert .= ") ";


    //echo "SQLINSERT=$sqlInsert";

    $result = mysql_query($sqlInsert);

    if (!$result) {
        echo "copySession_InsertSessionDataToDb: " . mysql_error() . "<br/>";
    }
}

function saveSession_UpdateSessionDataToDb($sessionid)
{

    if ($_REQUEST["title"] == "") {
        $_REQUEST["title"] = "Unnamed Session";
        echo "<b>Warning:</b> Session has no title, it will be named \"Unnamed Session\"<br/>\n";
    }

    $sqlUpdate = "";
    $sqlUpdate .= "UPDATE mission ";
    $sqlUpdate .= "SET    `title` = '" . mysql_real_escape_string($_REQUEST["title"]) . "', ";
    $sqlUpdate .= "       `charter` = '" . mysql_real_escape_string($_REQUEST["charter"]) . "', ";
    $sqlUpdate .= "       `notes` = '" . mysql_real_escape_string($_REQUEST["notes"]) . "', ";
    $sqlUpdate .= "       `username` = '" . $_SESSION['username'] . "', ";
    if ($_REQUEST['sprint'] == "") {
        $sqlUpdate .= "       `sprintname` = null, ";
    }
    else
    {
        $sqlUpdate .= "       `sprintname` = '" . mysql_real_escape_string($_REQUEST['sprint']) . "', ";
    }

    if ($_REQUEST['teamsprint'] == "") {
        $sqlUpdate .= "       `teamsprintname` = null, ";
    }
    else
    {
        $sqlUpdate .= "       `teamsprintname` = '" . mysql_real_escape_string($_REQUEST['teamsprint']) . "', ";
    }

    if ($_REQUEST['testenv'] == "") {
        $sqlUpdate .= "       `testenvironment` = null, ";
    }
    else
    {
        $sqlUpdate .= "       `testenvironment` = '" . mysql_real_escape_string($_REQUEST['testenv']) . "', ";
    }

    if ($_REQUEST['textareaswundertest'] == "") {
        $sqlUpdate .= "       `software` = null, ";
    }
    else
    {
        $sqlUpdate .= "       `software` = '" . mysql_real_escape_string($_REQUEST['textareaswundertest']) . "', ";
    }

    if ($_REQUEST['team'] == "") {
        $sqlUpdate .= "       `teamname` = null ";
    }
    else
    {
        $sqlUpdate .= "       `teamname` = '" . mysql_real_escape_string($_REQUEST['team']) . "' ";
    }
    $sqlUpdate .= "WHERE sessionid='$sessionid'";

    //echo $sqlUpdate."<br>";

    $result = mysql_query($sqlUpdate);

    if (!$result) {
        echo "saveSession_UpdateSessionDataToDb: " . mysql_error() . "<br/>";
    }
}

function saveSession_GetVersionIdForNewSession()
{
    $sqlSelect = "";
    $sqlSelect .= "SELECT * ";
    $sqlSelect .= "FROM   mission ";
    $sqlSelect .= "WHERE  username = '" . $_SESSION['username'] . "' ";
    $sqlSelect .= "ORDER  BY versionid DESC ";
    $sqlSelect .= "LIMIT  1";

    $result = mysql_query($sqlSelect);

    if (!$result) {
        echo "saveSession_GetVersionIdForNewSession: " . mysql_error() . "<br/>";
    }
    else
    {
        $row = mysql_fetch_array($result);
        $versionid = $row["versionid"];
    }
    return $versionid;
}

function GetVersionidFromSessionid($sessionid)
{

    $sqlSelect = "";
    $sqlSelect .= "SELECT versionid  ";
    $sqlSelect .= "FROM   `mission` ";
    $sqlSelect .= "WHERE  `sessionid` = $sessionid ";


    $result = mysql_query($sqlSelect);

    if (mysql_errno() == 1054) {
        echo "Session id provided is not an integer, will not add it.<br>";
    }
    elseif (!$result)
    {
        echo "GetVersionidFromSessionid: " . mysql_error() . "<br/>";
    }
    else
    {
        $row = mysql_fetch_array($result);
        $versionid = $row["versionid"];
    }

    return $versionid;
}

function GetSessionIdFromVersionIdNoNewDbConnection($versionid)
{

    $sqlSelect = "";
    $sqlSelect .= "SELECT sessionid  ";
    $sqlSelect .= "FROM   `mission` ";
    $sqlSelect .= "WHERE  `versionid` = $versionid ";


    $result = mysql_query($sqlSelect);

    if (mysql_errno() == 1054) {
        echo "Session id provided is not an integer, will not add it.<br>";
    }
    elseif (!$result)
    {
        echo "GetSessionIdFromVersionId: " . mysql_error() . "<br/>";
    }
    else
    {
        $row = mysql_fetch_array($result);
        $sessionid = $row["sessionid"];
    }

    return $sessionid;
}

function GetSessionIdFromVersionId($versionid)
{

    //    $conSql1 = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
    //    mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

    $sessionid = GetSessionIdFromVersionIdNoNewDbConnection($versionid);

    //    mysql_close($conSql1);

    return $sessionid;
}


function GetSessionTitle($versionid)
{

    $sqlSelect = "";
    $sqlSelect .= "SELECT title  ";
    $sqlSelect .= "FROM   `mission` ";
    $sqlSelect .= "WHERE  `versionid` = $versionid ";

    $result = mysql_query($sqlSelect);

    if (!$result) {
        echo "GetSessionTitle: " . mysql_error() . "<br/>";
    }
    else
    {
        $row = mysql_fetch_array($result);
        $title = $row["title"];

    }

    return $title;
}

function GetSessionTitleFromSessionId($sessionid)
{

    $sqlSelect = "";
    $sqlSelect .= "SELECT title  ";
    $sqlSelect .= "FROM   `mission` ";
    $sqlSelect .= "WHERE  `sessionid` = $sessionid ";

    $result = mysql_query($sqlSelect);

    if (!$result) {
        echo "GetSessionTitleFromSessionId: " . mysql_error() . "<br/>";
    }
    else
    {
        $row = mysql_fetch_array($result);
        $title = $row["title"];

    }

    return $title;
}

/**
 * @param  $versionid
 * @param  $executedStatus Shall be a boolean
 * @return void
 */
function saveSession_InsertSessionStatusToDb($versionid, $executedStatus)
{


    $sqlInsert = "";
    $sqlInsert .= "INSERT INTO mission_status ";
    $sqlInsert .= "            (`versionid`, ";
    $sqlInsert .= "             `executed`, ";
    $sqlInsert .= "             `debriefed`, ";
    $sqlInsert .= "             `masterdibriefed`, ";
    $sqlInsert .= "             `executed_timestamp` ) ";
    $sqlInsert .= "VALUES      ('$versionid', ";
    $sqlInsert .= "             '$executedStatus', ";
    $sqlInsert .= "             'false', ";
    $sqlInsert .= "             'false', ";
    $sqlInsert .= "             NOW())";

    $result = mysql_query($sqlInsert);

    if (!$result) {
        echo "saveSession_InsertSessionStatusToDb: " . mysql_error() . "<br/>";
    }
}

function saveSession_UpdateSessionStatusToDb($versionid)
{
    $executed = false;

    if ($_REQUEST["executed"] != "") {
        $executed = true;
    }


    $sqlUpdate = "";
    $sqlUpdate .= "UPDATE mission_status ";
    $sqlUpdate .= "SET    `executed` = '$executed', ";
    $sqlUpdate .= "       `debriefed` = 'false', ";
    $sqlUpdate .= "       `closed` = 'false', ";
    $sqlUpdate .= "       `masterdibriefed` = 'false', ";
    $sqlUpdate .= "       `executed_timestamp` = NOW() ";
    $sqlUpdate .= "WHERE versionid='$versionid'";

    $result = mysql_query($sqlUpdate);

    if (!$result) {
        echo "saveSession_UpdateSessionStatusToDb: " . mysql_error() . "<br><br>";
    }
}

function saveSession_UpdateSessionDebriefedStatusToDb($versionid, $debriefed, $closed, $masterdibriefed)
{

    $sqlUpdate = "";
    $sqlUpdate .= "UPDATE mission_status ";
    $sqlUpdate .= "SET    `debriefed` = $debriefed, ";
    $sqlUpdate .= "       `closed` = $closed, ";
    $sqlUpdate .= "       `masterdibriefed` = $masterdibriefed, ";
    $sqlUpdate .= "       `debriefed_timestamp` = NOW() ";
    $sqlUpdate .= "WHERE versionid='$versionid'";

    $result = mysql_query($sqlUpdate);

    if (!$result) {
        echo "saveSession_UpdateSessionDebriefedStatusToDb: " . mysql_error() . "<br><br>";
    }
}

function saveSession_InsertSessionDebriefedNotesToDb($versionid, $notes)
{

    $sqlInsert = "";
    $sqlInsert .= "INSERT INTO mission_debriefnotes ";
    $sqlInsert .= "            (`versionid`, ";
    $sqlInsert .= "             `notes`, ";
    $sqlInsert .= "             `debriefedby`) ";
    $sqlInsert .= "VALUES      ('$versionid', ";
    $sqlInsert .= "             '" . mysql_real_escape_string($notes) . "', ";
    $sqlInsert .= "             '" . $_SESSION['username'] . "')";


    $result = mysql_query($sqlInsert);

    if (!$result) {
        echo "saveSession_InsertSessionDebriefedNotesToDb: " . mysql_error() . "<br><br>";
    }
}

function saveSession_InsertSessionMetricsToDb($versionid, $metrics)
{

    $sqlInsert = "";
    $sqlInsert .= "INSERT INTO mission_sessionmetrics ";
    $sqlInsert .= "            (`versionid`, ";
    $sqlInsert .= "             `setup_percent`, ";
    $sqlInsert .= "             `test_percent`, ";
    $sqlInsert .= "             `bug_percent`, ";
    $sqlInsert .= "             `opportunity_percent`, ";
    $sqlInsert .= "             `duration_time`) ";
    $sqlInsert .= "VALUES      ('$versionid', ";
    $sqlInsert .= "             '" . mysql_real_escape_string($metrics["setuppercent"]) . "', ";
    $sqlInsert .= "             '" . mysql_real_escape_string($metrics["testpercent"]) . "', ";
    $sqlInsert .= "             '" . mysql_real_escape_string($metrics["bugpercent"]) . "', ";
    $sqlInsert .= "             '" . mysql_real_escape_string($metrics["oppertunitypercent"]) . "', ";
    $sqlInsert .= "             '" . mysql_real_escape_string($metrics["duration"]) . "')";

    $result = mysql_query($sqlInsert);

    if (!$result) {
        echo "saveSession_InsertSessionMetricsToDb: " . mysql_error() . "<br/>";
    }
}

function saveSession_InsertSessionAreaToDb($versionid, $areas)
{
    //    $_SESSION['settings']['area'] == 1 &&
    if ($areas != null) {
        //        $areas = $_REQUEST["area"];

        foreach ($areas as $area) {
            if ($area != "") {
                $sqlInsert = "";
                $sqlInsert .= "INSERT INTO mission_areas ";
                $sqlInsert .= "            (`versionid`, ";
                $sqlInsert .= "             `areaname`) ";
                $sqlInsert .= "VALUES      ('$versionid', ";
                $sqlInsert .= "             '" . mysql_real_escape_string($area) . "')";

                $result = mysql_query($sqlInsert);

                if (!$result) {
                    echo "saveSession_InsertSessionAreaToDb: " . mysql_error() . "<br/>";
                }
            }
        }
    }
}

function saveSession_InsertSessionBugsToDb($versionid)
{
    $bugs = $_REQUEST["buglist_hidden"];
    if ($bugs != "") {
        $bugsArray = explode(",", $bugs);
        foreach ($bugsArray as $aBug) {
            $aBug = str_replace(" ", "", $aBug);
            if ($aBug != "") {

                $sqlInsert = "";
                $sqlInsert .= "INSERT INTO mission_bugs ";
                $sqlInsert .= "            (`versionid`, ";
                $sqlInsert .= "             `bugid`) ";
                $sqlInsert .= "VALUES      ('$versionid', ";
                $sqlInsert .= "             '" . mysql_real_escape_string($aBug) . "')";

                $result = mysql_query($sqlInsert);

                if (!$result) {
                    echo "saveSession_InsertSessionBugsToDb: " . mysql_error() . "<br/>";
                }
            }
        }
    }
}

function saveSession_InsertSessionSessionsLinksToDb($linked_from_versionid)
{
    $sessionstoLinkTo = $_REQUEST["sessionlink_hidden"];

    if ($sessionstoLinkTo != "") {

        $sessionsToLinkToArray = explode(",", $sessionstoLinkTo);
        foreach ($sessionsToLinkToArray as $aSessionLinkTo) {
            $aSessionLinkTo = str_replace(" ", "", $aSessionLinkTo);
            if ($aSessionLinkTo != "") {
                $linked_to_versionid = GetVersionidFromSessionid($aSessionLinkTo);

                if ($linked_to_versionid != "") {
                    $sqlInsert = "";
                    $sqlInsert .= "INSERT INTO mission_sessionsconnections ";
                    $sqlInsert .= "            (`linked_from_versionid`, ";
                    $sqlInsert .= "             `linked_to_versionid`) ";
                    $sqlInsert .= "VALUES      ('$linked_from_versionid', ";
                    $sqlInsert .= "             '$linked_to_versionid')";

                    $result = mysql_query($sqlInsert);

                    if (!$result) {
                        echo "saveSession_InsertSessionSessionsLinksToDb: " . mysql_error() . "<br/>";
                    }
                }
                else
                {
                    echo "Could not add a connection to session $aSessionLinkTo since it does not exist.<br>";
                }
            }
            //            else
            //            {
            //                echo "Session ID is empty<br>";
            //            }
        }
    }
}

function saveSession_InsertSessionRequirementsToDb($versionid)
{

    $requirements = $_REQUEST["requirementlist_hidden"];
    if ($requirements != "") {
        $requirementsArray = explode(",", $requirements);
        foreach ($requirementsArray as $aRequirement) {
            $aRequirement = str_replace(" ", "", $aRequirement);
            if ($aRequirement != "") {

                $sqlInsert = "";
                $sqlInsert .= "INSERT INTO mission_requirements ";
                $sqlInsert .= "            (`versionid`, ";
                $sqlInsert .= "             `requirementsid`) ";
                $sqlInsert .= "VALUES      ('$versionid', ";
                $sqlInsert .= "             '" . mysql_real_escape_string($aRequirement) . "')";

                $result = mysql_query($sqlInsert);

                if (!$result) {
                    echo "saveSession_InsertSessionRequirementsToDb: " . mysql_error() . "<br/>";
                }
            }
        }
    }
}

function saveSession_UpdateSessionMetricsToDb($versionid)
{
    $sqlUpdate = "";
    $sqlUpdate .= "UPDATE mission_sessionmetrics ";
    $sqlUpdate .= "SET    `setup_percent` = '" . mysql_real_escape_string($_REQUEST["setuppercent"]) . "', ";
    $sqlUpdate .= "       `test_percent` = '" . mysql_real_escape_string($_REQUEST["testpercent"]) . "', ";
    $sqlUpdate .= "       `bug_percent` = '" . mysql_real_escape_string($_REQUEST["bugpercent"]) . "', ";
    $sqlUpdate .= "       `opportunity_percent` = '" . mysql_real_escape_string($_REQUEST["oppertunitypercent"]) . "', ";
    $sqlUpdate .= "       `duration_time` = '" . mysql_real_escape_string($_REQUEST["duration"]) . "' ";
    $sqlUpdate .= "WHERE versionid='$versionid'";

    $result = mysql_query($sqlUpdate);

    if (!$result) {
        echo "saveSession_UpdateSessionMetricsToDb: " . mysql_error() . "<br/>";
    }
}

function saveSession_UpdateSessionAreasToDb($versionid, $areas)
{
    $sqlDelete = "";
    $sqlDelete .= "DELETE FROM mission_areas ";
    $sqlDelete .= "WHERE  `versionid` = '$versionid'";

    $result = mysql_query($sqlDelete);

    if (!$result) {
        echo "saveSession_UpdateSessionAreasToDb: " . mysql_error() . "<br/>";
    }

    saveSession_InsertSessionAreaToDb($versionid, $areas);
}

function saveSession_DeleteSessionsNotesFromDb($versionid)
{
    $sqlDelete = "";
    $sqlDelete .= "DELETE FROM mission_debriefnotes ";
    $sqlDelete .= "WHERE  `versionid` = '$versionid'";

    $result = mysql_query($sqlDelete);

    if (!$result) {
        echo "saveSession_DeleteSessionsNotesFromDb: " . mysql_error() . "<br/>";
    }
}

function saveSession_UpdateSessionBugsToDb($versionid)
{
    $sqlDelete = "";
    $sqlDelete .= "DELETE FROM mission_bugs ";
    $sqlDelete .= "WHERE  `versionid` = '$versionid'";

    $result = mysql_query($sqlDelete);

    if (!$result) {
        echo "saveSession_UpdateSessionBugsToDb: " . mysql_error() . "<br/>";
    }

    saveSession_InsertSessionBugsToDb($versionid);

}

function updateSessionOwner($sessionid, $tester)
{
    $sqlUpdate = "";
    $sqlUpdate .= "UPDATE `mission` ";
    $sqlUpdate .= "SET    `username` = '$tester' ";
    $sqlUpdate .= "WHERE  `sessionid` = '$sessionid'";

    $result = mysql_query($sqlUpdate);

    if (!$result) {
        echo "updateSessionOwner: " . mysql_error() . "<br/>";
        return false;
    }

    return true;

}

function saveSession_UpdateSessionRequirementsToDb($versionid)
{
    $sqlDelete = "";
    $sqlDelete .= "DELETE FROM mission_requirements ";
    $sqlDelete .= "WHERE  `versionid` = '$versionid'";

    $result = mysql_query($sqlDelete);

    if (!$result) {
        echo "saveSession_UpdateSessionRequirementsToDb: " . mysql_error() . "<br/>";
    }

    saveSession_InsertSessionRequirementsToDb($versionid);

}

function saveSession_UpdateSessionLinkedToDb($versionid)
{
    $sqlDelete = "";
    $sqlDelete .= "DELETE FROM mission_sessionsconnections ";
    $sqlDelete .= "WHERE  `linked_from_versionid` = '$versionid'";

    $result = mysql_query($sqlDelete);

    if (!$result) {
        echo "saveSession_UpdateSessionLinkedToDb: " . mysql_error() . "<br/>";
    }

    saveSession_InsertSessionSessionsLinksToDb($versionid);

}

/**
 * Delete a session from the database by delete all rows in all tables that have infromation about the session
 * @param $versionid
 */
function deleteSessionFromDatabase($sessionId)
{

    $conDeleteSession = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("cannot connect");
    mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

    $versionid = GetVersionidFromSessionid($sessionId);
    //	$sessionId = GetSessionIdFromVersionId($versionid);

    $sqlDeleteMissionStatus = "";
    $sqlDeleteMissionStatus .= "DELETE FROM mission_status ";
    $sqlDeleteMissionStatus .= "WHERE  versionid = $versionid ";
    $result = mysql_query($sqlDeleteMissionStatus);

    $sqlDeleteMissionMetrics = "";
    $sqlDeleteMissionMetrics .= "DELETE FROM mission_sessionmetrics ";
    $sqlDeleteMissionMetrics .= "WHERE  versionid = $versionid ";
    $result = mysql_query($sqlDeleteMissionMetrics);

    $sqlDeleteMissionRequirements = "";
    $sqlDeleteMissionRequirements .= "DELETE FROM mission_requirements ";
    $sqlDeleteMissionRequirements .= "WHERE  versionid = $versionid ";
    $result = mysql_query($sqlDeleteMissionRequirements);

    $sqlDeleteMissionDebriefNotes = "";
    $sqlDeleteMissionDebriefNotes .= "DELETE FROM mission_debriefnotes ";
    $sqlDeleteMissionDebriefNotes .= "WHERE  versionid = $versionid ";
    $result = mysql_query($sqlDeleteMissionDebriefNotes);

    $sqlDeleteMissionBugs = "";
    $sqlDeleteMissionBugs .= "DELETE FROM mission_bugs ";
    $sqlDeleteMissionBugs .= "WHERE  versionid = $versionid ";
    $result = mysql_query($sqlDeleteMissionBugs);

    $sqlDeleteMissionAreas = "";
    $sqlDeleteMissionAreas .= "DELETE FROM mission_areas ";
    $sqlDeleteMissionAreas .= "WHERE  versionid = $versionid ";
    $result = mysql_query($sqlDeleteMissionAreas);

    $sqlDeleteMissionConnectionsFrom = "";
    $sqlDeleteMissionConnectionsFrom .= "DELETE FROM mission_sessionsconnections ";
    $sqlDeleteMissionConnectionsFrom .= "WHERE  linked_from_versionid = $versionid ";
    $result = mysql_query($sqlDeleteMissionConnectionsFrom);

    $sqlDeleteMissionConnectionsTo = "";
    $sqlDeleteMissionConnectionsTo .= "DELETE FROM mission_sessionsconnections ";
    $sqlDeleteMissionConnectionsTo .= "WHERE  linked_to_versionid = $versionid ";
    $result = mysql_query($sqlDeleteMissionConnectionsTo);

    $sqlDeleteMission = "";
    $sqlDeleteMission .= "DELETE FROM mission ";
    $sqlDeleteMission .= "WHERE  versionid = $versionid ";
    $result = mysql_query($sqlDeleteMission);

    $sqlDeleteSessionId = "";
    $sqlDeleteSessionId .= "DELETE FROM sessionid ";
    $sqlDeleteSessionId .= "WHERE  sessionid = $sessionId ";
    $result = mysql_query($sqlDeleteSessionId);

    mysql_close($conDeleteSession);

}


function doesSessionKeyExist($publickey)
{
    $sqlSelect = "";
    $sqlSelect .= "SELECT publickey ";
    $sqlSelect .= "FROM   `mission` ";
    $sqlSelect .= "WHERE  publickey = '$publickey'";

    $result = mysql_query($sqlSelect);

    if (!$result) {
        echo "doesSessionKeyExist: " . mysql_error() . "<br/>";
    }
    else
    {
        $num_rows = mysql_num_rows($result);
        if ($num_rows == 0) {
            return false;
        }
        else
        {
            return true;
        }
    }
}

?>