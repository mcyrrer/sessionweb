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

    <!--<meta name="keywords" content="rgraph html5 canvas example line chart"/>-->
    <!--<meta name="description" content="RGraph: Line chart example"/>-->

    <!--<meta property="og:title" content="RGraph: HTML5 canvas graph library"/>-->
    <!--<meta property="og:description" content="A graph library based on the HTML5 canvas tag"/>-->
    <!--<meta property="og:image" content="http://www.rgraph.net/images/logo.png"/>-->

    <link rel="stylesheet" href="RGraph/css/website.css" type="text/css" media="screen"/>
    <!--<link rel="icon" type="image/png" href="../images/favicon.png">-->
    <script src="../js/jquery-1.4.4.js"></script>
    <script src="RGraph/libraries/RGraph.common.core.js"></script>
    <script src="RGraph/libraries/RGraph.common.context.js"></script>
    <script src="RGraph/libraries/RGraph.common.annotate.js"></script>
    <script src="RGraph/libraries/RGraph.common.tooltips.js"></script>
    <script src="RGraph/libraries/RGraph.common.zoom.js"></script>
    <script src="RGraph/libraries/RGraph.common.resizing.js"></script>
    <script src="RGraph/libraries/RGraph.line.js"></script>
    <script src="RGraph/libraries/RGraph.pie.js"></script>

    <!--    <script src="../js/sdi_statistics.js"></script>-->
    <!--[if IE 8]>
   <script src="RGraph/excanvas/excanvas.compressed.js"></script>-->
    <![endif]-->

