<?php
session_start();
require_once('../include/validatesession.inc');
include_once('../config/db.php.inc');
include_once('../include/db.php');
include_once ('../include/commonFunctions.php.inc');
include_once ('../include/session_database_functions.php.inc');
include_once ('../include/session_common_functions.php.inc');


echo '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
          <meta http-equiv="Content-type" content="text/html;charset=utf-8">
      <title>Sessionweb</title>
           <link rel="stylesheet" type="text/css" href="../css/sprintreport.css">
           <script src="../js/jquery-1.7.1.js" type="text/javascript"></script>
  </head>
<body>';

if (isset($_REQUEST['sprint'])) {
    $con1 = getMySqlConnection();

    generateReport($_REQUEST['sprint']);
    mysql_close($con1);
}
else
{
    echo '<form method="post" action="sprintreport.php">';

    echo "Sprint: ";
    echoSprintSelect("");

    echo '<input type="submit" name="Submit" value="Generate report">';
}
echo '</body>
</html>';

function generateReport($sprint)
{


    echo "<H1>Sprint: $sprint</H1>";
    echo '<div class="shortboldline"></div>';
    printNumberOrSessions($sprint);
    echo '<div class="shortboldline"></div>';

    printTeamStatistics($sprint);
    echo "</div>";

}

function printNumberOrSessions($sprint)
{
    $sql = "SELECT sessionid,versionid,username,title,teamname,sprintname,setup_percent,test_percent,bug_percent,opportunity_percent,duration_time FROM sessioninfo WHERE sprintname = '$sprint'";
    $result = mysql_query($sql);
    $numberOfSession = mysql_num_rows($result);

    echo "<div>Total number of session executed: $numberOfSession</div>";
}

function printTeamStatistics($sprint)
{
    echo "<div><h2>Total number of sessions executed:</h2>";
    $sql = "SELECT teamname, COUNT(*) as numberofsessions FROM sessioninfo WHERE sprintname = 'Feb12' GROUP by teamname";
    $resultTeam = mysql_query($sql);
    $totalCount = 0;
    $teamNumberOfSessionArray = array();
    echo '<table border=0><tbody>';
    while ($row = mysql_fetch_array($resultTeam))
    {
        echo "<tr><td>" . $row['teamname'] . "</td><td>" . $row['numberofsessions']."</td></tr>";
        $totalCount = $totalCount+ (int)$row['numberofsessions'];
        $teamNumberOfSessionArray[$row['teamname']]=$row['numberofsessions'];
    }
    echo "<tr><td><span class='stronger'>Total</span></td><td><span class='stronger'>$totalCount</span></td></tr>";

    echo '</tbody></table>';


    //echo createBarChart($teamNumberOfSessionArray);

    echo "</div>";
    echo '<div class="shortboldline"></div>';
    printAreaStatistics($sprint);
}

function printAreaStatistics($sprint)
{
    $sw_settings = getSettingsDoNotCreateMysqlConnection();
    $normalizedSessionTime = $sw_settings['normalized_session_time'];
    echo "<div><h2>Areastatistics:</h2>";


    $sql = "SELECT teamname FROM sessioninfo WHERE sprintname = '$sprint' GROUP by teamname";
    $resultTeam = mysql_query($sql);

    while ($row = mysql_fetch_array($resultTeam))
    {
        $areasArray = array();
        $totalTimeInSessions = 0;
        $teamname = $row['teamname'];

        $sql = "SELECT versionid,duration_time,setup_percent,test_percent,bug_percent,opportunity_percent FROM sessioninfo WHERE sprintname = 'Feb12' AND teamname = '$teamname'";
        $resultTeamSessions = mysql_query($sql);
        while ($rowSessions = mysql_fetch_array($resultTeamSessions))
        {
            list($totalTimeInSessions, $areasArray) = getAreasConnectedToSession($rowSessions, $areasArray, $totalTimeInSessions);
        }
        echo "<h3>$teamname</h3>";
        echo '<table border=0><tbody>';
        echo "<tr valign='top'><th width='300' valign='top'>Area</th><th>Time distribution</th></tr>";


        echo "<tr><td valign='top'>";
        echo '<table border=0><tbody>';
        echo "<tr valign='top'><td valign='baseline' width='200'>Area</td><td>Number of sessions</td></tr>";

        if(!empty($areasArray))
        {
        foreach ($areasArray as $key => $value)
        {
            echo "<tr><td>$key</td><td>$value</td></tr>";
        }
        }
        else
        {
            echo "<tr><td>&nbsp</td><td>&nbsp</td></tr>";

        }

        echo '</tbody></table></td>';
        if ($totalTimeInSessions != 0)
        {
            $hoursInsessions = $totalTimeInSessions / 60;
            $norm_session_cnt = round((int)$totalTimeInSessions/(int)$normalizedSessionTime,1);
        }
        echo "Number of normalized sessions: $norm_session_cnt ($hoursInsessions h)";

        echo "<td valign='top'><img alt='pie chart' src='".getSprintMetricsPieWithChartUrl($teamname)."'></td></tr>";
        echo '</tbody></table>';
        echo '<div class="shortthinline"></div>';
    }
}

function getSprintMetricsPieWithChartUrl($teamname)
{
    $sprint = $_REQUEST['sprint'];
    $sql = "SELECT COUNT(*) as cnt, SUM(setup_percent) as setup, SUM(test_percent) as test, SUM(bug_percent) as bug, SUM(opportunity_percent) as opportunity FROM sessioninfo WHERE sprintname = '$sprint' AND teamname = '$teamname'";
    $result = mysql_query($sql);
    $row = null;
    while ($row = mysql_fetch_array($result))
    {
    $cnt = $row['cnt'];
        $setup = $row['setup'];
        $test = $row['test'];
        $bug = $row['bug'];
        $oppertunity = $row['opportunity'];

        return createPercentPieChart($setup, $test, $bug, $oppertunity);
    }


}

function getAreasConnectedToSession($rowSessions, $areasArray, $totalTimeInSessions)
{
    $versionid = $rowSessions['versionid'];
    $totalTimeInSessions = (int)$totalTimeInSessions + (int)$rowSessions['duration_time'];
    $sqlAreas = "SELECT * FROM sessionwebos.mission_areas WHERE versionid = $versionid";
    $resultTeamAreas = mysql_query($sqlAreas);
    while ($rowTeamAreas = mysql_fetch_array($resultTeamAreas))
    {
        $area = $rowTeamAreas['areaname'];
        if (array_key_exists($area, $areasArray)) {
            $areasArray[$area] = $areasArray[$area] + 1;
        }
        else
        {
            $areasArray[$area] = 1;
        }
    }

    return array($totalTimeInSessions, $areasArray);
}

function createBarChart($KeyValueArray)
{   $barValues = "";
    $barTitles = "";
    print_r($KeyValueArray);
    foreach($KeyValueArray as $key=>$value)
    {
        echo $barValues;
      $barTitles = $barTitles . "|" .$key;
      $barValues =  $barValues . "," .$value;
    }
    //echo $barValues;
    //$barTitles = substr($barTitles,1,strlen($barTitles));
    $barValues = substr($barValues,1,strlen($barValues));



   return "https://chart.googleapis.com/chart?chxt=x&cht=bvs&chd=s:c9uDc&chco=76A4FB&chls=2.0&chs=200x125&chd=t:$barValues&chxl=0:$barTitles";
}
?>