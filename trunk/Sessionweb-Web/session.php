<?php
require_once('include/loggingsetup.php');
session_start();

if (!session_is_registered(myusername)) {
    header("location:index.php");
}
include("include/header.php.inc");
include_once('config/db.php.inc');
include_once ('include/session_edit_functions.php.inc');
include_once ('include/session_view_functions.php.inc');
include_once ('include/session_database_functions.php.inc');
include_once ('include/commonFunctions.php.inc');
include_once ('include/session_common_functions.php.inc');
if (is_file("include/customfunctions.php.inc")) {
    include "include/customfunctions.php.inc";
}


if (strcmp($_REQUEST["command"], "new") == 0) {
    echoSessionForm();
}
elseif (strcmp($_REQUEST["command"], "view") == 0)
{
    echoSessionAction();
    echoViewSession();
}
elseif (strcmp($_REQUEST["command"], "edit") == 0)
{
    echoSessionForm();
}
elseif (strcmp($_REQUEST["command"], "delete") == 0)
{
    deleteSession();
}
elseif (strcmp($_REQUEST["command"], "reassign") == 0)
{
    reassignSession();
}
elseif (strcmp($_REQUEST["command"], "reassignexecute") == 0)
{
    reassignSessionExecute();
}
elseif (strcmp($_REQUEST["command"], "debrief") == 0)
{
    echoViewSession();
    echoDebriefSession();
}
elseif (strcmp($_REQUEST["command"], "debriefed") == 0)
{
    saveDebriefedSession();
}

elseif (strcmp($_REQUEST["command"], "copy") == 0)
{
    copySession();
}

elseif (strcmp($_REQUEST["command"], "save") == 0)
{
    //RapidReporter importer
    if (strstr(substr($_REQUEST["notes"], 0, 26), 'Time,Reporter,Type,Content') != false) {
        $_REQUEST["notes"] = parseRapidReporterNotes($_REQUEST["notes"]);
        echo "RapidReporter CVS notes parsed to HTML<br/>\n";
    }

        //BB test assistant importer
    elseif (strstr(substr($_REQUEST["notes"], 0, 43), "xml version") != false)
    {
        $_REQUEST["notes"] = parseBBTestAssistantNotes($_REQUEST["notes"]);
        echo "BB Test Assistant XML notes parsed to HTML<br/>\n";
    }

    saveSession();
}

include("include/footer.php.inc");


function reassignSessionExecute()
{

    $sessionid = $_REQUEST["sessionid"];
    $tester = $_REQUEST["tester"];
    $con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("cannot connect");
    mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");
    $result = updateSessionOwner($sessionid, $tester);
    mysql_close($con);
    if ($result) {
        echo "Session reassigned.\n";
    }
    else
    {
        echo "Error, could not reassign session.\n";
    }

}

function reassignSession()
{
    $sessionid = $_REQUEST["sessionid"];
    echo "<h2>Reassign session</h2>\n";
    echo "Reassign session $sessionid to:\n";
    echo "<form id=\"reassignform\" name=\"reassignform\" action=\"session.php?command=reassignexecute\" method=\"POST\" accept-charset=\"utf-8\">\n";
    echoTesterSelect("");
    echo "<input type=\"hidden\" name=\"sessionid\" value=\"" . $_GET["sessionid"] . "\">\n";
    echo "<p><input type=\"submit\" value=\"Continue\" /></p>\n";
    echo "</form>\n";
}

function echoDebriefSession()
{
    if (strcmp($_SESSION['superuser'], "1") == 0 || strcmp($_SESSION['useradmin'], "1") == 0) {

        echo "<img src=\"pictures/line.png\" alt=\"line\">\n";
        echo "<form id=\"sessionform\" name=\"sessionform\" action=\"session.php?command=debriefed\" method=\"POST\" accept-charset=\"utf-8\">\n";
        echo "<h4>Debrief notes</h4>\n";
        echo "<textarea id=\"debriefnotes\" class=\"ckeditor\" name=\"debriefnotes\" rows=\"20\" cols=\"50\" style=\"width:1024px;height:200px;\"></textarea>\n";
        echo "<div>Debriefed: <input type=\"checkbox\" class=\"debriefoption\" name=\"debriefedcheckbox\"  value=\"yes\"></div>\n";
        if (strcmp($_SESSION['useradmin'], "1") == 0) {
            echo "<div>Debriefed by manager: <input type=\"checkbox\" class=\"debriefoption\" name=\"debriefedbymanagercheckbox\" value=\"yes\"></div>\n";
            echo "<div>Close session (do not mark it as debriefed): <input type=\"checkbox\" class=\"debriefoption\" name=\"closed\" value=\"yes\"></div>\n";

        }
        echo "<input type=\"hidden\" name=\"sessionid\" value=\"" . $_GET["sessionid"] . "\">\n";
        echo "<p><input type=\"submit\" value=\"Continue\" /></p>\n";
        echo "</form>\n";
    }
    else
    {
        echo "You do not have enough permisions to debrief sessions.";
    }
}


