<?php
session_start();

require_once('../../include/validatesession.inc');
require_once('../../classes/dbHelper.php');
require_once('../../config/db.php.inc');
require_once('../../classes/logging.php');
require_once('../../classes/sessionReadObject.php');
require_once('../../classes/sessionObject.php');
require_once('../../classes/sessionHelper.php');
require_once('../../include/commonFunctions.php.inc');

$logger = new logging();

$dbManager = new dbHelper();
$con = $dbManager->db_getMySqliConnection();

$whereSql = "";
$StringSearchSql = null;

if (isset($_REQUEST['searchstringref']) && $_REQUEST['searchstringref'] != "") {
    $data = generateDataForIssueAndRequritementSearch($con);
} else {
    if ($_REQUEST['searchstring'] != null) {
        $searchstring = $dbManager->escape($con,$_REQUEST['searchstring']);
        $StringSearchSql = $whereSql . "MATCH(charter,notes, title,software) AGAINST ('$searchstring' IN BOOLEAN MODE) ";
    }


    if ($_REQUEST['tester'] != null) {
        if (strpos($whereSql, "WHERE") === false) {
            $whereSql = "WHERE ";
        } else {
            $whereSql = $whereSql . "AND ";
        }
        $username = $_REQUEST['tester'];
        $whereSql = $whereSql . "username='$username' ";
    }

    if ($_REQUEST['sprint'] != null) {
        if (strpos($whereSql, "WHERE") === false) {
            $whereSql = "WHERE ";
        } else {
            $whereSql = $whereSql . "AND ";
        }
        $sprint = $_REQUEST['sprint'];
        $whereSql = $whereSql . "sprintname='$sprint' ";
    }

    if ($_REQUEST['team'] != null) {
        if (strpos($whereSql, "WHERE") === false) {
            $whereSql = "WHERE ";
        } else {
            $whereSql = $whereSql . "AND ";
        }
        $team = $_REQUEST['team'];
        $whereSql = $whereSql . "teamname='$team' ";
    }

    if ($_REQUEST['area'] != null) {
        if (strpos($whereSql, "WHERE") === false) {
            $whereSql = "WHERE ";
        } else {
            $whereSql = $whereSql . "AND ";
        }
        $team = $_REQUEST['area'];
        $whereSql = $whereSql . "areaname='$team' ";
    }

    if ($StringSearchSql == null) {
        if ($_REQUEST['status'] != null) {
            $status = $_REQUEST['status'];

            if (strpos($whereSql, "WHERE") === false) {
                $whereSql = "WHERE ";
            } else {
                $whereSql = $whereSql . "AND ";
            }
            if ($status == 1) {
                $whereSql = $whereSql . "executed=0 AND notes LIKE  ''";
            } elseif ($status == 2) {
                $whereSql = $whereSql . "executed=0 AND closed=0 AND notes NOT LIKE  ''";
            }
            elseif ($status == 3) {
                $whereSql = $whereSql . "executed=1 AND debriefed=0 AND closed=0 ";
            }
            elseif ($status == 4) {
                $whereSql = $whereSql . "debriefed=1 ";
            }
            elseif ($status == 5) {
                $whereSql = $whereSql . "closed=1 ";
            }
        }
    }

    if ($whereSql != "") {
        if ($StringSearchSql != null)
            $StringSearchSql = " AND $StringSearchSql ";
    } else {
        if ($StringSearchSql != null)
            $StringSearchSql = " WHERE $StringSearchSql ";
    }

    $data = array();
    $data['page'] = (int)$_REQUEST['page'];
//$data['total'] = 500;

    $data = getNumberOfSessions($con,$dbManager,$data, $whereSql, $StringSearchSql);

    $data = getSessions($con,$dbManager,$data, $whereSql, $StringSearchSql);


}
echo json_encode($data);

mysqli_close($con);

