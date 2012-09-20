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

if (isset($_REQUEST['sprint'])) {
    generateReport();
} else {
    echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '">';

    echo "<h1>Application Report:</h1>";
    echo "Report is based on on the area field and the application is filtred based on the prefix of the area.<br>";
    echo "If the area is \"Firefox-javascript\" then the application will be Firefox and the area will be javascript in the graphs created";
    //    echo "<h2>What should the report output be based on</h2>";
    //    echo '<input type="radio" name="target" value="Application" checked/>Application*<br />';
    //    echo '<input type="radio" name="target" value="Team" />Team';
    echo "<h2>Filter the result by choosing different values below:</h2>";
    echo "<div>Sprint: ";
    echoSprintSelect("", true);
    echo "</div>";
    //    echo "<div>Application: ";
    //    echoApplicationBasedOnAreasSelect("", false, "select_app");
    //    echo "</div>";
    echo "And/or<br>";

    echo '<label for="from">From</label>';
    echo '<input type="text" id="from" name="from"/>';
    echo '<label for="to">to</label>';
    echo '<input type="text" id="to" name="to"/><br>';

    echo "<h2>Include bug and requirement list</h2>";
    echo '<input type="radio" name="buglist" value="yes" />List all bugs found<br />';
    echo '<input type="radio" name="reqlist" value="yes" />List all requirement tested';
    echo '<br><input type="submit" name="Submit" value="Generate report">';

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

    //    ($allSessions);
    echo "<h1>Application Report</h1>";

    echo '<div class="demo">

<div id="tabs">
	<ul>
		<li><a href="#tabs-1">Summary</a></li>
		<li><a href="#tabs-2">Detailed</a></li>
		';

    if (isset($_REQUEST['buglist']))
        echo '<li><a href="#tabs-3">Bugs found</a></li>';
    if (isset($_REQUEST['reqlist']))
        echo '<li><a href="#tabs-4">Requirements tested</a></li>';

    echo '	</ul>
	<div id="tabs-1">
		<p>' . generateOverviewTabContent($allSessions, $sql) . '</p>
	</div>
	<div id="tabs-2">
		<p>' . getTeamOrApplicationStatistics($allSessions) . '</p>
	</div>';
    if (isset($_REQUEST['buglist']))
        echo '
	<div id="tabs-3">
		<p>' . getNumberOfBugsFoundAsListWithLink($allSessions) . '</p>
	</div>';
    if (isset($_REQUEST['reqlist']))
        echo '
    <div id="tabs-4">
		<p>' . getNumberOfRequirementsFoundAsListWithLink($allSessions) . '</p>
	</div>';
    echo '</div>

</div>

';
    $end = time();
    $delta = $end - $start;

    echo "Report generated in $delta sec";
    echo "<br>";
    echo "Report based on SQL <br>$sql";

}

function getTeamOrApplicationStatistics($allSessions)
{
    $settings = getSettings();

    $htmlReturn = "";
    $appsToDisplay = array();
    $sessionsByArea = array();
    $areaSessionIdMap = array();
    $allApplicationsBasedOnAreaName = getApplicationsFromAreaNames();

    //To get all "applications" into an array.
    foreach ($allSessions as $sessionId => $aSession) {
        $areas = $aSession['areas'];
        foreach ($areas as $area) {
            $appName = getApplicationNameFromAreaName($area);
            if (!in_array($appName, $appsToDisplay)) {
                $appsToDisplay[$appName] = $appName;
                $sessionsByArea[$appName] = array();
                $sessionsByArea[$appName][$sessionId] = $aSession;
                $areaSessionIdMap[$sessionId] = $area;
            } else {
                $sessionsByArea[$appName][$sessionId] = $aSession;
                $areaSessionIdMap[$sessionId] = $area;
            }
        }
    }
    $con = getMySqlConnection();
    $allAreas = getAreas();
    mysql_close($con);
    //Print the result
    foreach ($appsToDisplay as $appName => $aApp) {
        $durationTimeTotal = 0;
        $numberOfSessions = count($sessionsByArea[$aApp]);
        $htmlReturn .= "<h2>$aApp</h2>";
        $areasUsedInApp = array();

        $setupTime = 0;
        $testTime = 0;
        $bugtime = 0;
        $oppTime = 0;


        iterateAllSessionsForOneApplication($sessionsByArea, $aApp, $setupTime, $testTime, $bugtime, $oppTime, $durationTimeTotal, $areasUsedInApp);

        $areasUsedInApp = setAreasWithZeroSessionsToZero($allAreas, $aApp, $areasUsedInApp);

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


        $htmlReturn .= generateBarChartForAreas("div_" . $appName, $areasUsedInApp, round($setupTime, 2), round($testTime, 2), round($bugtime, 2), round($oppTime, 2), $areaSessionIdMap, $allSessions);
        $htmlReturn .= "<div id =\"div_" . $appName . "\" style=\"min-width: 1200px; height: 400px; margin: 0 auto\"></div>";
    }
    return $htmlReturn;


}

