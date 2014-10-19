<?php
require_once('include/loggingsetup.php');
session_start();
require_once('include/validatesession.inc');
require_once('include/db.php');
require_once('config/db.php.inc');
require_once('include/commonFunctions.php.inc');
require_once("classes/StatisticsHelper.php");
require_once("classes/dbHelper.php");

require_once("include/header.php.inc");
echo "<h1>Statistics/Graphs</h1>";

if (!isset($_REQUEST['type']))
    echo StatisticsHelper::getStatisticMainPage();
else {

    $dbh = new dbHelper();
    $dbh->escapeAllRequestParameters();
    switch ($_REQUEST['type']) {
        case "progressOverTime":
            if (strcmp($_REQUEST['action'], "select") == 0)
                StatisticsHelper::getProgressReportSelectionPage();
            elseif (strcmp($_REQUEST['action'], "display") == 0)
                StatisticsHelper::getProgressReportDisplayPage();
            break;
        case "areaGridReport":
            if (strcmp($_REQUEST['action'], "select") == 0)
            {
                StatisticsHelper::getAreaGridReportSelectionPage();
            }
            elseif (strcmp($_REQUEST['action'], "display") == 0)
            {
                StatisticsHelper::getAreaGridReportDisplayPage();
            }
            break;
        default:
            echo "Not a valid type";
    }
}
include("include/footer.php.inc");


?>