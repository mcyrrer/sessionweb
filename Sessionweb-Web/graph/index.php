<?php
session_start();
if (!session_is_registered(myusername)) {
    header("location:../index.php");
}

//include("../include/header.php.inc");
include_once('../config/db.php.inc');
include_once ('../include/commonFunctions.php.inc');
$failedToCreateGraph = false;
?>
<!DOCTYPE html >
<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="chrome=1">

    <title>Sessionweb graph</title></title>
    <script src="../js/jquery-1.4.4.js"></script>


<?php


    $con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("cannot connect");
    mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

    if ($_GET['type'] == "line_overtime") {
        //        lineGraph();
        dateLineGraphGoogle();
    }
    elseif ($_GET['type'] == "pie_timedist")
    {
        pieTimeDistribution();
    }
    else
    {
        echo "No graph type added";
    }
    mysql_close($con);
    ?>
</head>
<body>

<div>
    <?php echo $sql; echo $labels;?>
    <div id='chart_div' style='width: 1024; height: 400px;'>
        <canvas id="graphDiv" width="1024" height="400"> [Please wait...]</canvas>
    </div>
</div>
<!--<div id='chart_div' style='width: 700px; height: 240px;'></div>-->
</body>
</html>
<?php
function dateLineGraphGoogle()
{
    //TODO: Fix undefined for team in javascript!!!!!
    $sql = "SELECT DATE(executed_timestamp) AS date, COUNT(*) AS count, sessionid  ";
    $sql .= "FROM sessionwebos.sessioninfo  ";
    $sql .= "WHERE executed = 1  ";
    if ($_GET['tester'] != null) {
        if ($_SESSION['useradmin'] == 1) {
            $sql .= "AND username = '" . $_GET['tester'] . "' ";
        }
    }
    if ($_GET['team'] != null) {
        if ($_SESSION['useradmin'] == 1) {
            $sql .= "AND teamname = '" . $_GET['team'] . "' ";
        }
    }
    if ($_GET['sprint'] != null) {
        $sql .= "AND sprintname = '" . $_GET['sprint'] . "' ";
    }

    $sql .= "GROUP BY DATE(executed_timestamp)  ";
    $sql .= "ORDER BY date;";
    $result = mysql_query($sql);
    //    echo $sql;
    if ($result) {
        echo "<script type='text/javascript' src='https://www.google.com/jsapi'></script>\n";
        echo "    <script type='text/javascript'>\n";
        echo "      google.load('visualization', '1', {'packages':['annotatedtimeline']});\n";
        echo "      google.setOnLoadCallback(drawChart);\n";
        echo "      function drawChart() {\n";
        echo "        var data = new google.visualization.DataTable();\n";
        echo "        data.addColumn('date', 'Date');\n";
        echo "        data.addColumn('number', 'Accumulated session records');\n";
        echo "        data.addColumn('string', 'title1');\n";
        echo "        data.addColumn('string', 'text1');\n";
        echo "        data.addColumn('number', 'Accumulated normalized sessions');\n";
        echo "        data.addColumn('string', 'title2');\n";
        echo "        data.addColumn('string', 'text2');\n";
        echo "        data.addColumn('number', 'Nbr of sessions records');\n";
        echo "        data.addColumn('string', 'title2');\n";
        echo "        data.addColumn('string', 'text2');\n";
        echo "        data.addColumn('number', 'Normalized sessions');\n";
        echo "        data.addColumn('string', 'title2');\n";
        echo "        data.addColumn('string', 'text2');\n";
        echo "        data.addRows([\n";
        $addComma = false;
        $totalNormalizedSessions = 0;
        $totalSessionRecords = 0;
        $i = 1;

        while ($row = mysql_fetch_array($result))
        {
            $date = $row['date'];
            $year = substr($date, 0, 4);
            $month = intval(substr($date, 5, 2)) - 1;
            $day = substr($date, 8, 2);
            //        print_r($row);
            $sql = "SELECT SUM(duration_time)/ ";
            $sql .= "  (SELECT normalized_session_time ";
            $sql .= "   FROM settings) AS duration ";
            $sql .= "FROM `sessionwebos`.`sessioninfo`  ";
            $sql .= "WHERE DATE(executed_timestamp) = '" . $row['date'] . "' ";
            if ($_GET['tester'] != null) {
                $sql .= "AND username = '" . $_GET['tester'] . "' ";
            }
            if ($_GET['team'] != null) {
                $sql .= "AND teamname = '" . $_GET['team'] . "' ";
            }
            if ($_GET['sprint'] != null) {
                $sql .= "AND sprintname = '" . $_GET['sprint'] . "' ";
            }
            $sql .= "  AND executed = 1;";
            //                echo $sql;
            $resultAvrTime = mysql_query($sql);
            $rowDuration = mysql_fetch_row($resultAvrTime);

            $sessionsThatDay = $row['count'];
            $totalNormalizedSessions = $totalNormalizedSessions + $sessionsThatDay;
            $normalizedSessionsThatDay = $rowDuration[0];
            //        print_r($rowDuration);
            $totalSessionRecords = $totalSessionRecords + $normalizedSessionsThatDay;

            echo "";

            echo "          [new Date($year, $month ,$day), $totalNormalizedSessions, undefined, undefined, $totalSessionRecords, undefined, undefined, $sessionsThatDay, undefined, undefined, $normalizedSessionsThatDay, undefined, undefined],\n";

            //        echo "          [new Date(2008, 1 ,2), $totalSessions, undefined, undefined, 90374, undefined, undefined],\n";
            //        echo "          [new Date(2008, 1 ,3), 55022, undefined, undefined, 50766, undefined, undefined],\n";
            //        echo "          [new Date(2008, 1 ,4), 75284, undefined, undefined, 14334, undefined, undefined],\n";
            //        echo "          [new Date(2008, 1 ,5), 41476, undefined, undefined, 66467, undefined, undefined],\n";
            //        echo "          [new Date(2008, 1 ,6), 33322, undefined, undefined, 39463, undefined, undefined]\n";
            $addComma = true;
            $i++;
        }

        echo "        ]);\n";
        echo "        var chart = new google.visualization.AnnotatedTimeLine(document.getElementById('chart_div'));\n";
        echo "        chart.draw(data, {displayAnnotations: true});\n";
        echo "      }\n";
        echo "    </script>\n";
    }
    else
    {
        $failedToCreateGraph = true;
    }
}