function saveDebriefedSession()
{

    //TODO: Add logic to manage a closed session....
    if (strcmp($_SESSION['superuser'], "1") == 0 || strcmp($_SESSION['useradmin'], "1") == 0) {
        $con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("cannot connect");
        mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

        $versionid = getSessionVersionId($_REQUEST["sessionid"]);

        $debriefed = "false";
        if (strcmp($_REQUEST["debriefedcheckbox"], "yes") == 0) {
            $debriefed = "true";
        }


        $masterdibriefed = "false";
        if (strcmp($_REQUEST["debriefedbymanagercheckbox"], "yes") == 0) {
            $masterdibriefed = "true";
        }

        $closed = "false";
        if (strcmp($_REQUEST["closed"], "yes") == 0) {
            $closed = "true";
            $debriefed = "false";
            $masterdibriefed = "false";

        }

        if (doesSessionNotesExist($versionid)) {
            saveSession_DeleteSessionsNotesFromDb($versionid);
        }
        //        else
        //        {
        //            echo "session does not have notes.<br>";
        //        }


        saveSession_UpdateSessionDebriefedStatusToDb($versionid, $debriefed, $closed, $masterdibriefed);

        saveSession_InsertSessionDebriefedNotesToDb($versionid, $_REQUEST["debriefnotes"]);

        echo "<h4>Debrief notes saved</h4>\n";
    }
    else
    {
        echo "You can not save since you do not have the permissions to debrief\n";
    }

}


function checkSessionTitleNotToLong()
{
    $_TITLELENGTH = 500;

    echo "<h1>Save session</h1>\n";
    if (strlen($_REQUEST["title"]) > $_TITLELENGTH) {
        echo "<b>Warning:</b> Title of session is exceding the maximum number of chars ($_TITLELENGTH). Will only save the first $_TITLELENGTH chars<br/>\n";
    }
}

/**
 * Save session to database
 */
function saveSession()
{

    //insertAutomaticGoBackOnePage();
    checkSessionTitleNotToLong();

    $sessionid = false;
    $versionid = false;

    $con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("cannot connect");
    mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

    //New session
    if ($_REQUEST["sessionid"] == "") {
        if (!doesSessionKeyExist($_REQUEST["publickey"])) {

            //Will create a new session id to map to a session
            saveSession_CreateNewSessionId();

            //Get the new session id for user x
            $sessionid = saveSession_GetSessionIdForNewSession();

            //Insert sessiondata to mission table

            saveSession_InsertSessionDataToDb($sessionid);

            //Get versionId from db
            $versionid = saveSession_GetVersionIdForNewSession();

            //Create missionstatus record in Db
            $executed = false;
            if ($_REQUEST["executed"] != "") {
                $executed = true;
            }
            saveSession_InsertSessionStatusToDb($versionid, $executed);

            //Create metrics record for session
            $metrics = array();
            $metrics["setuppercent"] = $_REQUEST["setuppercent"];
            $metrics["testpercent"] = $_REQUEST["testpercent"];
            $metrics["bugpercent"] = $_REQUEST["bugpercent"];
            $metrics["oppertunitypercent"] = $_REQUEST["oppertunitypercent"];
            $metrics["duration"] = $_REQUEST["duration"];
            saveSession_InsertSessionMetricsToDb($versionid, $metrics);

            //Create areas for session
            $areas = $_REQUEST["area"];
            saveSession_InsertSessionAreaToDb($versionid, $areas);

            //Create bugs connected to session
            saveSession_InsertSessionBugsToDb($versionid);

            //Create requirements connected to mission
            saveSession_InsertSessionRequirementsToDb($versionid);

            //Create sessionLinks connected to mission
            saveSession_InsertSessionSessionsLinksToDb($versionid);
        }
        else
        {
            echo "Session already saved.";
            $alreadySaved = true;
        }

    }
        //Update existing session
    else
    {
        $sessionid = $_REQUEST["sessionid"];
        $versionid = $_REQUEST["versionid"];

        saveSession_UpdateSessionDataToDb($sessionid);

        saveSession_UpdateSessionStatusToDb($versionid);


        saveSession_UpdateSessionMetricsToDb($versionid);

        $areas = $_REQUEST["area"];
        saveSession_UpdateSessionAreasToDb($versionid, $areas);

        saveSession_UpdateSessionBugsToDb($versionid);

        saveSession_UpdateSessionRequirementsToDb($versionid);

        saveSession_UpdateSessionRequirementsToDb($versionid);

        saveSession_UpdateSessionLinkedToDb($versionid);
    }


    mysql_close($con);

    if (!$alreadySaved) {
        echo "<p><b>Session saved</b></p>\n";
        echo "<p><a href=\"session.php?sessionid=$sessionid&command=view\" id=\"view_session\">View session</a></p>";
        echo "<p><a href=\"session.php?sessionid=$sessionid&command=edit\" id=\"edit_session\">Edit session</a></p>";

        echo "<span style=\"color:white\"><div id=\"sessioninfo\">sessionid:<div id=\"sessionid\">$sessionid</div>, versionid:<div id=\"versionid\">$versionid</div></span></div>\n";
    }
}


