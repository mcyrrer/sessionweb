<?php
session_start();
if(!session_is_registered(myusername)){
	header("location:index.php");
}
include("include/header.php.inc");
include_once('config/db.php.inc');
include_once ('include/commonFunctions.php.inc');


echo "<br>";

$currentPage=$_GET["page"];

//TODO: Get settings from Db!



if(count($_REQUEST)<3)
{
	$userSettings = getUserSettings();
	if($userSettings['list_view']=="all")
	{
		$tester="";
	}
	else if($userSettings['list_view']=="mine")
	{
		$tester=$_SESSION['username'];
	}
	else
	{
		$tester="";
	}
	
	
	$listSettings = array();
	$listSettings["tester"]=$tester;
	$listSettings["sprint"]="";
	$listSettings["teamsprint"]="";
	$listSettings["team"]="";
	$listSettings["area"]="";
	$listSettings["status"]="";
	$listSettings["norowdisplay"]="30";

}
else
{
	$listSettings = $_REQUEST;
}
	print_r($listSettings);
if($currentPage=="")
{
	$currentPage=1;
}

echoSearchDiv($listSettings);

echoSessionTable($currentPage,$listSettings);



echoColorExplanation();

echoIconExplanation();


include("include/footer.php.inc");


function echoSessionTable($currentPage,$listSettings)
{
	echo "<table width=\"1024\" border=\"0\">\n";
	echo "  <tr>\n";
	echo "      <td id=\"tableheader_id\" width=\"25\">Id</td>\n";
	echo "      <td id=\"tableheader_actions\" width=\"100\">Actions</td>\n";
	echo "      <td id=\"tableheader_title\" >Title</td>\n";
	echo "      <td id=\"tableheader_users\" >User</td>\n";
	if($_SESSION['settings']['sprint']==1 )
	{
		echo "      <td id=\"tableheader_sprint\" >Sprint</td>\n";
	}
	//	if($_SESSION['settings']['teamsprint']==1 )
	//	{
	//		echo "      <td id=\"tableheader_teamsprint\" >Team sprint</td>\n";
	//	}
	if($_SESSION['settings']['team']==1 )
	{
		echo "      <td id=\"tableheader_team\" >Team</td>\n";
	}

	echo "      <td id=\"tableheader_updated\" width=\"120\">Updated</td>\n";
	echo "  </tr>\n";

	$rowsToDisplay = 30;
	if($listSettings["norowdisplay"]!="")
	{
		$rowsToDisplay=$listSettings["norowdisplay"];
	}


	$limitDown = ($currentPage*$rowsToDisplay)-$rowsToDisplay;

	$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
	mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

	$sqlSelect = createSelectQueryForSessions($limitDown, $rowsToDisplay,$listSettings);

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
	mysql_close($con);
	echoPreviouseAndNextLink($currentPage,$num_rows);

}

