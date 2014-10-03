<?php
session_start();
require_once('../include/validatesession.inc');
include_once('../config/db.php.inc');
include_once('../include/db.php');
include_once ('../include/commonFunctions.php.inc');
include_once ('../include/session_database_functions.php.inc');
include_once ('../include/session_common_functions.php.inc');
include_once ('../include/graphcommon.inc');
include_once ('../classes/sessionReadObject.php');
include_once ('../classes/statistics.php');
include_once ('../classes/logging.php');
include_once ('../classes/pagetimer.php');
include_once ('../classes/dbHelper.php');
if (file_exists('../include/customfunctions.php.inc')) {
    include_once ('../include/customfunctions.php.inc');

}
$pageTimer = new pagetimer();
$pageTimer->startMeasurePageLoadTime();

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
          <meta http-equiv="Content-type" content="text/html;charset=utf-8">
      <title>Sessionweb</title>

           <link rel="stylesheet" type="text/css" href="../css/sprintreport.css">
           <link rel="stylesheet" type="text/css" href="../css/sessionwebcss.css">
           <link rel="stylesheet" type="text/css" href="../js/DataTables/css/demo_page.css">
           <link rel="stylesheet" type="text/css" href="../js/DataTables/css/demo_table.css">
           <link rel="stylesheet" type="text/css" href="../js/DataTables/css/TableTools.css">

           <link rel="stylesheet" type="text/css" href="../js/jqueryui/jquery-ui-1.10.0.custom.css">
           <script src="../js/jquery-1.9.1.min.js" type="text/javascript"></script>
           <script src="../js/jqueryui/jquery-ui-1.10.0.custom.min.js" type="text/javascript"></script>
           <script type="text/javascript" src="../js/highstock/highstock.js"></script>
<!--           <script type="text/javascript" src="../js/highcharts/modules/exporting.js"></script>-->

           <script type="text/javascript" src="../js/DataTables/js/jquery.dataTables.min.js"></script>
           <script type="text/javascript" src="../js/DataTables/js/TableTools.min.js"></script>
           <script type="text/javascript" src="../js/DataTables/js/ZeroClipboard.js"></script>
           <script src="../js/sessionweb-graph-generic-v20.js" type="text/javascript"></script>
           <script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
            TableTools.DEFAULTS.sSwfPath = "../js/DataTables/swf/copy_csv_xls_pdf.swf";
            $("#tabs").tabs({
                "show": function (event, ui) {
                    var jqTable = $("table.display", ui.panel);
                    if (jqTable.length > 0) {
                        var oTableTools = TableTools.fnGetInstance(jqTable[0]);
                        if (oTableTools != null && oTableTools.fnResizeRequired()) {
                            /* A resize of TableTools" buttons and DataTables" columns is only required on the
                             * first visible draw of the table
                             */
                            jqTable.dataTable().fnAdjustColumnSizing();
                            oTableTools.fnResizeButtons();
                        }
                    }
                }
            });

            $("#areaTable").dataTable({
                "bJQueryUI": true,
                 "aLengthMenu": [[-1,10, 25, 50,100], ["All",10, 25, 50,100]],
                 "iDisplayLength": 50,
                "sPaginationType": "full_numbers",
                "sDom": "T<\"clear\">lfrtip"
            });

            $("#bugTable").dataTable({
                "bJQueryUI": true,
                 "aLengthMenu": [[-1,10, 25, 50,100], ["All",10, 25, 50,100]],
                 "iDisplayLength": 50,
                "sPaginationType": "full_numbers",
                "sDom": "T<\"clear\">lfrtip"
            });

            $("#reqTable").dataTable({
                "bJQueryUI": true,
                 "aLengthMenu": [[-1,10, 25, 50,100], ["All",10, 25, 50,100]],
                 "iDisplayLength": 50,
                "sPaginationType": "full_numbers",
                "sDom": "T<\"clear\">lfrtip"
            });

            $("#charterTable").dataTable({
                "bJQueryUI": true,
                 "aLengthMenu": [[-1,10, 25, 50,100], ["All",10, 25, 50,100]],
                 "iDisplayLength": 50,
                "sPaginationType": "full_numbers",
                "sDom": "T<\"clear\">lfrtip"
            });

            });
		    </script>

  </head>