function pieTimeDistribution()
{

    //TODO: Fix undefined for team in javascript!!!!!
    $sql = "SELECT SUM(setup_percent) as setup,SUM(test_percent) as test, SUM(bug_percent) as bug, SUM(opportunity_percent) as opportunity, COUNT(*) as numberOfSessions  ";
    $sql .= "FROM sessionwebos.sessioninfo  ";
    $sql .= "WHERE executed = 1  ";
    if ($_GET['tester'] != null) {
        if ($_SESSION['useradmin'] == 1) {
            $sql .= "AND username = '" . $_GET['tester'] . "' ";
        }
    }
    if ($_GET['team'] != null) {
        if ($_SESSION['useradmin'] == 1) {
            $sql .= "AND teamname = '" . $_GET['team'] . "' ";
        }
    }
    if ($_GET['sprint'] != null) {
        $sql .= "AND sprintname = '" . $_GET['sprint'] . "' ";
    }


   
    $result = mysql_query($sql);
//       echo $sql; //For debug purpose!
    if ($result) {

        $addComma = false;
        $i = 1;
        $row = mysql_fetch_array($result);
        $setup = $row['setup'];
        $test = $row['test'];
        $bug = $row['bug'];
        $opportunity = $row['opportunity'];
        $numberOfSessions = $row['numberOfSessions'];
        
        
        echo "<script type='text/javascript' src='https://www.google.com/jsapi'></script>\n";
        echo "    <script type='text/javascript'>\n";
        echo "      google.load('visualization', '1', {packages:['corechart']});\n";
        echo "      google.setOnLoadCallback(drawChart);\n";
        echo "      function drawChart() {\n";
        echo "        var data = new google.visualization.DataTable();\n";
        echo "        data.addColumn('string', 'Task');\n";
        echo "        data.addColumn('number', 'output');\n";
        echo "          data.addRows(4);\n";
        echo "          data.setValue(0, 0, 'Setup');\n";
        echo "          data.setValue(0, 1, $setup);\n";
        echo "          data.setValue(1, 0, 'Test');\n";
        echo "          data.setValue(1, 1, $test);\n";
        echo "          data.setValue(2, 0, 'Bug');\n";
        echo "          data.setValue(2, 1, $bug);\n";
        echo "          data.setValue(3, 0, 'Opportunity');\n";
        echo "          data.setValue(3, 1, $opportunity);\n";

        // Create and draw the visualization.
        echo "var chart = new google.visualization.PieChart(document.getElementById('chart_div'));\n";
        echo "chart.draw(data, {width: 750, height: 500, title: 'Time distribution', is3D : 'true'});\n";
        echo "        }\n";
        echo "    </script>\n";
    }
    else
    {
        $failedToCreateGraph = true;
    }
}

?>