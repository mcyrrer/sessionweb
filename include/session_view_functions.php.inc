<?php

function echoSessionAction()
{
    $sessionid = $_REQUEST["sessionid"];

    $con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("cannot connect");
    mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");
    $row = getSessionData($sessionid);

    echo "Action menu: ";
    if (strcmp($_SESSION['username'], $row["username"]) == 0 || strcmp($_SESSION['superuser'], "1") == 0 || strcmp($_SESSION['useradmin'], "1") == 0) {
        echo "<a id='edit_session' class='url_edit_session' href='session.php?sessionid=$sessionid&command=edit'>";
        echo "<img class='picture_edit_session' src='pictures/edit.png' border='0' alt='edit session' title='Edit session'>";
        echo "</a>";
        echo " | <a id='reassign_session' class='reassign_session' href='session.php?sessionid=$sessionid&command=reassign'>";
        echo "<img class='picture_reassign_session' src='pictures/user-new-2-small.png' border='0' alt='Reassign session' title='Reassign session'>";
        echo "</a>";
    }
    if (strcmp($_SESSION['username'], $row["username"]) == 0 || strcmp($_SESSION['useradmin'], "1") == 0) {
        echo " | <a id='delete_session' href='session.php?sessionid=$sessionid&command=delete'>";
        echo "<img src='pictures/edit-delete-2-small.png' border='0' alt='Delete session' title='Delete session' class='delete_session'>";
        echo "</a>";
    }
    echo "<script type='text/javascript'>$('#delete_session').click(function(){ var answer = confirm('Delete session from database?'); if(answer) { return true; } else { return false; }});  </script>";
    if ($_SESSION['settings']['publicview'] == 1) {
        echo " | <a id='publicview_session' class='publicview_session' href='publicview.php?sessionid=$sessionid&command=view&publickey=" . getSessionPublicKeyBasedOnSessionId($sessionid) . "'>";
        echo "<img src='pictures/share-3-small.png' border='0' alt='Share session' title='Share session'>";
        echo "</a>";
    }
    echo " | <a id='copy_session' class='copy_session' href='session.php?command=copy&sessionid=$sessionid'>";
    echo "<img src='pictures/edit-copy-9-small.png' border='0' alt='Copy session' title='Copy session'>";
    echo "</a>";
    echo "<script type='text/javascript'>$('#copy_session').click(function(){ var answer = confirm('Copy session?'); if(answer) { return true; } else { return false; }});  </script>";
    mysql_close($con);
}

