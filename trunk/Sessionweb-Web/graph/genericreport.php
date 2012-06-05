<?php
session_start();
require_once('../include/validatesession.inc');
include_once('../config/db.php.inc');
include_once('../include/db.php');
include_once ('../include/commonFunctions.php.inc');
include_once ('../include/session_database_functions.php.inc');
include_once ('../include/session_common_functions.php.inc');
include_once ('../include/graphcommon.inc');
include_once ('../classes/session.php');
if (file_exists('../include/customfunctions.php.inc')) {
    include_once ('../include/customfunctions.php.inc');

}


echo '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
          <meta http-equiv="Content-type" content="text/html;charset=latin-1">
      <title>Sessionweb</title>
           <link rel="stylesheet" type="text/css" href="../css/sprintreport.css">
           <link rel="stylesheet" type="text/css" href="../css/sessionwebcss.css">

           <link rel="stylesheet" type="text/css" href="../js/jqueryui/jquery-ui-1.8.20.custom.css">
           <script src="../js/jquery-1.7.1.js" type="text/javascript"></script>
           <script src="../js/jqueryui/jquery-ui-1.8.20.custom.min.js" type="text/javascript"></script>
           <script type="text/javascript" src="../js/highcharts/highcharts.js"></script>
           <script type="text/javascript" src="../js/highcharts/modules/exporting.js"></script>
           <script type="text/javascript" src="../js/highstock/highstock.js"></script>
           <script src="../js/sessionweb-graph-generic-v20.js" type="text/javascript"></script>
  </head>
<body>
<a name="top"></a>
';

if (isset($_REQUEST['target'])) {
    generateReport();
}
else
{
    echo '<form method="post" action="genericreport.php">';

    echo "<h1>Generic Report:</h1>";
    echo "<h2>What should the report output be based on</h2>";
    echo '<input type="radio" name="target" value="Application" />Application*<br />';
    echo '<input type="radio" name="target" value="Team" />Team';
    echo "<h2>Filter the result by choosing different values below:</h2>";
    echo "<div>Sprint: ";
    echoSprintSelect("", true);
    echo "</div>";
    //    echo "<div>Application: ";
    //    echoApplicationBasedOnAreasSelect("", false, "select_app");
    //    echo "</div>";

    echo '<label for="from">From</label>';
    echo '<input type="text" id="from" name="from"/>';
    echo '<label for="to">to</label>';
    echo '<input type="text" id="to" name="to"/><br>';

    echo '<input type="submit" name="Submit" value="Generate report">';
    echo '<div>*=Applications is filtred out from areas where the paradigm "appname-areaname" is used.</div>';
}

echo '</body>
</html>';

/**
 *
 */
function generateReport()
{
    $con1 = getMySqlConnection();

    $start = time();
    $sql = generateSql();
    $allSessions = generateSessionObjects($sql);
    mysql_close($con1);

    //    print_r($allSessions);
    echo "<h1>Generic report</h1>";

    echo '<div class="demo">

<div id="tabs">
	<ul>
		<li><a href="#tabs-1">Summary</a></li>
		<li><a href="#tabs-2">Detailed</a></li>
		<li><a href="#tabs-3">Bugs found</a></li>
	    <li><a href="#tabs-4">Requirements tested</a></li>

	</ul>
	<div id="tabs-1">
		<p>' . generateOverviewTabContent($allSessions) . '</p>
	</div>
	<div id="tabs-2">
		<p>' . getTeamOrApplicationStatistics($allSessions) . '</p>
	</div>
	<div id="tabs-3">
		<p>' . getNumberOfBugsFoundAsListWithLink($allSessions) . '</p>
	</div>
    <div id="tabs-4">
		<p>' . getNumberOfRequirementsFoundAsListWithLink($allSessions) . '</p>
	</div>
</div>

</div>

';
    $end = time();
    $delta = $end - $start;

    echo "Report generated in $delta sec";
}

