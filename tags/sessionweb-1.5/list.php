<?php
require_once('include/loggingsetup.php');

session_start();
if (!session_is_registered(myusername)) {
    header("location:index.php");
}
include_once('config/db.php.inc');
include_once ('include/commonFunctions.php.inc');
include("include/header.php.inc");


echo "<br>";

$currentPage = $_GET["page"];
$listSettings = "";

if (count($_REQUEST) < 3 && $_REQUEST["data"] != "stored") {

    $userSettings = getUserSettings();
    if ($userSettings['list_view'] == "all") {
        $tester = "";
    }
    else if ($userSettings['list_view'] == "mine") {
        $tester = $_SESSION['username'];
    }
    else
    {
        $tester = "";
    }
    $listSettings = array();
    $listSettings["tester"] = $tester;
    $listSettings["sprint"] = "";
    $listSettings["teamsprint"] = "";
    $listSettings["team"] = "";
    $listSettings["area"] = "";
    $listSettings["status"] = "";
    $listSettings["norowdisplay"] = "30";

}
elseif ($_POST != null)
{

    $listSettings = $_REQUEST;
}
else
{
    if ($_REQUEST["data"] == "stored") {
        $listSettings = $_SESSION['listsearch'];
    }
}

$_SESSION['listsearch'] = $listSettings;

if ($currentPage == "") {
    $currentPage = 1;
}

echoSearchDiv($listSettings);

echoSessionTable($currentPage, $listSettings);


echoColorExplanation();

echoIconExplanation();


include("include/footer.php.inc");


function echoSessionTable($currentPage, $listSettings)
{
    echo "<table width=\"1024\" border=\"0\">\n";
    echo "  <tr>\n";
    echo "      <td id=\"tableheader_id\" width=\"25\">Id</td>\n";
    echo "      <td id=\"tableheader_actions\" width=\"100\">Actions</td>\n";
    echo "      <td id=\"tableheader_title\" >Title</td>\n";
    echo "      <td id=\"tableheader_users\" >User</td>\n";
    if ($_SESSION['settings']['sprint'] == 1) {
        echo "      <td id=\"tableheader_sprint\" >Sprint</td>\n";
    }
    //	if($_SESSION['settings']['teamsprint']==1 )
    //	{
    //		echo "      <td id=\"tableheader_teamsprint\" >Team sprint</td>\n";
    //	}
    if ($_SESSION['settings']['team'] == 1) {
        echo "      <td id=\"tableheader_team\" >Team</td>\n";
    }

    echo "      <td id=\"tableheader_updated\" width=\"120\">Updated</td>\n";
    echo "  </tr>\n";

    $rowsToDisplay = 30;
    if ($listSettings["norowdisplay"] != "") {
        $rowsToDisplay = $listSettings["norowdisplay"];
    }


    $limitDown = ($currentPage * $rowsToDisplay) - $rowsToDisplay;

    $con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("cannot connect");
    mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

    $sqlSelect = createSelectQueryForSessions($limitDown, $rowsToDisplay, $listSettings);

    $result = mysql_query($sqlSelect);
    //   echo "$sqlSelect<br>";
    //    print_r($listSettings);
    $num_rows = 0;

    if ($result) {
        $num_rows = mysql_num_rows($result);
        while ($row = mysql_fetch_array($result)) {

            echoAllSessions($row, $listSettings);
        }
    }
    else
    {
        echo "echoSessionTable: " . mysql_error() . "<br/>";
    }

    echo "</table>\n";
    mysql_close($con);
    echoPreviouseAndNextLink($currentPage, $num_rows);

}

/**
 * Echo all sessions to the table
 * @param  $row sessions to display
 * @param  $listSettings filter settings
 * @return void
 */
