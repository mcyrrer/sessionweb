<?php

session_start();
if(!session_is_registered(myusername)){
	header("location:index.php");
}
include("include/header.php.inc");
include_once('config/db.php.inc');
include_once 'include/commonFunctions.php.inc';
if (is_file("include/customfunctions.php.inc")) {
	include "include/customfunctions.php.inc";
}


if(strcmp($_REQUEST["command"],"new")==0)
{
	echoSessionForm();
}
elseif(strcmp($_REQUEST["command"],"view")==0)
{
	echoViewSession();
}
elseif(strcmp($_REQUEST["command"],"edit")==0)
{
	echoSessionForm();
}
elseif(strcmp($_REQUEST["command"],"debrief")==0)
{
	echoViewSession();
	echoDebriefSession();
}
elseif(strcmp($_REQUEST["command"],"debriefed")==0)
{
	saveDebriefedSession();
}

elseif(strcmp($_REQUEST["command"],"save")==0)
{
	//RapidReporter importer
	if(strstr( substr($_REQUEST["notes"],0,26)     ,'Time,Reporter,Type,Content')!=false)
	{
		$_REQUEST["notes"] = parseRapidReporterNotes($_REQUEST["notes"]);
		echo "RapidReporter CVS notes parsed to HTML<br/>\n";
	}

	//BB test assistant importer
	elseif(strstr(    substr($_REQUEST["notes"],0,43)   ,"xml version"   )!=false)
	{
		$_REQUEST["notes"] = parseBBTestAssistantNotes($_REQUEST["notes"]);
		echo "BB Test Assistant XML notes parsed to HTML<br/>\n";
	}

	saveSession();
}

include("include/footer.php.inc");

function echoRequirementsView($versionid)
{
	$rowSessionreqs = getSessionRequirements($versionid);

	echo "<h4>Requirements connected to session</h4>\n";
	echo "<div id=viewreqgid style=\"background-color:#efefef;\">\n";
	foreach($rowSessionreqs as $aRequirement)
	{
		if (is_file("include/customfunctions.php.inc")) {
			$aRequirementName = getRequirementNameFromServer($aRequirement);

		} else {
			$aRequirementName = $aRequirement;
		}
		if($aRequirementName!="" && $aRequirementName!="-1")
		{
			echo "                                   #$aRequirement:<a href=\"".$_SESSION['settings']['url_to_rms']."$aRequirement\" class=\"requirementurl\" target=\"_blank\">$aRequirementName</a><br>\n";

		}
		elseif($aRequirementName==-1)
		{
			echo "#$aRequirement:Could not connect to server to get title";
		}
		else
		{
			echo "                                   #$aRequirement:<a href=\"".$_SESSION['settings']['url_to_rms']."$aRequirement\" class=\"requirementurl\" target=\"_blank\">Requirement identifier ($aRequirement) could not be found</a><br>\n";
		}

	}
	echo "</div>\n";
}

function echoBugsView($versionid)
{
	$rowSessionreqs = getSessionBugs($versionid);

	echo "<h4>Defects connected to session</h4>\n";
	echo "<div id=viewbugid style=\"background-color:#efefef;\">\n";

	foreach($rowSessionreqs as $aBug)
	{
		if (is_file("include/customfunctions.php.inc")) {
			$aBugName = getBugNameFromServer($aBug);
		} else {
			$aBugName = $aBug;
		}
		if($aBugName!="" && $aBugName!="-1")
		{
			echo "                                   #$aBug:<a href=\"".$_SESSION['settings']['url_to_dms']."$aBug\" class=\"requirementurl\" target=\"_blank\">$aBugName</a><br>\n";

		}
		elseif($aBugName==-1)
		{
			echo "#$aBug:Could not connect to server to get title";
		}
		else
		{
			echo "                                   #$aBug:<a href=\"".$_SESSION['settings']['url_to_dms']."$aBug\" class=\"requirementurl\" target=\"_blank\">Defect identifier ($aBug) could not be found</a><br>\n";
		}
	}
	echo "</div>\n";
}

function echoBugsEdit($versionid)
{
	$myBugsArray = "";
	$rowSessionBugs = "";

	if($versionid!="")
	{

		$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
		mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

		$rowSessionBugs = getSessionBugs($versionid);

		mysql_close($con);

		if($rowSessionBugs!="")
		{
			$myBugsArray = implode("\",\"", $rowSessionBugs);
		}
		echo "            <script>\n";
		if($myBugsArray!="")
		{
			echo "              var myBugs = new Array(\"$myBugsArray\");\n";
		}
		else
		{
			echo "              var myBugs = new Array();\n";
		}
		echo "            </script>\n";


		foreach($rowSessionBugs as $row)
		{
			$bug = $row;
			echo "      <div id=\"bugdiv_$bug\">\n";
			echo "            <table width=\"*\" border=\"0\">\n";
			echo "                        <tr>\n";
			echo "                              <td>\n";
			echo "                                   <a href=\"".$_SESSION['settings']['url_to_rms']."$bug\" class=\"bugurl\" target=\"_blank\">$bug</a>\n";
			echo "                              </td>\n";
			echo "                              <td>\n";
			echo "                                    <div id=\"bug_$bug\">\n";
			echo "                                          <img src=\"pictures/removeicon.png\" alt=\"[remove]\"></div>\n";
			echo "                                    </td>\n";
			echo "                              </tr>\n";
			echo "                  </table>\n";
			echo "      </div>\n";
			echo "          <script>\n";
			echo "                      var bugValue = \"".$bug."\"\n";
			echo "                      $(\"#bug_$bug\").click(function(){\n";
			echo "                          var thisIe = this.id;\n";
			echo "                          var bugUrlId = \"bugdiv_\" +\n";
			echo "                          bugValue;\n";
			echo "                          if (this.id != bugUrlId) {\n";
			echo "                              var answer = confirm(\"Remove bug $bug?\")\n";
			echo "                              if (answer) {\n";
			echo "                                  $(\"#bugdiv_$bug\").remove();\n";
			echo "                                  bugPos = jQuery.inArray(\"$bug\", myBugs);\n";
			echo "                                  if (bugPos != -1) {\n";
			echo "                                      var removedelements = myBugs.splice(bugPos, 1);\n";
			echo "                                      $('#buglist_hidden').text(myBugs.toString());\n";
			echo "                                  }\n";
			echo "                              }\n";
			echo "                          }\n";
			echo "                      });\n";
			echo "          </script>\n";
		}

	}
	else
	{
		echo "            <script>\n";
		echo "              var myBugs = new Array();\n";
		echo "            </script>\n";
	}
}

