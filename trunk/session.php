<?php

session_start();
if(!session_is_registered(myusername)){
	header("location:index.php");
}
include("include/header.php.inc");
include_once('config/db.php.inc');
include_once 'include/commonFunctions.php.inc';



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
		echo "            <div style=\"background-color:#efefef;\">\n";
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
		echo "            <img src=\"pictures/line2.png\" alt=\"line\">\n";
		echo "        </td>\n";
		echo "    </tr>\n";
		echo "    <tr>\n";
		echo "        <td align=\"left\" valign=\"top\">\n";
		echo "            \n";
		echo "        </td>\n";
		echo "        <td align=\"left\">\n";
		echo "        <h2>Charter</h2>\n";
		echo "            <div style=\"background-color:#efefef;\">\n";
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
		echo "            <div style=\"background-color:#efefef;\">\n";
		echo "                ".$row["notes"]."\n";
		echo "            </div>\n";
		echo "        </td>\n";
		echo "    </tr>\n";
		echo "    <tr>\n";
		echo "        <td></td>\n";
		echo "        <td>\n";
		echo "            <img src=\"pictures/line.png\" alt=\"line\">\n";
		echo "        </td>\n";
		echo "    </tr>\n";
		echoSessionMetrics($rowSessionMetric,$row["versionid"]);


		echo "</table>";
	}

	mysql_close($con);
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
	}


	mysql_close($con);

	echo "<p/><b>Session saved</b><br/>(sessionid = $sessionid, versionid = $versionid)";

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
	echo "<table width=\"1024\" border=\"1\">\n";
	echo "      <tr>\n";
	echo "            <td>\n";
	echo "                  <table width=\"1024\" border=\"1\">\n";
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
	echo "                              <td>Defects: </td>\n";
	echo "                              <td><input id=\"bug\" type=\"text\" size=\"50\" value=\"\"><p id=\"add_bug\">add</p>\n";
    echo "                              <div style= \"visibility:hidden\"><textarea id=\"buglist_hidden\" name=\"buglist_hidden\"></textarea></div>\n";
    echo "                              </td>\n";
	echo "                        </tr>\n";

	echo "                        <tr>\n";
	echo "                              <td></td>\n";
	echo "                              <td><div id=\"buglist_visible\"></div></td>\n";
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
			echo "                                      <option selected=\"selected\">$index</option>";
		}
		else
		{
			echo "                                      <option>$index</option>";
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
		echo "                                      <option>$index</option>";
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