/**
 * Save session to database
 */
function copySession()
{

    echo "<h1>Copy session</h1>\n";
    $sessionid = false;
    $versionid = false;


    $publickey = md5(rand());
    $con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("cannot connect");
    mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

    //Copy session
    if ($_REQUEST["sessionid"] != "") {


        $sessionDataToCopy = (getSessionData($_REQUEST["sessionid"]));

        $sessionIdToCopy = $_REQUEST["sessionid"];

        //Create a new random key
        $sessionDataToCopy["publickey"] = md5(rand());

        //Will create a new session id to map to a session
        saveSession_CreateNewSessionId();

        //Get the new session id for user x
        $sessionid = saveSession_GetSessionIdForNewSession();
        echo "SessionId Created: <div id=\"copySessionid\">$sessionid</div>";
        //Insert sessiondata to mission table

        copySession_InsertSessionDataToDb($sessionid, $sessionDataToCopy);

        //Get versionId from db
        $versionid = saveSession_GetVersionIdForNewSession();

        $versionIdToCopy = getSessionVersionId($sessionIdToCopy);

        //Create missionstatus record in Db
        $executed = false;
        if ($_REQUEST["executed"] != "") {
            $executed = true;
        }
        saveSession_InsertSessionStatusToDb($versionid, $executed);

        //Create metrics record for session
        $metrics = array();
        $metrics["setuppercent"] = $_REQUEST["setuppercent"];
        $metrics["testpercent"] = $_REQUEST["testpercent"];
        $metrics["bugpercent"] = $_REQUEST["bugpercent"];
        $metrics["oppertunitypercent"] = $_REQUEST["oppertunitypercent"];
        $metrics["duration"] = $_REQUEST["duration"];
        saveSession_InsertSessionMetricsToDb($versionid, $metrics);


        //Create areas for session

        $areasFromOldSession = getSessionAreas($versionIdToCopy);

        saveSession_InsertSessionAreaToDb($versionid, $areasFromOldSession);


        //TODO: Fix the rest of copy session....
        //Create bugs connected to session
        //        saveSession_InsertSessionBugsToDb($versionid);
        //
        //        //Create requirements connected to mission
        //        saveSession_InsertSessionRequirementsToDb($versionid);
        //
        //        //Create sessionLinks connected to mission
        //        saveSession_InsertSessionSessionsLinksToDb($versionid);

        echo "Copy created...";

    }


    mysql_close($con);

}

/**
 *
 * @return unknown_type
 */