function echoRequirementsEdit($versionid)
{
	$myRequirementsArray = "";
	$rowSessionRequirements = "";
	if($versionid!="")
	{
		$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
		mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

		$rowSessionRequirements = getSessionRequirements($versionid);

		mysql_close($con);

		if($rowSessionRequirements!="")
		{
			$myRequirementsArray = implode("\",\"", $rowSessionRequirements);
		}
		echo "            <script>\n";
		if($myRequirementsArray!="")
		{
			echo "              var myRequirements = new Array(\"$myRequirementsArray\");\n";
		}
		else
		{
			echo "              var myRequirements = new Array();\n";
		}
		echo "            </script>\n";


		foreach($rowSessionRequirements as $row)
		{
			$requirement = $row;
			echo "      <div id=\"requirementdiv_$requirement\">\n";
			echo "            <table width=\"*\" border=\"0\">\n";
			echo "                        <tr>\n";
			echo "                              <td>\n";
			echo "                                   <a href=\"".$_SESSION['settings']['url_to_rms']."$requirement\" class=\"requirementurl\" target=\"_blank\">$requirement</a>\n";
			echo "                              </td>\n";
			echo "                              <td>\n";
			echo "                                    <div id=\"requirement_$requirement\">\n";
			echo "                                          <img src=\"pictures/removeicon.png\" alt=\"[remove]\"></div>\n";
			echo "                                    </td>\n";
			echo "                              </tr>\n";
			echo "                  </table>\n";
			echo "      </div>\n";
			echo "			<script>\n";
			echo "                      var requirementValue = \"".$requirement."\"\n";
			echo "                      $(\"#requirement_$requirement\").click(function(){\n";
			echo "                          var thisIe = this.id;\n";
			echo "                          var requirementUrlId = \"requirementdiv_\" +\n";
			echo "                          requirementValue;\n";
			echo "                          if (this.id != requirementUrlId) {\n";
			echo "                              var answer = confirm(\"Remove requirement $requirement?\")\n";
			echo "                              if (answer) {\n";
			echo "                                  $(\"#requirementdiv_$requirement\").remove();\n";
			echo "                                  requirementPos = jQuery.inArray(\"$requirement\", myRequirements);\n";
			echo "                                  if (requirementPos != -1) {\n";
			echo "                                      var removedelements = myRequirements.splice(requirementPos, 1);\n";
			echo "                                      $('#requirementlist_hidden').text(myRequirements.toString());\n";
			echo "                                  }\n";
			echo "                              }\n";
			echo "                          }\n";
			echo "                      });\n";
			echo "          </script>\n";
		}

	}
	else
	{
		echo "            <script>\n";
		echo "              var myRequirements = new Array();\n";
		echo "            </script>\n";
	}
}

function echoExecutedStatus($rowSessionStatus)
{
	if($rowSessionStatus["executed"]==1)
	{
		echo "                          Executed\n";
	}
	else
	{
		echo "                          Not Executed\n";
	}
}

function echoDebriefedStatus($rowSessionStatus)
{
	if($rowSessionStatus["debriefed"]==1)
	{
		echo "                          Debriefed\n";
	}
	else
	{
		echo "                          Not debriefed\n";
	}
}

function echoViewSession()
{
	$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
	mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

	$sqlSelectSession = "";
	$sqlSelectSession .= "SELECT * ";
	$sqlSelectSession .= "FROM   mission ";
	$sqlSelectSession .= "WHERE  sessionid = ".$_GET["sessionid"] ;

	$resultSession = mysql_query($sqlSelectSession);

	if(!$resultSession)
	{
		echo "echoViewSession: ".mysql_error()."<br/>";
	}
	else
	{
		$row = mysql_fetch_array($resultSession);

		$rowSessionMetric = getSessionMetrics($row["versionid"]);

		$rowSessionStatus = getSessionStatus($row["versionid"]);

		echo "<h1>View Session</h1>\n";

		echo "<table style=\"text-align: left;\" border=\"0\" cellpadding=\"0\" cellspacing=\"5\">";
		echo "    <tr>\n";
		echo "        <td></td>\n";
		echo "        <td>\n";
		echo "            <h2>Session title</h2>\n";
		echo "            <div style=\"background-color:#efefef;width: 1024px; height: 100%; background-color: rgb(239, 239, 239);\">\n";
		echo "                ".$row["title"]."\n";
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
		echo "                <td align=\"left\">\n";
		echo "                    <h4>Sessionid</h4>\n";
		echo "                    <div style=\"background-color:#efefef;\">\n";
		echo "                        ".$row["sessionid"]."\n";
		echo "                    </div>\n";
		echo "                </td>\n";
		echo "                <td align=\"left\">\n";
		echo "                    <h4>Username</h4>\n";
		echo "                    <div style=\"background-color:#efefef;\">\n";
		echo "                        ".$row["username"]."\n";
		echo "                    </div>\n";
		echo "                </td>\n";
		if($_SESSION['settings']['team']==1 )
		{
			$value=$row["teamname"];
			if($row["teamname"]=="")
			{
				$value = "&nbsp;";
			}
			echo "                <td align=\"left\">\n";
			echo "                    <h4>Teamname</h4>\n";
			echo "                    <div style=\"background-color:#efefef;\">\n";
			echo "                        $value\n";
			echo "                    </div>\n";
			echo "                </td>\n";
		}
		if($_SESSION['settings']['sprint']==1 )
		{
			$value=$row["sprintname"];
			if($row["sprintname"]=="")
			{
				$value = "&nbsp;";
			}
			echo "                <td align=\"left\">\n";
			echo "                    <h4>Sprintname</h4>\n";
			echo "                    <div style=\"background-color:#efefef;\">\n";
			echo "                        $value\n";
			echo "                    </div>\n";
			echo "                </td>\n";
		}
		if($_SESSION['settings']['teamsprint']==1 )
		{
			$value=$row["teamsprintname"];
			if($row["teamsprintname"]=="")
			{
				$value = "&nbsp;";
			}
			echo "                <td align=\"left\">\n";
			echo "                    <h4>Team sprintname</h4>\n";
			echo "                    <div style=\"background-color:#efefef;\">\n";
			echo "                        $value\n";
			echo "                    </div>\n";
			echo "                </td>\n";
		}

		echo "                <td align=\"left\">\n";
		echo "                      <h4>Status</h4>\n";
		echo "                      <div style=\"background-color:#efefef;\">\n";
		echoExecutedStatus($rowSessionStatus);
		echo "                      </div>\n";
		echo "                </td>\n";
		echo "                <td align=\"left\">\n";
		echo "                      <h4>Debriefed</h4>\n";
		echo "                      <div style=\"background-color:#efefef;\">\n";
		echoDebriefedStatus($rowSessionStatus);
		echo "                      </div>\n";
		echo "                </td>\n";
		echo "                <td align=\"left\">\n";
		echo "                    <h4>Updated</h4>\n";
		echo "                    <div style=\"background-color:#efefef;\">\n";
		echo "                        ".$row["updated"]."\n";
		echo "                    </div>\n";
		echo "                </td>\n";
		echo "            <tr>\n";
		echo "        </table>";
		echo "    </tr>\n";

		echo "    <tr>\n";
		echo "        <td></td>\n";
		echo "        <td>\n";
		echo "            ".echoRequirementsView($row["versionid"])."\n";
		echo "        </td>\n";
		echo "    </tr>\n";
		$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
		mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");
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
		echo "                ".$row["charter"]."\n";
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
		echo "                ".$row["notes"]."\n";
		echo "            </div>\n";
		echo "        </td>\n";
		echo "    </tr>\n";

		echo "    <tr>\n";
		echo "        <td></td>\n";
		echo "        <td>\n";
		echo "            ".echoBugsView($row["versionid"])."\n";
		echo "        </td>\n";
		echo "    </tr>\n";

		echo "    <tr>\n";
		echo "        <td></td>\n";
		echo "        <td>\n";
		echo "            <img src=\"pictures/line.png\" alt=\"line\">\n";
		echo "        </td>\n";
		echo "    </tr>\n";
		$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
		mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");
		echoSessionMetrics($rowSessionMetric,$row["versionid"]);


		echo "</table>";
	}

	mysql_close($con);
}


