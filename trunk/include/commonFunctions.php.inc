<?php

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