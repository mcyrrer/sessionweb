<!--select DATE(updated), count(*)-->
<!--  from mission group by DATE(updated);-->

<?php
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
echoTesterSelect("");
echo "Team:";
echoTeamSelect("");
echo "Sprint:";
echoSprintSelect("");
echo "Session status:";
echoSessionStatusTypesSelect();
echo "<img src='pictures/go-next.png' alt='Show Graph' id='showgraph'>";

echo "<div id='graphdiv'>";
echo "<iframe id='iframegraph' src='http://localhost/sessionweb/graph/index.php' width='1024' height='600' frameborder='0'></iframe>";
echo "</div>";


function echoGraphTypes()
{
    echo "<select id='choosegraph'>\n";
    echo "    <option value='' >Choose graph</option>\n";
    echo "    <option value='line_overtime'>Line graph: Accumulated test sessions over time(Normalized)</option>\n";
        echo "    <option value='pie_generic'>Pie graph:test</option>\n";
    echo "</select>\n";
}

?>