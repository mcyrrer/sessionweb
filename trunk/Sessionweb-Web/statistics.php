<?php
require_once('include/loggingsetup.php');
session_start();
if (!session_is_registered(myusername)) {
    header("location:index.php");
}
include("include/header.php.inc");
include_once('config/db.php.inc');
include_once ('include/commonFunctions.php.inc');
echo "<h1>Statistics/Graphs</h1>";
echo "Graph type:";
echoGraphTypes();
echo "Tester:";
if ($_SESSION['useradmin'] == 1) {
    echoTesterSelect("");
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
//else
//{
//    echo "                                      <select id=\"select_team\" name=\"team\">\n";
//    echo "                                      <option></option>\n";
//    echo "                                      </select>\n";
//}


echo "Sprint:";
echoSprintSelect("");
//echo "Session status:";
//echoSessionStatusTypesSelect();
echo "<img src='pictures/go-next.png' alt='Show Graph' id='showgraph'>";

echo "<div id='graphdiv'>";
echo "<iframe id='iframegraph' src='graph/index.php' width='1200' height='600' frameborder='0'></iframe>";
echo "</div>";
echo "<div id='url_graph'></div>";


function echoGraphTypes()
{
    echo "<select id='choosegraph'>\n";
//    echo "    <option value='' >Choose graph</option>\n";
    echo "    <option value='line_overtime'>Line graph: Test sessions over time</option>\n";
    echo "    <option value='pie_timedist'>Pie graph: Time distribution</option>\n";
    echo "</select>\n";
}

?>