function echoAllSessions($row, $listSettings)
{
    // $rowSessionStatus = getSessionStatus($row["versionid"]);

    if ($listSettings["status"] != "") {
        if (strcmp($listSettings["status"], "Not Executed") == 0 && $row["debriefed"] == 0) {
            if ($row["executed"] == 0) {
                echoOneSession($row, $row);
            }
        }
        elseif (strcmp($listSettings["status"], "Executed") == 0)
        {
            if ($row["executed"] == 1 && $row["debriefed"] == 0) {
                echoOneSession($row, $row);
            }
        }
        elseif (strcmp($listSettings["status"], "Debriefed") == 0)
        {
            if ($row["debriefed"] == 1) {
                echoOneSession($row);
            }
        }
    }
    else
    {
        echoOneSession($row);
    }
}

/**
 * Echos one session in the session table
 * @param  $row session to echo
 * @return void
 */
function echoOneSession($row)
{
    $color = getSessionColorCode($row);
    echo "  <tr class=\"tr_sessionrow \" bgcolor=\"$color\">\n";
    echo "      <td>" . $row["sessionid"] . "</td>\n";
    echo "      <td width='125'>\n";

    echoEditSessionIcon($row);

    echoReassignSessionIcon($row);

    echoDeleteSessionIcon($row);

    evalPublicViewIcon($row);

    echoCopyIcon($row);

    echoDebriefSessionIcon($row);
    addjQueryCopyPopUp($row["sessionid"]);

    echo "      </td>\n";

    echoTitle($row);

    echoSessionUser($row);

    //	if($_SESSION['settings']['teamsprint']==1 )
    //	{
    //		echo "      <td id=\"tablerowteamsprint_".$row["sessionid"]."\">".$row["teamsprintname"]."</td>\n";
    //	}

    echoSessionTeam($row);
}

function echoSessionTeam($row)
{
    if ($_SESSION['settings']['team'] == 1) {
        echo "      <td id=\"tablerowteam_" . $row["sessionid"] . "\">" . $row["teamname"] . "</td>\n";
    }
    echo "      <td width='135' id=\"tablerowupdatead_" . $row["sessionid"] . "\">" . $row["updated"] . "</td>\n";
    echo "  </tr>\n";
}


function echoSessionUser($row)
{
    echo "      <td id=\"tablerowuser_" . $row["sessionid"] . "\">" . $row["username"] . "</td>\n";
    if ($_SESSION['settings']['sprint'] == 1) {
        echo "      <td id=\"tablerowsprint_" . $row["sessionid"] . "\">" . $row["sprintname"] . "</td>\n";
    }
}


function echoEditSessionIcon($row)
{
    if (strcmp($_SESSION['username'], $row["username"]) == 0 || strcmp($_SESSION['superuser'], "1") == 0 || strcmp($_SESSION['useradmin'], "1") == 0) {
        echo "      <a id=\"edit_session" . $row["sessionid"] . "\"  class=\"url_edit_session\" href=\"session.php?sessionid=" . $row["sessionid"] . "&amp;command=edit\"><img class=\"picture_edit_session\" src=\"pictures/edit.png\" border=\"0\" alt=\"edit session\" title=\"Edit session\"/></a>\n";
    }
}

function echoTitle($row)
{
    $title = $row["title"];
    if (strlen($row["title"]) > 30) {
        $title = substr($row["title"], 0, 50) . "...";
    }

    echo "      <td >\n";
    echo "<div id=\"tablerowtitle_" . $row["sessionid"] . "\" title=\"" . $row["title"] . "\">\n";
    echo "<a id=\"view_session" . $row["sessionid"] . "\" class=\"url_view_session\" href=\"session.php?sessionid=" . $row["sessionid"] . "&amp;command=view\">$title</a></div>\n";
    echo "</td>\n";
}


function echoReassignSessionIcon($row)
{
    if (strcmp($_SESSION['username'], $row["username"]) == 0 || strcmp($_SESSION['superuser'], "1") == 0 || strcmp($_SESSION['useradmin'], "1") == 0) {
        echo "      <a id=\"reassign_session" . $row["sessionid"] . "\" class=\"reassign_session\" href=\"session.php?sessionid=" . $row["sessionid"] . "&amp;command=reassign\"><img class=\"picture_reassign_session\" src=\"pictures/user-new-2-small.png\" border=\"0\" alt=\"Reassign session\" title=\"Reassign session\"/></a>\n";

    }
}

