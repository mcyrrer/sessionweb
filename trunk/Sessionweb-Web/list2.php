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
    <form id="sform">
        <div id="filterbox" class="flexigrid">

            <?php
            echo "Tester:";
            echoTesterFullNameSelect(null);
            echo "Sprint:";
            echoSprintSelect(null);
            echo "Team:";
            echoTeamSelect(null);
            echo "Area";
            echoAreaSelectSingel(null);
            echo "Status:";
            echoStatusTypes(null);
            ?>


        </div>
        <div id="searchbox" class="flexigrid">
            <form id="sform2">
                <?php
                echo "Search: <input id='searchstring' type='text' size='50' value='' name='searchstring' style='width:500px;'>";
                ?>
                <span id="searchSessions">[Search]</span>
                <span id="clearSearchSessions">[Clear]</span>
                <img id="helpsearch" src="pictures/dialog-question.png">

            </form>

        </div>
        <div id="msgdiv"></div>
        <!-- Primary content: Stuff that goes in the primary content column (by default, the left column) -->
        <div id="primarycontainer">
            <div id="primarycontent">
                <!-- Primary content area start -->
                <table id="flexgrid1"></table>


                <!-- Primary content area end -->
            </div>
        </div>
</div>
<?php
require_once('include/footer.php.inc');
?>