<?php
//require_once('../../include/validatesession.inc');
require_once('../../include/db.php');
require_once('../../config/db.php.inc');

$con = getMySqlConnection();


$data = array();
$data['page'] = (int)$_REQUEST['page'];
//$data['total'] = 500;
$data = getNumberOfSessions($data);

$data = getSessions($data);

echo json_encode($data);

mysql_close($con);


function getNumberOfSessions($data)
{
    $sql = "SELECT COUNT(*) as numberofSessions FROM sessioninfo";
    $result = mysql_query($sql);
    $result = mysql_fetch_row($result);
    $data['total'] = $result[0];
    return $data;
}

function getSessions($data)
{
    $numberOfSessionsToDisplay = 50;
    if (isset($_REQUEST['page'])) {
        $page = $_REQUEST['page'];
        $startLimit = ((int)$page * (int)$numberOfSessionsToDisplay) - $numberOfSessionsToDisplay;
        $stopLimit = (int)$page * (int)$numberOfSessionsToDisplay;
    }
    else
    {
        $startLimit = 0;
        $stopLimit = (int)$numberOfSessionsToDisplay;
    }

    $sortname = $_REQUEST['sortname'];
    
    if (strstr($sortname,"id")!=false)
        $sortname = "sessionid";
    elseif (strstr($sortname,"updated")!=false)
        $sortname = "updated";
    elseif (strstr($sortname,"executed")!=false)
        $sortname = "executed_timestamp";
    else
        $sortname = "sessionid";

    $sortorder = $_REQUEST['sortorder'];
    $sql = "SELECT * FROM sessioninfo ORDER BY $sortname $sortorder LIMIT " . $startLimit . ",50;";

    //echo $sql;

    $result = mysql_query($sql);

    if (!$result) {
        echo mysql_error() . "<br/>";
    }

    while ($row = mysql_fetch_array($result)) {
        $sessionid = $row['sessionid'];
        $versionid = $row['versionid'];
        $title = $row['title'];
        $username = $row['username'];
        $executed = $row['executed'];
        if ($executed == 0) {
            $executed = "-";
        }
        $debriefed = $row['debriefed'];
        $closed = $row['closed'];
        $updated = $row['updated'];
        $executed_timestamp = $row['executed_timestamp'];

        $teamname = $row['teamname'];
        $sprintname = $row['sprintname'];
        $executed_timestamp = $row['executed_timestamp'];

        $status = "Not Executed";
        if ($executed == 1) {
            $status = "Executed";
        }
        if ($debriefed == 1) {
            $status = "Debriefed";
        }
        if ($closed == 1) {
            $status = "Closed";
        }
        $data['rows'][] = array('id' => "1", 'cell' => array("$sessionid", "$status", "$title", "$username", "$sprintname", "$teamname", "$updated", "$executed_timestamp", "keypad,siren"));
    }
    return $data;
}

?>