function echoViewSession()
{
    if ($_GET["sessionid"] != null) {
        $con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("cannot connect");
        mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

        $sqlSelectSession = "";
        $sqlSelectSession .= "SELECT * ";
        $sqlSelectSession .= "FROM   mission ";
        $sqlSelectSession .= "WHERE  sessionid = " . $_GET["sessionid"];

        $resultSession = mysql_query($sqlSelectSession);

        if (!$resultSession) {
            echo "echoViewSession: " . mysql_error() . "<br/>";
        }
        else
        {
            $row = mysql_fetch_array($resultSession);
            if ($row["versionid"] != null) {
                $rowSessionMetric = getSessionMetrics($row["versionid"]);

                $rowSessionStatus = getSessionStatus($row["versionid"]);

                echo "<h1>View Session</h1>\n";
                echo "<table style=\"text-align: left;\" border=\"0\" cellpadding=\"0\" cellspacing=\"5\">";
                echo "    <tr>\n";
                echo "        <td></td>\n";
                echo "        <td>\n";
                echo "            <h2>Session title</h2>\n";
                echo "            <div style=\"background-color:#efefef;width: 1024px; height: 100%; background-color: rgb(239, 239, 239);\">\n";
                echo "                " . $row["title"] . "\n";
                echo "            </div>\n";
                echo "        </td>\n";
                echo "    </tr>\n";
                echo "    <tr>\n";
                echo "        <td align=\"left\">\n";
                echo "            &nbsp;&nbsp;&nbsp;\n";
                echo "        </td>\n";
                echo "        <td align=\"left\">\n";
                echo "        <table style=\"text-align: left;\" border=\"0\" cellpadding=\"0\" cellspacing=\"4\">";
                echo "            <tr>\n";
                echo "                <td align=\"left\" valign='top'>\n";
                echo "                    <h4>Sessionid</h4>\n";
                echo "                    <div style=\"background-color:#efefef;\">\n";
                echo "                        " . $row["sessionid"] . "\n";
                echo "                    </div>\n";
                echo "                </td>\n";
                echo "                <td align=\"left\" valign='top'>\n";
                echo "                    <h4>Username</h4>\n";
                echo "                    <div style=\"background-color:#efefef;\">\n";
                echo "                        " . $row["username"] . "\n";
                echo "                    </div>\n";
                echo "                </td>\n";
                if ($_SESSION['settings']['team'] == 1) {
                    $value = $row["teamname"];
                    if ($row["teamname"] == "") {
                        $value = "&nbsp;";
                    }
                    echo "                <td align=\"left\" valign='top'>\n";
                    echo "                    <h4>Teamname</h4>\n";
                    echo "                    <div style=\"background-color:#efefef;\">\n";
                    echo "                        $value\n";
                    echo "                    </div>\n";
                    echo "                </td>\n";
                }
                if ($_SESSION['settings']['sprint'] == 1) {
                    $value = $row["sprintname"];
                    if ($row["sprintname"] == "") {
                        $value = "&nbsp;";
                    }
                    echo "                <td align=\"left\" valign='top'>\n";
                    echo "                    <h4>Sprintname</h4>\n";
                    echo "                    <div style=\"background-color:#efefef;\">\n";
                    echo "                        $value\n";
                    echo "                    </div>\n";
                    echo "                </td>\n";
                }
                if ($_SESSION['settings']['teamsprint'] == 1) {
                    $value = $row["teamsprintname"];
                    if ($row["teamsprintname"] == "") {
                        $value = "&nbsp;";
                    }
                    echo "                <td align=\"left\" valign='top'>\n";
                    echo "                    <h4>Team sprintname</h4>\n";
                    echo "                    <div style=\"background-color:#efefef;\">\n";
                    echo "                        $value\n";
                    echo "                    </div>\n";
                    echo "                </td>\n";
                }

                if ($_SESSION['settings']['area'] == 1) {
                    $sessionid = $row["sessionid"];
                    $versionid = getSessionVersionId($sessionid);
                    $areaArray = getSessionAreas($versionid);
                    echo "                <td align=\"left\" valign='top'>\n";
                    echo "                    <h4>Area</h4>\n";
                    echo "                    <div style=\"background-color:#efefef;\">\n";
                    foreach ($areaArray as $area)
                    {
                        echo "                        $area<br>\n";
                    }
                    echo "                    </div>\n";
                    echo "                </td>\n";
                }

                if ($_SESSION['settings']['testenvironment'] == 1) {
                    $value = $row["testenvironment"];
                    if ($row["testenvironment"] == "") {
                        $value = "&nbsp;";
                    }
                    echo "                <td align=\"left\" valign='top'>\n";
                    echo "                    <h4>Testenvironment</h4>\n";
                    echo "                    <div style=\"background-color:#efefef;\">\n";
                    echo "                        $value\n";
                    echo "                    </div>\n";
                    echo "                </td>\n";
                }

                echo "                <td align=\"left\" valign='top'>\n";
                echo "                      <h4>Status</h4>\n";
                echo "                      <div style=\"background-color:#efefef;\">\n";
                echoExecutedStatus($rowSessionStatus);
                echo "                      </div>\n";
                echo "                </td>\n";
                echo "                <td align=\"left\" valign='top'>\n";
                echo "                      <h4>Debriefed</h4>\n";
                echo "                      <div style=\"background-color:#efefef;\">\n";
                echoDebriefedStatus($rowSessionStatus);
                echo "                      </div>\n";
                echo "                </td>\n";
                echo "                <td align=\"left\" valign='top'>\n";
                echo "                    <h4>Updated</h4>\n";
                echo "                    <div style=\"background-color:#efefef;\">\n";
                echo "                        " . $row["updated"] . "\n";
                echo "                    </div>\n";
                echo "                </td>\n";
                echo "            </tr>\n";
                echo "        </table>";
                echo " </td>\n";
                echo "    </tr>\n";

                echo "    <tr>\n";
                echo "        <td></td>\n";
                echo "        <td>\n";
                echo "            <h4>Software under test</h4>\n";
                echo "        </td>\n";
                echo "    </tr>\n";
                echo "    <tr>\n";
                echo "        <td></td>\n";
                echo "        <td>\n";
                echo str_replace("\n", "<br>", $row["software"]);
                echo "        </td>\n";
                echo "    </tr>\n";

                echo "    <tr>\n";
                echo "        <td></td>\n";
                echo "        <td>\n";
                echo "            " . echoRequirementsView($row["versionid"]) . "\n";
                echo "        </td>\n";
                echo "    </tr>\n";

                $con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("cannot connect");
                mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

                echo "    <tr>\n";
                echo "        <td></td>\n";
                echo "        <td>\n";
                echo "            " . echoSessionsLinkToView($row["versionid"]) . "\n";
                echo "        </td>\n";
                echo "    </tr>\n";
                echo "    <tr>\n";
                echo "        <td></td>\n";
                echo "        <td>\n";
                echo "            " . echoSessionsLinkFromoView($row["versionid"]) . "\n";
                echo "        </td>\n";
                echo "    </tr>\n";

                echo "    <tr>\n";
                echo "        <td></td>\n";
                echo "        <td>\n";
                echo "            <img src=\"pictures/line2.png\" alt=\"line\">\n";
                echo "        </td>\n";
                echo "    </tr>\n";
                echo "    <tr>\n";
                echo "        <td align=\"left\" valign=\"top\">\n";
                echo "            \n";
                echo "        </td>\n";
                echo "        <td align=\"left\">\n";
                echo "        <h2>Charter</h2>\n";
                echo "            <div style=\"background-color:#efefef;width: 1024px; height: 100%; background-color: rgb(239, 239, 239);\">\n";
                echo "                " . $row["charter"] . "\n";
                echo "            </div>\n";
                echo "        </td>\n";
                echo "    </tr>\n";
                echo "    <tr>\n";
                echo "        <td></td>\n";
                echo "        <td>\n";
                echo "            <img src=\"pictures/line.png\" alt=\"line\">\n";
                echo "        </td>\n";
                echo "    </tr>\n";
                echo "    <tr>\n";
                echo "        <td align=\"left\" valign=\"top\">\n";
                echo "            \n";
                echo "        </td>\n";
                echo "        <td align=\"left\">\n";
                echo "        <h2>Session notes</h2>\n";
                echo "            <div style=\"background-color:#efefef;width: 1024px; height: 100%; background-color: rgb(239, 239, 239);\">\n";
                echo "                " . $row["notes"] . "\n";
                echo "            </div>\n";
                echo "        </td>\n";
                echo "    </tr>\n";

                echo "    <tr>\n";
                echo "        <td></td>\n";
                echo "        <td>\n";
                echo "            " . echoBugsView($row["versionid"]) . "\n";
                echo "        </td>\n";
                echo "    </tr>\n";

                echo "    <tr>\n";
                echo "        <td></td>\n";
                echo "        <td>\n";
                echo "            <img src=\"pictures/line.png\" alt=\"line\">\n";
                echo "        </td>\n";
                echo "    </tr>\n";
                $con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("cannot connect");
                mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");
                echoSessionMetrics($rowSessionMetric, $row["versionid"]);

                $sessionDebriefNotes = getSessionDebrief($row["versionid"]);
                echo "    <tr>\n";
                echo "        <td></td>\n";
                echo "        <td>\n";
                echo "            <h2>Debrief comments</h2>\n";
                echo "        </td>\n";
                echo "    </tr>\n";
                echo "    <tr>\n";
                echo "        <td></td>\n";
                echo "        <td>\n";
                echo             $sessionDebriefNotes['notes'];
                echo "        </td>\n";
                echo "    </tr>\n";
                echo "    <tr>\n";
                echo "        <td></td>\n";
                echo "        <td>\n";
                echo "            Debriefed by: ".$sessionDebriefNotes['debriefedby'];
                echo "        </td>\n";
                echo "    </tr>\n";

                echo "</table>";
            } else
            {
                echo "<p>Session does not exit..</p>";
            }

            mysql_close($con);
        }

    }
    else
    {
        echo "No session given in GET parameters..";
    }
}

