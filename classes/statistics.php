<?php

require_once 'logging.php';
require_once 'sessionReadObject.php';

class statistics
{
    private $logger;

    function __construct($sessionid = null)
    {
        $this->logger = new logging();
    }

    /**
     * Create HTML links to each unique bug in the sessionReadObject array provided
     * @param $allSessions a array of sessionReadObjects
     * @return string html code with link to each bug
     */
    public function getNumberOfBugsFoundAsListWithLink($allSessions)
    {
        $settings = ApplicationSettings::getSettings();
        $dmsUrl = $settings['url_to_dms'];
        $htmlReturn = "
    <div id=\"save\">
    <table class='display' id='bugTable'>
     <thead>
        <tr>
            <th width='60'>Bug id</th>
            <th>Bug title</th>

        </tr>
      </thead>
      <tbody>";
        $buglist = array();
        foreach ($allSessions as $aSession) {
            if (count($aSession['bugs']) != null) {
                foreach ($aSession['bugs'] as $aBug) {
                    if (!in_array($aBug, $buglist)) {
                        $buglist[$aBug] = $aBug;
                        if (file_exists('../include/customfunctions.php.inc')) {
                            $title = getBugNameFromServer($aBug);
                        } else {
                            $title = $aBug;
                        }
                        $htmlReturn .= "
                    <tr>
                        <td><a href='$dmsUrl$aBug'>$aBug</a></td>
                        <td>$title</td>

                    </tr>";
                    }
                }
            }
        }

        $htmlReturn .= "
    </tbody>
    </table>
    </div>";
        return $htmlReturn;
    }

    /**
     * Create HTML links to each unique req in the sessionReadObject array provided
     * @param $allSessions a array of sessionReadObjects
     * @return string html code with link to each req
     */
    public function getNumberOfRequirementsFoundAsListWithLink($allSessions)
    {
        $settings = ApplicationSettings::getSettings();
        $dmsRms = $settings['url_to_rms'];
        $htmlReturn = "
    <div id=\"save\">
    <table class='display' id='reqTable'>
     <thead>
        <tr>
            <th width='60'>Req id</th>
            <th>Requirement title</th>
        </tr>
      </thead>
      <tbody>";
        $reqlist = array();
        foreach ($allSessions as $aSession) {
            if (count($aSession['requirements']) != null) {
                foreach ($aSession['requirements'] as $aReq) {
                    if (!in_array($aReq, $reqlist)) {
                        $reqlist[$aReq] = $aReq;
                        if (file_exists('../include/customfunctions.php.inc')) {
                            $title = getRequirementNameFromServer($aReq);
                        } else {
                            $title = $aReq;
                        }
                        $htmlReturn .= "
                    <tr>
                        <td><a href='$dmsRms$aReq'>$aReq<a></td>
                        <td>" . htmlspecialchars($title) . "</td>
                    </tr>";
                    }
                }
            }
        }

        $htmlReturn .= "
    </tbody>
    </table>
    </div>";
        return $htmlReturn;
    }

