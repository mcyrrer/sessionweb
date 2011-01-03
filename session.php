<?php
session_start();
if(!session_is_registered(myusername)){
	header("location:index.php");
}
include("include/header.php.inc");
include_once('config/db.php.inc');
include_once 'include/commonFunctions.php.inc';



if(strstr($_REQUEST["savesession"],"true")!=false)
{

	//RapidReporter importer
	if(strstr( substr($_REQUEST["notes"],0,26)     ,'Time,Reporter,Type,Content')!=false)
	{
		$_REQUEST["notes"] = parseRapidReporterNotes($_REQUEST["notes"]);
		echo "RapidReporter CVS notes parsed to HTML<br>\n";
	}

	//BB test assistant importer
	elseif(strstr(    substr($_REQUEST["notes"],0,43)   ,"xml version"   )!=false)
	{
		$_REQUEST["notes"] = parseBBTestAssistantNotes($_REQUEST["notes"]);
		echo "BB Test Assistant XML notes parsed to HTML<br>\n";
	}

	saveSession();

}

elseif (strstr($_GET["new"],"true")!=false)
{
	echoSessionForm();
}

include("include/footer.php.inc");


function saveSession_CreateNewSessionId()
{
	$sqlInsert = "";
	$sqlInsert .= "INSERT INTO sessionid ";
	$sqlInsert .= "            (`createdby`) ";
	$sqlInsert .= "VALUES      ('".$_SESSION['username']."') " ;

	$result = mysql_query($sqlInsert);

	if(!$result)
	{
		echo "saveSession_CreateNewSessionId: ".mysql_error()."<br>";
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
		echo "saveSession_GetSessionIdForNewSession: ".mysql_error()."<br>";
	}

	return $sessionid;
}


function saveSession_InsertSessionDataToDb($sessionid)
{
	$sqlInsert = "";
	$sqlInsert .= "INSERT INTO mission ";
	$sqlInsert .= "            (`sessionid`, ";
	$sqlInsert .= "             `title`, ";
	$sqlInsert .= "             `charter`, ";
	$sqlInsert .= "             `notes`, ";
	$sqlInsert .= "             `username`, ";
	$sqlInsert .= "             `sprintname`, ";
	$sqlInsert .= "             `teamname`) ";
	$sqlInsert .= "VALUES      ('$sessionid', ";
	$sqlInsert .= "             '".mysql_real_escape_string($_REQUEST["title"])."', ";
	$sqlInsert .= "             '".mysql_real_escape_string($_REQUEST["charter"])."', ";
	$sqlInsert .= "             '".mysql_real_escape_string($_REQUEST["notes"])."', ";
	$sqlInsert .= "             '".$_SESSION['username']."', ";
	$sqlInsert .= "             '".mysql_real_escape_string($_REQUEST['sprint'])."', ";
	$sqlInsert .= "             '".mysql_real_escape_string($_REQUEST['team'])."')" ;

	$result = mysql_query($sqlInsert);

	if(!$result)
	{
		echo "saveSession_InsertSessionDataToDb: ".mysql_error()."<br>";
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
		echo "saveSession_GetVersionIdForNewSession: ".mysql_error()."<br>";
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
	if(strstr($_REQUEST["executed"],"yes")==0)
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
		echo "saveSession_InsertSessionStatusToDb: ".mysql_error()."<br>";
	}
}

function saveSession_InsertSessionMetricsToDb($versionid)
{
	$totalPercent = $_REQUEST["setuppercent"] + $_REQUEST["testpercent"] + $_REQUEST["bugpercent"] + $_REQUEST["oppertunitypercent"];
	if($totalPercent!=100)
	{
		echo "<b>Warning:</b> Percentage for Session metrics is $totalPercent% and not 100%. Session will be saved but session metrics will be missleading<br>\n";
	}
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
		echo "saveSession_InsertSessionMetricsToDb: ".mysql_error()."<br>";
	}
}

/**
 * Save session to database
 */
function saveSession()
{
	$_TITLELENGTH = 500;

	echo "<h1>Save session</h1>\n";
	if(strlen($_REQUEST["title"])>$_TITLELENGTH)
	{
		echo "<b>Warning:</b> Title of session is exceding the maximum number of chars ($_TITLELENGTH). Will only save the first $_TITLELENGTH chars<br>\n";
	}


	$sessionid = false;
	$versionid = false;

	$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
	mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");


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

	mysql_close($con);

	echo "<p/>Session saved as (sessionid = $sessionid, versionid = $versionid)";

}