function echoDebriefSessionIcon($row)
{
    if (strcmp($_SESSION['superuser'], "1") == 0 || strcmp($_SESSION['useradmin'], "1") == 0) {
        if ($row['executed'] != false && $row['debriefed'] != true && $row['closed'] != true) {
            echo "      <a id=\"debrief_session" . $row["sessionid"] . "\" class=\"url_edit_session\" href=\"session.php?sessionid=" . $row["sessionid"] . "&amp;command=debrief\"><img class=\"picture_edit_session\" src=\"pictures/debrieficon.png\" border=\"0\" alt=\"debrief session\" title=\"Debrief session\"/></a>\n";
        }
    }
}

function echoDeleteSessionIcon($row)
{
    if (strcmp($_SESSION['username'], $row["username"]) == 0 || strcmp($_SESSION['useradmin'], "1") == 0) {
        echo "      <a id=\"delete_session" . $row["sessionid"] . "\" href=\"session.php?sessionid=" . $row["sessionid"] . "&amp;command=delete\"><img src=\"pictures/edit-delete-2-small.png\" border=\"0\" alt=\"Delete session\" title=\"Delete session\" class=\"delete_session\"/></a>\n";
        addjQueryDeletePopUp($row["sessionid"]);
    }
}

function evalPublicViewIcon($row)
{
    if ($_SESSION['settings']['publicview'] == 1) {
        echoPublicViewIcon($row);
    }
}

function createSelectQueryForSessions($limitDown, $rowsToDisplay, $listSettings)
{
    $sqlSelect = "";
    $sqlSelect .= "SELECT * ";
    $sqlSelect .= "FROM   `sessioninfo` ";
    if ($listSettings["tester"] != "" || $listSettings["sprint"] != "" || $listSettings["teamsprint"] != "" || $listSettings["team"] != "" | $listSettings["status"] != "") {
        $sqlSelect .= " WHERE  sessionid NOT LIKE \"\" ";
        if ($listSettings["tester"] != "") {
            $sqlSelect .= "     AND username=\"" . $listSettings["tester"] . "\" ";
        }
        if ($listSettings["sprint"] != "") {
            $sqlSelect .= "     AND sprintname=\"" . $listSettings["sprint"] . "\" ";
        }
        if ($listSettings["teamsprint"] != "") {
            $sqlSelect .= "     AND teamsprintname=\"" . $listSettings["teamsprint"] . "\" ";
        }
        if ($listSettings["team"] != "") {
            $sqlSelect .= "     AND teamname=\"" . $listSettings["team"] . "\" ";
        }
        if ($listSettings["status"] != "") {
            if ($listSettings["status"] == "Not Executed")
                $sqlSelect .= "     AND executed=0 ";
            if ($listSettings["status"] == "Executed")
                $sqlSelect .= "     AND executed=1 ";
            if ($listSettings["status"] == "Debriefed")
                $sqlSelect .= "     AND debriefed=1 ";
        }

    }


    $sqlSelect .= "ORDER BY updated DESC ";
    $sqlSelect .= "LIMIT  $limitDown, $rowsToDisplay ";

    return $sqlSelect;
}