function generateDataForIssueAndRequritementSearch($con)
{
    $data = null;

    $issueReference = $_REQUEST['searchstringref'];
    $sqlReq = "SELECT * FROM `mission_requirements` WHERE `requirementsid` = '$issueReference'";
    $sqlBugs = "SELECT * FROM `mission_bugs` WHERE `bugid` = '$issueReference'";

    $issueList = getListofSessionsFromBugOrRequirementsSearch($con,$sqlReq, $sqlBugs);
    $sessionList = array();

    foreach ($issueList as $aIssue) {
        $sessionInfo = new sessionReadObject($aIssue);
        $session = $sessionInfo->getSession();
        //
        //    $data['rows'][] = array('id' => "1", 'cell' => array(
        $sessionid = $aIssue;

        $status = "Not Executed";
        if ($session['executed'] == 0 && $session['notes'] != null) {
            $status = "In progress";
            //echo "notes'".$notes."'";
            //$status = "Executed";
        }
        if ($session['executed'] == 1) {
            $status = "Executed";
        }
        if ($session['debriefed'] == 1) {
            $status = "Debriefed";
        }
        if ($session['closed'] == 1) {
            $status = "Closed";
        }

        $title = $session['title'];
        $sh = new sessionHelper();
        $fullname = $sh->getUserFullName($session['username']);
        $sprintname = $session['sprintname'];
        $teamname = $session['teamname'];
        $areas = "";
        foreach ($session['areas'] as $area) {
            $areas .= " $area";
        }
        $updated = $session['updated'];
        $executed_timestamp = $session['executed_timestamp'];
        $data['page'] = 1;
        $data['total'] = 1;
        $data['rows'][] = array('id' => "1", 'cell' => array("$sessionid", "$status", "$title", "$fullname", "$sprintname", "$teamname", "$areas", "$updated", "$executed_timestamp"));
    }

    return $data;
}

function getListofSessionsFromBugOrRequirementsSearch($con,$sqlReq, $sqlBugs)
{
    $sh = new sessionHelper();

    $issueList = array();

    $result = dbHelper::sw_mysqli_execute($con,$sqlReq,__FILE__,__LINE__);

    if (!$result) {
        echo mysqli_error($con) . "<br/>";
    }

    while ($row = mysqli_fetch_array($result)) {
        $issueList[] = $sh->getSessionIdFromVersionId($row['versionid'],$con);
    }

    $result = dbHelper::sw_mysqli_execute($con,$sqlBugs,__FILE__,__LINE__);

    if (!$result) {
        echo mysqli_error($con) . "<br/>";
    }

    while ($row = mysqli_fetch_array($result)) {
        $issueList[] = $sh->getSessionIdFromVersionId($row['versionid'],$con);
    }
    return $issueList;
}

function getNumberOfSessions($con,$dbManager,$data, $whereSql, $StringSearchSql)
{

    $whereSql = str_replace("WHERE", " AND ", $whereSql);
    $StringSearchSql = str_replace("WHERE", " AND ", $StringSearchSql);
    if ($_REQUEST['area'] != null) {
        $sql = "SELECT COUNT(*) as numberofSessions FROM mission as m, mission_status as ms, mission_areas as ma WHERE m.versionid = ms.versionid AND m.versionid=ma.versionid $whereSql $StringSearchSql";
    } else {
        $sql = "SELECT COUNT(*) as numberofSessions FROM mission as m, mission_status as ms WHERE m.versionid = ms.versionid $whereSql $StringSearchSql";
    }
    //$sql = "SELECT COUNT(*) as numberofSessions FROM mission as m, mission_status as ms WHERE m.versionid = ms.versionid $whereSql $StringSearchSql";
    //echo $sql;

    $result = $dbManager->sw_mysqli_execute($con,$sql,__FILE__,__LINE__);
    $result = mysqli_fetch_row($result);
    $data['total'] = $result[0];
    return $data;
}

