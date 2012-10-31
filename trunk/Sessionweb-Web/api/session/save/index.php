<?php
session_start();
require_once('../../../include/validatesession.inc');
require_once ('../../../include/db.php');
include_once('../../../config/db.php.inc');
include_once ('../../../include/commonFunctions.php.inc');
include_once ('../../../classes/logging.php');
saveSession(false);

/**
 * Save session to database
 */
function saveSession($echo = true)
{
    $logger = new logging();
//    print_r($_REQUEST);

    if ($_REQUEST["title"] != "") {

        //insertAutomaticGoBackOnePage();
        checkSessionTitleNotToLong($echo);

        $sessionid = false;
        $versionid = false;

        $con = getMySqlConnection();

        if (isset($_REQUEST["sessionid"])) {
            $sessionid = $_REQUEST["sessionid"];
        } else {
            $sessionid = getSessionSessionId($_REQUEST["versionid"]);
        }
        $versionid = $_REQUEST["versionid"];

        saveSession_UpdateSessionDataToDb($sessionid);

        saveSession_UpdateSessionStatusToDb($versionid);


        saveSession_UpdateSessionMetricsToDb($versionid);

        $areas = $_REQUEST["area"];
        saveSession_UpdateSessionAreasToDb($versionid, $areas);

        $additionalTester = $_REQUEST["additionalTester"];
        saveSession_UpdateSessionAdditionalTesterDb($versionid, $additionalTester);

        saveSession_UpdateSessionBugsToDb($versionid);

        saveSession_UpdateSessionRequirementsToDb($versionid);

        saveSession_UpdateSessionRequirementsToDb($versionid);

        saveSession_UpdateSessionLinkedToDb($versionid);

        saveSession_UpdateCustomFieldsToDb($versionid);

//        }

        mysql_close($con);
        if ($echo) {
//            if (!$alreadySaved) {
            echo "<p><b>Session saved</b></p>\n";
            echo "<p><a href=\"session.php?sessionid=$sessionid&command=view\" id=\"view_session\">View session</a></p>";
            echo "<p><a href=\"session.php?sessionid=$sessionid&command=edit\" id=\"edit_session\">Edit session</a></p>";

            echo "<span style=\"color:white\"><div id=\"sessioninfo\">sessionid:<div id=\"sessionid\">$sessionid</div>, versionid:<div id=\"versionid\">$versionid</div></span></div>\n";
//            }
        }
    } else {

    }

    if ($_REQUEST["title"] != "") {
        echo '<h2 class="popup_save">Session saved</h2><p class="popup_save"><img src="pictures/document-save-5.png" alt=""></p>';
    } else {
        echo '<h2 class="popup_save">Session not saved</h2><p class="popup_save"><img src="pictures/dialog-error.png" alt=""><br><br><br>Title missing. Add it and save again.</p>';
    }
    return $sessionid;
}


function checkSessionTitleNotToLong($echo = true)
{
    $_TITLELENGTH = 500;

    if ($echo)
        echo "<h1>Save session</h1>\n";
    if (strlen($_REQUEST["title"]) > $_TITLELENGTH) {
        echo "<p class='center'><b>Warning:</b> Title of session is exceding the maximum number of chars ($_TITLELENGTH). Will only save the first $_TITLELENGTH chars<br/></p>\n";
    }
}

?>