    public function generateOverviewTabContent($allSessions, $sql)
    {
        $sql = explode("WHERE", $sql);
        $sql = explode("LIMIT", $sql[1]);
        $sql = urlencode($sql[0]);

        $settings = ApplicationSettings::getSettings();
        $timeInSessionsInHours = $this->getTotalTimeInSessionInHours($allSessions);
        $timeInSessionsInHoursNormalized = round($timeInSessionsInHours / ($settings['normalized_session_time'] / 60), 2);
        $htmlString = "<table border='0' width='100%'>";
        $htmlString .= "<tr>";
        $htmlString .= "<td valign='top'>";
        $htmlString .= "<div>Number of sessions: " . count($allSessions) . "</div>";
        $htmlString .= "<div>Number of normalized sessions: " . $timeInSessionsInHoursNormalized . " ( one normalized session = " . $settings['normalized_session_time'] . " min)    </div>";
        $htmlString .= "</td>";
        $htmlString .= "<td valign='top'>";
        $htmlString .= "<div>Time in sessions: " . $timeInSessionsInHours . "h</div>";
        $htmlString .= "<div>Requirements tested: " . $this->getNumberOfRequirementsFound($allSessions) . "</div>";
        $htmlString .= "<div>Bugs found: " . $this->getNumberOfBugsFound($allSessions) . "</div>";
        $htmlString .= "</td>";
        $htmlString .= "</tr>";
        $htmlString .= "<tr>";

        $htmlString .= "<td valign='top' width=50%>";
        $htmlString .= '<div id="containerProgress"></div>';


        $htmlString .= "</td>";
        $htmlString .= "<td valign='top'>";
        $htmlString .= $this->getPieCharTimeDistribution($allSessions, "timeDistcontainer");
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

    function getChartersIntoGridHtml($allSessions)
    {
        $dbm = new dbHelper();
        $htmlReturn = "";
        $chartersToDisplay = array();
        $areaCountArray = array();
        $testerArray = array();

        foreach ($allSessions as $sessionId => $aSession) {
            $chartersToDisplay[$sessionId] = $aSession['title'];
            $testerArray[$sessionId] = $aSession['username'];
        }
        //Print the result
        $htmlReturn .= "
    <div id=\"save\">
    <table class='display' id='charterTable'>
     <thead>
        <tr>
            <th width='60'>SessionId</th>
            <th>Charter title</th>
            <th width='200'>Tester</th>
        </tr>
      </thead>
      <tbody>";
        ksort($chartersToDisplay);
        foreach ($chartersToDisplay as $sessionId => $title) {

            $htmlReturn .= "
        <tr>
            <td><a href='view.php?sessionid=$sessionId'>$sessionId</a></td>
            <td>$title</td>
            <td>" . QueryHelper::getTesterFullName($testerArray[$sessionId]) . "</td>
        </tr>";
        }
        $htmlReturn .= "
    </tbody>
    </table>
    </div>";
        return $htmlReturn;
    }


    public function getNumberOfBugsFound($allSessions)
    {
        $bugArray = array();
        foreach ($allSessions as $aSession) {
            $bugArray = array_merge($bugArray, $aSession['bugs']);
        }
        $bugArrayUnique = array_unique($bugArray);
        return count($bugArrayUnique);
    }

    public function getNumberOfRequirementsFound($allSessions)
    {
        $reqArray = array();
        foreach ($allSessions as $aSession) {
            $reqArray = array_merge($reqArray, $aSession['requirements']);
        }
        $reqArrayUnique = array_unique($reqArray);
        return count($reqArrayUnique);
    }

    public function getTotalTimeInSessionInHours($allSessions)
    {
        $duration = 0;
        foreach ($allSessions as $aSessions) {
            $duration = $duration + $aSessions['duration_time'];
        }
        return round($duration / 60, 1);
    }

    public function getPieCharTimeDistribution($allSessions, $divId, $title = "Time distribution")
    {
        $setup = 0;
        $test = 0;
        $bug = 0;
        $opp = 0;
        $duration = 0;

        foreach ($allSessions as $sessionid => $sessionObject) {
            $setup = $setup + $sessionObject['setup_percent'];
            $test = $test + $sessionObject['test_percent'];
            $bug = $bug + $sessionObject['bug_percent'];
            $opp = $opp + $sessionObject['opportunity_percent'];
            $duration = $duration + $sessionObject['duration_time'];
        }
        if ($allSessions != 0 && $setup != 0)
            $setup = $setup / count($allSessions);
        if ($allSessions != 0 && $test != 0)
            $test = $test / count($allSessions);
        if ($allSessions != 0 && $bug != 0)
            $bug = $bug / count($allSessions);
        if ($allSessions != 0 && $opp != 0)
            $opp = $opp / count($allSessions);

        $setupTime = round($setup * $duration / 100, 2);
        $testTime = round($test * $duration / 100, 2);
        $bugTime = round($bug * $duration / 100, 2);
        $oppTime = round($opp * $duration / 100, 2);

        $htmlString = '
<script type="text/javascript">
    var chart;
    $(document).ready(function () {
        chart = new Highcharts.Chart({
            chart:{
                renderTo:\'' . $divId . '\',
                plotBackgroundColor:null,
                plotBorderWidth:null,
                plotShadow:false
            },
            title:{
                text:\'' . $title . '\'
            },
            tooltip:{
                formatter:function () {
                    return \'<b>\' + this.point.name + \'</b>: \' + this.percentage.toFixed(1) + \' %\';
                }
            },
            plotOptions:{
                pie:{
                    allowPointSelect:true,
                    cursor:\'pointer\',
                    dataLabels:{
                        enabled:true,
                        color:\'#000000\',
                        connectorColor:\'#000000\',
                        formatter:function () {
                            return \'<b>\' + this.point.name + \'</b>: \' + this.percentage.toFixed(1) + \' %\';
                        }
                    }
                }
            },
            series:[
                    {
                        type:\'pie\',
                        name:\'Browser share\',
                        data:
                        [{
                        name: \'Setup (' . $setupTime . 'h)\',
                        y:  ' . $setup . ',
                        color: \'#0000FF\'
                    }, {
                        name: \'Test (' . $testTime . 'h)\',
                        y: ' . $test . ',
                        color: \'#00FF00\'
                    }, {
                        name: \'Bug (' . $bugTime . 'h)\',
                        y: ' . $bug . ',
                        color: \'#FF0000\'
                    }, {
                        name: \'opportunity (' . $oppTime . 'h)\',
                        y: ' . $opp . ',
                        color: \'#000000\'
                    }
                ]
                }
            ]
        });
    });

</script>';
        return $htmlString;

    }

    public static function generateSqlToGetAllSessionsForStatistics()
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

    public function generateSessionObjectsForStatistics()
    {
        $dbh = new dbHelper();
        $sql = self::generateSqlToGetAllSessionsForStatistics();
        $results = $dbh->executeQuery($con = $dbh->connectToLocalDb(), $sql);
        $allSessions = null;
        while ($row = mysqli_fetch_array($results)) {
            $aSessionObject = new sessionReadObject($row['sessionid']);
            $allSessions[$row['sessionid']] = $aSessionObject->getSession();
        }
        return $allSessions;

    }

}