function echoDebriefSession()
{
	if(strcmp($_SESSION['superuser'],"1")==0 || strcmp($_SESSION['useradmin'],"1")==0)
	{

		echo "<img src=\"pictures/line.png\" alt=\"line\">\n";
		echo "<form id=\"sessionform\" name=\"sessionform\" action=\"session.php?command=debriefed\" method=\"POST\" accept-charset=\"utf-8\">\n";
		echo "<h4>Debrief notes</h4>\n";
		echo "<textarea id=\"debriefnotes\" name=\"debriefnotes\" rows=\"20\" cols=\"50\" style=\"width:1024px;height:200px;\"></textarea>\n";
		echo "<div>Debriefed: <input type=\"checkbox\" name=\"debriefedcheckbox\" checked=\"checked\" value=\"yes\"></div>\n";
		if(strcmp($_SESSION['useradmin'],"1")==0)
		{
			echo "<div>Debriefed by manager: <input type=\"checkbox\" name=\"debriefedbymanagercheckbox\" checked=\"checked\" value=\"yes\"></div>\n";
		}
		echo "<input type=\"hidden\" name=\"sessionid\" value=\"".$_GET["sessionid"]."\">\n";
		echo "<p><input type=\"submit\" value=\"Continue\" /></p>\n";
		echo "</form>\n";
	}
	else
	{
		echo "You do not have enough permisions to debrief sessions.";
	}
}

function echoSessionMetrics($rowSessionMetric, $versionid)
{
	$settings = $_SESSION['settings'];

	$setup_percent = $rowSessionMetric["setup_percent"];
	$test_percent = $rowSessionMetric["test_percent"];
	$bug_percent = $rowSessionMetric["bug_percent"];
	$opportunity_percent = $rowSessionMetric["opportunity_percent"];
	$duration_time = $rowSessionMetric["duration_time"];

	$normalized_session_time = $settings['normalized_session_time'];
	$nbrOfSession = round($duration_time/$normalized_session_time,1);

	echo "    <tr>\n";
	echo "        <td></td>\n";
	echo "        <td>\n";
	echo "            <h2>Session metrics</h2>\n";
	echo "        <table style=\"text-align: left;\" border=\"0\" cellpadding=\"0\" cellspacing=\"4\">";
	echo "            <tr>\n";
	echo "                <td align=\"left\" width=\"60\">\n";
	echo "                    <h4>Setup</h4>\n";
	echo "                    <div style=\"background-color:#efefef;\">\n";
	echo "                        $setup_percent %\n";
	echo "                    </div>\n";
	echo "                </td>\n";
	echo "                <td align=\"left\" width=\"60\">\n";
	echo "                    <h4>Test</h4>\n";
	echo "                    <div style=\"background-color:#efefef;\">\n";
	echo "                        $test_percent %\n";
	echo "                    </div>\n";
	echo "                </td>\n";
	echo "                <td align=\"left\" width=\"60\">\n";
	echo "                    <h4>Bug</h4>\n";
	echo "                    <div style=\"background-color:#efefef;\">\n";
	echo "                        $bug_percent %\n";
	echo "                    </div>\n";
	echo "                </td>\n";
	echo "                <td align=\"left\">\n";
	echo "                    <h4>Oppertunity</h4>\n";
	echo "                    <div style=\"background-color:#efefef;\">\n";
	echo "                        $opportunity_percent %\n";
	echo "                    </div>\n";
	echo "                </td>\n";
	echo "                <td align=\"left\">\n";
	echo "                      <h4>Sessions duration</h4>\n";
	echo "                      <div style=\"background-color:#efefef;\">\n";
	echo "                          $duration_time (min)\n";
	echo "                      </div>\n";
	echo "                </td>\n";
	echo "                <td align=\"left\">\n";
	echo "                      <h4>Normalized Sessions count</h4>\n";
	echo "                      <div style=\"background-color:#efefef;\">\n";
	echo "                          $nbrOfSession\n";
	echo "                      </div>\n";
	echo "                </td>\n";
	echo "            <tr>\n";
	echo "        </table>";
	echo "        </td>\n";
	echo "    </tr>\n";
	echo "    <tr>\n";
	echo "        <td></td>\n";
	echo "        <td>\n";
	echo "            <img alt=\"pie chart\" src=\"".getSessionMetricsPieChartUrl($versionid)."\" /\">\n";
	echo "        </td>\n";
	echo "    </tr>\n";
}