function getTeamOrApplicationStatistics($allSessions)
{
    $settings = getSettings();

    $htmlReturn = "";
    $appsToDisplay = array();
    $sessionsByArea = array();
    $allApplicationsBasedOnAreaName = getApplicationsFromAreaNames();

    //To get all "applications" into an array.
    foreach ($allSessions as $sessionId => $aSession)
    {
        $areas = $aSession['areas'];
        foreach ($areas as $area)
        {
            $appName = getApplicationNameFromAreaName($area);
            if (!in_array($appName, $appsToDisplay)) {
                $appsToDisplay[$appName] = $appName;
                $sessionsByArea[$appName] = array();
                $sessionsByArea[$appName][$sessionId] = $aSession;
            }
            else
            {
                $sessionsByArea[$appName][$sessionId] = $aSession;
            }
        }
    }
    $con = getMySqlConnection();
    $allAreas = getAreas();
    mysql_close($con);
    //Print the result
    foreach ($appsToDisplay as $appName => $aApp)
    {
        $durationTimeTotal = 0;
        $numberOfSessions = count($sessionsByArea[$aApp]);
        $htmlReturn .= "<h2>$aApp</h2>";
        $areasUsedInApp = array();
        //Lopar över alla sessioner i Appen $aApp
        foreach ($sessionsByArea[$aApp] as $sessionId => $aSession)
        {
            //echo $aApp;
            $durationTimeTotal = $durationTimeTotal + $aSession['duration_time'];
            $areas = $aSession['areas'];
            //Loopar över alla Areas i en session som tillhör $aApp
            foreach ($areas as $area)
            {
                if (!array_key_exists($area, $areasUsedInApp)) {
                    $areasUsedInApp[$area] = 1;
                }
                else
                {
                    $areasUsedInApp[$area] = $areasUsedInApp[$area] + 1;
                }
            }

        }
        //Loop through all areas in sessionweb and get those that is belongs to the app but have 0 sessions connected.
        foreach ($allAreas as $aArea)
        {
            if (str_startsWith($aArea, $aApp)) {
                if (!array_key_exists($aArea, $areasUsedInApp)) {
                    $areasUsedInApp[$aArea] = 0;

                }
            }

        }
        $durationTimeInHoursTotal = round($durationTimeTotal / 60, 1);
        $timeInSessionsInHoursNormalized = round($durationTimeInHoursTotal / ($settings['normalized_session_time'] / 60), 1);

        // print_r($areasUsedInApp);
        //foreach ($areasUsedInApp as $area => $nbrOfTimes)
        //{
        //    $htmlReturn .= "$area($nbrOfTimes)<br>";
        //}
        $htmlReturn .= "Number of sessions: " . $numberOfSessions . "<br>";
        $htmlReturn .= "Number of normalized sessions: " . $timeInSessionsInHoursNormalized . "<br>";
        $htmlReturn .= "Time in sessions: " . $durationTimeInHoursTotal . "h<br>";


        $htmlReturn .= generateBarChartForAreas("div_" . $appName, $areasUsedInApp);
        $htmlReturn .= "<div id =\"div_" . $appName . "\" style=\"min-width: 1200px; height: 400px; margin: 0 auto\"></div>";
    }
    return $htmlReturn;


}

