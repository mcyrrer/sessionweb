<?php

function getSessionWebSettings()
{
	$sqlSelect = "";
	$sqlSelect .= "SELECT * ";
	$sqlSelect .= "FROM   settings ";

	$result = mysql_query($sqlSelect);

	if(!$result)
	{
		echo "getSessionWebSettings: ".mysql_error()."<br/>";
	}
	else
	{
		$row = mysql_fetch_array($result);
		return $row;
	}
}


function getSessionMetrics($versionid)
{
	//	$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
	mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

	$sqlSelectSessionMetrics = "";
	$sqlSelectSessionMetrics .= "SELECT * ";
	$sqlSelectSessionMetrics .= "FROM   mission_sessionmetrics ";
	$sqlSelectSessionMetrics .= "WHERE  versionid = $versionid";
	$resultSessionMetrics = mysql_query($sqlSelectSessionMetrics);

	if(!$resultSessionMetrics)
	{
		echo "getSessionMetrics: ".mysql_error()."<br/>";
	}

	return mysql_fetch_array($resultSessionMetrics);
}

function getSessionData($sessionid)
{
	$sqlSelectSession = "";
	$sqlSelectSession .= "SELECT * ";
	$sqlSelectSession .= "FROM   mission ";
	$sqlSelectSession .= "WHERE  sessionid = $sessionid" ;

	$resultSession = mysql_query($sqlSelectSession);

	if(!$resultSession)
	{
		echo "getSessionData: ".mysql_error()."<br/>";
	}

	return mysql_fetch_array($resultSession);
}

function getSessionVersionId($sessionid)
{
	$result = getSessionData($sessionid);
	return $result['versionid'];
}

function doesSessionNotesExist($versionid)
{

	$sqlSelect = "";
	$sqlSelect .= "SELECT * ";
	$sqlSelect .= "FROM   mission_debriefnotes ";
	$sqlSelect .= "WHERE  versionid = $versionid";

	$result = mysql_query($sqlSelect);
	if(!$result)
	{
		return false;
	}

	$result =  mysql_fetch_array($result);

	if($result!=null)
	{
		return true;
	}
	else
	{
		return false;
	}
}


function getSessionStatus($versionid)
{
	$sqlSelectSessionStatus = "";
	$sqlSelectSessionStatus .= "SELECT * ";
	$sqlSelectSessionStatus .= "FROM   mission_status ";
	$sqlSelectSessionStatus .= "WHERE  versionid = $versionid";
	$resultSessionStatus = mysql_query($sqlSelectSessionStatus);
	if(!$resultSessionStatus)
	{
		echo "getSessionStatus: ".mysql_error()."<br/>";
	}

	return mysql_fetch_array($resultSessionStatus);
}

function getSessionNotes($versionid)
{
	$sqlSelect = "";
	$sqlSelect .= "SELECT * ";
	$sqlSelect .= "FROM   mission_debriefnotes ";
	$sqlSelect .= "WHERE  versionid = $versionid";
	$result = mysql_query($sqlSelectSessionStatus);
	if(!$result)
	{
		echo "getSessionNotes: ".mysql_error()."<br/>";
	}

	return mysql_fetch_array($result);
}

function getSessionRequirements($versionid)
{
	$sqlSelect = "";
	$sqlSelect .= "SELECT requirementsid ";
	$sqlSelect .= "FROM   mission_requirements ";
	$sqlSelect .= "WHERE  versionid = $versionid";
	$result = mysql_query($sqlSelect);

	if(!$result)
	{
		echo "getSessionRequirements: ".mysql_error()."<br/>";
	}

	$resultToReturn = array();
	while ($row = mysql_fetch_array($result)) {
		$resultToReturn[]=$row["requirementsid"];
	}

	return $resultToReturn;
}

function getSessionBugs($versionid)
{
	$sqlSelect = "";
	$sqlSelect .= "SELECT * ";
	$sqlSelect .= "FROM   mission_bugs ";
	$sqlSelect .= "WHERE  versionid = $versionid";
	$result = mysql_query($sqlSelect);
	if(!$result)
	{
		echo "getSessionBugs: ".mysql_error()."<br/>";
	}

	$resultToReturn = array();
	while ($row = mysql_fetch_array($result)) {
		$resultToReturn[]=$row["bugid"];
	}

	return $resultToReturn;
}

