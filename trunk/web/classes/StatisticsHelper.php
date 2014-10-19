<?php
require_once('include/commonFunctions.php.inc');
require_once('classes/Areagridreport.php');
require_once('classes/ProgressGraphHelper.php');

class StatisticsHelper
{

    function __construct()
    {
    }


    public static function getStatisticMainPage()
    {
        return '
            <table>
                <tr>
                    <td width="600">
                        <p><span class="larger"> <a class="largepopup cboxElement" href="graph/sprintreport.php">Sprint report</a>
                        </span></p>

                        <p><img src="pictures/sprintreport.png" alt=""></p>
                    </td>
                    <td>
                        <p><span class="larger"><a class="mediumpopup cboxElement" href="graph/timedistribution.php">Time distribution</a></span></p>

                        <p><img src="pictures/timedistribution.png" alt=""></p>

                    </td>
                </tr>
                <tr>
                    <td width="600">
                        <p><span class="larger"><a class="mediumpopup cboxElement" href="graph/wordcloud.php?all=true">Word cloud</a></span></p>
                        <p><img src="pictures/wordcloud.png" alt=""></p>
                    </td>
                    <td>
                        <p><span class="larger"><a href="?type=progressOverTime&action=select">Progress over time</a></span></p>
                        <p><img src="pictures/progressovertime.png" alt=""></p>
                    <td>
                </tr>
                <tr>
                    <td width="600">
                        <p><span class="larger"> <a href="graph/applicationreport.php">Application report</a>
                        </span></p>
                        <p><img src="pictures/appreport.png" alt=""></p>
                    </td>
                    <td><p><span class="larger"> <a href="?type=areaGridReport&action=select">Area grid report</a>
                        </span></p>
                        <p><img src="pictures/areagridreport.png" alt=""></p>
                    </td>
                </tr>
            </table>';
    }


    public static function getProgressReportSelectionPage()
    {

        echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '?type=progressOverTime&action=display">

    Tester:';
        if ($_SESSION['useradmin'] == 1) {
            echoTesterFullNameSelect("");
        } else {
            echo "<select id='select_tester' name='tester'> \n";
            echo "        <option></option>\n";
            echo "        <option>" . $_SESSION['username'] . "</option> \n";
            echo "</select>\n";

        }

        if ($_SESSION['useradmin'] == 1) {
            echo "Team:";
            echoTeamSelect("", true);
        }

        echo "Sprint:";
        echoSprintSelect("", true);


        echo '<input type="submit" name="Submit" value="Generate graph">';

    }

    public static function getProgressReportDisplayPage()
    {
        echo "<h2>Session executed over time</h2>";

        ProgressGraphHelper::getProgressGraphJavaScriptCode();

        echo '<div id="container" style="width: 100%; height: 400px; margin: 0 auto"></div>';
    }

    public static function getAreaGridReportSelectionPage()
    {
        echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '?type=areaGridReport&action=display">';

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

    public static function getAreaGridReportDisplayPage()
    {
        $ag = new Areagridreport();
        $ag->generateAreaGridReport();
    }

}

?>