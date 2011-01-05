<?php
session_start();
if(!session_is_registered(myusername)){
	header("location:index.php");
}
include("include/header.php.inc");
include_once('config/db.php.inc');
include_once 'include/commonFunctions.php.inc';



$currentPage=$_GET["page"];

if($currentPage=="")
{
	$currentPage=1;
}


echo "<table width=\"1024\" border=\"0\">\n";
echo "  <tr>\n";
echo "      <td width=\"25\">Id</td>\n";
echo "      <td width=\"25\">Actions</td>\n";
echo "      <td>Title</td>\n";
echo "      <td>User</td>\n";
echo "      <td>Sprint</td>\n";
echo "      <td>Team</td>\n";
echo "      <td>Updated</td>\n";
echo "  </tr>\n";


$limitDown = ($currentPage*30)-30;

$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

$sqlSelect = "";
$sqlSelect .= "SELECT * ";
$sqlSelect .= "FROM   `mission` ";
$sqlSelect .= "ORDER BY updated DESC " ;
$sqlSelect .= "LIMIT  $limitDown, 30 " ;

$result = mysql_query($sqlSelect);
$num_rows = mysql_num_rows($result);

if($result)
{
	while($row = mysql_fetch_array($result)) {
		$rowSessionStatus = getSessionStatus($row["versionid"]);
		$color = getSessionColorCode($rowSessionStatus);
		echo "  <tr bgcolor=\"$color\">\n";
		echo "      <td>".$row["sessionid"]."</td>\n";
		if(strcmp($_SESSION['username'],$row["username"])==0 || strcmp($_SESSION['superuser'],"1")==0 || strcmp($_SESSION['admin'],"1")==0)
		{
			echo "      <td><a href=\"session.php?sessionid=".$row["sessionid"]."&command=edit\"><img src=\"pictures/edit.png\" border=\"0\" alt=\"Sessionweb logo\"/></a></td>\n";
		}
		else
		{
			echo "      <td></td>\n";
		}
		echo "      <td><a href=\"session.php?sessionid=".$row["sessionid"]."&command=view\">".$row["title"]."</a></td>\n";
		echo "      <td>".$row["username"]."</td>\n";
		echo "      <td>".$row["sprintname"]."</td>\n";
		echo "      <td>".$row["teamname"]."</td>\n";
		echo "      <td>".$row["updated"]."</td>\n";
		echo "  </tr>\n";
	}
}
else
{
	echo "saveSession_GetSessionIdForNewSession: ".mysql_error()."<br/>";
}

echo "</table>\n";

echoPreviouseAndNextLink($currentPage,$num_rows);


include("include/footer.php.inc");

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
