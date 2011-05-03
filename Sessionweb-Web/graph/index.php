<?php
session_start();
if (!session_is_registered(myusername)) {
    header("location:index.php");
}
//include("../include/header.php.inc");
include_once('../config/db.php.inc');
include_once ('../include/commonFunctions.php.inc');
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
        lineGraph();
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
    <div>
        <canvas id="graphDiv" width="800" height="400"> [Please wait...]</canvas>
    </div>
</div>
</body>
</html>
<?php

function lineGraph()
{
    echo "<script>\n";
    echo "window.onload = function (){\n";
    $sql = "select DATE(updated) as date ,count(*) as count from mission group by DATE(updated)";
    $result = mysql_query($sql);

    $stat = "var statToDisplay = [";
    $labels = "[";
    $addComma = false;

    while ($row = mysql_fetch_array($result))
    {
        if ($addComma) {
            $stat .= ",";
            $labels .= ",";
        }
        $stat .= $row['count'];
        $labels .= "'" . $row['date'] . "'";
        $addComma = true;
    }
    $stat .= "];\n";
    $labels .= "]";

    echo $stat;

    $key = $_GET['tester'] . ", " . $_GET['team'] . ", " . $_GET['sprint'];
    echo "var graphDiv = new RGraph . Line('graphDiv', statToDisplay);\n";
    echo "graphDiv .Set('chart.key', ['$key']);\n";
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
    echo "graphDiv . Set('chart.title', 'Accumulated test sessions over time(Normalized)');\n";
    echo "graphDiv . Set('chart.colors', ['rgba(169, 222, 244, 0.7)', 'red', '#ff0']);\n";
    echo "graphDiv . Set('chart.fillstyle', ['#daf1fa', '#faa', '#ffa']);\n";
    echo "graphDiv . Set('chart.labels', $labels);\n";
    echo "graphDiv . Set('chart.text.angle', 45);\n";
    echo "graphDiv . Set('chart.yaxispos', 'right');\n";
    echo "graphDiv . Set('chart.linewidth', 5);\n";
    echo "graphDiv . Set('chart.height', 400);\n";

    echo "graphDiv.Set('chart.contextmenu', [['Get PNG', RGraph.showPNG]]);\n";

    echo "graphDiv . Draw();\n";
    echo "}\n";
    echo "</script>\n";

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