function echoSearchDiv($listSettings)
{
    echo "<img src='pictures/listsettings.png' class='showoption' id='showoptionpicture' alt='Session list settings'>\n";
    echo "<a class='showoption' id='showoptiontext' href=\"#\">Show Search Functionality</a>\n";

    echo "<div style=\"width: 1024px; height: 100%; background-color: rgb(239, 239, 239);\" id=\"option_list\">\n";
    echo "<form id=\"narrowform\" name=\"narrowform\" action=\"list.php\" method=\"POST\" accept-charset=\"utf-8\">\n";

    echo "<table width=\"1024\" border=\"0\">\n";
    echo "    <tr>\n";
    echo "        <td id=\"option_user\">User";
    echoTesterFullNameSelect($listSettings["tester"]);
    //    echoTesterSelect($listSettings["tester"]);
    echo "        </td>\n";
    if ($_SESSION['settings']['sprint'] == 1) {
        echo "        <td id=\"option_sprint\">Sprint:";
        echoSprintSelect($listSettings["sprint"]);
        echo "        </td>\n";
    }
    if ($_SESSION['settings']['teamsprint'] == 1) {
        echo "        <td id=\"option_teamsprint\">Team sprint:";
        echoTeamSprintSelect($listSettings["teamsprint"]);
        echo "        </td>\n";
    }
    if ($_SESSION['settings']['team'] == 1) {
        echo "        <td id=\"option_team\">Team:";
        echoTeamSelect($listSettings["team"]);
        echo "        </td>\n";
    }
    echo "    </tr>\n";
    echo "    <tr>\n";
    if ($_SESSION['settings']['area'] == 1) {
        echo "        <td>\n";
        echo "<table width=\"*\" border=\"0\">\n";
        echo "    <tr valign=\"top\">\n";
        echo "        <td valign=\"top\">Area:";
        echo "        </td>\n";
        echo "        <td id=\"option_area\">\n";
        echoAreaSelectSingel($listSettings["area"]);
        echo "        </td>\n";
        echo "</table>\n";
        echo "        </td>\n";
    }
    echo "        <td id=\"option_status\">Status:\n";
    echoStatusTypes($listSettings["status"]);
    echo "        </td>\n";
    echo "    </tr>\n";
    echo "</table>\n";
    echo "Number of session to show on each page\n";
    echoNumberOfRowToDisplay();
    echo "    <p><input id=\"input_continue\" type=\"submit\" value=\"Continue\" /></p>\n";
    echo "</form>\n";

    echo "</div>\n";
}

function echoColorExplanation()
{
    echo "<table width=\"*\" border=\"0\">\n";
    echo "    <tr >\n";
    echo "        <td bgcolor=\"#c2c287\">Not Executed\n";
    echo "        </td>\n";
    echo "        <td>&rarr;";
    echo "        </td>\n";
    echo "        <td bgcolor=\"#66ffff\">In Progress\n";
    echo "        </td>\n";
    echo "        <td>&rarr;";
    echo "        </td>\n";
    echo "        <td bgcolor=\"#ffff77\">Executed\n";
    echo "        </td>\n";
    echo "        <td>&rarr;";
    echo "        </td>\n";
        echo "        <td bgcolor=\"#99ff99\">Debriefed\n";
    echo "        </td>\n";
    echo "    </tr>\n";
        echo "    <tr >\n";
    echo "        <td>\n";
    echo "        </td>\n";
    echo "        <td>";
    echo "        </td>\n";
    echo "        <td>\n";
    echo "        </td>\n";
    echo "        <td>";
    echo "        </td>\n";
    echo "        <td>";
    echo "        </td>\n";
    echo "        <td>&rarr;";
    echo "        </td>\n";
    echo "        <td bgcolor=\"#ffcccc\">Closed\n";
    echo "        </td>\n";
    echo "    </tr>\n";

    echo "    </table>\n";
}

function echoIconExplanation()
{
    echo "<table width=\"*\" border=\"0\" cellpadding='4' cellspacing='0'>\n";
    echo "    <tr bgcolor='B3B3B3'>\n";
    echo "        <td valign=\"top\"><img src=\"pictures/edit.png\" alt=\"Edit Session\" /></td><td valign=\"top\">Edit Session\n";
    echo "        </td>\n";
    echo "        <td valign=\"top\"><img src=\"pictures/debrieficon.png\" alt=\"Debrief Session\" /></td><td valign=\"top\">Debrief Session\n";
    echo "        </td>\n";
    echo "        <td valign=\"top\"><img src=\"pictures/edit-delete-2-small.png\" alt=\"Delete Session\" /></td><td valign=\"top\">Delete Session\n";
    echo "        </td>\n";
    echo "        <td valign=\"top\"><img src=\"pictures/user-new-2-small.png\" alt=\"Reassign Session\" /></td><td valign=\"top\">Reassign Session\n";
    echo "        </td>\n";
    echo "        <td valign=\"top\"><img src=\"pictures/share-3-small.png\" alt=\"Share Session\" /></td><td valign=\"top\">Share Session\n";
    echo "        </td>\n";
    echo "        <td valign=\"top\"><img src=\"pictures/edit-copy-9-small.png\" alt=\"Copy Session\" /></td><td valign=\"top\">Copy Session\n";
    echo "        </td>\n";
    echo "    </tr>\n";
    echo "</table>\n";
}