function iterateAllSessionsForOneApplication($sessionsByArea, $aApp, &$setupTime, &$testTime, &$bugtime, &$oppTime, &$durationTimeTotal, &$areasUsedInApp)
{
    foreach ($sessionsByArea[$aApp] as $sessionId => $aSession) {
        $setupTime = $setupTime + (($aSession['duration_time'] * $aSession['setup_percent']) / 100 / 60);
        $testTime = $testTime + (($aSession['duration_time'] * $aSession['test_percent']) / 100 / 60);
        $bugtime = $bugtime + (($aSession['duration_time'] * $aSession['bug_percent']) / 100 / 60);
        $oppTime = $oppTime + (($aSession['duration_time'] * $aSession['opportunity_percent']) / 100 / 60);

        $durationTimeTotal = $durationTimeTotal + $aSession['duration_time'];

        $areas = $aSession['areas'];

        $areasUsedInApp = iterateAllAreasForOneApplication($areas, $areasUsedInApp);
    }
}

/**
 * iterate over all Areas for one session that belongs to $aApp
 * @param $areas
 * @param $areasUsedInApp
 * @return array
 */
function iterateAllAreasForOneApplication($areas, $areasUsedInApp)
{
    foreach ($areas as $area) {
        if (!array_key_exists($area, $areasUsedInApp)) {
            $areasUsedInApp[$area] = 1;
        } else {
            $areasUsedInApp[$area] = $areasUsedInApp[$area] + 1;
        }
    }
    return $areasUsedInApp;
}

/**
 * Loop through all areas in sessionweb and get those that belongs to the app but have 0 sessions connected.
 * @param $allAreas
 * @param $aApp
 * @param $areasUsedInApp
 * @return array
 */
function setAreasWithZeroSessionsToZero($allAreas, $aApp, $areasUsedInApp)
{
    foreach ($allAreas as $aArea) {
        if (str_startsWith($aArea, $aApp)) {
            if (!array_key_exists($aArea, $areasUsedInApp)) {
                $areasUsedInApp[$aArea] = 0;
            }
        }
    }
    return $areasUsedInApp;
}

function generateBarChartForAreas($divName, $areasUsedInApp, $setupTime, $testTime, $bugtime, $oppTime, $areaSessionIdMap, $allSessions)
{
    //    print_r($areaSessionIdMap);
    ksort($areasUsedInApp);
    $firstTime = true;
    $category = "";
    $dataColumnNumberOfSessions = "{
            type: 'column',
            name: 'Number of sessions',
			data: [";
    $dataColumnTimeDistributionSetup = "{
            type: 'column',
            name: 'Setup %',
			data: [";
    $dataColumnTimeDistributionTest = "{
            type: 'column',
            name: 'Test %',
			data: [";
    $dataColumnTimeDistributionBug = "{
            type: 'column',
            name: 'Bug %',
			data: [";
    $dataColumnTimeDistributionOpp = "{
            type: 'column',
            name: 'Opportunity %',
			data: [";
    foreach ($areasUsedInApp as $area => $nbrOfTimes) {
        //echo $area . "<br>";
        $sessionsThatBelongsToArea = array_keys($areaSessionIdMap, $area);
        //print_r($sessionsThatBelongsToArea);

        $areaMetrics = getMetricsForOneArea($sessionsThatBelongsToArea, $allSessions);

        if (!$firstTime) {
            $category .= ",";
            $dataColumnNumberOfSessions .= ",";
            $dataColumnTimeDistributionSetup .= ",";
            $dataColumnTimeDistributionTest .= ",";
            $dataColumnTimeDistributionBug .= ",";
            $dataColumnTimeDistributionOpp .= ",";

        }
        $category .= "'$area'";
        $dataColumnNumberOfSessions .= "$nbrOfTimes";

        $dataColumnTimeDistributionSetup .= $areaMetrics['setup'];
        $dataColumnTimeDistributionTest .= $areaMetrics['test'];
        $dataColumnTimeDistributionBug .= $areaMetrics['bug'];
        $dataColumnTimeDistributionOpp .= $areaMetrics['opp'];

        $firstTime = false;
    }
    $dataColumnNumberOfSessions .= "],
       stack: 'sessionCount',
       color: '#808000',
       yAxis: 0
    }";
    $dataColumnTimeDistributionSetup .= "],
       stack: 'metrics',
       color: '#0000FF',
       yAxis: 1
    }";
    $dataColumnTimeDistributionTest .= "],
       stack: 'metrics',
       color: '#00FF00',
       yAxis: 1
    }";
    $dataColumnTimeDistributionBug .= "],
       stack: 'metrics',
       color: '#FF0000',
       yAxis: 1
    }";
    $dataColumnTimeDistributionOpp .= "],
       stack: 'metrics',
       color: '#000000',
       yAxis: 1
    }";

    $html = generateJavascriptCodeForBarChart($divName, $category, $dataColumnNumberOfSessions, $dataColumnTimeDistributionSetup, $dataColumnTimeDistributionTest, $dataColumnTimeDistributionBug, $dataColumnTimeDistributionOpp, $setupTime, $testTime, $bugtime, $oppTime, $areaMetrics);

    return $html;
}

