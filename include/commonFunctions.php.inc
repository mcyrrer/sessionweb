<?php

function getSessionMetrics($versionid)
{
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

function createPercentPieChart($setup,$test,$bug,$oppertunity)
{

	$totalTime = $setup +$test+$bug+$oppertunity;

	$imgURL = "http://chart.apis.google.com/chart?cht=p3&chd=s:Uf9a&chs=450x150&chd=t:$setup,$test,$bug,$oppertunity&chl=Setup($setup%)|Test($test%)|Bug inv($bug%)|Oppertunity($oppertunity%)|&chco=FF0000|00FF00|0000FF|000000&";
	//	echo "<img alt=\"pie chart\" src=\"$imgURL\" /\">";
	return $imgURL;
}

function echoTeamSelect()
{
	$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
	mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

	$sqlSelect = "";
	$sqlSelect .= "SELECT teamname ";
	$sqlSelect .= "FROM   teamnames ";
	$sqlSelect .= "ORDER  BY `teamname` ASC " ;

	$result = mysql_query($sqlSelect);

	echo "                                  <select name=\"team\">\n";


	while($row = mysql_fetch_array($result))
	{
		echo "                                      <option>" . htmlspecialchars($row['teamname']) . "</option>\n";
	}

	echo "                                  </select>\n";

	mysql_close($con);
}

function echoTesterSelect()
{
	$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
	mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

	$sqlSelect = "";
	$sqlSelect .= "SELECT fullname ";
	$sqlSelect .= "FROM   members ";
	$sqlSelect .= "WHERE active = TRUE ";
	$sqlSelect .= "ORDER  BY fullname ASC" ;

	$result = mysql_query($sqlSelect);

	echo "                                  <select name=\"tester\">\n";


	while($row = mysql_fetch_array($result))
	{
		echo "                                      <option>" . htmlspecialchars($row['fullname']) . "</option>\n";
	}

	echo "                                  </select>\n";

	mysql_close($con);
}

function echoSprintSelect()
{
	$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
	mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

	$sqlSelect = "";
	$sqlSelect .= "SELECT sprintname ";
	$sqlSelect .= "FROM   sprintnames ";
	$sqlSelect .= "ORDER  BY sprintname ASC" ;

	$result = mysql_query($sqlSelect);

	echo "                                  <select name=\"sprint\">\n";


	while($row = mysql_fetch_array($result))
	{
		echo "                                      <option>" . htmlspecialchars($row['sprintname']) . "</option>\n";
	}

	echo "                                  </select>\n";

	mysql_close($con);
}

?>