function getSessionMetricsPieChartUrl($versionid)
{

	$sqlSelect = "";
	$sqlSelect .= "SELECT * ";
	$sqlSelect .= "FROM   mission_sessionmetrics ";
	$sqlSelect .= "WHERE  versionid = $versionid";

	$result = mysql_query($sqlSelect);

	if(!$result)
	{
		echo "getSessionMetrics: ".mysql_error()."<br/>";
	}
	else
	{
		$row = mysql_fetch_array($result);

		$setup_percent = $row["setup_percent"];
		$test_percent = $row["test_percent"];
		$bug_percent = $row["bug_percent"];
		$opportunity_percent = $row["opportunity_percent"];

		return createPercentPieChart($setup_percent,$test_percent,$bug_percent,$opportunity_percent);
	}
}

function saveSession_CreateNewSessionId()
{
	$sqlInsert = "";
	$sqlInsert .= "INSERT INTO sessionid ";
	$sqlInsert .= "            (`createdby`) ";
	$sqlInsert .= "VALUES      ('".$_SESSION['username']."') " ;

	$result = mysql_query($sqlInsert);

	if(!$result)
	{
		echo "saveSession_CreateNewSessionId: ".mysql_error()."<br/>";
	}
}

function saveSession_GetSessionIdForNewSession()
{
	$sqlSelect = "";
	$sqlSelect .= "SELECT * ";
	$sqlSelect .= "FROM   sessionid ";
	$sqlSelect .= "WHERE  createdby = '".$_SESSION['username']."' ";
	$sqlSelect .= "ORDER  BY sessionid DESC ";
	$sqlSelect .= "LIMIT  1" ;

	$result = mysql_query($sqlSelect);

	if($result)
	{
		$row = mysql_fetch_array($result);
		$sessionid = $row["sessionid"];
	}
	else
	{
		echo "saveSession_GetSessionIdForNewSession: ".mysql_error()."<br/>";
	}

	return $sessionid;
}


function saveSession_InsertSessionDataToDb($sessionid)
{


	if($_REQUEST["title"]=="")
	{
		$_REQUEST["title"]="Unnamed Session";
		echo "<b>Warning:</b> Session has no title, it will be named \"Unnamed Session\"<br/>\n";
	}
	$sqlInsert = "";
	$sqlInsert .= "INSERT INTO mission ";
	$sqlInsert .= "            (`sessionid`, ";
	$sqlInsert .= "             `title`, ";
	$sqlInsert .= "             `charter`, ";
	$sqlInsert .= "             `notes`, ";
	$sqlInsert .= "             `username`, ";
	$sqlInsert .= "             `sprintname`, ";
	$sqlInsert .= "             `teamsprintname`, ";
	$sqlInsert .= "             `teamname`) ";
	$sqlInsert .= "VALUES      ('$sessionid', ";
	$sqlInsert .= "             '".mysql_real_escape_string($_REQUEST["title"])."', ";
	$sqlInsert .= "             '".mysql_real_escape_string($_REQUEST["charter"])."', ";
	$sqlInsert .= "             '".mysql_real_escape_string($_REQUEST["notes"])."', ";
	$sqlInsert .= "             '".$_SESSION['username']."', ";
	if($_REQUEST['sprint']=="")
	{
		$sqlInsert .= "             null, ";
	}
	else
	{
		$sqlInsert .= "             '".mysql_real_escape_string($_REQUEST['sprint'])."', ";
	}
	if($_REQUEST['teamsprint']=="")
	{
		$sqlInsert .= "             null, ";
	}
	else
	{

		$sqlInsert .= "             '".mysql_real_escape_string($_REQUEST['teamsprint'])."', ";
	}
	if($_REQUEST['team']=="")
	{
		$sqlInsert .= "             null ";
	}
	else
	{
		$sqlInsert .= "             '".mysql_real_escape_string($_REQUEST['team'])."'";
	}
	$sqlInsert .= ") " ;

	$result = mysql_query($sqlInsert);

	if(!$result)
	{
		echo "saveSession_InsertSessionDataToDb: ".mysql_error()."<br/>";
	}
}

function saveSession_UpdateSessionDataToDb($sessionid)
{

	if($_REQUEST["title"]=="")
	{
		$_REQUEST["title"]="Unnamed Session";
		echo "<b>Warning:</b> Session has no title, it will be named \"Unnamed Session\"<br/>\n";
	}

	$sqlUpdate = "";
	$sqlUpdate .= "UPDATE mission ";
	$sqlUpdate .= "SET    `title` = '".mysql_real_escape_string($_REQUEST["title"])."', ";
	$sqlUpdate .= "       `charter` = '".mysql_real_escape_string($_REQUEST["charter"])."', ";
	$sqlUpdate .= "       `notes` = '".mysql_real_escape_string($_REQUEST["notes"])."', ";
	$sqlUpdate .= "       `username` = '".$_SESSION['username']."', ";
	if($_REQUEST['sprint']=="")
	{
		$sqlUpdate .= "       `sprintname` = null, ";
	}
	else
	{
		$sqlUpdate .= "       `sprintname` = '".mysql_real_escape_string($_REQUEST['sprint'])."', ";
	}

	if($_REQUEST['teamsprint']=="")
	{
		$sqlUpdate .= "       `teamsprintname` = null, ";
	}
	else
	{
		$sqlUpdate .= "       `teamsprintname` = '".mysql_real_escape_string($_REQUEST['teamsprint'])."', ";
	}

	if($_REQUEST['team']=="")
	{
		$sqlUpdate .= "       `teamname` = null ";
	}
	else
	{
		$sqlUpdate .= "       `teamname` = '".mysql_real_escape_string($_REQUEST['team'])."' ";
	}
	$sqlUpdate .= "WHERE sessionid='$sessionid'" ;


	$result = mysql_query($sqlUpdate);

	if(!$result)
	{
		echo "saveSession_UpdateSessionDataToDb: ".mysql_error()."<br/>";
	}
}

function saveSession_GetVersionIdForNewSession()
{
	$sqlSelect = "";
	$sqlSelect .= "SELECT * ";
	$sqlSelect .= "FROM   mission ";
	$sqlSelect .= "WHERE  username = '".$_SESSION['username']."' ";
	$sqlSelect .= "ORDER  BY versionid DESC ";
	$sqlSelect .= "LIMIT  1" ;

	$result = mysql_query($sqlSelect);

	if(!$result)
	{
		echo "saveSession_GetVersionIdForNewSession: ".mysql_error()."<br/>";
	}
	else
	{
		$row = mysql_fetch_array($result);
		$versionid = $row["versionid"];
	}
	return $versionid;
}