function echoRequirementsView($versionid)
{
    $rowSessionreqs = getSessionRequirements($versionid);

    echo "<h4>Requirements connected to session</h4>\n";
    echo "<div id=viewreqgid style=\"background-color:#efefef;\">\n";
    foreach ($rowSessionreqs as $aRequirement)
    {
        if (is_file("include/customfunctions.php.inc")) {
            $aRequirementName = getRequirementNameFromServer($aRequirement);

        } else {
            $aRequirementName = "Link to requirement";
        }
        if ($aRequirementName != "" && $aRequirementName != "-1") {
            echo "                                   #$aRequirement:<a href=\"" . $_SESSION['settings']['url_to_rms'] . "$aRequirement\" class=\"requirementurl\" target=\"_blank\">$aRequirementName</a><br>\n";

        }
        elseif ($aRequirementName == -1)
        {
            echo "#$aRequirement:Could not connect to server to get title";
        }
        else
        {
            echo "                                   #$aRequirement:<a href=\"" . $_SESSION['settings']['url_to_rms'] . "$aRequirement\" class=\"requirementurl\" target=\"_blank\">Requirement identifier ($aRequirement) could not be found</a><br>\n";
        }

    }
    echo "</div>\n";
}


function echoBugsView($versionid)
{
    $rowSessionreqs = getSessionBugs($versionid);

    echo "<h4>Defects connected to session</h4>\n";
    echo "<div id=viewbugid style=\"background-color:#efefef;\">\n";

    foreach ($rowSessionreqs as $aBug)
    {
        if (is_file("include/customfunctions.php.inc")) {
            $aBugName = getBugNameFromServer($aBug);
        } else {
            $aBugName = $aBug;
        }
        if ($aBugName != "" && $aBugName != "-1") {
            echo "                                   #$aBug:<a href=\"" . $_SESSION['settings']['url_to_dms'] . "$aBug\" class=\"requirementurl\" target=\"_blank\">$aBugName</a><br>\n";

        }
        elseif ($aBugName == -1)
        {
            echo "#$aBug:Could not connect to server to get title";
        }
        else
        {
            echo "                                   #$aBug:<a href=\"" . $_SESSION['settings']['url_to_dms'] . "$aBug\" class=\"requirementurl\" target=\"_blank\">Defect identifier ($aBug) could not be found</a><br>\n";
        }
    }
    echo "</div>\n";
}

