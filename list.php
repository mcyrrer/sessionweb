<?php
session_start();
if(!session_is_registered(myusername)){
	header("location:index.php");
}
include("include/header.php.inc");
include_once('config/db.php.inc');
include_once 'include/commonFunctions.php.inc';

echo "<br>";

$currentPage=$_GET["page"];

if($currentPage=="")
{
	$currentPage=1;
}

echoSearchDiv();

echoSessionTable($currentPage);

echoPreviouseAndNextLink($currentPage,$num_rows);

echoColorExplanation();


include("include/footer.php.inc");


function echoSessionTable($currentPage)
{
	echo "<table width=\"1024\" border=\"0\">\n";
	echo "  <tr>\n";
	echo "      <td width=\"25\">Id</td>\n";
	echo "      <td width=\"25\">Actions</td>\n";
	echo "      <td>Title</td>\n";
	echo "      <td>User</td>\n";
	if($_SESSION['settings']['sprint']==1 )
	{
		echo "      <td>Sprint</td>\n";
	}
	if($_SESSION['settings']['teamsprint']==1 )
	{
		echo "      <td>Team sprint</td>\n";
	}
	if($_SESSION['settings']['team']==1 )
	{
		echo "      <td>Team</td>\n";
	}

	echo "      <td>Updated</td>\n";
	echo "  </tr>\n";

	$rowsToDisplay = 30;
	if($_REQUEST["norowdisplay"]!="")
	{
		$rowsToDisplay=$_REQUEST["norowdisplay"];
	}


	$limitDown = ($currentPage*$rowsToDisplay)-$rowsToDisplay;

	$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
	mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

	$sqlSelect = createSelectQueryForSessions($limitDown, $rowsToDisplay);

	$result = mysql_query($sqlSelect);
	$num_rows = mysql_num_rows($result);

	if($result)
	{
		while($row = mysql_fetch_array($result)) {

			echoAllSessions($row);
		}
	}
	else
	{
		echo "saveSession_GetSessionIdForNewSession: ".mysql_error()."<br/>";
	}

	echo "</table>\n";

}

function echoAllSessions($row)
{
	$rowSessionStatus = getSessionStatus($row["versionid"]);

	if($_REQUEST["status"]!="")
	{
		if(strcmp($_REQUEST["status"],"Not Executed")==0 && $rowSessionStatus["debriefed"]==0)
		{
			if($rowSessionStatus["executed"]==0)
			{
				echoOneSession($row,$rowSessionStatus);
			}
		}
		elseif(strcmp($_REQUEST["status"],"Executed")==0)
		{
			if($rowSessionStatus["executed"]==1 && $rowSessionStatus["debriefed"]==0)
			{
				echoOneSession($row,$rowSessionStatus);
			}
		}
		elseif(strcmp($_REQUEST["status"],"Debriefed")==0)
		{
			if($rowSessionStatus["debriefed"]==1)
			{
				echoOneSession($row,$rowSessionStatus);
			}
		}
	}
	else
	{
		echoOneSession($row,$rowSessionStatus);
	}
}
function echoOneSession($row,$rowSessionStatus)
{

	$color = getSessionColorCode($rowSessionStatus);
	echo "  <tr class=\"tr_sessionrow \" bgcolor=\"$color\">\n";
	echo "      <td>".$row["sessionid"]."</td>\n";
	if(strcmp($_SESSION['username'],$row["username"])==0 || strcmp($_SESSION['superuser'],"1")==0 || strcmp($_SESSION['admin'],"1")==0)
	{
		echo "      <td><a class=\"url_edit_session\" href=\"session.php?sessionid=".$row["sessionid"]."&command=edit\"><img class=\"picture_edit_session\" src=\"pictures/edit.png\" border=\"0\" alt=\"Sessionweb logo\"/></a></td>\n";
	}
	else
	{
		echo "      <td></td>\n";
	}
	$title = $row["title"];
	if(strlen($row["title"])>30)
	{
		$title = substr($row["title"],0,50)."...";
	}
	echo "      <td>\n";
	echo "<div title=\"".$row["title"]."\">\n";
	echo "<a class=\"url_view_session\" href=\"session.php?sessionid=".$row["sessionid"]."&command=view\">$title</a></td>\n";
	echo "</div>\n";
	echo "      <td>".$row["username"]."</td>\n";
	if($_SESSION['settings']['sprint']==1 )
	{
		echo "      <td>".$row["sprintname"]."</td>\n";
	}
	if($_SESSION['settings']['teamsprint']==1 )
	{
		echo "      <td>".$row["teamsprintname"]."</td>\n";
	}
	if($_SESSION['settings']['team']==1 )
	{
		echo "      <td>".$row["teamname"]."</td>\n";
	}
	echo "      <td>".$row["updated"]."</td>\n";
	echo "  </tr>\n";
}