function saveSession_InsertSessionStatusToDb($versionid)
{
	$executed = false;

	if($_REQUEST["executed"]!="")
	{
		$executed = true;
	}

	$sqlInsert = "";
	$sqlInsert .= "INSERT INTO mission_status ";
	$sqlInsert .= "            (`versionid`, ";
	$sqlInsert .= "             `executed`, ";
	$sqlInsert .= "             `debriefed`, ";
	$sqlInsert .= "             `masterdibriefed`, ";
	$sqlInsert .= "             `executed_timestamp` ) ";
	$sqlInsert .= "VALUES      ('$versionid', ";
	$sqlInsert .= "             '$executed', ";
	$sqlInsert .= "             'false', ";
	$sqlInsert .= "             'false', " ;
	$sqlInsert .= "             '".date("Y-d-j H:i:s")."')" ;

	$result = mysql_query($sqlInsert);

	if(!$result)
	{
		echo "saveSession_InsertSessionStatusToDb: ".mysql_error()."<br/>";
	}
}

function saveSession_UpdateSessionStatusToDb($versionid)
{
	$executed = false;

	if($_REQUEST["executed"]!="")
	{
		$executed = true;
	}


	$sqlUpdate = "";
	$sqlUpdate .= "UPDATE mission_status ";
	$sqlUpdate .= "SET    `executed` = '$executed', ";
	$sqlUpdate .= "       `debriefed` = 'false', ";
	$sqlUpdate .= "       `masterdibriefed` = 'false', ";
	$sqlUpdate .= "       `executed_timestamp` = '".date("Y-d-j H:i:s")."' ";
	$sqlUpdate .= "WHERE versionid='$versionid'" ;

	$result = mysql_query($sqlUpdate);

	if(!$result)
	{
		echo "saveSession_UpdateSessionStatusToDb: ".mysql_error()."<br><br>";
	}
}

function saveSession_UpdateSessionDebriefedStatusToDb($versionid, $debriefed, $masterdibriefed)
{

	$sqlUpdate = "";
	$sqlUpdate .= "UPDATE mission_status ";
	$sqlUpdate .= "SET    `debriefed` = $debriefed, ";
	$sqlUpdate .= "       `masterdibriefed` = $masterdibriefed, ";
	$sqlUpdate .= "       `debriefed_timestamp` = '".date("Y-d-j H:i:s")."' ";
	$sqlUpdate .= "WHERE versionid='$versionid'" ;

	$result = mysql_query($sqlUpdate);

	if(!$result)
	{
		echo "saveSession_UpdateSessionDebriefedStatusToDb: ".mysql_error()."<br><br>";
	}
}

function saveSession_InsertSessionDebriefedNotesToDb($versionid, $notes)
{

	$sqlInsert = "";
	$sqlInsert .= "INSERT INTO mission_debriefnotes ";
	$sqlInsert .= "            (`versionid`, ";
	$sqlInsert .= "             `notes`, ";
	$sqlInsert .= "             `debriefedby`) ";
	$sqlInsert .= "VALUES      ('$versionid', ";
	$sqlInsert .= "             '".mysql_real_escape_string($notes)."', ";
	$sqlInsert .= "             '".$_SESSION['username']."')" ;


	$result = mysql_query($sqlInsert);

	if(!$result)
	{
		echo "saveSession_InsertSessionDebriefedNotesToDb: ".mysql_error()."<br><br>";
	}
}

function saveSession_InsertSessionMetricsToDb($versionid)
{

	$sqlInsert = "";
	$sqlInsert .= "INSERT INTO mission_sessionmetrics ";
	$sqlInsert .= "            (`versionid`, ";
	$sqlInsert .= "             `setup_percent`, ";
	$sqlInsert .= "             `test_percent`, ";
	$sqlInsert .= "             `bug_percent`, ";
	$sqlInsert .= "             `opportunity_percent`, ";
	$sqlInsert .= "             `duration_time`) ";
	$sqlInsert .= "VALUES      ('$versionid', ";
	$sqlInsert .= "             '".mysql_real_escape_string($_REQUEST["setuppercent"])."', ";
	$sqlInsert .= "             '".mysql_real_escape_string($_REQUEST["testpercent"])."', ";
	$sqlInsert .= "             '".mysql_real_escape_string($_REQUEST["bugpercent"])."', ";
	$sqlInsert .= "             '".mysql_real_escape_string($_REQUEST["oppertunitypercent"])."', ";
	$sqlInsert .= "             '".mysql_real_escape_string($_REQUEST["duration"])."')" ;

	$result = mysql_query($sqlInsert);

	if(!$result)
	{
		echo "saveSession_InsertSessionMetricsToDb: ".mysql_error()."<br/>";
	}
}

function saveDebriefedSession()
{
	if(strcmp($_SESSION['superuser'],"1")==0 || strcmp($_SESSION['useradmin'],"1")==0)
	{
		$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
		mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

		$versionid = getSessionVersionId($_REQUEST["sessionid"]);

		$debriefed = "false";
		if(strcmp($_REQUEST["debriefedcheckbox"],"yes")==0)
		{
			$debriefed = "true";
		}

		$masterdibriefed = "false";
		if(strcmp($_REQUEST["debriefedbymanagercheckbox"],"yes")==0)
		{
			$masterdibriefed = "true";
		}

		if(doesSessionNotesExist($versionid))
		{
			saveSession_DeleteSessionsNotesFromDb($versionid);
		}
		else
		{
			echo "session does not have notes.<br>";
		}

		
		saveSession_UpdateSessionDebriefedStatusToDb($versionid, $debriefed, $masterdibriefed);
		
		saveSession_InsertSessionDebriefedNotesToDb($versionid, $_REQUEST["debriefnotes"]);
		
		echo "<h4>Debrief notes saved</h4>\n";
	}
	else
	{
		echo "You can not save since you do not have the persmisions to debrief\n";
	}

}

function saveSession_InsertSessionAreaToDb($versionid)
{
	if($_SESSION['settings']['area']==1 && $area!=null)
	{
		$areas = $_REQUEST["area"];

		foreach ($areas as $area) {
			if($area!="")
			{
				$sqlInsert = "";
				$sqlInsert .= "INSERT INTO mission_areas ";
				$sqlInsert .= "            (`versionid`, ";
				$sqlInsert .= "             `areaname`) ";
				$sqlInsert .= "VALUES      ('$versionid', ";
				$sqlInsert .= "             '".mysql_real_escape_string($area)."')" ;

				$result = mysql_query($sqlInsert);

				if(!$result)
				{
					echo "saveSession_InsertSessionAreaToDb: ".mysql_error()."<br/>";
				}
			}
		}
	}
}