<body>
<a name="top"></a>
';

if (isset($_REQUEST['sprint'])) {
    generateReport();
} else {
    echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '">';

    echo "<h1>Area Grid Report:</h1>";
    echo "Report is based on on the area field. <br>
    It contains data from the database that have an area and is in state executed/closed/debriefed.<br>";

    echo "<h2>Filter the result by choosing different values below:</h2>";
    echo "<div>Sprint: ";
    echoSprintSelect("", true);
    echo "</div>";
    echo "And/or<br>";
    echo '<label for="from">From</label>';
    echo '<input type="text" id="from" name="from"/>';
    echo '<label for="to">to</label>';
    echo '<input type="text" id="to" name="to"/><br>';

//    echo "<h2>Include bug and requirement list</h2>";
    echo '<input type="checkbox" name="all" value="true" />List areas that have 0 sessions<br />';
//    echo '<input type="radio" name="reqlist" value="yes" />List all requirement tested';
    echo '<br><input type="submit" name="Submit" value="Generate report">';

}
$pageTimer->stopMeasurePageLoadTime();
$pageTimer->echoTime();

if (isset($_REQUEST['sprint'])) {
    $pageURLTmp = "http://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    $pageURLTmp = explode("?", $pageURLTmp); //to remove params from URI
    $pageURL = $pageURLTmp[0];
    if (isset($_REQUEST['sprint'])) {
        $sprint = "sprint=" . $_REQUEST['sprint'];
    } else {
        $sprint = "";
    }

    if (isset($_REQUEST['from'])) {
        $from = "from=" . $_REQUEST['from'];
    } else {
        $from = "";
    }

    if (isset($_REQUEST['to'])) {
        $to = "to=" . $_REQUEST['to'];
    } else {
        $to = "";
    }

    if (isset($_REQUEST['all'])) {
        $all = "all=true";
    } else {
        $all = "";
    }
    $pageParams = "?$sprint&$from&$to&$all";
    echo "<br><p>URL to this report:<a href='$pageURL$pageParams'>$pageURL$pageParams</a></p>";
}


echo '</body>
</html>';

/**
 *
 */
function generateReport()
{
    $con1 = getMySqlConnection();
    $statHelper = new statistics();

    $sql = generateSql();
    $allSessions = generateSessionObjects($sql);
    mysql_close($con1);

    //    ($allSessions);
    echo "<h1>Area Grid Report:</h1>";

    echo '<div class="demo">

<div id="tabs">
	<ul>
        <li><a href="#tabs-1">Summary</a></li>

		<li><a href="#tabs-2">Test Effort by Area</a></li>
		';


    echo '<li><a href="#tabs-3">Bugs found</a></li>';
    echo '<li><a href="#tabs-4">Requirements tested</a></li>';
    echo '<li><a href="#tabs-5">Sessions</a></li>';

    echo '	</ul>
    <div id="tabs-1">
		<p>' . $statHelper->generateOverviewTabContent($allSessions, $sql) . '</p>
	</div>
	<div id="tabs-2">
		<p>' . getAreaStatisticIntoGridHtml($allSessions) . '</p>
	</div>';
    echo '
	<div id="tabs-3">
		<p>' . $statHelper->getNumberOfBugsFoundAsListWithLink($allSessions) . '</p>
	</div>';
    echo '
    <div id="tabs-4">
		<p>' . $statHelper->getNumberOfRequirementsFoundAsListWithLink($allSessions) . '</p>
	</div>

	    <div id="tabs-5">
		<p>' . $statHelper->getChartersIntoGridHtml($allSessions) . '</p>
	</div>

	';
    echo '</div>

</div>

';

    echo "Report based on SQL <br>$sql";


}

