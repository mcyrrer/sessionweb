<?php

require_once('classes/autoloader.php');

$logger = new logging();
$logger->debug("Enter index page", __FILE__, __LINE__);


if (isset($_GET["logout"])) {
    $logout = $_GET["logout"];
} else {
    $logout = null;
}

if ($logout == "yes") {
    session_destroy();
    $logger->info("User logged out", __FILE__, __LINE__);
    unset($_SESSION);
}

include("include/header.php.inc");

if ($logout == "yes") {
    echo "         You are logged out. Please log in again to use Sessionweb\n";
}

//if (!session_is_registered(myusername)) {
if (!isset($_SESSION['username'])) {

    echo "         <form name=\"loginform\" method=\"post\" action=\"checklogin.php\">\n";
    echo "             <table width=\"100%\" border=\"0\" cellpadding=\"3\" cellspacing=\"1\" bgcolor=\"#FFFFFF\">\n";
    echo "                 <tr>\n";
    echo "                     <td colspan=\"3\"><strong>User Login </strong></td>\n";
    echo "                 </tr>\n";
    echo "                 <tr>\n";
    echo "                     <td width=\"78\">Username</td>\n";
    echo "                     <td width=\"294\"><input name=\"myusername\" type=\"text\" id=\"myusername\">";
    if (LDAP_ENABLED) {
        echo "AD or sessionweb user";
    }
    echo "</td>\n";
    echo "                 </tr>\n";
    echo "                 <tr>\n";
    echo "                     <td>Password</td>\n";
    echo "                     <td><input name=\"mypassword\" type=\"password\" id=\"mypassword\" AUTOCOMPLETE=\"OFF\"></td>\n";
    echo "                 </tr>\n";
    echo "                 <tr>\n";
    echo "                     <td>&nbsp;</td>\n";
    echo "                     <td><input type=\"submit\" name=\"Submit\" value=\"Login\"></td>\n";
    echo "                 </tr>\n";
    echo "                 </table>\n";
    echo '                 <input type="hidden" name="ref" value="' . $_SERVER["REQUEST_URI"] . '">';
    echo "         </form>\n";
    if (isset($_GET['login']) && strcmp($_GET['login'], "failed") == 0) {
        echo "Wrong user name or password.<br><br>";
    }

} else {

    echo "         Welcome to sessionweb " . $_SESSION['user'] . "<br> \n";
    printSessionsStatus();
    //printLast10SessionsTable();
}


include("include/footer.php.inc");


function printLast10SessionsTable()
{
    echo "<div id='last10sessions'>";
    echo "<h2>10 sessions yet to be completed</h2>";
    $dbm = new dbHelper();
    $con = $dbm->connectToLocalDb();


    $user = $_SESSION['username'];

    $sql = "SELECT * FROM `sessioninfo` WHERE executed = 0 and username = '$user' ORDER BY sessionid limit 0,10;";
    $result = $dbm->executeQuery($con, $sql);

    echo "<ul>\n";
    while ($row = mysqli_fetch_array($result)) {
        echo "<li><a id='edit_session" . $row['sessionid'] . "' class='url_edit_session' href='session.php?sessionid=" . $row['sessionid'] . "&amp;command=edit'>" . $row['title'] . "</a></li>\n";
    }
    echo "</ul>\n";
    echo "</div>";
}

function printSessionsStatus()
{
    $logger = new logging();
    $dbm = new dbHelper();
    $con = $dbm->connectToLocalDb();

    echo "<div id='sessionstatus'>";

    $user = $_SESSION['username'];

    $sqlClosed = "SELECT COUNT(closed) AS closed FROM `sessioninfo` WHERE username = '$user' and closed = 1;";
    $logger->sql($sqlClosed, __FILE__, __LINE__);
    $resultSession = $dbm->executeQuery($con, $sqlClosed);

    $closedResultArray = mysqli_fetch_array($resultSession);
    $closed = $closedResultArray['closed'];

    $sqlExecuted = "SELECT COUNT(executed) AS executed FROM `sessioninfo` WHERE username = '$user' and executed = 1;";
    $resultSession = $dbm->executeQuery($con, $sqlExecuted);
    $executedResultArray = mysqli_fetch_array($resultSession);
    $executed = $executedResultArray['executed'];

    $sqlNoRun = "SELECT COUNT(executed) AS norun FROM `sessioninfo` WHERE username = '$user' and executed = 0;";
    $resultSession = $dbm->executeQuery($con, $sqlNoRun);
    $NoRunResultArray = mysqli_fetch_array($resultSession);
    $noRun = $NoRunResultArray['norun'];

    $sqlDebriefed = "SELECT COUNT(debriefed) AS debriefed FROM `sessioninfo` WHERE username = '$user' and debriefed = 1;";
    $resultSession = $dbm->executeQuery($con, $sqlDebriefed);
    $debriefedResultArray = mysqli_fetch_array($resultSession);
    $debriefed = $debriefedResultArray['debriefed'];

    $sqlTotalSessions = "SELECT COUNT(debriefed) AS totalsessions FROM `sessioninfo` WHERE username = '$user';";
    $resultSession = $dbm->executeQuery($con, $sqlTotalSessions);
    $totalSessionsResultArray = mysqli_fetch_array($resultSession);
    $totalSessions = $totalSessionsResultArray['totalsessions'];
    if ($debriefed == 0 || $totalSessions == 0) {
        $debriefProcentage = 0;
    } else {
        $debriefProcentage = ((int)$debriefed / (int)$totalSessions) * 100;
    }
    $notDebriefedCount = intval($executed) - intval($debriefed) - intval($closed);
    $debriefProcentage = sprintf("%02d", $debriefProcentage);


    if ($closed == 0 || $totalSessions == 0) {
        $closedProcentage = 0;
    } else {
        $closedProcentage = (intval($closed) / intval($totalSessions)) * 100;
    }
    $notClosedCount = intval($closed) - intval($debriefed) - intval($executed);
    $closedProcentage = sprintf("%02d", $closedProcentage);


    echo "<h2>Session statistics for " . $_SESSION['user'] . "</h2>\n";
    echo "<img src = 'http://chart.googleapis.com/chart?chst=d_fnote_title&chld=taped_y|1|004400|l|To do|$noRun'  alt = 'note' />\n";
    echo "<img src = 'http://chart.googleapis.com/chart?chst=d_fnote_title&chld=taped_y|1|004400|l|Executed|$executed'  alt = 'note' />\n";
    echo "<img src = 'http://chart.googleapis.com/chart?chst=d_fnote_title&chld=taped_y|1|004400|l|Closed|$closed|$closedProcentage%' alt = 'note' />\n";

    if ($notDebriefedCount < 10)
        $color = '004400';
    else
        $color = 'ff0033';
    echo "<img src = 'http://chart.googleapis.com/chart?chst=d_fnote_title&chld=taped_y|1|$color|l|Debriefed|$debriefed|$debriefProcentage%|$notDebriefedCount to debrief' alt = 'note' />\n";
    echo "<img src = 'http://chart.googleapis.com/chart?chst=d_fnote_title&chld=taped_y|1|004400|l|Total|$totalSessions'  alt = 'note' />\n";

    echo "</div>";

}

?>