function saveSession_InsertSessionBugsToDb($versionid)
{
	$bugs = $_REQUEST["buglist_hidden"];
	if($bugs!="")
	{
		$bugsArray = explode(",",$bugs);
		foreach ($bugsArray as $aBug) {
			if($aBug!="")
			{

				$sqlInsert = "";
				$sqlInsert .= "INSERT INTO mission_bugs ";
				$sqlInsert .= "            (`versionid`, ";
				$sqlInsert .= "             `bugid`) ";
				$sqlInsert .= "VALUES      ('$versionid', ";
				$sqlInsert .= "             '".mysql_real_escape_string($aBug)."')" ;

				$result = mysql_query($sqlInsert);

				if(!$result)
				{
					echo "saveSession_InsertSessionBugsToDb: ".mysql_error()."<br/>";
				}
			}
		}
	}
}

function saveSession_InsertSessionRequirementsToDb($versionid)
{

	$requirements = $_REQUEST["requirementlist_hidden"];
	if($requirements!="")
	{
		$requirementsArray = explode(",",$requirements);
		foreach ($requirementsArray as $aRequirement) {
			if($aRequirement!="")
			{

				$sqlInsert = "";
				$sqlInsert .= "INSERT INTO mission_requirements ";
				$sqlInsert .= "            (`versionid`, ";
				$sqlInsert .= "             `requirementsid`) ";
				$sqlInsert .= "VALUES      ('$versionid', ";
				$sqlInsert .= "             '".mysql_real_escape_string($aRequirement)."')" ;

				$result = mysql_query($sqlInsert);

				if(!$result)
				{
					echo "saveSession_InsertSessionRequirementsToDb: ".mysql_error()."<br/>";
				}
			}
		}
	}
}

function saveSession_UpdateSessionMetricsToDb($versionid)
{
	$sqlUpdate = "";
	$sqlUpdate .= "UPDATE mission_sessionmetrics ";
	$sqlUpdate .= "SET    `setup_percent` = '".mysql_real_escape_string($_REQUEST["setuppercent"])."', ";
	$sqlUpdate .= "       `test_percent` = '".mysql_real_escape_string($_REQUEST["testpercent"])."', ";
	$sqlUpdate .= "       `bug_percent` = '".mysql_real_escape_string($_REQUEST["bugpercent"])."', ";
	$sqlUpdate .= "       `opportunity_percent` = '".mysql_real_escape_string($_REQUEST["oppertunitypercent"])."', ";
	$sqlUpdate .= "       `duration_time` = '".mysql_real_escape_string($_REQUEST["duration"])."' ";
	$sqlUpdate .= "WHERE versionid='$versionid'" ;

	$result = mysql_query($sqlUpdate);

	if(!$result)
	{
		echo "saveSession_UpdateSessionMetricsToDb: ".mysql_error()."<br/>";
	}
}

function saveSession_UpdateSessionAreasToDb($versionid)
{
	$sqlDelete = "";
	$sqlDelete .= "DELETE FROM mission_areas ";
	$sqlDelete .= "WHERE  `versionid` = '$versionid'" ;

	$result = mysql_query($sqlDelete);

	if(!$result)
	{
		echo "saveSession_UpdateSessionAreasToDb: ".mysql_error()."<br/>";
	}

	saveSession_InsertSessionAreaToDb($versionid);
}

function saveSession_DeleteSessionsNotesFromDb($versionid)
{
    $sqlDelete = "";
    $sqlDelete .= "DELETE FROM mission_debriefnotes ";
    $sqlDelete .= "WHERE  `versionid` = '$versionid'" ;

    $result = mysql_query($sqlDelete);

    if(!$result)
    {
        echo "saveSession_DeleteSessionsNotesFromDb: ".mysql_error()."<br/>";
    }

    saveSession_InsertSessionAreaToDb($versionid);
}

function saveSession_UpdateSessionBugsToDb($versionid)
{
	$sqlDelete = "";
	$sqlDelete .= "DELETE FROM mission_bugs ";
	$sqlDelete .= "WHERE  `versionid` = '$versionid'" ;

	$result = mysql_query($sqlDelete);

	if(!$result)
	{
		echo "saveSession_UpdateSessionBugsToDb: ".mysql_error()."<br/>";
	}

	saveSession_InsertSessionBugsToDb($versionid);

}

function saveSession_UpdateSessionRequirementsToDb($versionid)
{
	$sqlDelete = "";
	$sqlDelete .= "DELETE FROM mission_requirements ";
	$sqlDelete .= "WHERE  `versionid` = '$versionid'" ;

	$result = mysql_query($sqlDelete);

	if(!$result)
	{
		echo "saveSession_UpdateSessionRequirementsToDb: ".mysql_error()."<br/>";
	}

	saveSession_InsertSessionRequirementsToDb($versionid);

}



function checkSessionTitleNotToLong()
{
	$_TITLELENGTH = 500;

	echo "<h1>Save session</h1>\n";
	if(strlen($_REQUEST["title"])>$_TITLELENGTH)
	{
		echo "<b>Warning:</b> Title of session is exceding the maximum number of chars ($_TITLELENGTH). Will only save the first $_TITLELENGTH chars<br/>\n";
	}
}
/**
 * Save session to database
 */
