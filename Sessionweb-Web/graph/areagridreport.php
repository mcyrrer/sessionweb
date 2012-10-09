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
           <link rel="stylesheet" type="text/css" href="../js/DataTables/css/demo_page.css">
           <link rel="stylesheet" type="text/css" href="../js/DataTables/css/demo_table.css">
           <link rel="stylesheet" type="text/css" href="../js/DataTables/css/TableTools.css">

           <link rel="stylesheet" type="text/css" href="../js/jqueryui/jquery-ui-1.8.20.custom.css">
           <script src="../js/jquery-1.7.1.js" type="text/javascript"></script>
           <script src="../js/jqueryui/jquery-ui-1.8.20.custom.min.js" type="text/javascript"></script>
           <script type="text/javascript" src="../js/highcharts/highcharts.js"></script>
           <script type="text/javascript" src="../js/highcharts/modules/exporting.js"></script>
           <script type="text/javascript" src="../js/highstock/highstock.js"></script>
           <script type="text/javascript" src="../js/DataTables/js/jquery.dataTables.min.js"></script>
           <script type="text/javascript" src="../js/DataTables/js/TableTools.min.js"></script>
           <script type="text/javascript" src="../js/DataTables/js/ZeroClipboard.js"></script>
           <script src="../js/sessionweb-graph-generic-v20.js" type="text/javascript"></script>
           <script type="text/javascript" charset="utf-8">
			$(document).ready(function() {
				$("#areaTable").dataTable( {
                    "aLengthMenu": [[-1,10, 25, 50,100], ["All",10, 25, 50,100]],
                    "iDisplayLength": -1,
                    "sDom": "T<\"clear\">lfrtip",
                    "sSwfPath": "../js/DataTables/swf/copy_csv_xls_pdf.swf"
                } );

} );

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
    echo "Report is based on on the area field.<br>";

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
    //    echo '<input type="radio" name="buglist" value="yes" />List all bugs found<br />';
    //    echo '<input type="radio" name="reqlist" value="yes" />List all requirement tested';
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
    echo "<h1>Area Grid Report:</h1>";

    echo '<div class="demo">

<div id="tabs">
	<ul>

		<li><a href="#tabs-2">Test Effort by Area</a></li>
		';

    if (isset($_REQUEST['buglist']))
        echo '<li><a href="#tabs-3">Bugs found</a></li>';
    if (isset($_REQUEST['reqlist']))
        echo '<li><a href="#tabs-4">Requirements tested</a></li>';

    echo '	</ul>

	<div id="tabs-2">
		<p>' . getAreaStatisticIntoGridHtml($allSessions) . '</p>
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

    $pageURLTmp = "http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    $pageURLTmp = explode("?",$pageURLTmp);       //to remove params from URI
    $pageURL = $pageURLTmp[0];
    $sprint=$_REQUEST['sprint'];
    $from=$_REQUEST['from'];
    $to=$_REQUEST['to'];
    $pageParams="?sprint=$sprint&from=$from&to=$to";
    echo "<p>URL to this report:<a href='$pageURL$pageParams'>$pageURL$pageParams</a></p>";

}

function getAreaStatisticIntoGridHtml($allSessions)
{
    $settings = getSettings();

    $htmlReturn = "";
    $appsToDisplay = array();
    $areasToDisplay = array();
    $areaCountArray = array();
    $bugsInOneArea = array();
    $requirementsInOneArea = array();
    $durationInOneArea = array();
    $sessionCountForOneArea = array();
    $sessionsByArea = array();
    $areaSessionIdMap = array();
    $allApplicationsBasedOnAreaName = getApplicationsFromAreaNames();


    foreach ($allSessions as $sessionId => $aSession) {
        //print_r(array_keys($aSession));
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

            //echo count($aSession['bugs']) . " : $sessionId<br>";
        }


    }

    $sessionCountForOneArea = array_count_values($areaCountArray);

    $con = getMySqlConnection();
    $allAreas = getAreas();
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
    ksort($areasToDisplay);
    foreach ($areasToDisplay as $areaName => $aArea) {
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


        $htmlReturn .= "
        <tr>
            <td>$aArea</td>
            <td>$nbrOfSessionsInOneArea</td>
            <td>" . round($metricsOnCharter, 2) . "</td>
            <td>" . round($metricsForOneArea['setup'], 2) . "</td>
            <td>" . round($metricsForOneArea['test'], 2) . "</td>
            <td>" . round($metricsForOneArea['bug'], 2) . "</td>
            <td>" . round($metricsForOneArea['opp'], 2) . "</td>
            <td>" . round($metricsForOneArea['durationInHours'], 2) . "</td>
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


?>