/**
 * @param $sessionsThatBelongsToArea
 * @param $allSessions
 * @return array
 */
function getMetricsForOneArea($sessionsThatBelongsToArea, $allSessions)
{
    $areaSetupTime = 0;
    $areaTestTime = 0;
    $areaBugTime = 0;
    $areaOppTime = 0;
    $areaDuration = 0;

    foreach ($sessionsThatBelongsToArea as $sessionid) {
        $areaSetupTime = $areaSetupTime + $allSessions[$sessionid]['setup_percent'];
        $areaTestTime = $areaTestTime + $allSessions[$sessionid]['test_percent'];
        $areaBugTime = $areaBugTime + $allSessions[$sessionid]['bug_percent'];
        $areaOppTime = $areaOppTime + $allSessions[$sessionid]['opportunity_percent'];
        $areaDuration = $areaDuration + $allSessions[$sessionid]['duration_time'];
    }
    $numberOfSessionsInOneArea = count($sessionsThatBelongsToArea);
    $areaMetrics = array();

    if ($areaSetupTime != 0 && $numberOfSessionsInOneArea != 0)
        $areaMetrics['setup'] = $areaSetupTime / $numberOfSessionsInOneArea;
    else
        $areaMetrics['setup'] = 0;

    if ($areaTestTime != 0 && $numberOfSessionsInOneArea != 0)
        $areaMetrics['test'] = $areaTestTime / $numberOfSessionsInOneArea;
    else
        $areaMetrics['test'] = 0;

    if ($areaBugTime != 0 && $numberOfSessionsInOneArea != 0)
        $areaMetrics['bug'] = $areaBugTime / $numberOfSessionsInOneArea;
    else
        $areaMetrics['bug'] = 0;

    if ($areaOppTime != 0 && $numberOfSessionsInOneArea != 0)
        $areaMetrics['opp'] = $areaOppTime / $numberOfSessionsInOneArea;
    else
        $areaMetrics['opp'] = 0;

    if ($areaDuration != 0)
        $areaMetrics['durationInHours'] = $areaDuration / 60;
    else
        $areaDuration = 0;
    return $areaMetrics;
}

function generateJavascriptCodeForBarChart($divName, $category, $dataColumnNumberOfSessions, $dataColumnTimeDistributionSetup, $dataColumnTimeDistributionTest, $dataColumnTimeDistributionBug, $dataColumnTimeDistributionOpp, $setupTime, $testTime, $bugtime, $oppTime)
{
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
			text: 'Number of Sessions Per Application'
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
            yAxis: [{ // Primary yAxis
                labels: {
                    formatter: function() {
                        return this.value;
                    },
                    style: {
                        color: '#89A54E'
                    }
                },
                title: {
                    text: 'Nbr of sessions',
                    style: {
                        color: '#89A54E'
                    }
                },
                opposite: false

            },
            { // Tertiary yAxis
                gridLineWidth: 0,
                title: {
                    text: 'Time distribution',
                    style: {
                        color: '#AA4643'
                    }
                },
                labels: {
                    formatter: function() {
                        return this.value;
                    },
                    style: {
                        color: '#AA4643'
                    }
                },
                opposite: true,
                max: 100
            }],
            tooltip: {
                formatter: function() {
                    var unit = {
                        'Number of sessions': 'sessions',
                        'Setup %': '% Setup',
                        'Bug %': '% Bug',
                        'Opportunity %': '% Opportunity',
                        'Test %': '% Test'
                    }[this.series.name];

                    return ''+
                        this.x +': '+ this.y +' '+ unit;
                }
            },
		labels: {
                items: [{
                    html: 'Time distribution',
                    style: {
                        left: '10px',
                        top: '0px',
                        color: 'black'
                    }
                }]
            },
		plotOptions: {
			column: {
				pointPadding: 0.2,
				borderWidth: 0,
                stacking: 'normal'
			}
		},
			series: [
			$dataColumnNumberOfSessions
			,
			$dataColumnTimeDistributionSetup
			,
			$dataColumnTimeDistributionTest
			,
			$dataColumnTimeDistributionBug
			,
			$dataColumnTimeDistributionOpp
			,{
                type: 'pie',
                name: 'Total consumption',
                data: [{
                    name: 'Setup',
                    y: $setupTime,
                    color: '#0000FF'
                }, {
                    name: 'Test',
                    y: $testTime,
                    color: '#00FF00'
                }, {
                    name: 'Bug',
                    y: $bugtime,
                    color: '#FF0000'
                }, {
                    name: 'opportunity',
                    y: $oppTime,
                    color: '#000000'
                }],
                center: [50, 50],
                size: 60,
                showInLegend: false,
                dataLabels: {
                    enabled: false
                }
             }]
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

    while ($row = mysql_fetch_array($result)) {
        $aSessionObject = new session($row['sessionid']);
        $allSessions[$row['sessionid']] = $aSessionObject->getSession();
    }
    return $allSessions;
}