function saveSession()
{


	checkSessionTitleNotToLong();

	$sessionid = false;
	$versionid = false;

	$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
	mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

	//New session
	if($_REQUEST["sessionid"]=="")
	{

		//Will create a new session id to map to a session
		saveSession_CreateNewSessionId();

		//Get the new session id for user x
		$sessionid = saveSession_GetSessionIdForNewSession();

		//Insert sessiondata to mission table
		saveSession_InsertSessionDataToDb($sessionid);

		//Get versionId from db
		$versionid = saveSession_GetVersionIdForNewSession();

		//Create missionstatus record in Db
		saveSession_InsertSessionStatusToDb($versionid);

		//Create metrics record for session
		saveSession_InsertSessionMetricsToDb($versionid);

		//Create areas for session
		saveSession_InsertSessionAreaToDb($versionid);

		//Create bugs connected to session
		saveSession_InsertSessionBugsToDb($versionid);

		//Create requirements connected to mission
		saveSession_InsertSessionRequirementsToDb($versionid);

	}
	//Update existing session
	else
	{
		$sessionid = $_REQUEST["sessionid"];
		$versionid = $_REQUEST["versionid"];

		saveSession_UpdateSessionDataToDb($sessionid);

		saveSession_UpdateSessionStatusToDb($versionid);

		saveSession_UpdateSessionMetricsToDb($versionid);

		saveSession_UpdateSessionAreasToDb($versionid);

		saveSession_UpdateSessionBugsToDb($versionid);

		saveSession_UpdateSessionRequirementsToDb($versionid);
	}


	mysql_close($con);

	echo "<p><b>Session saved</b></p>\n";
	echo "<p><a href=\"session.php?sessionid=$sessionid&command=view\" id=\"view_session\">View session</a></p>";
	echo "<p><a href=\"session.php?sessionid=$sessionid&command=edit\" id=\"edit_session\">Edit session</a></p>";
	echo "(sessionid = $sessionid, versionid = $versionid)\n";

}




/**
 *
 * @return unknown_type
 */
