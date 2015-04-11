<?php

require_once('classes/autoloader.php');
require_once('include/header.php.inc');


$pageTimer = new pagetimer();
$pageTimer->startMeasurePageLoadTime();

$userSettings = new UserSettings();
$htmlFunctions = new HtmlFunctions();
?>

<div id="content">
    <form id="sform" action="">
        <div id="filterbox" class="flexigrid">

            <?php
$userSettings = $userSettings->getUserSettings();

echo "Tester:";
if (isset($_GET['tester'])) {
    $htmlFunctions->echoTesterFullNameSelect($_REQUEST['tester'], false, true);
} elseif ($userSettings['list_view'] == "mine") {
    $tester = $_SESSION['username'];
    $htmlFunctions->echoTesterFullNameSelect($tester, false, true);
} else {
    $htmlFunctions->echoTesterFullNameSelect(null, false, true);
}
echo "Sprint:";
if (isset($_GET['sprint'])) {
    $htmlFunctions->echoSprintSelect($_REQUEST['sprint'], true);
} else {
    $htmlFunctions->echoSprintSelect(null, true);
}
echo "Team:";
if (isset($_GET['team'])) {
    $htmlFunctions->echoTeamSelect($_REQUEST['team'], true);
} elseif ($userSettings['list_view'] == "team") {
    $team = $userSettings['teamname'];
    $htmlFunctions->echoTeamSelect($team, true);
} else {
    $htmlFunctions->echoTeamSelect(null, true);
}
echo "Area";
if (isset($_GET['area'])) {
    $htmlFunctions->echoAreaSelectSingel($_REQUEST['area'], true);
} else {
    $htmlFunctions->echoAreaSelectSingel(null, true);
}
echo "Status:";
if (isset($_GET['status'])) {
    $htmlFunctions->echoStatusTypes($_REQUEST['status']);
} else {
    $htmlFunctions->echoStatusTypes(null);
}
?>


        </div>
    <div id="searchbox" class="flexigrid">

        <form id="sform2" action="">

            <?php
$textValue = "";
if (isset($_REQUEST['searchstring'])) {
    $textValue = $_REQUEST['searchstring'];
} else {
    $textValue = "";

}
echo "Search: <input id='searchstring' type='text' size='50' value='" . $textValue . "' name='searchstring' style='width:500px;'>";
?>
            <span id="searchSessions">[Search]</span>
            <span id="clearSearchSessions">[Clear]</span>
            <img id="helpsearch" src="pictures/dialog-question.png" alt="">

            <?php
if (isset($_REQUEST['searchstringref'])) {
    $refissueValue = $_REQUEST['searchstringref'];
} else {
    $refissueValue = "";
}
echo "Requirement/bug search: <input id='searchstringref' type='text' size='15' value='" . $refissueValue . "' name='searchstringref' style='width:100px;'>";
?>
            <span id="searchSessionsRef">[Search]</span>
            <span id="clearSearchSessionsRef">[Clear]</span>
        </form>
    </form>

    </div>

</div>
<div id="msgdiv"></div>
<!-- Primary content: Stuff that goes in the primary content column (by default, the left column) -->
<div id="primarycontainer">
    <div id="primarycontent">
        <!-- Primary content area start -->
        <table id="flexgrid1"></table>


        <!-- Primary content area end -->
        <div id="urldiv"></div>

    </div>
</div>
    <div id="dialog" style="display:none;">
        <iframe id="dialogurl" width="450" height="450" frameborder="0"></iframe>
    </div>
<?php
require_once('include/footer.php.inc');
$pageTimer->stopMeasurePageLoadTime();
?>