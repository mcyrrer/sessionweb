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
           <script src="../js/jquery-1.7.1.js" type="text/javascript"></script>
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
    echo '<form method="post" action="'.$PHP_SELF.'">';

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
        echoTeamSelect("");
    }

    echo "Sprint:";
    echoSprintSelect("");


    echo '<input type="submit" name="Submit" value="Generate report">';
}

function printChart()
{
    echo "<script type='text/javascript'>
$(function() {

	$.getJSON('../api/statistics/progress/index.php?callback=?', function(data) {
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
			}]
		});
	});

});

		</script>";

    echo '<div id="container" style="width: 800px; height: 400px; margin: 0 auto"></div>';
}

?>