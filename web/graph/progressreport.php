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
           <script type="text/javascript" src="../js/highstock/highstock.js"></script>
            <script type="text/javascript" src="../js/highstock/modules/exporting.js"></script>
  </head>
<body>
<a name="top"></a>';

if (isset($_REQUEST['tester']) || isset($_REQUEST['team']) || isset($_REQUEST['sprint'])) {

    printChart();
}
else
{
    echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';

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

function printChart()
{
    $parameters = "";
    if(isset($_REQUEST['tester']) && strcmp($_REQUEST['tester'],'')!=0)
    {
      $parameters = "tester=".$_REQUEST['tester'];
    }
    if(isset($_REQUEST['team']) && strcmp($_REQUEST['team'],'')!=0)
    {
        $parameters = $parameters."&team=".$_REQUEST['team'];
    }
    if(isset($_REQUEST['sprint']) && strcmp($_REQUEST['sprint'],'')!=0)
    {
        $parameters = $parameters."&sprint=".$_REQUEST['sprint'];
    }
    echo "<script type='text/javascript'>
$(function() {
    var params = '".$parameters."';

	$.getJSON('../api/statistics/progress/index.php?'+params+'&callback=?', function(data) {
		// Create the chart
		window.chart = new Highcharts.StockChart({
		    
			chart : {
				renderTo : 'container'
			},

			rangeSelector : {
				selected : 1
			},

			title : {
				text : 'Progress over time'
			},

			series : [{
				name : 'Total number of sessions',
				data : data,
				tooltip: {
					valueDecimals: 2
				}
			}],
			xAxis: {
            type: 'datetime',
            dateTimeLabelFormats: { // don't display the dummy year
                month: '%e. %b',
                year: '%b'
            },
            title: {
                text: 'Date'
            }
			}
		});
	});

});

		</script>";

    echo '<div id="container" style="width: 800px; height: 400px; margin: 0 auto"></div>';
}

?>