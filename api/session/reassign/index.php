<?php
session_start();
require_once('../../../include/validatesession.inc');
require_once ('../../../include/db.php');
include_once('../../../config/db.php.inc');
include_once ('../../../include/commonFunctions.php.inc');
$con = getMySqlConnection();

$sessionInfo = (getSessionData($_REQUEST["sessionid"]));
$title = $sessionInfo["title"];
if (strcmp($_SESSION['username'], $sessionInfo['username']) == 0 || $_SESSION['superuser']==1 || $_SESSION['useradmin']==1) {

    if (!isset($_REQUEST['tester'])) {
        $sessionid = $_REQUEST["sessionid"];
        echo "<center>";
        echo "<img src='../../../pictures/multiUserIcon.jpg' alt=''>";
        echo "<h2>Reassign session</h2>\n";
        echo "<p>Title: $title</p>";
        echo "Reassign session $sessionid to:\n";
        echo "<form id=\"reassignform\" name=\"reassignform\" action=\"index.php?sessionid=$sessionid\" method=\"POST\" accept-charset=\"utf-8\">\n";
        echoTesterFullNameSelect("",true);
        echo "<input type=\"hidden\" name=\"sessionid\" value=\"" . $_GET["sessionid"] . "\">\n";
        echo "<p><input type=\"submit\" value=\"Continue\" /></p>\n";
        echo "</form>\n";
        echo "</center>";
        die();
    }



    $sessionid = $_REQUEST["sessionid"];
    $tester = $_REQUEST["tester"];

    $result = updateSessionOwner($sessionid, $tester);

    if ($result) {
        echo "<center>";
        echo "<img src='../../../pictures/multiUserIcon.jpg' alt=''>";

        echo "<h2>Session reassigned</h2>";
        $tester = getTesterFullName($tester);
        echo "Session was reassigned to $tester.";

        echo "</center>";
    }
    else
    {
        echo "Error, could not reassign session.\n";
    }



}
else
{
    echo "<center>";
    echo "<img src='pictures/multiUserIcon.jpg' alt=''>";

    echo "<h2>Could not reassign session $title</h2>";
    echo "Session " . $_REQUEST["sessionid"] . " could not be reassigned.<br>
    You are not the owner of the session.";

    echo "</center>";
}




?>