<?php
session_start();
require_once('../../../include/validatesession.inc');
require_once ('../../../include/db.php');
include_once('../../../config/db.php.inc');
include_once ('../../../include/commonFunctions.php.inc');
saveSession(false);

/**
 * Save session to database
 */
function saveSession($echo = true)
{
//    print_r($_REQUEST);

    if ($_REQUEST["title"] != "") {
        echo "HAS TITLTE<br>";


        //insertAutomaticGoBackOnePage();
        checkSessionTitleNotToLong($echo);

        $sessionid = false;
        $versionid = false;

        $con = getMySqlConnection();

        //New session
//        if ($_REQUEST["versionid"] == "") {
//            echo "DOES NOT  HAVE a SESSIONID<br>";
//            if (!doesSessionKeyExist($_REQUEST["publickey"])) {
//                echo "NEW existing session!!!<br>";
//
//                //Will create a new session id to map to a session
//                saveSession_CreateNewSessionId();
//
//                //Get the new session id for user x
//                $sessionid = saveSession_GetSessionIdForNewSession();
//
//                //Insert sessiondata to mission table
//
//                saveSession_InsertSessionDataToDb($sessionid, $echo);
//
//                //Get versionId from db
//                $versionid = saveSession_GetVersionIdForNewSession();
//
//                //Create missionstatus record in Db
//                $executed = false;
//                if ($_REQUEST["executed"] != "") {
//                    $executed = true;
//                }
//                saveSession_InsertSessionStatusToDb($versionid, $executed);
//
//                //Create metrics record for session
//                $metrics = array();
//                $metrics["setuppercent"] = $_REQUEST["setuppercent"];
//                $metrics["testpercent"] = $_REQUEST["testpercent"];
//                $metrics["bugpercent"] = $_REQUEST["bugpercent"];
//                $metrics["oppertunitypercent"] = $_REQUEST["oppertunitypercent"];
//                $metrics["duration"] = $_REQUEST["duration"];
//                $metrics["mood"] = $_REQUEST["mood"];
//                saveSession_InsertSessionMetricsToDb($versionid, $metrics);
//
//                //Create areas for session
//                $areas = $_REQUEST["area"];
//                saveSession_InsertSessionAreaToDb($versionid, $areas);
//
//                $additionalTester = $_REQUEST["additionalTester"];
//                saveSession_InsertSessionAdditionalTesterToDb($versionid, $additionalTester);
//
//                //Create bugs connected to session
//                saveSession_InsertSessionBugsToDb($versionid);
//
//                //Create requirements connected to mission
//                saveSession_InsertSessionRequirementsToDb($versionid);
//
//                //Create sessionLinks connected to mission
//                saveSession_InsertSessionSessionsLinksToDb($versionid);
//
//                //Save Custom fields to db
//                $arr = array("custom1", "custom2", "custom3");
//                foreach ($arr as $oneField) {
//                    if (isset($_REQUEST[$oneField]))
//                        saveSession_InsertSessionCustomFieldsToDb($versionid, $oneField, $_REQUEST[$oneField]);
//                }
//
//                saveSession_InsertSessionSessionsLinksToDb($versionid);
//            } else {
//                //echo "Session already saved.";
//                $alreadySaved = true;
//            }

//        } //Update existing session
//        else {
        echo "Update existing session!!!<br>";
        if (isset($_REQUEST["sessionid"])) {
            $sessionid = $_REQUEST["sessionid"];
        } else {
            $sessionid = getSessionSessionId($_REQUEST["versionid"]);
        }
        $versionid = $_REQUEST["versionid"];

        echo "SESSIONID: $sessionid, VERSIONID: $versionid";

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
        echo "DOES NOT HAVE TITLE<br>";
    }
    echo "sessionid:" . $sessionid;
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