function getAreaStatisticIntoGridHtml($allSessions)
{
    $logger = new logging();
    $settings = getSettings();

    $htmlReturn = "";
    $appsToDisplay = array();
    $areasWithOneOrMoreSessions = array();
    $areaCountArray = array();
    $bugsInOneArea = array();
    $requirementsInOneArea = array();
    $durationInOneArea = array();
    $sessionCountForOneArea = array();
    $sessionsByArea = array();
    $areaSessionIdMap = array();
    $allApplicationsBasedOnAreaName = getApplicationsFromAreaNames();


    list($areasWithOneOrMoreSessions, $areaCountArray, $sessionsByArea, $bugsInOneArea, $requirementsInOneArea, $durationInOneArea) = getAllAreasWithOneOrMoreSessions($allSessions, $areasWithOneOrMoreSessions, $areaCountArray, $sessionsByArea, $bugsInOneArea, $requirementsInOneArea, $durationInOneArea);

    $sessionCountForOneArea = array_count_values($areaCountArray);

    $con = getMySqlConnection();
    $allAreas = getAreasFromAreaTable();
    mysql_close($con);
    //Print the result
    $htmlReturn .= "
    <div id=\"save\">
    <table class='display' id='areaTable'>
     <thead>
        <tr>
            <th>Area</th>
            <th>Charter count</th>
            <th>On Charter(%)</th>
            <th>Setup(%)</th>
            <th>Test(%)</th>
            <th>Bug(%)</th>
            <th>Opportunity(%)</th>
            <th>Duration(h)</th>
            <th>Bug count</th>
            <th>Requirement count</th>
        </tr>
      </thead>
      <tbody>";

    $areasToLoop = getArrayToLoop($allAreas, $areasWithOneOrMoreSessions);

    ksort($areasToLoop);


    foreach ($areasToLoop as $areaName => $aArea) {
        list($metricsOnCharter, $nbrOfSessionsInOneArea, $bugCountForOneArea, $reqCountForOneArea, $setup, $test, $bug, $opp, $durationInHours) = getValuesToPopulateHtmlGrid($areaName, $sessionsByArea, $aArea, $allSessions, $sessionCountForOneArea, $bugsInOneArea, $requirementsInOneArea, $durationInOneArea);


        $htmlReturn .= "
        <tr>
            <td>$aArea</td>
            <td>$nbrOfSessionsInOneArea</td>
            <td>" . round($metricsOnCharter, 2) . "</td>
            <td>$setup</td>
            <td>$test</td>
            <td>$bug</td>
            <td>$opp</td>
            <td>$durationInHours</td>
            <td>$bugCountForOneArea</td>
            <td>$reqCountForOneArea</td>
        </tr>";


//        $htmlReturn .= "<div id =\"div_" . $areaName . "\" style=\"min-width: 1200px; height: 400px; margin: 0 auto\"></div>";
    }
    $htmlReturn .= "
    </tbody>
    </table>
    </div>";
//    print_r($sessionCountForOneArea);
    return $htmlReturn;


}

function getAllAreasWithOneOrMoreSessions($allSessions, $areasToDisplay, $areaCountArray, $sessionsByArea, $bugsInOneArea, $requirementsInOneArea, $durationInOneArea)
{
    foreach ($allSessions as $sessionId => $aSession) {
        if (count($aSession['areas']) != 0) {
            foreach ($aSession['areas'] as $area) {
                if (!in_array($area, $areasToDisplay)) {
                    $areasToDisplay[$area] = $area;
                }
                $areaCountArray[] = $area;

                if (isset($sessionsByArea[$area]))
                    $sessionsByArea[$area] = array_merge($sessionsByArea[$area], array($sessionId));
                else
                    $sessionsByArea[$area] = array($sessionId);

                if (isset($bugsInOneArea[$area]))
                    $bugsInOneArea[$area] = array_merge($bugsInOneArea[$area], $aSession['bugs']);
                else
                    $bugsInOneArea[$area] = $aSession['bugs'];

                if (isset($requirementsInOneArea[$area]))
                    $requirementsInOneArea[$area] = array_merge($requirementsInOneArea[$area], $aSession['requirements']);
                else
                    $requirementsInOneArea[$area] = $aSession['requirements'];

                if (isset($durationInOneArea[$area]))
                    $durationInOneArea[$area] = $durationInOneArea[$area] + $aSession['duration_time'];
                else
                    $durationInOneArea[$area] = $aSession['duration_time'];
            }
        } else {
            $area = "Areas not specified";
            if (!in_array($area, $areasToDisplay)) {
                $areasToDisplay[$area] = $area;
            }
            $areaCountArray[] = $area;

            if (isset($sessionsByArea[$area]))
                $sessionsByArea[$area] = array_merge($sessionsByArea[$area], array($sessionId));
            else
                $sessionsByArea[$area] = array($sessionId);

            if (isset($bugsInOneArea[$area]))
                $bugsInOneArea[$area] = array_merge($bugsInOneArea[$area], $aSession['bugs']);
            else
                $bugsInOneArea[$area] = $aSession['bugs'];

            if (isset($requirementsInOneArea[$area]))
                $requirementsInOneArea[$area] = array_merge($requirementsInOneArea[$area], $aSession['requirements']);
            else
                $requirementsInOneArea[$area] = $aSession['requirements'];

            if (isset($durationInOneArea[$area]))
                $durationInOneArea[$area] = $durationInOneArea[$area] + $aSession['duration_time'];
            else
                $durationInOneArea[$area] = $aSession['duration_time'];
        }
    }
    return array($areasToDisplay, $areaCountArray, $sessionsByArea, $bugsInOneArea, $requirementsInOneArea, $durationInOneArea);
}

