<?php
require_once('classes/autoloader.php');

require_once("include/header.php.inc");
echo "<h1>Statistics/Graphs</h1>";

$logger = new logging();

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
            if (strcmp($_REQUEST['action'], "select") == 0) {
                StatisticsHelper::getAreaGridReportSelectionPage();
            } elseif (strcmp($_REQUEST['action'], "display") == 0) {
                StatisticsHelper::getAreaGridReportDisplayPage();
            }
            break;
        default:
            echo "Not a valid type";
    }
}
include("include/footer.php.inc");


?>