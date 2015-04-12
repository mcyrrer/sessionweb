<?php
require_once('../../../classes/autoloader.php');
require_once('../../../include/apistatuscodes.inc');

$logger = new logging();
$sHelper = new sessionHelper();
$dbm = new dbHelper();


$con = $dbm->connectToLocalDb();

//$sessionInfo = (getSessionData($_REQUEST["sessionid"]));
$sessionInfo = new sessionObject($_REQUEST["sessionid"]);
$title = $sessionInfo->getTitle();
if (strcmp($_SESSION['username'], $sessionInfo->getUsername()) == 0 || $_SESSION['superuser'] == 1 || $_SESSION['useradmin'] == 1) {

    if (!isset($_REQUEST['tester'])) {
        $sessionid = $_REQUEST["sessionid"];
        echo "<center>";
        echo "<img src='../../../pictures/multiUserIcon.jpg' alt=''>";
        echo "<h2>Reassign session</h2>\n";
        echo "<p>Title: $title</p>";
        echo "Reassign session $sessionid to:\n";
        echo "<form id=\"reassignform\" name=\"reassignform\" action=\"index.php?sessionid=$sessionid\" method=\"POST\" accept-charset=\"utf-8\">\n";
        HtmlFunctions::echoTesterFullNameSelect("", true);
        echo "<input type=\"hidden\" name=\"sessionid\" value=\"" . $_GET["sessionid"] . "\">\n";
        echo "<p><input type=\"submit\" value=\"Continue\" /></p>\n";
        echo "</form>\n";
        echo "</center>";
        die();
    }


    $sessionid = $_REQUEST["sessionid"];
    $tester = $_REQUEST["tester"];

    //./web/include/session_database_functions.php.inc
    //move into new class!
    $result = QueryHelper::updateSessionOwner($con,$sessionid, $tester);

    if ($result) {
        echo "<center>";
        echo "<img src='../../../pictures/multiUserIcon.jpg' alt=''>";

        echo "<h2>Session reassigned</h2>";
        $tester = QueryHelper::getTesterFullName($con,$tester);
        echo "Session was reassigned to $tester.";

        echo "</center>";
    } else {
        echo "Error, could not reassign session.\n";
    }


} else {
    echo "<center>";
    echo "<img src='pictures/multiUserIcon.jpg' alt=''>";

    echo "<h2>Could not reassign session $title</h2>";
    echo "Session " . $_REQUEST["sessionid"] . " could not be reassigned.<br>
    You are not the owner of the session.";

    echo "</center>";
}




?>