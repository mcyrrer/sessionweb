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
        <div id="searchbox" class="flexigrid">
            <form id="sform">
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
            </form>

        </div>
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