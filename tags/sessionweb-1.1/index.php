<?php
//if(is_file("install.php"))
//{
//    header("Location: install.php");
//    exit();
//}

$logout = $_GET["logout"];
session_start();

if ($logout == "yes") {
    session_destroy();
    unset($_SESSION);
}

include("include/header.php.inc");
include_once('config/db.php.inc');
include_once ('include/commonFunctions.php.inc');

if ($logout == "yes") {
    echo "         You are logged out. Please log in again to use Sessionweb\n";
}

if (!session_is_registered(myusername)) {

    echo "         <form name=\"loginform\" method=\"post\" action=\"checklogin.php\">\n";
    echo "             <table width=\"100%\" border=\"0\" cellpadding=\"3\" cellspacing=\"1\" bgcolor=\"#FFFFFF\">\n";
    echo "                 <tr>\n";
    echo "                     <td colspan=\"3\"><strong>User Login </strong></td>\n";
    echo "                 </tr>\n";
    echo "                 <tr>\n";
    echo "                     <td width=\"78\">Username</td>\n";
    echo "                     <td width=\"294\"><input name=\"myusername\" type=\"text\" id=\"myusername\"></td>\n";
    echo "                 </tr>\n";
    echo "                 <tr>\n";
    echo "                     <td>Password</td>\n";
    echo "                     <td><input name=\"mypassword\" type=\"password\" id=\"mypassword\"></td>\n";
    echo "                 </tr>\n";
    echo "                 <tr>\n";
    echo "                     <td>&nbsp;</td>\n";
    echo "                     <td><input type=\"submit\" name=\"Submit\" value=\"Login\"></td>\n";
    echo "                 </tr>\n";
    echo "                 </table>\n";
    echo "         </form>\n";
}
else
{
    echo "         Welcome to sessionweb " . $_SESSION['user'] . "<br> \n";
    printSessionsStatus();
    printLast10SessionsTable();
}

include("include/footer.php.inc");

function printLast10SessionsTable()
{
    echo "<div id='last10sessions'>";
    echo "<h2>10 sessions yet to completed</h2>";
    $con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("cannot connect");
    mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

    $user = $_SESSION['username'];

    $sql = "SELECT * FROM `sessionwebos`.`sessioninfo` WHERE executed = 0 and username = '$user' ORDER BY sessionid limit 0,10;";
    $result = mysql_query($sql);

    echo "<ul>\n";
    while ($row = mysql_fetch_array($result))
    {
        echo "<li><a id='edit_session" . $row['sessionid'] . "' class='url_edit_session' href='session.php?sessionid=" . $row['sessionid'] . "&amp;command=edit'>" . $row['title'] . "</a></li>\n";
    }
    echo "</ul>\n";
    mysql_close($con);
    echo "</div>";
}

function printSessionsStatus()
{
    echo "<div id='sessionstatus'>";
    $con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("cannot connect");
    mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

    $user = $_SESSION['username'];

    $sqlExecuted = "SELECT COUNT(executed) AS executed FROM `sessionwebos`.`sessioninfo` WHERE username = '$user' and executed = 1;";
    $resultSession = mysql_query($sqlExecuted);
    $executedResultArray = mysql_fetch_array($resultSession);
    $executed = $executedResultArray['executed'];

    $sqlNoRun = "SELECT COUNT(executed) AS norun FROM `sessionwebos`.`sessioninfo` WHERE username = '$user' and executed = 0;";
    $resultSession = mysql_query($sqlNoRun);
    $NoRunResultArray = mysql_fetch_array($resultSession);
    $noRun = $NoRunResultArray['norun'];

    $sqlDebriefed = "SELECT COUNT(debriefed) AS debriefed FROM `sessionwebos`.`sessioninfo` WHERE username = '$user' and debriefed = 1;";
    $resultSession = mysql_query($sqlDebriefed);
    $debriefedResultArray = mysql_fetch_array($resultSession);
    $debriefed = $debriefedResultArray['debriefed'];

    $sqlTotalSessions = "SELECT COUNT(debriefed) AS totalsessions FROM `sessionwebos`.`sessioninfo` WHERE username = '$user';";
    $resultSession = mysql_query($sqlTotalSessions);
    $totalSessionsResultArray = mysql_fetch_array($resultSession);
    $totalSessions = $totalSessionsResultArray['totalsessions'];
    if ($debriefed == 0 || $totalSessions == 0) {
        $debriefProcentage = 0;
    }
    else
    {
        $debriefProcentage = (intval($debriefed) / intval($totalSessions)) * 100;
    }
    $notDebriefedCount = intval($executed) - intval($debriefed);
    $debriefProcentage = sprintf("%02d", $debriefProcentage);

    echo "<h2>Session statistics for " . $_SESSION['user'] . "</h2>\n";
    echo "<img src = 'http://chart.googleapis.com/chart?chst=d_fnote_title&chld=taped_y|1|004400|l|To do|$noRun'  alt = 'note' />\n";
    echo "<img src = 'http://chart.googleapis.com/chart?chst=d_fnote_title&chld=taped_y|1|004400|l|Executed|$executed'  alt = 'note' />\n";
    if ($notDebriefedCount < 10)
        $color = '004400';
    else
        $color = 'ff0033';
    echo "<img src = 'http://chart.googleapis.com/chart?chst=d_fnote_title&chld=taped_y|1|$color|l|Debriefed|$debriefed|$debriefProcentage%|$notDebriefedCount to debrief' alt = 'note' />\n";
    echo "<img src = 'http://chart.googleapis.com/chart?chst=d_fnote_title&chld=taped_y|1|004400|l|Total|$totalSessions'  alt = 'note' />\n";

    mysql_close($con);
    echo "</div>";

}

?>