function echoSessionForm()
{
    $userSettings = getUserSettings();
    $title = "";
    $team = "";
    $charter = "";
    $notes = "";
    $sprint = "";
    $teamsprint = "";
    $area = "";

    $con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("cannot connect");
    mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

    $insertSessionData = false;

    if (strcmp($_REQUEST["command"], "edit") == 0) {
        $rowSessionData = getSessionData($_GET["sessionid"]);
        $insertSessionData = true;
    }

    if ($_GET["sessionid"] != "") {
        $rowSessionMetric = getSessionMetrics($rowSessionData["versionid"]);

        $rowSessionStatus = getSessionStatus($rowSessionData["versionid"]);

        $rowSessionAreas = getSessionAreas($rowSessionData["versionid"]);
    }
    mysql_close($con);

    if ($insertSessionData) {
        $title = htmlspecialchars($rowSessionData["title"]);
        $charter = $rowSessionData["charter"];
        $notes = $rowSessionData["notes"];
        $sprint = $rowSessionData["sprintname"];
        $teamsprint = $rowSessionData["teamsprintname"];
        $team = $rowSessionData["teamname"];
        $publickey = $rowSessionData["publickey"];
        $area = $rowSessionAreas;
        $testenvironment = $rowSessionData["testenvironment"];
        $software = $rowSessionData["software"];
    }
    else
    {
        $publickey = md5(rand());
    }
    echo "<form id=\"sessionform\" name=\"sessionform\" action=\"session.php?command=save\" method=\"POST\" accept-charset=\"utf-8\" onsubmit=\"return validate_form(this)\">\n";
    echo "<input type=\"hidden\" name=\"savesession\" value=\"true\">\n";
    echo "<input type=\"hidden\" name=\"publickey\" value=\"" . $publickey . "\">\n";
    echo "<input type=\"hidden\" name=\"sessionid\" value=\"" . $rowSessionData["sessionid"] . "\">\n";
    echo "<input type=\"hidden\" name=\"versionid\" value=\"" . $rowSessionData["versionid"] . "\">\n";
    echo "<input type=\"hidden\" name=\"tester\" value=\"" . $_SESSION['username'] . "\">\n";
    echo "<table width=\"1024\" border=\"0\">\n";
    echo "      <tr>\n";
    echo "            <td>\n";
    echo "                  <table width=\"1024\" border=\"0\">\n";
    echo "                        <tr>\n";
    echo "                              <td></td>\n";
    echo "                              <td>\n";
    if ($_REQUEST['command'] == 'edit') {
        echo "                                   <h1>Edit Session</h1>\n";
        echo "    <img src='pictures/information-small.png'>Last saved: <span id='autosaved'></span>";
    }
    else
    {
        echo "                                   <h1>New Session</h1>\n";
        echo "    <span id='autosaved'> <img src='pictures/information-small.png'> Autosave is enabled after first save of session.</span>";
    }

    echo "                              </td>\n";
    echo "                        </tr>\n";
    echo "                        <tr>\n";
    echo "                              <td></td>\n";
    echo "                              <td>\n";
    echo "                                   <img src=\"pictures/line.png\" alt=\"line\">\n";
    echo "                              </td>\n";
    echo "                        </tr>\n";
    echo "                        <tr>\n";
    echo "                              <td></td>\n";
    echo "                              <td>\n";
    echo "                                   <h3>Setup</h3>\n";
    echo "                              </td>\n";
    echo "                        </tr>\n";
    echo "                        <tr>\n";
    echo "                              <td>Session title: </td>\n";
    echo "                              <td><input id=\"input_title\" type=\"text\" size=\"133\" value=\"$title\" name=\"title\" style=\"width:1024px;height:20px;\"></td>\n";
    echo "                        </tr>\n";
    if ($_SESSION['settings']['team'] == 1) {
        echo "                        <tr>\n";
        echo "                              <td>Team: </td>\n";
        echo "                              <td>\n";
        if ($_REQUEST['sessionid'] != "")
            echoTeamSelect($team);
        else
            echoTeamSelect($userSettings['default_team']);
        echo "                              </td>\n";
        echo "                        </tr>\n";
    }
    if ($_SESSION['settings']['sprint'] == 1) {
        echo "                        <tr>\n";
        echo "                              <td valign=\"top\">Sprint: </td>\n";
        echo "                              <td>\n";
        if ($_REQUEST['sessionid'] != "")
            echoSprintSelect($sprint);
        else
            echoSprintSelect($userSettings['default_sprint']);
        echo "                              </td>\n";
        echo "                        </tr>\n";
    }
    if ($_SESSION['settings']['teamsprint'] == 1) {
        echo "                        <tr>\n";
        echo "                              <td valign=\"top\">Team sprint: </td>\n";
        echo "                              <td>\n";
        if ($_REQUEST['sessionid'] != "")
            echoTeamSprintSelect($teamsprint);
        else
            echoTeamSprintSelect($userSettings['default_teamsprint']);
        echo "                              </td>\n";
        echo "                        </tr>\n";
    }

    if ($_SESSION['settings']['area'] == 1) {
        echo "                        <tr>\n";
        echo "                              <td valign=\"top\">Area: </td>\n";
        echo "                              <td>\n";
        if ($_REQUEST['sessionid'] != "")
            echoAreaSelect($area);
        else
            echoAreaSelect($userSettings['default_area']);
        echo "                              </td>\n";
        echo "                        </tr>\n";
    }

    if ($_SESSION['settings']['testenvironment'] == 1) {
        echo "                        <tr>\n";
        echo "                              <td valign=\"top\">Testenvironment: </td>\n";
        echo "                              <td>\n";
        echoTestEnvironmentSelect($testenvironment);
        echo "                              </td>\n";
        echo "                        </tr>\n";
    }

    echo "                        <tr>\n";
    echo "                              <td valign=\"top\">Software under test: </td>\n";
    echo "                              <td>\n";
    echo "                                  <textarea id=\"textareaswundertest\" name=\"textareaswundertest\" rows=\"20\" cols=\"50\" style=\"width:1024px;height:50px;\">$software</textarea>\n";
    echo "                              </td>\n";
    echo "                        </tr>\n";

    echo "                        <tr>\n";
    echo "                              <td valign=\"top\">Test requirements: </td>\n";
    echo "                              <td>\n";
    echo "                              <table width=\"*\" border=\"0\">\n";
    echo "                                  <tr>\n";
    echo "                                      <td><input id=\"requirement\" type=\"text\" size=\"50\" value=\"\">\n";
    echo "                                      </td>\n";
    echo "                                      <td><div id=\"add_requirement\" class=\"addaction\">add requirement</div>\n";
    echo "                                      </td>\n";
    echo "                                  </tr>\n";
    echo "                                  <tr>\n";
    echo "                                      <td><div id=\"helptext1\" class=\"helptext1\" >Only add the requirements id</div></td>\n";
    echo "                                      <td></td>\n";
    echo "                                  </tr>\n";
    echo "                              </table>\n";
    echo "                              </td>\n";
    echo "                        </tr>\n";
    echo "                        <tr>\n";
    echo "                              <td></td>\n";
    echo "                              <td><div id=\"requirementlist_visible\" style=\"width: 1024px; height: 100%; background-color: rgb(239, 239, 239);\">";
    echo "                                " . echoRequirementsEdit($rowSessionData["versionid"]) . "</div></td>\n";
    echo "                        </tr>\n";

    echo "                        <tr>\n";
    echo "                              <td></td>\n";
    echo "                              <td>\n";
    echo "                                   <img src=\"pictures/line2.png\" alt=\"line\">\n";
    echo "                              </td>\n";
    echo "                        </tr>\n";

    echo "                        <tr>\n";
    echo "                              <td valign=\"top\">Link to other sessions:</td>\n";
    echo "                              <td>\n";
    echo "                              <table width=\"*\" border=\"0\">\n";
    echo "                                  <tr>\n";
    echo "                                      <td><input id=\"sessionlink\" type=\"text\" size=\"50\" value=\"\">\n";
    echo "                                      </td>\n";
    echo "                                      <td><div id=\"add_sessionlink\" class=\"addaction\">add link</div>\n";
    echo "                                      </td>\n";
    echo "                                  </tr>\n";
    echo "                                  <tr>\n";
    echo "                                      <td><div id=\"helptext2\" class=\"helptext1\" >Add the session id</div></td>\n";
    echo "                                      <td></td>\n";
    echo "                                  </tr>\n";
    echo "                              </table>\n";
    echo "                              </td>\n";
    echo "                        </tr>\n";
    echo "                        <tr>\n";
    echo "                              <td></td>\n";
    echo "                              <td><div id=\"sessionlinklist_visible\" style=\"width: 1024px; height: 100%; background-color: rgb(239, 239, 239);\">";
    echo "                                " . echoSessionlinkEdit($rowSessionData["versionid"]) . "</div></td>\n";
    echo "                        </tr>\n";

    echo "                        <tr>\n";
    echo "                              <td></td>\n";
    echo "                              <td>\n";
    echo "                                   <img src=\"pictures/line2.png\" alt=\"line\">\n";
    echo "                              </td>\n";
    echo "                        </tr>\n";

    echo "                        <tr>\n";
    echo "                              <td valign=\"top\">Charter: </td>\n";
    echo "                              <td>Describe what you will test (E.g. not the defect description), you should think about what to test and not just copy/paste from another source.\n";
    echo "                                  <textarea id=\"textarea1\"name=\"charter\" rows=\"20\" cols=\"50\" style=\"width:1024px;height:200px;\">$charter</textarea>\n";
    echo "                              </td>\n";
    echo "                        </tr>\n";
    echo "                        <tr>\n";
    echo "                              <td></td>\n";
    echo "                              <td>\n";
    echo "                                  <input type=\"submit\" value=\"Save\"/>\n";
    echo "                              </td>\n";
    echo "                        </tr>\n";
    echo "                        <tr>\n";
    echo "                              <td></td>\n";
    echo "                              <td>\n";
    echo "                                   <p><img src=\"pictures/line.png\" alt=\"line\"></p>\n";
    echo "                              </td>\n";
    echo "                        </tr>\n";
    echo "                        <tr>\n";
    echo "                              <td></td>\n";
    echo "                              <td>\n";
    echo "                                   <h3>Execution</h3>\n";
    echo "                              </td>\n";
    echo "                        </tr>\n";
    echo "                        <tr>\n";
    echo "                              <td valign=\"top\">Notes: </td>\n";
    echo "                              <td>\n";
    //echo "                              <i>It is possible to paste <a href=\"http://testing.gershon.info/reporter/\">RapidReporter</a> CVS notes or <a href=\"http://www.bbtestassistant.com\">BB TestAssistant</a> XML notes into the notes field.</i>\n";
    echo "                                  <textarea id=\"textarea2\" name=\"notes\" rows=\"20\" cols=\"50\" style=\"width:1024px;height:400px;\">$notes</textarea>\n";
    echo "                              </td>\n";
    echo "                        </tr>\n";

    echo "                        <tr>";
    echo "                              <td></td>\n";
    echo "                              <td>\n";
    echo "                                   <p><img src=\"pictures/line2.png\" alt=\"line\"></p>\n";
    echo "                              </td>\n";
    echo "                        </tr>\n";

    echo "                        <tr>\n";
    echo "                              <td valign=\"top\">Attachments:</td>\n";
    echo "                              <td>\n";
    if($_GET['sessionid']!=null)
    {

        echo "                                   <p><a class='uploadajax' href='include/filemanagement/index.php?sessionid=".$_GET['sessionid']."'>Manage attachments</a> Max file size: ".getMaxUploadSize()." mb</p>";
        echoAttachments();

    }
    else
    {
        echo "                                   <p>To be able to upload attachment the session need to be saved once</p>";

    }
    echo "                              </td>\n";
    echo "                        </tr>\n";

    echo "                        <tr>\n";
    echo "                              <td></td>\n";
    echo "                              <td>\n";
    echo "                                   <p><img src=\"pictures/line2.png\" alt=\"line\"></p>\n";
    echo "                              </td>\n";
    echo "                        </tr>\n";

    echo "                        <tr>\n";
    echo "                              <td valign=\"top\">Defects: </td>\n";
    echo "                              <td>\n";
    echo "                              <table width=\"*\" border=\"0\">\n";
    echo "                                  <tr>\n";
    echo "                                      <td><input id=\"bug\" type=\"text\" size=\"50\" value=\"\">\n";
    echo "                                      </td>\n";
    echo "                                      <td><div id=\"add_bug\" class=\"addaction\">add bug</div>\n";
    echo "                                      </td>\n";
    echo "                                  </tr>\n";
    echo "                                  <tr>\n";
    echo "                                      <td><div id=\"helptext3\" class=\"helptext1\" >Only add the defect id</div></td>\n";
    echo "                                      <td></td>\n";
    echo "                                  </tr>\n";
    echo "                              </table>\n";
    echo "                              </td>\n";
    echo "                        </tr>\n";

    echo "                        <tr>\n";
    echo "                              <td></td>\n";
    echo "                              <td><div id=\"buglist_visible\" style=\"width: 1024px; height: 100%; background-color: rgb(239, 239, 239);\">";
    echo "                             " . echoBugsEdit($rowSessionData["versionid"]) . "</div></td>\n";
    echo "                        </tr>\n";

    echo "                        <tr>\n";
    echo "                              <td></td>\n";
    echo "                              <td>\n";
    echo "                                   <img src=\"pictures/line2.png\" alt=\"line\">\n";
    echo "                              </td>\n";
    echo "                        </tr>\n";

    echo "                        <tr>\n";
    echo "                              <td>Metrics: </td>\n";
    echo "                              <td>\n";
    echo "                                    <table width=\"1024\" border=\"0\">\n";
    echo "                                          <tr>\n";
    echo "                                                <td>Setup(%): </td>\n";
    echo "                                                <td>\n";
    echo "                                                      <select id=\"setuppercent\" class=\"metricoption\" name=\"setuppercent\">\n";
    echoPercentSelection($rowSessionMetric["setup_percent"]);
    echo "                                                      </select>\n";
    echo "                                                </td>\n";
    echo "                                                <td>Test(%): </td>\n";
    echo "                                                <td>\n";
    echo "                                                      <select id=\"testpercent\" class=\"metricoption\" name=\"testpercent\">\n";
    echoPercentSelection($rowSessionMetric["test_percent"]);
    echo "                                                      </select>\n";
    echo "                                                </td>\n";
    echo "                                                <td>Bug(%): </td>\n";
    echo "                                                <td>\n";
    echo "                                                      <select id=\"bugpercent\" class=\"metricoption\" name=\"bugpercent\">\n";
    echoPercentSelection($rowSessionMetric["bug_percent"]);
    echo "                                                      </select>\n";
    echo "                                                </td>\n";
    echo "                                                <td>Opportunity(%): </td>\n";
    echo "                                                <td>\n";
    echo "                                                      <select id=\"oppertunitypercent\" class=\"metricoption\" name=\"oppertunitypercent\">\n";
    echoPercentSelection($rowSessionMetric["opportunity_percent"]);
    echo "                                                      </select>\n";
    echo "                                                </td>\n";
    echo "                                                <td>Session duration (min): </td>\n";
    echo "                                                <td>\n";
    echo "                                                      <select name=\"duration\">\n";
    echoDurationSelection($rowSessionMetric["duration_time"]);
    echo "                                                      </select>\n";
    echo "                                                </td>\n";
    echo "                                          </tr>\n";
    echo "                                    </table>\n";
    echo "                              </td>\n";
    echo "                        </tr>\n";
    echo "                        <tr>\n";
    echo "                              <td></td>\n";
    echo "                              <td>\n";
    echo "                                   <div id=\"metricscalculation\"></div>\n";
    echo "                              </td>\n";
    echo "                        </tr>\n";
    echo "                        <tr>\n";
    echo "                              <td></td>\n";
    echo "                              <td>\n";
    echo "                                   <p><img src=\"pictures/line2.png\" alt=\"line\"></p>\n";
    echo "                              </td>\n";
    echo "                        </tr>\n";
    echo "                        <tr>\n";
    echo "                              <td>Executed:</td>\n";
    echo "                              <td>\n";
    if ($rowSessionStatus['executed'] == 1) {
        echo "                                  <input type=\"checkbox\" name=\"executed\" checked=\"checked\" value=\"yes\" id=\"executed\">\n";
    }
    else
    {
        echo "                                  <input type=\"checkbox\" name=\"executed\" value=\"yes\" id=\"executed\">\n";
    }
    echo "                              </td>\n";
    echo "                        </tr>\n";
    echo "                        <tr>\n";
    echo "                              <td></td>\n";
    echo "                              <td>\n";
    echo "                                  <input id=\"input_submit\" type=\"submit\" value=\"Save\"/>\n";
    echo "                              </td>\n";
    echo "                        </tr>\n";
    echo "                  </table>\n";
    echo "            </td>\n";
    echo "      </tr>\n";
    echo "</table>\n";
    echo "                              <div><textarea id=\"buglist_hidden\" name=\"buglist_hidden\" rows=\"1\" cols=\"1\" style= \"visibility:hidden;width:10px;height:2px;\"></textarea></div>\n";
    echo "                              <div><textarea id=\"requirementlist_hidden\" name=\"requirementlist_hidden\" rows=\"1\" cols=\"1\" style= \"visibility:hidden;width:10px;height:2px;\"></textarea></div>\n";
    echo "                              <div><textarea id=\"sessionlinklist_hidden\" name=\"sessionlink_hidden\" rows=\"15\" cols=\"15\" style= \"visibility:hidden;width:10px;height:2px;\"></textarea></div>\n";
    //    style= \"visibility:hidden;width:10px;height:2px;\"

    echo "              <script type=\"text/javascript\"> $('#requirementlist_hidden').text(myRequirements.toString());</script> \n";
    echo "              <script type=\"text/javascript\"> $('#buglist_hidden').text(myBugs.toString());</script> \n";
    echo "              <script type=\"text/javascript\"> $('#sessionlinklist_hidden').text(mySessionlinks.toString());</script> \n";
    echo "</form>\n";
}