function getSessions($con,$dbManager,$data, $whereSql, $StringSearchSql)
{
    $logger = new logging();
    $numberOfSessionsToDisplay = $_REQUEST['rp'];
    if (isset($_REQUEST['page'])) {
        $page = $_REQUEST['page'];
        $startLimit = ((int)$page * (int)$numberOfSessionsToDisplay) - $numberOfSessionsToDisplay;
        $stopLimit = (int)$page * (int)$numberOfSessionsToDisplay;
    } else {
        $startLimit = 0;
        $stopLimit = (int)$numberOfSessionsToDisplay;
    }

    $sortname = $_REQUEST['sortname'];

    if (strstr($sortname, "id") != false)
        $sortname = "sessionid";
    elseif (strstr($sortname, "updated") != false)
        $sortname = "updated";
    elseif (strstr($sortname, "executed") != false)
        $sortname = "executed_timestamp";
    else
        $sortname = "updated";

    $sortorder = $_REQUEST['sortorder'];

    if ($StringSearchSql == null) {
        $tablename = "sessioninfo";
    } else {
        $tablename = "mission";
    }


    if ($_REQUEST['area'] != null) {
        $whereSql = str_replace("WHERE", "AND", $whereSql);
        $StringSearchSql = str_replace("WHERE", "AND", $StringSearchSql);
        $sql = "SELECT * FROM $tablename as m, mission_areas as ma WHERE m.versionid = ma.versionid $whereSql $StringSearchSql ORDER BY $sortname $sortorder LIMIT " . $startLimit . ",$numberOfSessionsToDisplay;";
    } else {
        $sql = "SELECT * FROM $tablename $whereSql $StringSearchSql ORDER BY $sortname $sortorder LIMIT " . $startLimit . ",$numberOfSessionsToDisplay;";
    }

    //$sql = "SELECT * FROM $tablename $whereSql $StringSearchSql ORDER BY $sortname $sortorder LIMIT " . $startLimit . ",$numberOfSessionsToDisplay;";

    //echo $sql;
    $result = $dbManager->sw_mysqli_execute($con,$sql,__FILE__,__LINE__);

    if (!$result) {
        echo mysqli_error($con) . "<br/>";
    }

    while ($row = mysqli_fetch_array($result)) {

        $sessionid = $row['sessionid'];
        $versionid = $row['versionid'];
        $title = $row['title'];
        $username = $row['username'];
        $notes = $row['notes'];
        //$testenvironment = $row['testenvironment'];

        $sqlSelect = "";
        $sqlSelect .= "SELECT fullname ";
        $sqlSelect .= "FROM   members ";
        $sqlSelect .= "WHERE username = '$username' ";
        $sqlSelect .= "ORDER  BY fullname ASC";

        $result2=$dbManager->sw_mysqli_execute($con,$sqlSelect,__FILE__,__LINE__);
//        $result2 = mysql_query($sqlSelect);

        $row2 = mysqli_fetch_row($result2);

        $fullname = $row2[0];

        $updated = $row['updated'];
        $teamname = $row['teamname'];
        $sprintname = $row['sprintname'];
        if (isset($row['executed_timestamp']))
            $executed_timestamp = $row['executed_timestamp'];
        else
            $executed_timestamp = null;

        $sqlSelectSessionMetrics = "";
        $sqlSelectSessionMetrics .= "SELECT * ";
        $sqlSelectSessionMetrics .= "FROM   mission_status ";
        $sqlSelectSessionMetrics .= "WHERE  versionid = $versionid";
        $resultSessionMetrics = $dbManager->sw_mysqli_execute($con,$sqlSelectSessionMetrics,__FILE__,__LINE__);

        $rowMetrics = mysqli_fetch_array($resultSessionMetrics);
        // print_r($rowMetrics);

        $executed = $rowMetrics['executed'];
        if ($executed == 0) {
            $executed = "-";
        }
        $debriefed = $rowMetrics['debriefed'];
        $closed = $rowMetrics['closed'];
        //$updated = $rowMetrics['updated'];

        $status = "Not Executed";
        if ($executed == 0 && $notes != null) {
            $status = "In progress";
            //echo "notes'".$notes."'";
            //$status = "Executed";
        }
        if ($executed == 1) {
            $status = "Executed";
        }
        if ($debriefed == 1) {
            $status = "Debriefed";
        }
        if ($closed == 1) {
            $status = "Closed";
        }


        $areas = "";
        $notFirstArea = false;
        $sqlSelectArea = "";
        $sqlSelectArea .= "SELECT * ";
        $sqlSelectArea .= "FROM   mission_areas ";
        $sqlSelectArea .= "WHERE  versionid = $versionid";
        $resultSessionAreas = $dbManager->sw_mysqli_execute($con,$sqlSelectArea,__FILE__,__LINE__);
        while ($row = mysqli_fetch_array($resultSessionAreas)) {
            if ($notFirstArea) {
                $areas = $areas . ", ";
            }
            $areas = $areas . $row['areaname'];
            $notFirstArea = true;
        }

        $sql = "SELECT * FROM mission_debriefnotes WHERE notes NOT LIKE '' AND versionid = $versionid";
        $resultDoesNotesExist = $dbManager->sw_mysqli_execute($con,$sql,__FILE__,__LINE__);
        if (strstr($status, "Executed") != false && mysqli_num_rows($resultDoesNotesExist) != 0) {
            $debriefComments = "<img src='pictures/notify-star.png' alt='Debrief comments exists'>";
        } else {
            $debriefComments = "";
        }

        $sessionid = "<img src='pictures/quickview.png' class='qview' onclick='javascript: quickView($sessionid);' alt='Debrief comments exists'>".$sessionid;


        $data['rows'][] = array('id' => "1", 'cell' => array("$sessionid", "$status", "$debriefComments $title", "$fullname", "$sprintname", "$teamname", "$areas", "$updated", "$executed_timestamp"));
    }
    return $data;
}

?>