<?php
class statistics
{
    /**
     * Create HTML links to each unique bug in the sessionReadObject array provided
     * @param $allSessions a array of sessionReadObjects
     * @return string html code with link to each bug
     */
    public function getNumberOfBugsFoundAsListWithLink($allSessions)
    {
        $settings = getSettings();
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
        foreach ($allSessions as $aSession) {

            if (count($aSession['bugs']) != null) {
                foreach ($aSession['bugs'] as $aBug) {
                    if (file_exists('../include/customfunctions.php.inc')) {
                        $title = getBugNameFromServer($aBug);
                    } else {
                        $title = $aBug;
                    }
                    $htmlReturn .= "
                    <tr>
                        <td>$aBug<a></td>
                        <td><a href='$dmsUrl$aBug'>$title<a></td>

                    </tr>";
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
        $settings = getSettings();
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
        foreach ($allSessions as $aSession) {

            if (count($aSession['requirements']) != null) {
                foreach ($aSession['requirements'] as $aReq) {
                    if (file_exists('../include/customfunctions.php.inc')) {
                        $title = getRequirementNameFromServer($aReq);
                    } else {
                        $title = $aReq;
                    }
                    $htmlReturn .= "
                    <tr>
                        <td>$aReq</td>
                        <td><a href='$dmsRms$aReq'>$title<a></td>

                    </tr>";
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

    function getChartersIntoGridHtml($allSessions)
    {
        $settings = getSettings();

        $htmlReturn = "";
        $chartersToDisplay = array();
        $appsToDisplay = array();
        $areasToDisplay = array();
        $areaCountArray = array();
        $bugsInOneArea = array();
        $requirementsInOneArea = array();
        $durationInOneArea = array();
        $sessionCountForOneArea = array();
        $sessionsByArea = array();
        $areaSessionIdMap = array();
        $testerArray = array();


        $allApplicationsBasedOnAreaName = getApplicationsFromAreaNames();


        foreach ($allSessions as $sessionId => $aSession) {
            $chartersToDisplay[$sessionId] = $aSession['title'];
            $testerArray[$sessionId] = $aSession['username'];
        }
        $sessionCountForOneArea = array_count_values($areaCountArray);

        $con = getMySqlConnection();
        $allAreas = getAreas();
        mysql_close($con);
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
            <td><a href='../session.php?sessionid=$sessionId&command=view'>$sessionId</a></td>
            <td>$title</td>
            <td>" . getTesterFullName($testerArray[$sessionId], true) . "</td>
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

}
