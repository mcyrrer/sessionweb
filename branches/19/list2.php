<?php
session_start();
require_once('include/validatesession.inc');
require_once('config/db.php.inc');
require_once('include/header.php.inc');
require_once('include/db.php');
require_once('include/commonFunctions.php.inc');
require_once('include/session_common_functions.php.inc');
?>

<div id="content">
    <form id="sform" action="">
        <div id="filterbox" class="flexigrid">

            <?php
            $userSettings = getUserSettings();
            echo "Tester:";
            if (isset($_GET['tester'])) {
                echoTesterFullNameSelect($_REQUEST['tester']);
            }
            elseif ($userSettings['list_view'] == "mine") {
                $tester = $_SESSION['username'];
                echoTesterFullNameSelect($tester);
            }
            else
            {
                echoTesterFullNameSelect(null);
            }
            echo "Sprint:";
            if (isset($_GET['sprint'])) {
                echoSprintSelect($_REQUEST['sprint'], true);
            }
            else {
                echoSprintSelect(null, true);
            }
            echo "Team:";
            if (isset($_GET['team'])) {
                echoTeamSelect($_REQUEST['team'], true, true);
            }
            elseif ($userSettings['list_view'] == "team") {
                $team = $userSettings['teamname'];
                echoTeamSelect($team, true, true);
            }
            else {
                echoTeamSelect(null, true, true);
            }
            echo "Area";
            if (isset($_GET['area'])) {
                echoAreaSelectSingel($_REQUEST['area'], true);
            }
            else {
                echoAreaSelectSingel(null, true);
            }
            echo "Status:";
            if (isset($_GET['status'])) {
                echoStatusTypes($_REQUEST['status']);
            }
            else {
                echoStatusTypes(null);
            }
            ?>


        </div>
    <div id="searchbox" class="flexigrid">

        <form id="sform2" action="">

            <?php
            if (isset($_REQUEST['searchstring'])) {
                $textValue = $_REQUEST['searchstring'];
            }
            echo "Search: <input id='searchstring' type='text' size='50' value='" . $textValue . "' name='searchstring' style='width:500px;'>";
            ?>
            <span id="searchSessions">[Search]</span>
            <span id="clearSearchSessions">[Clear]</span>
            <img id="helpsearch" src="pictures/dialog-question.png" alt="">

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

<?php
require_once('include/footer.php.inc');
?>