function echoSessionsLinkToView($versionid)
{
    $rowSessionLinkedTo = getConnectedSessions($versionid);

    echo "<h4>Connected to sessions</h4>\n";
    echo "<div id=\"sessionslinkedto\" style=\"background-color:#efefef;\">\n";


    foreach ($rowSessionLinkedTo as $versionid)
    {
        $aSessionToLink = GetSessionIdFromVersionIdNoNewDbConnection($versionid);
        $aSessionTitle = GetSessionTitleFromSessionId($aSessionToLink);

        echo "                                   Linked to: #$aSessionToLink:<a href=\"session.php?sessionid=$aSessionToLink&command=view\" class=\"requirementurl\" target=\"_blank\">$aSessionTitle</a><br>\n";
    }
    //	mysql_close($conSql1);
    echo "</div>\n";
}

function echoSessionsLinkFromoView($versionid)
{
    $rowSessionLinkedFrom = getSessionsLinkedFrom($versionid);

    echo "<h4>Connected from sessions</h4>\n";
    echo "<div id=\"sessionslinkedfrom\" style=\"background-color:#efefef;\">\n";

    if ($rowSessionLinkedFrom != "") {

        foreach ($rowSessionLinkedFrom as $versionid)
        {
            $aSessionFromLink = GetSessionIdFromVersionIdNoNewDbConnection($versionid);
            $aSessionTitle = GetSessionTitleFromSessionId($aSessionFromLink);

            echo "                                   Linked from: #$aSessionFromLink:<a href=\"session.php?sessionid=$aSessionFromLink&command=view\" class=\"requirementurl\" target=\"_blank\">$aSessionTitle</a><br>\n";
        }
    }
    //  mysql_close($conSql1);
    echo "</div>\n";
}


?>