function getValuesToPopulateHtmlGrid($areaName, $sessionsByArea, $aArea, $allSessions, $sessionCountForOneArea, $bugsInOneArea, $requirementsInOneArea, $durationInOneArea)
{
    if (array_key_exists($areaName, $sessionsByArea)) {
        $sessionsThatBelongsToOneArea = $sessionsByArea[$aArea];
        $metricsForOneArea = getMetricsForOneArea($sessionsThatBelongsToOneArea, $allSessions);
        $metricsOnCharter = 100 - $metricsForOneArea['opp'];
        if (isset($sessionCountForOneArea[$aArea]))
            $nbrOfSessionsInOneArea = $sessionCountForOneArea[$aArea];
        else
            $nbrOfSessionsInOneArea = 0;

        if (isset($bugsInOneArea[$aArea]))
            $bugCountForOneArea = count(array_unique($bugsInOneArea[$aArea]));
        else
            $bugCountForOneArea = 0;

        if (isset($requirementsInOneArea[$aArea]))
            $reqCountForOneArea = count(array_unique($requirementsInOneArea[$aArea]));
        else
            $reqCountForOneArea = 0;

        if (isset($requirementsInOneArea[$aArea]))
            $durationForOneArea = round($durationInOneArea[$aArea] / 60, 2);
        else
            $durationForOneArea = 0;
        $setup = round($metricsForOneArea['setup'], 2);
        $test = round($metricsForOneArea['test'], 2);
        $bug = round($metricsForOneArea['bug'], 2);
        $opp = round($metricsForOneArea['opp'], 2);
        $durationInHours = round($metricsForOneArea['durationInHours'], 2);
        return array($metricsOnCharter, $nbrOfSessionsInOneArea, $bugCountForOneArea, $reqCountForOneArea, $setup, $test, $bug, $opp, $durationInHours);
    } else {
        $sessionsThatBelongsToOneArea = 0;
        $metricsOnCharter = 0;
        $nbrOfSessionsInOneArea = 0;
        $bugCountForOneArea = 0;
        $reqCountForOneArea = 0;
        $durationForOneArea = 0;
        $setup = 0;
        $test = 0;
        $bug = 0;
        $opp = 0;
        $durationInHours = 0;
        return array($metricsOnCharter, $nbrOfSessionsInOneArea, $bugCountForOneArea, $reqCountForOneArea, $setup, $test, $bug, $opp, $durationInHours);
    }
}

function getArrayToLoop($allAreas, $areasThatHaveSessions)
{
    if (isset($_REQUEST['all'])) {
        if (strstr($_REQUEST['all'], "true") != false) {
            $areasToLoop = $allAreas;
            if (array_key_exists("Areas not specified", $areasThatHaveSessions)) {
                $areasToLoop["Areas not specified"] = "Areas not specified";
                return $areasToLoop;
            }
            return $areasToLoop;
        } else {
            $areasToLoop = $areasThatHaveSessions;
            return $areasToLoop;
        }
    } else {
        $areasToLoop = $areasThatHaveSessions;
        return $areasToLoop;
    }
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
        $areaMetrics['durationInHours'] = 0;
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
                    name: 'Opportunity',
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
    $result = dbHelper::sw_mysql_execute($sql, __FILE__, __LINE__);
    //$result = mysql_query($sql);

    while ($row = mysql_fetch_array($result)) {
        $aSessionObject = new sessionReadObject($row['sessionid']);
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

?>