function echoSessionForm()
{

	$title = "";
	$team ="";
	$charter = "";
	$notes = "";
	$sprint = "";
	$teamsprint = "";
	$area = "";

	$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
	mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

	$insertSessionData = false;

	if(strcmp($_REQUEST["command"],"edit")==0)
	{
		$rowSessionData = getSessionData($_GET["sessionid"]);
		$insertSessionData = true;
	}

	if($_GET["sessionid"]!="")
	{
		$rowSessionMetric = getSessionMetrics($rowSessionData["versionid"]);

		$rowSessionStatus = getSessionStatus($rowSessionData["versionid"]);

		$rowSessionAreas = getSessionAreas($rowSessionData["versionid"]);


	}
	mysql_close($con);

	if($insertSessionData)
	{
		$title = $rowSessionData["title"];
		$charter = $rowSessionData["charter"];
		$notes = $rowSessionData["notes"];
		$sprint = $rowSessionData["sprintname"];
		$teamsprint = $rowSessionData["teamsprintname"];
		$team = $rowSessionData["teamname"];
		$area = $rowSessionAreas;
	}

	echo "<form id=\"sessionform\" name=\"sessionform\" action=\"session.php?command=save\" method=\"POST\" accept-charset=\"utf-8\" onsubmit=\"return validate_form(this)\">\n";
	echo "<input type=\"hidden\" name=\"savesession\" value=\"true\">\n";
	echo "<input type=\"hidden\" name=\"sessionid\" value=\"".$rowSessionData["sessionid"]."\">\n";
	echo "<input type=\"hidden\" name=\"versionid\" value=\"".$rowSessionData["versionid"]."\">\n";
	echo "<input type=\"hidden\" name=\"tester\" value=\"".$_SESSION['username']."\">\n";
	echo "<table width=\"1024\" border=\"0\">\n";
	echo "      <tr>\n";
	echo "            <td>\n";
	echo "                  <table width=\"1024\" border=\"0\">\n";
	echo "                        <tr>\n";
	echo "                              <td></td>\n";
	echo "                              <td>\n";
	echo "                                   <h1>New Session</h1>\n";
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
	echo "                              <td><input id=\"input_title\" type=\"text\" size=\"133\" value=\"$title\" name=\"title\"></td>\n";
	echo "                        </tr>\n";
	if($_SESSION['settings']['team']==1)
	{
		echo "                        <tr>\n";
		echo "                              <td>Team: </td>\n";
		echo "                              <td>\n";
		echoTeamSelect($team);
		echo "                              </td>\n";
		echo "                        </tr>\n";
	}
	if($_SESSION['settings']['sprint']==1)
	{
		echo "                        <tr>\n";
		echo "                              <td valign=\"top\">Sprint: </td>\n";
		echo "                              <td>\n";
		echoSprintSelect($sprint);
		echo "                              </td>\n";
		echo "                        </tr>\n";
	}
	if($_SESSION['settings']['teamsprint']==1)
	{
		echo "                        <tr>\n";
		echo "                              <td valign=\"top\">Team sprint: </td>\n";
		echo "                              <td>\n";
		echoTeamSprintSelect($teamsprint);
		echo "                              </td>\n";
		echo "                        </tr>\n";
	}

	if($_SESSION['settings']['area']==1)
	{
		echo "                        <tr>\n";
		echo "                              <td valign=\"top\">Area: </td>\n";
		echo "                              <td>\n";
		echoAreaSelect($area);
		echo "                              </td>\n";
		echo "                        </tr>\n";
	}

	echo "                        <tr>\n";
	echo "                              <td valign=\"top\">Test requirements: </td>\n";
	echo "                              <td>\n";
	echo "                              <table width=\"*\" border=\"0\">\n";
	echo "                                  <tr>\n";
	echo "                                      <td><input id=\"requirement\" type=\"text\" size=\"50\" value=\"\">\n";
	echo "                                      </td>\n";
	echo "                                      <td><div id=\"add_requirement\">add</div>\n";
	echo "                                      </td>\n";
	echo "                                  </tr>\n";
	echo "                                  <tr>\n";
	echo "                                      <td><div id=\"helptext1\" >Only add the requirements id</div></td>\n";
	echo "                                      <td></td>\n";
	echo "                                  </tr>\n";
	echo "                              </table>\n";
	echo "                              </td>\n";
	echo "                        </tr>\n";

	echo "                        <tr>\n";
	echo "                              <td></td>\n";
	echo "                              <td>&nbsp;<div id=\"requirementlist_visible\" style=\"width: 1024px; height: 100%; background-color: rgb(239, 239, 239);\">";
	echo "                                ".echoRequirementsEdit($rowSessionData["versionid"])."</div></td>\n";
	echo "                        </tr>\n";

	echo "                        <tr>\n";
	echo "                              <td valign=\"top\">Charter: </td>\n";
	echo "                              <td>\n";
	echo "                                  <textarea id=\"textarea1\" name=\"charter\" rows=\"20\" cols=\"50\" style=\"width:1024px;height:200px;\">$charter</textarea>\n";
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
	echo "                              <td><i>It is possible to paste <a href=\"http://testing.gershon.info/reporter/\">RapidReporter</a> CVS notes or <a href=\"http://www.bbtestassistant.com\">BB TestAssistant</a> XML notes into the notes field.</i>\n";
	echo "                                  <textarea id=\"textarea2\" name=\"notes\" rows=\"20\" cols=\"50\" style=\"width:1024px;height:400px;\">$notes</textarea>\n";
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
	echo "                                      <td><div id=\"add_bug\">add</div>\n";
	echo "                                      </td>\n";
	echo "                                  </tr>\n";
	echo "                                  <tr>\n";
	echo "                                      <td><div id=\"helptext1\" >Only add the defect id</div></td>\n";
	echo "                                      <td></td>\n";
	echo "                                  </tr>\n";
	echo "                              </table>\n";
	echo "                              </td>\n";
	echo "                        </tr>\n";

	echo "                        <tr>\n";
	echo "                              <td></td>\n";
	echo "                              <td>&nbsp;<div id=\"buglist_visible\" style=\"width: 1024px; height: 100%; background-color: rgb(239, 239, 239);\">";
	echo "                             ".echoBugsEdit($rowSessionData["versionid"])."</div></td>\n";
	echo "                        </tr>\n";


	echo "                        <tr>\n";
	echo "                              <td>Metrics: </td>\n";
	echo "                              <td>\n";
	echo "                                    <table width=\"1024\" border=\"0\">\n";
	echo "                                          <tr>\n";
	echo "                                                <td>Setup(%): </td>\n";
	echo "                                                <td>\n";
	echo "                                                      <select class=\"metricoption\" name=\"setuppercent\">\n";
	echoPercentSelection($rowSessionMetric["setup_percent"]);
	echo "                                                      </select>\n";
	echo "                                                </td>\n";
	echo "                                                <td>Test(%): </td>\n";
	echo "                                                <td>\n";
	echo "                                                      <select class=\"metricoption\" name=\"testpercent\">\n";
	echoPercentSelection($rowSessionMetric["test_percent"]);
	echo "                                                      </select>\n";
	echo "                                                </td>\n";
	echo "                                                <td>Bug(%): </td>\n";
	echo "                                                <td>\n";
	echo "                                                      <select class=\"metricoption\" name=\"bugpercent\">\n";
	echoPercentSelection($rowSessionMetric["bug_percent"]);
	echo "                                                      </select>\n";
	echo "                                                </td>\n";
	echo "                                                <td>Oppertunity(%): </td>\n";
	echo "                                                <td>\n";
	echo "                                                      <select class=\"metricoption\" name=\"oppertunitypercent\">\n";
	echoPercentSelection($rowSessionMetric["opportunity_percent"]);
	echo "                                                      </select>\n";
	echo "                                                </td>\n";
	echo "                                                <td>Session duration (min): </td>\n";
	echo "                                                <td>\n";
	echo "                                                      <select name=\"duration\">\n";
	echoDurationSelection();
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
	if($rowSessionStatus['executed']==1)
	{
		echo "                                  <input type=\"checkbox\" name=\"executed\" checked=\"checked\" value=\"yes\" >\n";
	}
	else
	{
		echo "                                  <input type=\"checkbox\" name=\"executed\" value=\"yes\" >\n";
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
	echo "                              <div><textarea id=\"buglist_hidden\" name=\"buglist_hidden\" style= \"visibility:hidden\"></textarea></div>\n";
	echo "                              <div><textarea id=\"requirementlist_hidden\" name=\"requirementlist_hidden\" style= \"visibility:hidden\"></textarea></div>\n";
	echo "</form>\n";
}


/**
 * Prints percent (belongs to a HTML select item) to screen. E.g 5,10,15,20...
 *
 */
function echoPercentSelection($selected)
{
	echo "                                      <option>$selected</option>";
	for ($index  = 0; $index  <= 100; $index = $index + 5) {
		if($index==$selected)
		{
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
function echoDurationSelection()
{
	for ($index  = 15; $index  <= 480; $index = $index + 15) {
		echo "                                      <option>$index</option>\n";
	}
}

/**
 * Parse RapidReporter CVS notes to HTML
 * @param $notes RapidReporter CVS notes
 * @return parsed notes as HTML
 */

function parseRapidReporterNotes($notes)
{

	$explodedCharterNotes =  explode("<br/>",$notes);

	$charterParsed =  "<table width=\"1024\" border=\"0\">\n";
	$charterParsed .= "    <tr>\n";
	$charterParsed .= "      <td><b>Time</b></td>\n";
	$charterParsed .= "        <td><b>Type</b></td>\n";
	$charterParsed .= "        <td><b>Note</b></td>\n";

	$charterParsed .= "    </tr>\n";

	for ($index = 1; $index < count($explodedCharterNotes); $index++) {
		$charterParsed .= "   <tr>\n";
		$time = substr($explodedCharterNotes[$index],11,8);

		$commaArray = explode(",",$explodedCharterNotes[$index],4);
		$type = $commaArray[2];

		//Reverse the string to minimize the effort to strip the 2 last , chars.
		$reverseString = strrev($commaArray[3]);
		$stringArray = (explode(",",$reverseString,3));
		$string = strrev($stringArray[2]);

		$note = substr($string,1,strlen($string)-2);

		$charterParsed .= "       <td valign=\"top\">$time</td>\n";
		$charterParsed .= "       <td valign=\"top\">".htmlspecialchars($type)."</td>\n";
		$charterParsed .= "       <td valign=\"top\">".htmlspecialchars($note)."</td>\n";

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
	$notes = str_replace("<br/>","",$notes);
	$notes = str_replace("&nbsp;","",$notes);
	$charterParsed     =  "<table width=\"1024\" border=\"0\">\n";
	$charterParsed     .=  "    <tr>\n";
	$charterParsed     .=   "      <td width=\"100\"><b>Time</b></td>\n";
	$charterParsed     .= "        <td><b>Note</b></td>\n";
	$charterParsed     .= "    </tr>\n";


	$xmlDoc = new DOMDocument();
	$xmlDoc->loadXML( $notes );

	$searchNode = $xmlDoc->getElementsByTagName( "Note" );

	foreach( $searchNode as $searchNode )
	{
		$valueTimestamp   = $searchNode->getAttribute('timestamp');
		$valueNode        = $searchNode->nodeValue;

		$charterParsed    .= "   <tr>\n";
		$charterParsed    .= "       <td valign=\"top\">$valueTimestamp</td>\n";
		$charterParsed    .= "       <td valign=\"top\">".htmlspecialchars($valueNode)."</td>\n";
		$charterParsed    .= "   </tr>\n";
	}

	return $charterParsed;
}
