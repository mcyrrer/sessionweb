<?php
require_once('include/loggingsetup.php');
session_start();
require_once('include/validatesession.inc');
require_once('include/db.php');
require_once('config/db.php.inc');
require_once ('include/commonFunctions.php.inc');
require_once("include/header.php.inc");
echo "<h1>Statistics/Graphs</h1>";


?>
<table>
    <tr>
        <td><a class="largepopup cboxElement" href="graph/sprintreport.php">Sprint report</a> <td>
        <td><a class="mediumpopup cboxElement" href="graph/timedistribution.php">Time distribution</a> <td>
        <td><a class="mediumpopup cboxElement" href="graph/wordcloud.php?all=true">Word cloud</a> <td>
        <td><a class="mediumpopup cboxElement" href="graph/progressreport.php">Progress over time</a> <td>


    </tr>


</table>

<?php
include("include/footer.php.inc");
//echo "<a class='largepopup cboxElement' href='graph/sprintreport.php'>Create sprint report</a><br>";
//echo "Graph type:";
//echoGraphTypes();
//echo "Tester:";
//if ($_SESSION['useradmin'] == 1) {
//    echoTesterSelect("");
//}
//else
//{
//    echo "<select id='select_tester' name='tester'> \n";
//    echo "        <option></option>\n";
//    echo "        <option>" . $_SESSION['username'] . "</option> \n";
//    echo "</select>\n";
//
//}
//
//if ($_SESSION['useradmin'] == 1) {
//    echo "Team:";
//    echoTeamSelect("");
//}
////else
////{
////    echo "                                      <select id=\"select_team\" name=\"team\">\n";
////    echo "                                      <option></option>\n";
////    echo "                                      </select>\n";
////}
//
//
//echo "Sprint:";
//echoSprintSelect("");
////echo "Session status:";
////echoSessionStatusTypesSelect();
//echo "<img src='pictures/go-next.png' alt='Show Graph' id='showgraph'>";
//
//
//
//echo "<div id='graphdiv'>";
//echo "<iframe id='iframegraph' src='graph/index.php' width='1100' height='600' frameborder='0'></iframe>";
//echo "</div>";
//echo "<div id='url_graph'></div>";
//
//
//function echoGraphTypes()
//{
//    echo "<select id='choosegraph'>\n";
//    //    echo "    <option value='' >Choose graph</option>\n";
//    echo "    <option value='line_overtime'>Line graph: Test sessions over time</option>\n";
//    echo "    <option value='pie_timedist'>Pie graph: Time distribution</option>\n";
//    echo "    <option value='word_cloud'>Wordcloud</option>\n";
//    echo "</select>\n";
//}

?>