function echoAllSessions($row)
{
	$rowSessionStatus = getSessionStatus($row["versionid"]);

	if($listSettings["status"]!="")
	{
		if(strcmp($listSettings["status"],"Not Executed")==0 && $rowSessionStatus["debriefed"]==0)
		{
			if($rowSessionStatus["executed"]==0)
			{
				echoOneSession($row,$rowSessionStatus);
			}
		}
		elseif(strcmp($listSettings["status"],"Executed")==0)
		{
			if($rowSessionStatus["executed"]==1 && $rowSessionStatus["debriefed"]==0)
			{
				echoOneSession($row,$rowSessionStatus);
			}
		}
		elseif(strcmp($listSettings["status"],"Debriefed")==0)
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
	echo "      <td>\n";
	if(strcmp($_SESSION['username'],$row["username"])==0 || strcmp($_SESSION['superuser'],"1")==0 || strcmp($_SESSION['useradmin'],"1")==0)
	{
		echo "      <a id=\"edit_session".$row["sessionid"]."\"  class=\"url_edit_session\" href=\"session.php?sessionid=".$row["sessionid"]."&amp;command=edit\"><img class=\"picture_edit_session\" src=\"pictures/edit.png\" border=\"0\" alt=\"edit session\" title=\"Edit session\"/></a>\n";

		echo "      <a id=\"reassign_session".$row["sessionid"]."\" class=\"reassign_session\" href=\"session.php?sessionid=".$row["sessionid"]."&amp;command=reassign\"><img class=\"picture_reassign_session\" src=\"pictures/user-new-2-small.png\" border=\"0\" alt=\"Reassign session\" title=\"Reassign session\"/></a>\n";

	}
	if(strcmp($_SESSION['superuser'],"1")==0 || strcmp($_SESSION['useradmin'],"1")==0)
	{
		if($rowSessionStatus['executed']!=false && $rowSessionStatus['debriefed']!=true)
		{
			echo "      <a id=\"debrief_session".$row["sessionid"]."\" class=\"url_edit_session\" href=\"session.php?sessionid=".$row["sessionid"]."&amp;command=debrief\"><img class=\"picture_edit_session\" src=\"pictures/debrieficon.png\" border=\"0\" alt=\"debrief session\" title=\"Debrief session\"/></a>\n";
		}
	}
	if(strcmp($_SESSION['username'],$row["username"])==0 || strcmp($_SESSION['useradmin'],"1")==0)
	{
		echo "      <a id=\"delete_session".$row["sessionid"]."\" href=\"session.php?sessionid=".$row["sessionid"]."&amp;command=delete\"><img src=\"pictures/edit-delete-2-small.png\" border=\"0\" alt=\"Delete session\" title=\"Delete session\" class=\"delete_session\"/></a>\n";
		addjQueryDeletePopUp($row["sessionid"]);
	}
	echo "      <a id=\"publicview_session".$row["sessionid"]."\" class=\"publicview_session\" href=\"publicview.php?sessionid=".$row["sessionid"]."&amp;command=view&amp;publickey=".$row["publickey"]."\"><img src=\"pictures/share-3-small.png\" border=\"0\" alt=\"Share session\" title=\"Share session\"/></a>\n";

	echo "      </td>\n";
	$title = $row["title"];
	if(strlen($row["title"])>30)
	{
		$title = substr($row["title"],0,50)."...";
	}
	echo "      <td >\n";
	echo "<div id=\"tablerowtitle_".$row["sessionid"]."\" title=\"".$row["title"]."\">\n";
	echo "<a id=\"view_session".$row["sessionid"]."\" class=\"url_view_session\" href=\"session.php?sessionid=".$row["sessionid"]."&amp;command=view\">$title</a></div>\n";
	echo "</td>\n";
	echo "      <td id=\"tablerowuser_".$row["sessionid"]."\">".$row["username"]."</td>\n";
	if($_SESSION['settings']['sprint']==1 )
	{
		echo "      <td id=\"tablerowsprint_".$row["sessionid"]."\">".$row["sprintname"]."</td>\n";
	}
	//	if($_SESSION['settings']['teamsprint']==1 )
	//	{
	//		echo "      <td id=\"tablerowteamsprint_".$row["sessionid"]."\">".$row["teamsprintname"]."</td>\n";
	//	}
	if($_SESSION['settings']['team']==1 )
	{
		echo "      <td id=\"tablerowteam_".$row["sessionid"]."\">".$row["teamname"]."</td>\n";
	}
	echo "      <td id=\"tablerowupdatead_".$row["sessionid"]."\">".$row["updated"]."</td>\n";
	echo "  </tr>\n";
}

function createSelectQueryForSessions($limitDown, $rowsToDisplay,$listSettings)
{
	$sqlSelect = "";
	$sqlSelect .= "SELECT * ";
	$sqlSelect .= "FROM   `mission` ";
	$sqlSelect .= "WHERE   depricated = 0 ";
	if($listSettings["tester"]!="")
	{
		$sqlSelect .="     AND username=\"".$listSettings["tester"]."\" ";
	}
	if($listSettings["sprint"]!="")
	{
		$sqlSelect .="     AND sprintname=\"".$listSettings["sprint"]."\" ";
	}
	if($listSettings["teamsprint"]!="")
	{
		$sqlSelect .="     AND teamsprintname=\"".$listSettings["teamsprint"]."\" ";
	}
	if($listSettings["team"]!="")
	{
		$sqlSelect .="     AND teamname=\"".$listSettings["team"]."\" ";
	}

	$sqlSelect .= "ORDER BY updated DESC " ;
	$sqlSelect .= "LIMIT  $limitDown, $rowsToDisplay " ;
//	print $sqlSelect;
	return $sqlSelect;
}

function echoSearchDiv($listSettings)
{
	echo "<a id=\"showoption\" href=\"#\">Show table options</a>\n";

	echo "<div style=\"width: 1024px; height: 100%; background-color: rgb(239, 239, 239);\" id=\"option_list\">\n";
	echo "<form id=\"narrowform\" name=\"narrowform\" action=\"list.php\" method=\"POST\" accept-charset=\"utf-8\">\n";

	echo "<table width=\"1024\" border=\"0\">\n";
	echo "    <tr>\n";
	echo "        <td id=\"option_user\">User";
	echoTesterSelect($listSettings["tester"]);
	echo "        </td>\n";
	if($_SESSION['settings']['sprint']==1 )
	{
		echo "        <td id=\"option_sprint\">Sprint:";
		echoSprintSelect($listSettings["sprint"]);
		echo "        </td>\n";
	}
	if($_SESSION['settings']['teamsprint']==1 )
	{
		echo "        <td id=\"option_teamsprint\">Team sprint:";
		echoTeamSprintSelect($listSettings["teamsprint"]);
		echo "        </td>\n";
	}
	if($_SESSION['settings']['team']==1 )
	{
		echo "        <td id=\"option_team\">Team:";
		echoTeamSelect($listSettings["team"]);
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
	echo "        <td bgcolor=\"#ffff77\">Executed\n";
	echo "        </td>\n";
	echo "        <td>&rarr;";
	echo "        </td>\n";
	echo "        <td bgcolor=\"#99ff99\">Debriefed\n";
	echo "        </td>\n";
	echo "    </tr>\n";
	echo "    </table>\n";
}

function echoIconExplanation()
{
	echo "<table width=\"*\" border=\"0\">\n";
	echo "    <tr>\n";
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
	echo "    </tr>\n";
	echo "</table>\n";
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
		echo "     <td><a id=\"prev_page\" href=\"list.php?page=$prevPage\">Previous page</a></td>";
	}
	else
	{
		echo "     <td></td>\n";
	}
	if($num_rows==30)
	{
		echo "      <td id=\"next_page\" align=\"right\"><a href=\"list.php?page=$nextPage\">Next page</a><br/></td>\n";
	}
	else
	{
		echo "     <td></td>\n";
	}
	echo "  </tr>\n";

	echo "</table>\n";
}

function addjQueryDeletePopUp($id)
{
	//Delete Session questionbox
	echo "              <script type=\"text/javascript\">\n";
	echo "$(\"#delete_session".$id."\").click(function(){\n";
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