function getSessionAreas($versionid)
{
	$sqlSelectSessionStatus = "";
	$sqlSelectSessionStatus .= "SELECT areaname ";
	$sqlSelectSessionStatus .= "FROM   mission_areas ";
	$sqlSelectSessionStatus .= "WHERE  versionid = $versionid";

	$resultSessionAreas = mysql_query($sqlSelectSessionStatus);

	if(!$resultSessionAreas)
	{
		echo "getSessionAreas: ".mysql_error()."<br/>";
	}

	$result = array();
	while ($row = mysql_fetch_array($resultSessionAreas)) {
		$result[]=$row["areaname"];
	}

	return $result;
}

function createPercentPieChart($setup,$test,$bug,$oppertunity)
{

	$totalTime = $setup +$test+$bug+$oppertunity;

	$imgURL = "http://chart.apis.google.com/chart?cht=p3&chd=s:Uf9a&chs=450x150&chd=t:$setup,$test,$bug,$oppertunity&chl=Setup($setup%)|Test($test%)|Bug inv($bug%)|Oppertunity($oppertunity%)|&chco=FF0000|00FF00|0000FF|000000&";
	//	echo "<img alt=\"pie chart\" src=\"$imgURL\" /\">";
	return $imgURL;
}

function echoTeamSelect($team)
{
	$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
	mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

	$sqlSelect = "";
	$sqlSelect .= "SELECT teamname ";
	$sqlSelect .= "FROM   teamnames ";
	$sqlSelect .= "ORDER  BY `teamname` ASC " ;

	$result = mysql_query($sqlSelect);

	echo "                                  <select id=\"select_team\" name=\"team\">\n";


	echo "                                      <option></option>\n";


	while($row = mysql_fetch_array($result))
	{
		if(strcmp($team,$row['teamname'])==0)
		{
			echo "                                      <option selected=\"selected\">" . htmlspecialchars($row['teamname']) . "</option>\n";
		}
		else
		{
			echo "                                      <option>" . htmlspecialchars($row['teamname']) . "</option>\n";
		}
	}

	echo "                                  </select>\n";

	mysql_close($con);
}

function echoAreaSelect($area)
{
	$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
	mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

	$sqlSelect = "";
	$sqlSelect .= "SELECT areaname ";
	$sqlSelect .= "FROM   areas ";
	$sqlSelect .= "ORDER  BY `areaname` ASC " ;

	$result = mysql_query($sqlSelect);

	echo "                                  <select id=\"select_area\" name=\"area[]\" multiple=\"multiple\" size=\"4\">\n";


	if(count($area)==0)
	{
			
		echo "                                      <option value=\"\" selected=\"selected\"></option>\n";
	}
	else
	{
		echo "                                      <option value=\"\"></option>\n";
	}


	while($row = mysql_fetch_array($result))
	{
		if(in_array($row['areaname'],$area))
		{
			echo "                                      <option selected=\"selected\" value=\"" . $row['areaname'] . "\">" . htmlspecialchars($row['areaname']) . "</option>\n";
		}
		else
		{
			echo "                                      <option value=\"" . $row['areaname'] . "\">" . htmlspecialchars($row['areaname']) . "</option>\n";
		}
	}

	echo "                                  </select>\n";

	mysql_close($con);
}

function echoAreaSelectSingel($area)
{
	$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
	mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

	$sqlSelect = "";
	$sqlSelect .= "SELECT areaname ";
	$sqlSelect .= "FROM   areas ";
	$sqlSelect .= "ORDER  BY `areaname` ASC " ;

	$result = mysql_query($sqlSelect);

	echo "                                  <select id=\"select_area\" name=\"area\">\n";


	if(count($area)==0)
	{
			
		echo "                                      <option value=\"\" selected=\"selected\"></option>\n";
	}
	else
	{
		echo "                                      <option value=\"\"></option>\n";
	}


	while($row = mysql_fetch_array($result))
	{
		if(in_array($row['areaname'],$area))
		{
			echo "                                      <option selected=\"selected\" value=\"" . $row['areaname'] . "\">" . htmlspecialchars($row['areaname']) . "</option>\n";
		}
		else
		{
			echo "                                      <option value=\"" . $row['areaname'] . "\">" . htmlspecialchars($row['areaname']) . "</option>\n";
		}
	}

	echo "                                  </select>\n";

	mysql_close($con);
}