function generateSql()
{
    $sql = "SELECT sessionid "; //" FROM mission ";
    $addWhere = true;
    $sql .= "FROM sessioninfo  ";
    $sql .= "WHERE (executed = 1 OR debriefed = 1 OR closed = 1)  ";

    if (strcmp($_REQUEST['sprint'], "") != 0) {
        $sql .= " AND ";
        $sql .= " sprintname = \"" . $_REQUEST['sprint'] . "\" ";
        $addWhere = false;
    }
    if (strcmp($_REQUEST['from'], "") != 0 && strcmp($_REQUEST['to'], "") != 0) {
        //        if ($addWhere) {
        //            $sql .= "WHERE ";
        //            $addWhere = false;
        //        }
        //        else
        //        {
        $sql .= " AND ";

        //        }
        $sql .= "`updated` > '" . $_REQUEST['from'] . " 00:00:00' AND `updated`  <  '" . $_REQUEST['to'] . " 00:00:00' ";
    }
    $sql .= " LIMIT 0,10000";

    return $sql;
}


function getTotalTimeInSessionInHours($allSessions)
{
    $duration = 0;
    foreach ($allSessions as $aSessions) {
        $duration = $duration + $aSessions['duration_time'];
    }
    return round($duration / 60, 1);
}


function getNumberOfBugsFound($allSessions)
{
    $bugArray = array();
    foreach ($allSessions as $aSession) {
        $bugArray = array_merge($bugArray, $aSession['bugs']);
    }
    $bugArrayUnique = array_unique($bugArray);
    return count($bugArrayUnique);
}

function getNumberOfRequirementsFound($allSessions)
{
    $reqArray = array();
    foreach ($allSessions as $aSession) {
        $reqArray = array_merge($reqArray, $aSession['requirements']);
    }
    $reqArrayUnique = array_unique($reqArray);
    return count($reqArrayUnique);
}

function getNumberOfRequirementsFoundAsListWithLink($allSessions)
{
    $settings = getSettings();
    $dmsRms = $settings['url_to_rms'];
    $html = "";
    foreach ($allSessions as $aSession) {
        if (count($aSession['requirements']) != null) {
            foreach ($aSession['requirements'] as $aReq)
            {
                if (file_exists('../include/customfunctions.php.inc')) {
                    $title = getRequirementNameFromServer($aReq);
                } else {
                    $title = $aReq;
                }
                $html .= "<a href='$dmsRms$aReq'>$aReq - $title<a><br>";
            }
        }
    }
    return $html;
}


function getNumberOfBugsFoundAsListWithLink($allSessions)
{
    $settings = getSettings();
    $dmsUrl = $settings['url_to_dms'];
    $html = "";
    foreach ($allSessions as $aSession) {

        if (count($aSession['bugs']) != null) {
            foreach ($aSession['bugs'] as $aBug)
            {
                if (file_exists('../include/customfunctions.php.inc')) {
                    $title = getBugNameFromServer($aBug);
                } else {
                    $title = $aBug;
                }
                $html .= "<a href='$dmsUrl$aBug'>$aBug - $title<a><br>";
            }
        }
    }
    return $html;
}

function generateOverviewTabContent($allSessions, $sql)
{
    $sql = explode("WHERE", $sql);
    $sql = explode("LIMIT", $sql[1]);
    $sql = urlencode($sql[0]);

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

	$.getJSON('../api/statistics/progress/index.php?'+params+'&callback=?&sql=$sql', function(data) {
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