function createSelectQueryForSessions($limitDown, $rowsToDisplay)
{
	$sqlSelect = "";
	$sqlSelect .= "SELECT * ";
	$sqlSelect .= "FROM   `mission` ";
	$sqlSelect .= "WHERE   depricated = 0 ";
	if($_REQUEST["tester"]!="")
	{
		$sqlSelect .="     AND username=\"".$_REQUEST["tester"]."\" ";
	}
	if($_REQUEST["sprint"]!="")
	{
		$sqlSelect .="     AND sprintname=\"".$_REQUEST["sprint"]."\" ";
	}
	if($_REQUEST["teamsprint"]!="")
	{
		$sqlSelect .="     AND teamsprintname=\"".$_REQUEST["teamsprint"]."\" ";
	}
	if($_REQUEST["team"]!="")
	{
		$sqlSelect .="     AND teamname=\"".$_REQUEST["team"]."\" ";
	}

	$sqlSelect .= "ORDER BY updated DESC " ;
	$sqlSelect .= "LIMIT  $limitDown, $rowsToDisplay " ;

	return $sqlSelect;
}

function echoSearchDiv()
{
	echo "<a id=\"showoption\" href=\"#\">Show table options</a>\n";

	echo "<div style=\"width: 1024px; height: 100%; background-color: rgb(239, 239, 239);\" id=\"option_list\">\n";
	echo "<form id=\"narrowform\" name=\"narrowform\" action=\"list.php\" method=\"POST\" accept-charset=\"utf-8\">\n";

	echo "<table width=\"1024\" border=\"0\">\n";
	echo "    <tr>\n";
	echo "        <td>User";
	echoTesterSelect($_REQUEST["tester"]);
	echo "        </td>\n";
	if($_SESSION['settings']['sprint']==1 )
	{
		echo "        <td>Sprint:";
		echoSprintSelect($_REQUEST["sprint"]);
		echo "        </td>\n";
	}
	if($_SESSION['settings']['teamsprint']==1 )
	{
		echo "        <td>Team sprint:";
		echoTeamSprintSelect($_REQUEST["teamsprint"]);
		echo "        </td>\n";
	}
	if($_SESSION['settings']['team']==1 )
	{
		echo "        <td>Team:";
		echoTeamSelect($_REQUEST["team"]);
		echo "        </td>\n";
	}
	echo "    </tr>\n";
	echo "    <tr>\n";
	if($_SESSION['settings']['area']==1 )
	{
		echo "        <td>\n";
		echo "<table width=\"*\" border=\"0\">\n";
		echo "    <tr valign=\"top\">\n";
		echo "        <td valign=\"top\">Area:";
		echo "        </td>\n";
		echo "        <td>\n";
		echoAreaSelectSingel($_REQUEST["area"]);
		echo "        </td>\n";
		echo "</table>\n";
		echo "        </td>\n";
	}
	echo "        <td>Status:\n";
	echoStatusTypes($_REQUEST["status"]);
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
	echo "        <td bgcolor=\"#ffff77\">Executed\n";
	echo "        </td>\n";
    echo "        <td>&rarr;";
    echo "        </td>\n";
	echo "        <td bgcolor=\"#99ff99\">Debriefed\n";
	echo "        </td>\n";
	echo "    </tr>\n";
	echo "    </table>\n";



}

function getSessionColorCode($rowSessionStatus)
{
	$color = "#c2c287";
	if($rowSessionStatus["executed"]==1)
	{
		$color = "#ffff77";
	}
	if($rowSessionStatus["debriefed"]==1)
	{
		$color = "#99ff99";
	}
	return $color;
}

function echoPreviouseAndNextLink($currentPage,$num_rows)
{
	$nextPage = $currentPage+1;
	echo "<table width=\"1024\" border=\"0\">\n";
	echo "  <tr>\n";
	if($currentPage!=1)
	{
		$prevPage = $currentPage-1;
		echo "     <td><a href=\"list.php?page=$prevPage\">Previous page</a></td>";
	}
	else
	{
		echo "     <td></td>\n";
	}
	if($num_rows==30)
	{
		echo "      <td align=\"right\"><a href=\"list.php?page=$nextPage\">Next page</a><br/></td>\n";
	}
	else
	{
		echo "     <td></td>\n";
	}
	echo "  </tr>\n";

	echo "</table>\n";
}