function getSessionColorCode($rowSessionStatus)
{
    $notes = getSessionNotes($rowSessionStatus['versionid']);

    $color = "#c2c287";

    if ($notes != "") //session started but not executed.
    {
        $color = '#66ffff';
    }

    if ($rowSessionStatus["executed"] == 1) {
        $color = "#ffff77";
    }

    if ($rowSessionStatus["debriefed"] == 1) {
        $color = "#99ff99";
    }

    if ($rowSessionStatus["closed"] == 1) {
        $color = "#ffcccc";
    }
    return $color;
}

function echoPreviouseAndNextLink($currentPage, $num_rows)
{
    $nextPage = $currentPage + 1;
    echo "<table width=\"1024\" border=\"0\">\n";
    echo "  <tr>\n";
    if ($currentPage != 1) {
        $prevPage = $currentPage - 1;
        echo "     <td><a id=\"prev_page\" href=\"list.php?page=$prevPage&amp;data=stored\">Previous page</a></td>";
    }
    else
    {
        echo "     <td></td>\n";
    }
    if ($num_rows == 30) {
        echo "      <td id=\"next_page\" align=\"right\"><a href=\"list.php?page=$nextPage&amp;data=stored\">Next page</a><br/></td>\n";
    }
    else
    {
        echo "     <td></td>\n";
    }
    echo "  </tr>\n";

    echo "</table>\n";
}

/**
 * Popup for validating if a session should be deleted or not.
 * @param  $id
 * @return void
 */
function addjQueryDeletePopUp($id)
{
    //Delete Session questionbox
    echo "              <script type=\"text/javascript\">\n";
    echo "$(\"#delete_session" . $id . "\").click(function(){\n";
    echo "        var answer = confirm(\"Delete session from database?\");\n";
    echo "        if(answer)\n";
    echo "        {\n";
    echo "            return true;\n";
    echo "        }\n";
    echo "        else\n";
    echo "        {\n";
    echo "            return false;\n";
    echo "        }\n";
    echo "});\n";
    echo "              </script>\n";
}

/**
 * Popup for validating if a session should be copied or not.
 * @param  $id
 * @return void
 */
function addjQueryCopyPopUp($id)
{
    //Copy Session questionbox
    echo "              <script type=\"text/javascript\">\n";
    echo "$(\"#copy_session" . $id . "\").click(function(){\n";
    echo "        var answer = confirm(\"Copy session?\");\n";
    echo "        if(answer)\n";
    echo "        {\n";
    echo "            return true;\n";
    echo "        }\n";
    echo "        else\n";
    echo "        {\n";
    echo "            return false;\n";
    echo "        }\n";
    echo "});\n";
    echo "              </script>\n";
}

function echoPublicViewIcon($row)
{
    echo "<a id=\"publicview_session" . $row["sessionid"] . "\" class=\"publicview_session\" href=\"publicview.php?sessionid=" . $row["sessionid"] . "&amp;command=view&amp;publickey=" . $row["publickey"] . "\">";
    echo "  <img src=\"pictures/share-3-small.png\" border=\"0\" alt=\"Share session\" title=\"Share session\"/>";
    echo "</a>\n";
}

function echoCopyIcon($row)
{
    echo "<a id=\"copy_session" . $row["sessionid"] . "\" class=\"copy_session\" href=\"session.php?command=copy&amp;sessionid=" . $row["sessionid"] . "\">";
    echo "  <img src=\"pictures/edit-copy-9-small.png\" border=\"0\" alt=\"Copy session\" title=\"Copy session\"/>";
    echo "</a>\n";
}