/**
 * Prints percent (belongs to a HTML select item) to screen. E.g 5,10,15,20...
 *
 */
function echoPercentSelection($selected)
{
    //echo "                                      <option>$selected</option>\n";
    for ($index = 0; $index <= 100; $index = $index + 5) {
        if ($index == $selected) {
            echo "                                      <option selected=\"selected\">$index</option>\n";
        }
        else
        {
            echo "                                      <option>$index</option>\n";
        }
    }
}

/**
 * Prints duration option (belongs to a HTML select item) to screen
 *
 */
function echoDurationSelection($selected)
{
    for ($index = 15; $index <= 480; $index = $index + 15) {
        if ($index == $selected) {
            echo "                                      <option selected=\"selected\">$index</option>\n";
        }
        else
        {
            echo "                                      <option>$index</option>\n";
        }
    }
}

/**
 * Parse RapidReporter CVS notes to HTML
 * @param $notes RapidReporter CVS notes
 * @return parsed notes as HTML
 */

function parseRapidReporterNotes($notes)
{

    $explodedCharterNotes = explode("<br/>", $notes);

    $charterParsed = "<table width=\"1024\" border=\"0\">\n";
    $charterParsed .= "    <tr>\n";
    $charterParsed .= "      <td><b>Time</b></td>\n";
    $charterParsed .= "        <td><b>Type</b></td>\n";
    $charterParsed .= "        <td><b>Note</b></td>\n";

    $charterParsed .= "    </tr>\n";

    for ($index = 1; $index < count($explodedCharterNotes); $index++) {
        $charterParsed .= "   <tr>\n";
        $time = substr($explodedCharterNotes[$index], 11, 8);

        $commaArray = explode(",", $explodedCharterNotes[$index], 4);
        $type = $commaArray[2];

        //Reverse the string to minimize the effort to strip the 2 last , chars.
        $reverseString = strrev($commaArray[3]);
        $stringArray = (explode(",", $reverseString, 3));
        $string = strrev($stringArray[2]);

        $note = substr($string, 1, strlen($string) - 2);

        $charterParsed .= "       <td valign=\"top\">$time</td>\n";
        $charterParsed .= "       <td valign=\"top\">" . htmlspecialchars($type) . "</td>\n";
        $charterParsed .= "       <td valign=\"top\">" . htmlspecialchars($note) . "</td>\n";

        $charterParsed .= "   </tr>\n";
    }
    $charterParsed .= "</table>\n";
    return $charterParsed;
}