/**
 *
 * @return unknown_type
 */
function echoSessionForm()
{
	echo "<form action=\"session.php\" method=\"POST\" accept-charset=\"utf-8\">\n";
	echo "<input type=\"hidden\" name=\"savesession\" value=\"true\">\n";
	echo "<table width=\"1024\" border=\"1\">\n";
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
	echo "                              <td><input type=\"text\" size=\"133\" value=\"\" name=\"title\"></td>\n";
	echo "                        </tr>\n";
	echo "                        <tr>\n";
	echo "                              <td>Tester: </td>\n";
	echo "                              <td>\n";
	echoTesterSelect();
	echo "                              </td>\n";
	echo "                        </tr>\n";
	echo "                        <tr>\n";
	echo "                              <td>Team: </td>\n";
	echo "                              <td>\n";
	echoTeamSelect();
	echo "                              </td>\n";
	echo "                        </tr>\n";
	echo "                        <tr>\n";
	echo "                              <td valign=\"top\">Sprint: </td>\n";
	echo "                              <td>\n";
	echoSprintSelect();
	echo "                              </td>\n";
	echo "                        </tr>\n";
	echo "                        <tr>\n";
	echo "                              <td valign=\"top\">Charter: </td>\n";
	echo "                              <td>\n";
	echo "                                  <textarea id=\"textarea1\" name=\"charter\"  rows=\"20\" cols=\"50\" style=\"width:1024px;height:200px;\">";
	echo "                                  </textarea>\n";
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
	echo "                                  <textarea id=\"textarea2\" name=\"notes\" rows=\"20\" cols=\"50\" style=\"width:1024px;height:200px;\">";
	echo "                                  </textarea>\n";
	echo "                              </td>\n";
	echo "                        </tr>\n";
	echo "                        <tr>\n";
	echo "                              <td></td>\n";
	echo "                              <td>\n";
	echo "                                   <p><img src=\"pictures/line2.png\" alt=\"line\"></p>\n";
	echo "                              </td>\n";
	echo "                        </tr>\n";
	echo "                        <tr>\n";
	echo "                              <td>Metrics: </td>\n";
	echo "                              <td>\n";
	echo "                                    <table width=\"1024\" border=\"0\">\n";
	echo "                                          <tr>\n";
	echo "                                                <td>Setup(%): </td>\n";
	echo "                                                <td>\n";
	echo "                                                      <select name=\"setuppercent\">\n";
	echoPercentSelection();
	echo "                                                      </select>\n";
	echo "                                                </td>\n";
	echo "                                                <td>Test(%): </td>\n";
	echo "                                                <td>\n";
	echo "                                                      <select name=\"testpercent\">\n";
	echoPercentSelection();
	echo "                                                      </select>\n";
	echo "                                                </td>\n";
	echo "                                                <td>Bug(%): </td>\n";
	echo "                                                <td>\n";
	echo "                                                      <select name=\"bugpercent\">\n";
	echoPercentSelection();
	echo "                                                      </select>\n";
	echo "                                                </td>\n";
	echo "                                                <td>Oppertunity(%): </td>\n";
	echo "                                                <td>\n";
	echo "                                                      <select name=\"oppertunitypercent\">\n";
	echoPercentSelection();
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
	echo "                                   <p><img src=\"pictures/line2.png\" alt=\"line\"></p>\n";
	echo "                              </td>\n";
	echo "                        </tr>\n";
	echo "                        <tr>\n";
	echo "                              <td>Executed:</td>\n";
	echo "                              <td>\n";
	echo "                                  <input type=\"checkbox\" name=\"executed\" value=\"yes\" checked=\"checked\">\n";
	echo "                              </td>\n";
	echo "                        </tr>\n";
	echo "                        <tr>\n";
	echo "                              <td></td>\n";
	echo "                              <td>\n";
	echo "                                  <input type=\"submit\" value=\"Save\"/>\n";
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
function echoPercentSelection()
{
	for ($index  = 0; $index  <= 100; $index = $index + 5) {
		echo "                                      <option>$index</option>";
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

	$explodedCharterNotes =  explode("<br>",$notes);

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
	$notes = str_replace("<br>","",$notes);
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