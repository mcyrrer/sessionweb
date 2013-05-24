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
           <link rel="stylesheet" href="../css/sessionwebcss.css">
           <script src="../js/jquery-1.9.1.min.js" type="text/javascript"></script>
           <script type="text/javascript" src="../js/highcharts/highcharts.js"></script>
            <script type="text/javascript" src="../js/highcharts/modules/exporting.js"></script>
  </head>
<body>
<a name="top"></a>';

if (isset($_REQUEST['tester']) || isset($_REQUEST['team']) || isset($_REQUEST['sprint'])) {
    $con = getMySqlConnection();
    pieTimeDistribution();

    mysql_close($con);
}
else
{
    echo '<form method="post" action="timedistribution.php">';

    echo "Tester:";
    if ($_SESSION['useradmin'] == 1) {
        echoTesterFullNameSelect("");

    }
    else
    {
        echo "<select id='select_tester' name='tester'> \n";
        echo "        <option></option>\n";
        echo "        <option>" . $_SESSION['username'] . "</option> \n";
        echo "</select>\n";

    }

    if ($_SESSION['useradmin'] == 1) {
        echo "Team:";
        echoTeamSelect("",true);
    }

    echo "Sprint:";
    echoSprintSelect("",true);


    echo '<input type="submit" name="Submit" value="Generate report">';
}

function pieTimeDistribution()
{

    //TODO: Fix undefined for team in javascript!!!!!
    $sql = "SELECT SUM(setup_percent) as setup,SUM(test_percent) as test, SUM(bug_percent) as bug, SUM(opportunity_percent) as opportunity, COUNT(*) as numberOfSessions  ";
    $sql .= "FROM sessioninfo  ";
    $sql .= "WHERE executed = 1  ";
    if ($_REQUEST['tester'] != null) {
        if ($_SESSION['useradmin'] == 1) {
            $sql .= "AND username = '" . urldecode($_REQUEST['tester']) . "' ";
        }
    }
    if ($_REQUEST['team'] != null) {
        if ($_SESSION['useradmin'] == 1) {
            $sql .= "AND teamname = '" . urldecode($_REQUEST['team']) . "' ";
        }
    }
    if ($_REQUEST['sprint'] != null) {
        $sql .= "AND sprintname = '" . urldecode($_REQUEST['sprint']) . "' ";
    }

    $result = mysql_query($sql);
    //echo $sql; //For debug purpose!
    if ($result) {

        $addComma = false;
        $i = 1;
        $row = mysql_fetch_array($result);
        $setup = round($row['setup'], 2);
        $test = round($row['test'], 2);
        $bug = round($row['bug'], 2);
        $opportunity = round($row['opportunity'], 2);
        $numberOfSessions = $row['numberOfSessions'];
        $tester = getTesterFullName($_REQUEST['tester']);
        $team = $_REQUEST['team'];
        $sprint = $_REQUEST['sprint'];
        $title = " | $tester | $team | $sprint |";


        ?>

    <script type="text/javascript">
        var chart;
        $(document).ready(function () {
            chart = new Highcharts.Chart({
                chart:{
                    renderTo:'container',
                    plotBackgroundColor:null,
                    plotBorderWidth:null,
                    plotShadow:false
                },
                title:{
                    text:'Time distribution executed sessions <?php echo $title;?>'
                },
                tooltip:{
                    formatter:function () {
                        return '<b>' + this.point.name + '</b>: ' + this.percentage.toFixed(1) + ' %';
                    }
                },
                plotOptions:{
                    pie:{
                        allowPointSelect:true,
                        cursor:'pointer',
                        dataLabels:{
                            enabled:true,
                            color:'#000000',
                            connectorColor:'#000000',
                            formatter:function () {
                                return '<b>' + this.point.name + '</b>: ' + this.percentage.toFixed(1) + ' %';
                            }
                        }
                    }
                },
                series:[
                    {
                        type:'pie',
                        name:'Browser share',
                        data:
                            [{
                            name: 'Setup',
                            y:  <?php echo $setup;?>,
                            color: '#0000FF'
                            }, {
                            name: 'Test',
                            y: <?php echo $test;?>,
                            color: '#00FF00'
                            }, {
                            name: 'Bug',
                            y: <?php echo $bug;?>,
                                color: '#FF0000'
                            }, {
                            name: 'Opportunity',
                            y: <?php echo $opportunity;?>,
                                color: '#000000'
                            }

                        ]
                    }
                ]
            });
        });

    </script>
    <?php
        //        echo "<script type='text/javascript' src='https://www.google.com/jsapi'></script>\n";
        //        echo "    <script type='text/javascript'>\n";
        //        echo "      google.load('visualization', '1', {packages:['corechart']});\n";
        //        echo "      google.setOnLoadCallback(drawChart);\n";
        //        echo "      function drawChart() {\n";
        //        echo "        var data = new google.visualization.DataTable();\n";
        //        echo "        data.addColumn('string', 'Task');\n";
        //        echo "        data.addColumn('number', 'output');\n";
        //        echo "          data.addRows(4);\n";
        //        echo "          data.setValue(0, 0, 'Setup');\n";
        //        echo "          data.setValue(0, 1, $setup);\n";
        //        echo "          data.setValue(1, 0, 'Test');\n";
        //        echo "          data.setValue(1, 1, $test);\n";
        //        echo "          data.setValue(2, 0, 'Bug');\n";
        //        echo "          data.setValue(2, 1, $bug);\n";
        //        echo "          data.setValue(3, 0, 'Opportunity');\n";
        //        echo "          data.setValue(3, 1, $opportunity);\n";
        //
        //        // Create and draw the visualization.
        //        echo "var chart = new google.visualization.PieChart(document.getElementById('chart_div'));\n";
        //        echo "chart.draw(data, {width: 750, height: 500, title: 'Time distribution', is3D : 'true'});\n";
        //        echo "        }\n";
        //        echo "    </script>\n";
    }
    else
    {
        $failedToCreateGraph = true;
    }

    echo '<div id="container" style="width: 800px; height: 400px; margin: 0 auto"></div>';
}

?>