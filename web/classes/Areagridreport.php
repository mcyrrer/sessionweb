<?php

require_once('classes/logging.php');
require_once('classes/statistics.php');
require_once('classes/dbHelper.php');

if (file_exists('include/customfunctions.php.inc')) {
    require_once('include/customfunctions.php.inc');

}

class Areagridreport
{
    var $logger = null;
    var $con = null;
    var $dbh = null;
    var $statHelper = null;

    function __construct()
    {
        $this->logger = new logging();
        $this->dbh = new dbHelper();
        $this->statHelper = new statistics();
        $this->con = $this->dbh->db_getMySqliConnection();
    }

    public function generateAreaGridReport()
    {
        echo '    <script type="text/javascript">

       $(document).ready(function() {
           $("#areaTable").DataTable({
                "bJQueryUI": true,
                 "aLengthMenu": [[-1,10, 25, 50,100], ["All",10, 25, 50,100]],
                 "iDisplayLength": 50,
                "sPaginationType": "full_numbers",
                "sDom": "T<\"clear\">lfrtip"
            });
           $("#bugTable").DataTable({
                "bJQueryUI": true,
                 "aLengthMenu": [[-1,10, 25, 50,100], ["All",10, 25, 50,100]],
                 "iDisplayLength": 50,
                "sPaginationType": "full_numbers",
                "sDom": "T<\"clear\">lfrtip"
            });

            $("#reqTable").DataTable({
                "bJQueryUI": true,
                 "aLengthMenu": [[-1,10, 25, 50,100], ["All",10, 25, 50,100]],
                 "iDisplayLength": 50,
                "sPaginationType": "full_numbers",
                "sDom": "T<\"clear\">lfrtip"
            });
            $("#charterTable").DataTable({
                "bJQueryUI": true,
                 "aLengthMenu": [[-1,10, 25, 50,100], ["All",10, 25, 50,100]],
                 "iDisplayLength": 50,
                "sPaginationType": "full_numbers",
                "sDom": "T<\"clear\">lfrtip"
            });
       } );
       </script>';

        $sessionObjects = $this->statHelper->generateSessionObjectsForStatistics();
        //ToDo:Genereate nbr of bug, time etc....
        $settings = getSettings();
        $timeInSessionsInHours = $this->statHelper->getTotalTimeInSessionInHours($sessionObjects);
        $timeInSessionsInHoursNormalized = round($timeInSessionsInHours / ($settings['normalized_session_time'] / 60), 2);
        echo '

        <div id="tabs">
	<ul>
		<li><a href="#tabs-1">Summary</a></li>
		<li><a href="#tabs-2">Detailed</a></li>
    ';


        echo '<li><a href="#tabs-3">Bugs found</a></li>';

        echo '<li><a href="#tabs-4">Requirements tested</a></li>';

        echo '<li><a href="#tabs-5">Sessions</a></li>';


        echo '	</ul>
	<div id="tabs-1">
		<p>';
        ProgressGraphHelper::getProgressGraphJavaScriptCode();
        echo '<table border="1"><tr><td>';
        echo 'Number of sessions: ' . count($sessionObjects) . '<br>';
        echo 'Number of normalized sessions: ' . $timeInSessionsInHoursNormalized . '( one normalized session = ' . $settings['normalized_session_time'] . ' min)<br>';
        echo '<td>';
        echo 'Time in sessions: ' . $this->statHelper->getTotalTimeInSessionInHours($sessionObjects) . '<br>';
        echo 'Requirements tested: ' . $this->statHelper->getNumberOfRequirementsFound($sessionObjects) . '<br>';
        echo 'Bugs found: ' . $this->statHelper->getNumberOfBugsFound($sessionObjects) . '<br>';

        echo '</td></tr><tr>


        </tr></table>
                <div id="container" style="width: 100%; height: 400px; margin: 0 auto"></div>

		</p>
	</div>
	<div id="tabs-2">
		<p>' . $this->getAreaGridReportStatisticIntoGridHtml($sessionObjects) . '</p>



	</div>';
        echo '
    <div id="tabs-3">
		<p>';
        echo $this->statHelper->getNumberOfBugsFoundAsListWithLink($sessionObjects);

		echo '</p>
	</div>';
        echo '
    <div id="tabs-4">
		<p>';
        echo $this->statHelper->getNumberOfRequirementsFoundAsListWithLink($sessionObjects);

        echo '</p>
	</div>
	<div id="tabs-5">
		<p>' . $this->statHelper->getChartersIntoGridHtml($sessionObjects) . '</p>
	</div>
    </div>';

    }

    private function generateSqlToGetAllSessions()
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


    private function getAreaGridReportStatisticIntoGridHtml($allSessions)
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


        list($areasWithOneOrMoreSessions, $areaCountArray, $sessionsByArea, $bugsInOneArea, $requirementsInOneArea, $durationInOneArea) = $this->getAreaGridReportAllAreasWithOneOrMoreSessions($allSessions, $areasWithOneOrMoreSessions, $areaCountArray, $sessionsByArea, $bugsInOneArea, $requirementsInOneArea, $durationInOneArea);

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

        $areasToLoop = $this->getArrayToLoop($allAreas, $areasWithOneOrMoreSessions);

        ksort($areasToLoop);


        foreach ($areasToLoop as $areaName => $aArea) {
            list($metricsOnCharter, $nbrOfSessionsInOneArea, $bugCountForOneArea, $reqCountForOneArea, $setup, $test, $bug, $opp, $durationInHours) = $this->getValuesToPopulateHtmlGrid($areaName, $sessionsByArea, $aArea, $allSessions, $sessionCountForOneArea, $bugsInOneArea, $requirementsInOneArea, $durationInOneArea);


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

    private function getAreaGridReportAllAreasWithOneOrMoreSessions($allSessions, $areasToDisplay, $areaCountArray, $sessionsByArea, $bugsInOneArea, $requirementsInOneArea, $durationInOneArea)
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

    private function getArrayToLoop($allAreas, $areasThatHaveSessions)
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


    function getValuesToPopulateHtmlGrid($areaName, $sessionsByArea, $aArea, $allSessions, $sessionCountForOneArea, $bugsInOneArea, $requirementsInOneArea, $durationInOneArea)
    {
        if (array_key_exists($areaName, $sessionsByArea)) {
            $sessionsThatBelongsToOneArea = $sessionsByArea[$aArea];
            $metricsForOneArea = $this->getMetricsForOneArea($sessionsThatBelongsToOneArea, $allSessions);
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

    private function getMetricsForOneArea($sessionsThatBelongsToArea, $allSessions)
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
}

?>