/**
 * Parse BB TestAssistant XML notes to HTML
 * @param $notes BB TestAssistant XML notes
 * @return parsed notes as HTML
 */
function parseBBTestAssistantNotes($notes)
{
    $notes = htmlspecialchars_decode($notes);
    $notes = str_replace("<br/>", "", $notes);
    $notes = str_replace("&nbsp;", "", $notes);
    $charterParsed = "<table width=\"1024\" border=\"0\">\n";
    $charterParsed .= "    <tr>\n";
    $charterParsed .= "      <td width=\"100\"><b>Time</b></td>\n";
    $charterParsed .= "        <td><b>Note</b></td>\n";
    $charterParsed .= "    </tr>\n";


    $xmlDoc = new DOMDocument();
    $xmlDoc->loadXML($notes);

    $searchNode = $xmlDoc->getElementsByTagName("Note");

    foreach ($searchNode as $searchNode)
    {
        $valueTimestamp = $searchNode->getAttribute('timestamp');
        $valueNode = $searchNode->nodeValue;

        $charterParsed .= "   <tr>\n";
        $charterParsed .= "       <td valign=\"top\">$valueTimestamp</td>\n";
        $charterParsed .= "       <td valign=\"top\">" . htmlspecialchars($valueNode) . "</td>\n";
        $charterParsed .= "   </tr>\n";
    }

    return $charterParsed;
}

function deleteSession()
{


    $sessionid = $_REQUEST["sessionid"];
    insertAutomaticGoBackOnePage();
    //$versionid = GetSessionIdFromVersionId($_REQUEST["sessionid"]);
    if ($sessionid != "") {
        deleteSessionFromDatabase($sessionid);

        echo "Session " . $_REQUEST["sessionid"] . " deleted from database";
    }
    else
    {
        echo "Session " . $_REQUEST["sessionid"] . " could not be found in database.(Already deleted?)";
    }

}