function echoNumberOfRowToDisplay()
{
	echo "<select id=\"select_norowtodisplay\" class=\"metricoption\" name=\"norowdisplay\">\n";
	echo "      <option>30</option>\n";
	echo "      <option>60</option>\n";
	echo "      <option>100</option>\n";
	echo "      <option>200</option>\n";
	echo "      <option>500</option>\n";
	echo "      <option>1000</option>\n";
	echo "      <option>2000</option>\n";
	echo "</select>\n";
}

function echoStatusTypes($statusToSelect)
{
	echo "<select id=\"select_status_type\" class=\"metricoption\" name=\"status\">\n";
	if(strcmp($statusToSelect,"")==0)
	{
		echo "      <option selected=\"selected\"></option>\n";
	}
	else
	{
		echo "      <option</option>\n";
	}
	if(strcmp($statusToSelect,"Not Executed")==0)
	{
		echo "      <option selected=\"selected\">Not Executed</option>\n";
	}
	else
	{
		echo "      <option>Not Executed</option>\n";
	}

	if(strcmp($statusToSelect,"Executed")==0)
	{
		echo "      <option selected=\"selected\">Executed</option>\n";
	}
	else
	{
		echo "      <option>Executed</option>\n";
	}

	if(strcmp($statusToSelect,"Debriefed")==0)
	{
		echo "      <option selected=\"selected\">Debriefed</option>\n";
	}
	else
	{
		echo "      <option>Debriefed</option>\n";
	}
	echo "</select>\n";
}

function echoTesterSelect($name)
{
	$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
	mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

	$sqlSelect = "";
	$sqlSelect .= "SELECT username ";
	$sqlSelect .= "FROM   members ";
	$sqlSelect .= "WHERE active = TRUE ";
	$sqlSelect .= "ORDER  BY fullname ASC" ;

	$result = mysql_query($sqlSelect);

	echo "                                  <select id=\"select_tester\" name=\"tester\">\n";
	echo "                                      <option></option>\n";

	while($row = mysql_fetch_array($result))
	{
		if(strcmp($name,$row['username'])==0)
		{
			echo "                                      <option selected=\"selected\">" . htmlspecialchars($row['username']) . "</option>\n";
		}
		else
		{
			echo "                                      <option>" . htmlspecialchars($row['username']) . "</option>\n";
		}
	}

	echo "                                  </select>\n";

	mysql_close($con);
}

function echoSprintSelect($sprint)
{
	$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
	mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

	$sqlSelect = "";
	$sqlSelect .= "SELECT sprintname ";
	$sqlSelect .= "FROM   sprintnames ";
	$sqlSelect .= "ORDER  BY sprintname ASC" ;

	$result = mysql_query($sqlSelect);

	echo "                                  <select id=\"select_sprint\" name=\"sprint\">\n";


	echo "                                      <option></option>\n";

	while($row = mysql_fetch_array($result))
	{
		if(strcmp($sprint,$row['sprintname'])==0)
		{
			echo "                                      <option selected=\"selected\">" . htmlspecialchars($row['sprintname']) . "</option>\n";
		}
		else
		{
			echo "                                      <option>" . htmlspecialchars($row['sprintname']) . "</option>\n";
		}
	}

	echo "                                  </select>\n";

	mysql_close($con);
}

function echoTeamSprintSelect($teamsprint)
{
	$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
	mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

	$sqlSelect = "";
	$sqlSelect .= "SELECT teamsprintname ";
	$sqlSelect .= "FROM   teamsprintnames ";
	$sqlSelect .= "ORDER  BY teamsprintname ASC" ;

	$result = mysql_query($sqlSelect);

	echo "                                  <select id=\"select_teamsprint\" name=\"teamsprint\">\n";


	echo "                                      <option></option>\n";

	while($row = mysql_fetch_array($result))
	{
		if(strcmp($teamsprint,$row['teamsprintname'])==0)
		{
			echo "                                      <option selected=\"selected\">" . htmlspecialchars($row['teamsprintname']) . "</option>\n";
		}
		else
		{
			echo "                                      <option>" . htmlspecialchars($row['teamsprintname']) . "</option>\n";
		}
	}

	echo "                                  </select>\n";

	mysql_close($con);
}

?>