function generateBarChartForAreas($divName, $areasUsedInApp)
{
    ksort($areasUsedInApp);
    $firstTime = true;
    $category = "";
    $data = "{
			data: [";
    foreach ($areasUsedInApp as $area => $nbrOfTimes)
    {
        if (!$firstTime) {
            $category .= ",";
            $data .= ",";

        }
        $category .= "'$area'";
        $data .= "$nbrOfTimes";

        $firstTime = false;
    }
    $data .= "]}";
    $html = "<script type=\"text/javascript\">

(function($){ // encapsulate jQuery

var chart;
$(document).ready(function() {
	chart = new Highcharts.Chart({
		chart: {
			renderTo: '" . $divName . "',
			type: 'column'
		},
		title: {
			text: 'Number of Sessions Per Area'
		},
		xAxis: {
			categories: [

				$category
			],
			labels: {
				rotation: -45,
				align: 'right',
				style: {
					font: 'normal 13px Verdana, sans-serif'
				}
			}
		},
		yAxis: {
			min: 0,
			title: {
				text: 'Number Of Sessions'
			}
		},
		legend: {
			layout: 'vertical',
			backgroundColor: '#FFFFFF',
			align: 'left',
			verticalAlign: 'top',
			x: 100,
			y: 70,
			floating: true,
			shadow: true
		},
		tooltip: {
			formatter: function() {
				return ''+
					this.x +': '+ this.y;
			}
		},
		plotOptions: {
			column: {
				pointPadding: 0.2,
				borderWidth: 0
			}
		},
			series: [$data]
	});
});

})(jQuery);
</script>";

    return $html;
}

function generateSessionObjects($sql)
{

    $allSessions = array();
    $result = mysql_query($sql);

    while ($row = mysql_fetch_array($result))
    {
        $aSessionObject = new session($row['sessionid']);
        $allSessions[$row['sessionid']] = $aSessionObject->getSession();
    }
    return $allSessions;
}

function generateSql()
{
    $sql = "SELECT sessionid FROM mission limit 0,100";

    return $sql;
}


function getTotalTimeInSessionInHours($allSessions)
{
    $duration = 0;
    foreach ($allSessions as $aSessions)
    {
        $duration = $duration + $aSessions['duration_time'];
    }
    return round($duration / 60, 1);
}

function getNumberOfBugsFound($allSessions)
{
    $bugCount = 0;
    foreach ($allSessions as $aSession)
    {
        $bugCount = $bugCount + count($aSession['bugs']);
    }
    return $bugCount;
}

function getNumberOfRequirementsFound($allSessions)
{
    $requirementsCount = 0;
    foreach ($allSessions as $aSession)
    {
        $requirementsCount = $requirementsCount + count($aSession['requirements']);
    }
    return $requirementsCount;
}

function getNumberOfRequirementsFoundAsListWithLink($allSessions)
{
    $settings = getSettings();
    $dmsRms = $settings['url_to_rms'];
    $html = "";
    foreach ($allSessions as $aSession)
    {
        if (count($aSession['requirements']) != null) {
            foreach ($aSession['requirements'] as $aReq)
                if (file_exists('../include/customfunctions.php.inc')) {
                    $title = getRequirementNameFromServer($aReq);
                }
                else
                {
                    $title = $aReq;
                }
            $html .= "<a href='$dmsRms$aReq'>$aReq - $title<a><br>";
        }
    }
    return $html;
}


function getNumberOfBugsFoundAsListWithLink($allSessions)
{
    $settings = getSettings();
    $dmsUrl = $settings['url_to_dms'];
    $html = "";
    foreach ($allSessions as $aSession)
    {
        if (count($aSession['bugs']) != null) {
            foreach ($aSession['bugs'] as $aBug)
                if (file_exists('../include/customfunctions.php.inc')) {
                    $title = getBugNameFromServer($aBug);
                }
                else
                {
                    $title = $aBug;
                }
            $html .= "<a href='$dmsUrl$aBug'>$aBug - $title<a><br>";
        }
    }
    return $html;
}

function generateOverviewTabContent($allSessions)
{
    $settings = getSettings();
    $timeInSessionsInHours = getTotalTimeInSessionInHours($allSessions);
    $timeInSessionsInHoursNormalized = round($timeInSessionsInHours / ($settings['normalized_session_time'] / 60), 1);
    $htmlString = "<table border='0' width='100%'>";
    $htmlString .= "<tr>";
    $htmlString .= "<td valign='top'>";
    $htmlString .= "<div>Number of sessions: " . count($allSessions) . "</div>";
    $htmlString .= "<div>Number of normalized sessions: " . $timeInSessionsInHoursNormalized . " ( one normalized session = " . $settings['normalized_session_time'] . " min)    </div>";
    $htmlString .= "</td>";
    $htmlString .= "<td valign='top'>";
    $htmlString .= "<div>Time in sessions: " . $timeInSessionsInHours . "h</div>";
    $htmlString .= "<div>Requirements tested: " . getNumberOfRequirementsFound($allSessions) . "</div>";
    $htmlString .= "<div>Bugs found: " . getNumberOfBugsFound($allSessions) . "</div>";
    $htmlString .= "</td>";
    $htmlString .= "</tr>";
    $htmlString .= "<tr>";

    $htmlString .= "<td valign='top' width=50%>";
    $htmlString .= '<div id="containerProgress"></div>';


    $htmlString .= "</td>";
    $htmlString .= "<td valign='top'>";
    $htmlString .= getPieCharTimeDistribution($allSessions, "timeDistcontainer");
    $htmlString .= '<div id="timeDistcontainer"></div>';


    $parameters = ""; //"sprint=Apr12";


    $htmlString .= "<script type='text/javascript'>
$(function() {
    var params = '" . $parameters . "';

	$.getJSON('../api/statistics/progress/index.php?'+params+'&callback=?', function(data) {
		// Create the chart
		window.chart = new Highcharts.StockChart({
			chart : {
				renderTo : 'containerProgress'
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


    $htmlString .= "</td>";
    $htmlString .= "</tr>";

    $htmlString .= "</table>";
    return $htmlString;
}

?>