<?php


    $con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("cannot connect");
    mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

    if ($_GET['type'] == "line_overtime") {
        //        lineGraph();
        dateLineGraphGoogle();
    }
    elseif ($_GET['type'] == "pie_generic")
    {
        pieGraph();
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


function lineGraph()
{

    //    $sql = "select DATE(updated) as date ,count(*) as count from mission group by DATE(updated)";
    $sql = "SELECT DATE(executed_timestamp) AS date, COUNT(*) AS count, sessionid  ";
    $sql .= "FROM sessionwebos.sessioninfo  ";
    $sql .= "WHERE executed = 1  ";
    if ($_GET['tester'] != null) {
        $sql .= "AND username = '" . $_GET['tester'] . "' ";
    }
    $sql .= "GROUP BY DATE(executed_timestamp)  ";
    $sql .= "ORDER BY date;";
    //    echo $sql;
    //
    $result = mysql_query($sql);

    $stat = "[";
    $stat1 = "[";
    $labels = "[";
    $i = 0;
    $addComma = false;
    $totalSessions = 0;
    $totalSessions1 = 0;
    if (mysql_num_rows($result) > 0) {
        echo "<script>\n";
        echo "window.onload = function (){\n";
        while ($row = mysql_fetch_array($result))
        {
            $sql = "SELECT SUM(duration_time)/ ";
            $sql .= "  (SELECT normalized_session_time ";
            $sql .= "   FROM settings) AS duration ";
            $sql .= "FROM `sessionwebos`.`sessioninfo`  ";
            $sql .= "WHERE DATE(executed_timestamp) = '" . $row['date'] . "' ";
            if ($_GET['tester'] != null) {
                $sql .= "AND username = '" . $_GET['tester'] . "' ";
            }
            $sql .= "  AND executed = 1;";

            $resultAvrTime = mysql_query($sql);
            $rowDuration = mysql_fetch_row($resultAvrTime);

            $totalSessions = $totalSessions + $rowDuration[0];
            $totalSessions1 = $totalSessions1 + $row['count'];

            if ($addComma) {
                $stat .= ",";
                $stat1 .= ",";
                $labels .= ",";
            }
            $stat .= intval($totalSessions); //$row['count'];
            $stat1 .= intval($totalSessions1); //$row['count'];
            if ($i % 5 == 0) {
                $labels .= "'" . $row['date'] . "'";
            }
            else
            {
                $labels .= "''";
            }
            $addComma = true;
            $i++;

            //        }
        }
        $stat .= "]";
        $stat1 .= "]";
        $labels .= "]";

        //    echo "var statToDisplay = [211,116], [213,116];\n";
        //    echo "var statToDisplay = '[213.3333,116.6666],[213.3333,116.6666]';\n";

        $key = $_GET['tester'] . ", " . $_GET['team'] . ", " . $_GET['sprint'];
        echo "var graphDiv = new RGraph . Line('graphDiv', $stat, $stat1);\n";
        echo "graphDiv .Set('chart.key', ['Accumulated Normalized sessions',' Accumulated Non-normalized sessions']);\n";
        echo "graphDiv . Set('chart.key.background', 'white');\n";
        echo "graphDiv . Set('chart.key.shadow', true);\n";
        echo "graphDiv . Set('chart.key.shadow.offsetx', 0);\n";
        echo "graphDiv . Set('chart.key.shadow.offsety', 0);\n";
        echo "graphDiv . Set('chart.key.shadow.blur', 15);\n";
        echo "graphDiv . Set('chart.key.shadow.color', '#ccc');\n";
        echo "graphDiv . Set('chart.key.rounded', true);\n";
        echo "graphDiv . Set('chart.gutter', 90);\n";
        echo "graphDiv . Set('chart.filled', true);\n";
        echo "graphDiv . Set('chart.tickmarks', null);\n";
        echo "graphDiv . Set('chart.background.barcolor1', 'white');\n";
        echo "graphDiv . Set('chart.background.barcolor2', 'white');\n";
        echo "graphDiv . Set('chart.background.grid.autofit', true);\n";
        echo "graphDiv . Set('chart.title', 'Accumulated test sessions over time');\n";
        echo "graphDiv . Set('chart.colors', ['rgba(169, 222, 244, 0.7)', 'red', '#ff0']);\n";
        echo "graphDiv . Set('chart.fillstyle', ['#daf1fa', '#faa', '#ffa']);\n";
        echo "graphDiv . Set('chart.labels', $labels);\n";
        echo "graphDiv . Set('chart.text.angle', 45);\n";
        echo "graphDiv . Set('chart.yaxispos', 'right');\n";
        echo "graphDiv . Set('chart.linewidth', 2);\n";
        echo "graphDiv . Set('chart.height', 400);\n";

        echo "graphDiv.Set('chart.contextmenu', [['Get PNG', RGraph.showPNG]]);\n";

        echo "graphDiv . Draw();\n";
        echo "}\n";
        echo "</script>\n";
    }
    else
    {
        echo "No sessions found";
    }

}

/*
 * url with GET param
 * values[]
 * lables[]
 * colors[]
 * title
 */
function pieGraph()
{
    $values = $_GET['values'];
    $title = $_GET['title'];

    $addComma = false;
    $pieValues = "[";
    foreach ($values as $value) {
        if ($addComma) {
            $pieValues .= ",";
        }
        $pieValues .= $value;
        $addComma = true;
    }
    $pieValues .= "]";

    $labels = $_GET['labels'];
    $addComma = false;
    $pieLabels = "[";
    foreach ($labels as $label) {
        if ($addComma) {
            $pieLabels .= ",";
        }
        $pieLabels .= "'$label'";
        $addComma = true;
    }
    $pieLabels .= "]";

    if ($_GET['colors'] != null) {
        $colors = $_GET['colors'];
        $addComma = false;
        $pieColors = "[";
        foreach ($colors as $color) {
            if ($addComma) {
                $pieColors .= ",";
            }
            $pieColors .= "'$color'";
            $addComma = true;
        }
        $pieColors .= "]";
    }
    else
    {
        $pieColors = "['rgb(255,0,0)', '#ddd', 'rgb(0,255,0)', 'rgb(0,0,255)', 'rgb(255,255,0)', 'rgb(0,255,255)', 'red', 'pink', 'black', 'white']";
    }

    echo "<script>\n";
    echo "window.onload = function (){\n";
    //    echo "var graphDiv = new RGraph.Pie('graphDiv', [41,37,16,3,3]);\n"; // Create the pie object
    echo "var graphDiv = new RGraph.Pie('graphDiv', $pieValues);\n"; // Create the pie object
    echo "graphDiv.Set('chart.labels', $pieLabels);\n";
    echo "graphDiv.Set('chart.gutter', 30);\n";
    echo "graphDiv.Set('chart.title', '$title');\n";
    echo "graphDiv.Set('chart.shadow', false);\n";
    echo "graphDiv.Set('chart.tooltips.effect', 'fade');\n";
    echo "graphDiv.Set('chart.colors', $pieColors);\n";
    echo "graphDiv.Set('chart.tooltips.event', 'onmousemove');\n";
    //    echo "graphDiv.Set('chart.tooltips', [\n";
    //    echo "    'Internet Explorer 7 (41%)',\n";
    //    echo "    'Internet Explorer 6 (37%)',\n";
    //    echo "    'Mozilla Firefox (16%)',\n";
    //    echo "    'Apple Safari (3%)',\n";
    //    echo "    'Other (3%)'\n";
    //    echo "   ]\n";
    //    echo "  );\n";
    echo "graphDiv.Set('chart.highlight.style', '3d'); // Defaults to 3d anyway; can be 2d or 3d\n";

    echo "if (!RGraph.isIE8()) {\n";
    echo "    graphDiv.Set('chart.zoom.hdir', 'center');\n";
    echo "    graphDiv.Set('chart.zoom.vdir', 'up');\n";
    echo "    graphDiv.Set('chart.labels.sticks', true);\n";
    echo "    graphDiv.Set('chart.labels.sticks.color', '#aaa');\n";
    echo "}\n";
    echo "\n";
    echo "graphDiv.Set('chart.linewidth', 5);\n";
    echo "graphDiv.Set('chart.labels.sticks', true);\n";
    echo "graphDiv.Set('chart.strokestyle', 'white');\n";
    echo "graphDiv.Draw();\n";
    echo "}\n";
    echo "</script>\n";

}

?>