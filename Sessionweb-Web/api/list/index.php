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

$data['rows'][] = array('id' => "1", 'cell' => array("123", "Not Executed", "My Session", "Mattias Gustavsson", "MAR12", "MyPages", "2012-01-01 12:12", "keypad,siren"));
$data['rows'][] = array('id' => "2", 'cell' => array("2", "Closed", "My Session", "Mattias Gustavsson", "MAR12", "MyPages", "2012-01-01 12:12", "keypad,siren"));
$data['rows'][] = array('id' => "3", 'cell' => array("3", "In progress", "My Session", "Mattias Gustavsson", "MAR12", "MyPages", "2012-01-01 12:12", "keypad,siren"));
$data['rows'][] = array('id' => "4", 'cell' => array("4", "Executed", "My Session", "Mattias Gustavsson", "MAR12", "MyPages", "2012-01-01 12:12", "keypad,siren"));
$data['rows'][] = array('id' => "5", 'cell' => array("5", "Debriefed", "My Session", "Mattias Gustavsson", "MAR12", "MyPages", "2012-01-01 12:12", "keypad,siren"));


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
        $startLimit = ((int) $page * (int)$numberOfSessionsToDisplay)-$numberOfSessionsToDisplay;
        $stopLimit =(int) $page * (int)$numberOfSessionsToDisplay;
    }
    else
    {
        $startLimit = 0;
        $stopLimit = (int)$numberOfSessionsToDisplay;
    }

    $sql = "SELECT * FROM sessioninfo ORDER BY sessionid DESC LIMIT ".$startLimit.",".$stopLimit.";";
    //$sql = "SELECT * FROM sessioninfo ORDER BY sessionid DESC LIMIT 0,$numberOfSessionsToDisplay";
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
        $debriefed = $row['debriefed'];
        $closed = $row['closed'];
        $updated = $row['updated'];
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
        $data['rows'][] = array('id' => "1", 'cell' => array("$sessionid", "$status", "$title", "$username", "$sprintname", "$teamname", "$updated", "keypad,siren"));
    }
    return $data;
}

?>