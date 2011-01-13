<?php
session_start();
if(!session_is_registered(myusername)){
	header("location:index.php");
}
include("include/header.php.inc");
include_once('config/db.php.inc');
include_once 'include/commonFunctions.php.inc';

echo "<h1>Settings</h1>\n";

echoMenu();

executeCommand();

include("include/footer.php.inc");


//*************************************************************************************
//Function is located below
//*************************************************************************************s

function executeCommand()
{

	//Administartor Commands
	if($_SESSION['useradmin']==1)
	{
		if(strcmp($_GET["command"],"listusers")==0)
		{
			echo "Tjoho";
			echoAllUsersInfo();
		}
		elseif (strcmp($_GET["command"],"adduser")==0)
		{
			echoAddUser();
		}
		elseif (strcmp($_REQUEST["command"],"insertusertodb")==0)
		{
			createNewUser();
		}
		elseif(strcmp($_GET["command"],"addteam")==0)
		{
			echoAddTeamName();
		}
		elseif(strcmp($_GET["command"],"addarea")==0)
		{
			echoAddAreaName();
		}
		elseif (strcmp($_REQUEST["command"],"insertteamnametodb")==0)
		{
			insertTeamNameToDb($_REQUEST["teamtname"]);
		}
		elseif (strcmp($_REQUEST["command"],"insertareaname")==0)
		{
			insertAreaNameToDb($_REQUEST["areaname"]);
		}
		elseif(strcmp($_REQUEST["command"],"changeusersettings")==0)
		{
			updateUserSettings($_REQUEST["usernametoupdate"],$_REQUEST["active"],$_REQUEST["admin"],$_REQUEST["superuser"]);
		}
		elseif(strcmp($_GET["command"],"userinfo")==0)
		{
			echoChangeUserInfo($_GET["user"]);
		}
		elseif(strcmp($_GET["command"],"config")==0)
		{
			echoChangeConfig();
		}
		elseif(strcmp($_REQUEST["command"],"updateconfig")==0)
		{
			updateConfig();
		}



	}
	//SuperUser Commands
	if ($_SESSION['useradmin']==1 || $_SESSION['superuser']==1)
	{
		if(strcmp($_GET["command"],"addsprint")==0)
		{
			echoAddSprintName();
		}
		if(strcmp($_GET["command"],"addteamsprint")==0)
		{
			echoAddTeamSprintName();
		}


		elseif (strcmp($_REQUEST["command"],"insertsprintnametodb")==0)
		{
			insertSprintNameToDb($_REQUEST["sprintname"]);
		}
		elseif (strcmp($_REQUEST["command"],"insertteamsprintnametodb")==0)
		{
			insertTeamSprintNameToDb($_REQUEST["teamsprintname"]);
		}


	}

	//Common commands
	if(strcmp($_REQUEST["command"],"changepassword")==0)
	{
		updateUserPassword($_REQUEST["usernametoupdate"],$_REQUEST["swpassword1"], $_REQUEST["swpassword2"]);
	}
	if(strcmp($_GET["command"],"changepassword")==0)    {

		echoChangePassword($_SESSION['username']);
	}

}

function echoChangeConfig()
{

	$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
	mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

	$sqlSelect .= "SELECT * FROM settings";

	$result = mysql_query($sqlSelect);

	if(!$result)
	{
		echo "echoChangeConfig: ".mysql_error()."<br/>";
	}
	else
	{
		$row = mysql_fetch_array($result);
		echo "<h4>Change Application Configuration</h4>\n";
		echo "<form name=\"teamname\" action=\"settings.php\" method=\"POST\">\n";
		echo "<input type=\"hidden\" name=\"command\" value= \"updateconfig\">\n";
		echo "	<table width=\"*\" border=\"1\">\n";
		echo "    <tr>\n";
		echo "        <td>\n";
		echo "        </td>\n";
		echo "        <td><b>Common Settings</b>\n";
		echo "        </td>\n";
		echo "    </tr>\n";
		echo "    <tr>\n";
		echo "        <td>Normalized Sessions time(min)\n";
		echo "        </td>\n";
		echo "        <td> <input type=\"text\" size=\"50\" value=\"".$row[normalized_session_time]."\" name=\"normlizedsessiontime\">\n";
		echo "        </td>\n";
		echo "    </tr>\n";
		echo "    <tr>\n";
		echo "        <td>Defect Managment System URL\n";
		echo "        </td>\n";
		echo "        <td> <input type=\"text\" size=\"50\" value=\"".$row[url_to_dms]."\" name=\"url_to_dms\">\n";
		echo "        </td>\n";
		echo "    </tr>\n";
		echo "    <tr>\n";
		echo "        <td>Requirement Management System URL\n";
		echo "        </td>\n";
		echo "        <td> <input type=\"text\" size=\"50\" value=\"".$row[url_to_rms]."\" name=\"url_to_rms\">\n";
		echo "        </td>\n";
		echo "    </tr>\n";
		echo "    <tr>\n";
		echo "        <td>\n";
		echo "        </td>\n";
		echo "        <td><b>Activate Modules</b>\n";
		echo "        </td>\n";
		echo "    </tr>\n";
		echo "    <tr>\n";
		echo "        <td>Team\n";
		echo "        </td>\n";
		if($row[team]==1)
		{
			echo "        <td> <input type=\"checkbox\" name=\"team\" checked=\"checked\" value=\"checked\" >\n";
		}
		else
		{
			echo "        <td> <input type=\"checkbox\" name=\"team\" value=\"checked\" >\n";
		}
		echo "        </td>\n";
		echo "    </tr>\n";
		echo "    <tr>\n";
		echo "        <td>Sprint\n";
		echo "        </td>\n";
		if($row[sprint]==1)
		{
			echo "        <td> <input type=\"checkbox\" name=\"sprint\" checked=\"checked\" value=\"checked\" >\n";
		}
		else
		{
			echo "        <td> <input type=\"checkbox\" name=\"sprint\" value=\"checked\" >\n";
		}
		echo "        </td>\n";
		echo "    </tr>\n";
		echo "    <tr>\n";
		echo "        <td>Team sprint\n";
		echo "        </td>\n";
		if($row[teamsprint]==1)
		{
			echo "        <td> <input type=\"checkbox\" name=\"teamsprint\" checked=\"checked\" value=\"checked\" >\n";
		}
		else
		{
			echo "        <td> <input type=\"checkbox\" name=\"teamsprint\" value=\"checked\" >\n";
		}
		echo "        </td>\n";
		echo "    </tr>\n";
		echo "    <tr>\n";
		echo "        <td>Area\n";
		echo "        </td>\n";
		if($row[area]==1)
		{
			echo "        <td> <input type=\"checkbox\" name=\"area\" checked=\"checked\" value=\"checked\" >\n";
		}
		else
		{
			echo "        <td> <input type=\"checkbox\" name=\"area\" value=\"checked\" >\n";
		}
		echo "        </td>\n";
		echo "    </tr>\n";
		echo "</table>\n";
		echo "            <input align=left type=\"submit\" value=\"Change settings\" />\n";
		echo "</form>\n";
	}
	mysql_close($con);

}

function echoMenu()
{
	if($_SESSION['useradmin']==1)
	{
		echo "<div>Admin menu: ";
		if($_SESSION['settings']['team']==1)
		{
			echo "<a href=\"settings.php?command=addteam\">Add team</a> | ";
		}
		echo "<a id=\"url_listusers\" href=\"settings.php?command=listusers\">List users</a> | ";
		echo "<a id=\"url_adduser\" href=\"settings.php?command=adduser\">Add user</a> | ";
		echo "<a id=\"url_configuration\" href=\"settings.php?command=config\">Configuration</a> | ";
		echo "</div>";
	}
	if ($_SESSION['useradmin']==1 || $_SESSION['superuser']==1)
	{
		echo "<div>Superuser menu:  ";
		if($_SESSION['settings']['sprint']==1)
		{
			echo "<a id=\"url_addsprint\" href=\"settings.php?command=addsprint\">Add sprintname</a> | ";
		}
		if($_SESSION['settings']['area']==1)
		{
			echo "<a id=\"url_addarea\" href=\"settings.php?command=addarea\">Add area</a> | ";
		}
		if($_SESSION['settings']['teamsprint']==1)
		{
			echo "<a id=\"url_addteamsprint\" href=\"settings.php?command=addteamsprint\">Add team sprintname</a> | ";
		}
		echo "</div>";
	}
	echo "<div>User menu: <a id=\"url_changepassword\" href=\"settings.php?command=changepassword\">Change password</a></div>";
}

function echoAddTeamName()
{
	echo "<h2>Add new team name</h2>\n";
	echo "<form name=\"teamname\" action=\"settings.php\" method=\"POST\">\n";
	echo "<input type=\"hidden\" name=\"command\" value= \"insertteamnametodb\">\n";
	echo "<table style=\"text-align: left;\" border=\"0\" cellpadding=\"0\" cellspacing=\"2\">";
	echo "    <tr>\n";
	echo "        <td align=\"left\">\n";
	echo "            New team name\n";
	echo "        </td>\n";
	echo "        <td><input type=\"text\" size=\"50\" value=\"\" name=\"teamtname\">\n";
	echo "        </td>\n";
	echo "        <td align=\"left\">\n";
	echo "            <input align=left type=\"submit\" value=\"Add team\" />\n";
	echo "        </td>\n";
	echo "    </tr>\n";
	echo "</table>";
	echo "</form>\n";
}

function insertTeamNameToDb($teamName)
{
	$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
	mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

	$teamName = mysql_real_escape_string($teamName);

	$sqlInsert = "";
	$sqlInsert .= "INSERT INTO teamnames ";
	$sqlInsert .= "            (`teamname`) ";
	$sqlInsert .= "VALUES      ('$teamName')" ;


	$result = mysql_query($sqlInsert);

	if(!$result)
	{
		echo "InsertTeamNameToDb: ".mysql_error()."<br/>";
	}
	else
	{
		echo "<p>Team name $teamName added to database</p>\n";
	}

	mysql_close($con);
}

function insertAreaNameToDb($areaName)
{
	$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
	mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

	$areaName = mysql_real_escape_string($areaName);

	$sqlInsert = "";
	$sqlInsert .= "INSERT INTO areas ";
	$sqlInsert .= "            (`areaname`) ";
	$sqlInsert .= "VALUES      ('$areaName')" ;


	$result = mysql_query($sqlInsert);

	if(!$result)
	{
		if(mysql_errno()==1062)
		{
			echo "<p>Area $areaName not added since it already exists in database.</p>";
		}
		else
		{
			echo "insertAreaNameToDb: ".mysql_error()."<br>";
			echo "Mysql error no: ".mysql_errno()."<br>";
		}
	}
	else
	{
		echo "<p>Area name $areaName added to database</p>\n";
	}

	mysql_close($con);
}


function echoAddSprintName()
{
	echo "<h2>Add new sprint name</h2>\n";
	echo "<form name=\"sprintname\" action=\"settings.php\" method=\"POST\">\n";
	echo "<input type=\"hidden\" name=\"command\" value= \"insertsprintnametodb\">\n";
	echo "<table style=\"text-align: left;\" border=\"0\" cellpadding=\"0\" cellspacing=\"2\">";
	echo "    <tr>\n";
	echo "        <td align=\"left\">\n";
	echo "            New sprint name\n";
	echo "        </td>\n";
	echo "        <td><input type=\"text\" size=\"50\" value=\"\" name=\"sprintname\">\n";
	echo "        </td>\n";
	echo "        <td align=\"left\">\n";
	echo "            <input align=left type=\"submit\" value=\"Add name\" />\n";
	echo "        </td>\n";
	echo "    </tr>\n";
	echo "</table>";
	echo "</form>\n";
}

function echoAddAreaName()
{
	echo "<h2>Add new area name</h2>\n";
	echo "<form name=\"areaname\" action=\"settings.php\" method=\"POST\">\n";
	echo "<input type=\"hidden\" name=\"command\" value= \"insertareaname\">\n";
	echo "<table style=\"text-align: left;\" border=\"0\" cellpadding=\"0\" cellspacing=\"2\">";
	echo "    <tr>\n";
	echo "        <td align=\"left\">\n";
	echo "            New area name\n";
	echo "        </td>\n";
	echo "        <td><input type=\"text\" size=\"50\" value=\"\" name=\"areaname\">\n";
	echo "        </td>\n";
	echo "        <td align=\"left\">\n";
	echo "            <input align=left type=\"submit\" value=\"Add area\" />\n";
	echo "        </td>\n";
	echo "    </tr>\n";
	echo "</table>";
	echo "</form>\n";
}

function echoAddTeamSprintName()
{
	echo "<h2>Add new team sprint name</h2>\n";
	echo "<form name=\"teamsprintname\" action=\"settings.php\" method=\"POST\">\n";
	echo "<input type=\"hidden\" name=\"command\" value= \"insertteamsprintnametodb\">\n";
	echo "<table style=\"text-align: left;\" border=\"0\" cellpadding=\"0\" cellspacing=\"2\">";
	echo "    <tr>\n";
	echo "        <td align=\"left\">\n";
	echo "            New team sprint name\n";
	echo "        </td>\n";
	echo "        <td><input type=\"text\" size=\"50\" value=\"\" name=\"teamsprintname\">\n";
	echo "        </td>\n";
	echo "        <td align=\"left\">\n";
	echo "            <input align=left type=\"submit\" value=\"Add name\" />\n";
	echo "        </td>\n";
	echo "    </tr>\n";
	echo "</table>";
	echo "</form>\n";
}

function insertSprintNameToDb($sprintName)
{
	$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
	mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

	$sprintName = mysql_real_escape_string($sprintName);

	$sqlInsert = "";
	$sqlInsert .= "INSERT INTO sprintnames ";
	$sqlInsert .= "            (`sprintname`) ";
	$sqlInsert .= "VALUES      ('$sprintName')" ;


	$result = mysql_query($sqlInsert);

	if(!$result)
	{
		echo "InsertSprintNameToDb: ".mysql_error()."<br/>";
	}
	else
	{
		echo "<p>Sprint name $sprintName added to database</p>\n";
	}

	mysql_close($con);
}

function insertTeamSprintNameToDb($teamsprintName)
{
	$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
	mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

	$sprintName = mysql_real_escape_string($sprintName);

	$sqlInsert = "";
	$sqlInsert .= "INSERT INTO teamsprintnames ";
	$sqlInsert .= "            (`teamsprintname`) ";
	$sqlInsert .= "VALUES      ('$teamsprintName')" ;


	$result = mysql_query($sqlInsert);

	if(!$result)
	{
		echo "insertTeamSprintNameToDb: ".mysql_error()."<br/>";
	}
	else
	{
		echo "<p>Team sprint name $teamsprintName added to database</p>\n";
	}

	mysql_close($con);
}


function echoChangeUserInfo($username)
{
	$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
	mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

	$username = mysql_real_escape_string($username);

	$sqlSelect = "";
	$sqlSelect .= "SELECT * ";
	$sqlSelect .= "FROM   `members` ";
	$sqlSelect .= "WHERE  `username` = '$username' ";
	$sqlSelect .= "ORDER  BY `fullname` ASC " ;

	$result = mysql_query($sqlSelect);

	$row = mysql_fetch_array($result);

	echo "<h2>Edit user ". htmlspecialchars($row['fullname']) ."</h2>";
	echo "<form name=\"userinfo\" action=\"settings.php\" method=\"POST\">";
	echo " <input type=\"hidden\" name=\"usernametoupdate\" value=\"".urlencode($username)."\">\n";
	echo " <input type=\"hidden\" name=\"usersettings\" value=\"true\">\n";
	echo "    <input type=\"hidden\" name=\"command\" value= \"changeusersettings\">\n";
	echo "<table style=\"text-align: left; width: 1000px;\" border=\"1\" cellpadding=\"0\" cellspacing=\"0\">";
	echo "<tr>";
	echo "<td><b>Name</b></td>";
	echo "<td><b>User name</b></td>";
	echo "<td><b>Active</b></td>";
	echo "<td><b>Admin</b></td>";
	echo "<td><b>Superuser</b></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td>".  htmlspecialchars($row['fullname']). "</td>";
	echo "<td>" . htmlspecialchars($row['username']). "</td>";
	if ($row['active']=="1")
	{
		echo "<td><input type=\"checkbox\" name=\"active\" value=\"checked\" checked=\"checked\"></td>";
	}
	else
	{
		echo "<td><input type=\"checkbox\" name=\"active\" value=\"checked\"></td>";
	}
	if ($row['admin']=="1")
	{
		echo "<td><input type=\"checkbox\" name=\"admin\" value=\"checked\" checked=\"checked\"></td>";
	}
	else
	{
		echo "<td><input type=\"checkbox\" name=\"admin\" value=\"checked\"></td>";
	}
	if ($row['superuser']=="1")
	{
		echo "<td><input type=\"checkbox\" name=\"superuser\" value=\"checked\" checked=\"checked\"></td>";
	}
	else
	{
		echo "<td><input type=\"checkbox\" name=\"superuser\" value=\"checked\"></td>";
	}
	echo "</tr>";
	echo "</table>";
	echo "<input type=\"submit\" value=\"Update\">";
	echo "</form>";

	mysql_close($con);

	echoChangePassword($username);
}


function echoAllUsersInfo()
{
	$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
	mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

	$sqlSelect = "";
	$sqlSelect .= "SELECT * ";
	$sqlSelect .= "FROM   `members` ";
	$sqlSelect .= "ORDER  BY `fullname` ASC " ;


	$result = mysql_query($sqlSelect);

	echo "<h2>Users</h2>";

	echo "<table style=\"text-align: left; width: 1000px;\" border=\"1\"    cellpadding=\"0\" cellspacing=\"0\">";
	echo "<tr>";
	echo "<td><b>Name</b></td>";
	echo "<td><b>User name</b></td>";
	echo "<td><b>Active</b></td>";
	echo "<td><b>Admin</b></td>";
	echo "<td><b>Superuser</b></td>";
	echo "</tr>";
	while($row = mysql_fetch_array($result))
	{
		echo "<tr>";
		echo "<td><a href=\"settings.php?user=".urlencode($row['username']). "&command=userinfo\">".htmlspecialchars($row['fullname']). "</a></td>";
		echo "<td>" . urldecode($row['username']) . "</td>";
		echo "<td>" . urldecode($row['active']) . "</td>";
		echo "<td>" . urldecode($row['admin']) . "</td>";
		echo "<td>" . urldecode($row['superuser']) . "</td>";
		echo "</tr>";
	}

	echo "</table>";

	mysql_close($con);
}


function echoChangePassword($username)
{
	echo "<h2>Change Password</h2>\n";
	echo "<form name=\"password\" action=\"settings.php\" method=\"POST\">\n";
	echo "<table style=\"text-align: left; width: 1000px;\" border=\"0\" cellpadding=\"0\" cellspacing=\"2\">";
	echo "    <input type=\"hidden\" name=\"usernametoupdate\" value=\"".urlencode($username)."\">\n";
	echo "    <input type=\"hidden\" name=\"command\" value= \"changepassword\">\n";
	echo "    <tr>\n";
	echo "        <td align=\"left\">\n";
	echo "            New password\n";
	echo "        </td>\n";
	echo "        <td><input type=\"password\" size=\"50\" value=\"\" name=\"swpassword1\">\n";
	echo "        </td>\n";
	echo "    </tr>\n";
	echo "    <tr>\n";
	echo "        <td align=\"left\">\n";
	echo "            Retype password\n";
	echo "        </td>\n";
	echo "        <td><input type=\"password\" size=\"50\" value=\"\" name=\"swpassword2\">\n";
	echo "        </td>\n";
	echo "    </tr>\n";
	echo "    <tr>\n";
	echo "        <td align=\"left\">\n";
	echo "            <input align=left type=\"submit\" value=\"Change password\" />\n";
	echo "        </td>\n";
	echo "    </tr>\n";
	echo "</table>";
	echo "</form>\n";
}


function echoAddUser()
{
	echo "<h2>Add user:</h2>\n";
	echo "<table style=\"text-align: left; width: 1000px;\" border=\"0\" cellpadding=\"2\" cellspacing=\"2\">\n";
	echo "            <form name=\"sprint\" action=\"settings.php\" method= \"POST\">\n";
	echo "                <input type=\"hidden\" name=\"command\" value= \"insertusertodb\">\n";
	echo "                <tr>\n";
	echo "                    <td width=200>\n";
	echo "                        Add User:\n";
	echo "                    </td>\n";
	echo "                    <td align=\"left\">\n";
	echo "                        Full Name<input type=\"text\" size=\"50\" value=\"\" name=\"fullname\">\n";
	echo "                    </td>\n";
	echo "                    <td align=\"left\">\n";
	echo "                        UserName<input type=\"text\" size=\"50\" value=\"\" name=\"username\">\n";
	echo "                    </td>\n";
	echo "                    <td align=\"left\">\n";
	echo "                        Password<input type=\"password\" size=\"50\" value=\"\" name=\"swpassword1\">\n";
	echo "                    </td>\n";
	echo "                    <td>Admin<input type=\"checkbox\" name=\"admin\" value=\"yes\"></td>\n";
	echo "                    <td>Superuser<input type=\"checkbox\" name=\"superuser\" value=\"yes\"></td>\n";
	echo "                    <td align=\"left\">\n";
	echo "                        <input align=left type=\"submit\" value=\"Add\" />\n";
	echo "                    </td>\n";
	echo "                </tr>\n";
	echo "            </form>\n";
	echo "        </table>\n";
}


function updateConfig()
{
	$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
	mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

	$normlizedsessiontime = 90;

	if(is_int((int)$_REQUEST["normlizedsessiontime"]) && (int)$_REQUEST["normlizedsessiontime"]!=0)
	{
		$normlizedsessiontime = $_REQUEST["normlizedsessiontime"];
	}
	else
	{
		echo "Normalized Sessions time is equal to 0 or not an integer, will use default value 90 min.<br>\n";
	}

	$team = 0;
	if(strcmp($_REQUEST["team"],"checked")==0)
	{
		$team=1;
	}
	else
	{
		$team=0;
	}

	$sprint = 0;
	if(strcmp($_REQUEST["sprint"],"checked")==0)
	{
		$sprint=1;
	}
	else
	{
		$sprint=0;
	}

	$teamsprint = 0;
	if(strcmp($_REQUEST["teamsprint"],"checked")==0)
	{
		$teamsprint=1;
	}
	else
	{
		$teamsprint=0;
	}

	$area = 0;
	if(strcmp($_REQUEST["area"],"checked")==0)
	{
		$area=1;
	}
	else
	{
		$area=0;
	}
	
	$url_to_dms = $_REQUEST["url_to_dms"];
	
	$url_to_rms = $_REQUEST["url_to_rms"];

	$sqlUpdate = "";
	$sqlUpdate .= "UPDATE settings ";
	$sqlUpdate .= "SET    `normalized_session_time` = $normlizedsessiontime, ";
	$sqlUpdate .= "       `team` = '$team', ";
	$sqlUpdate .= "       `sprint` = '$sprint', ";
	$sqlUpdate .= "       `area` = '$area', ";
	$sqlUpdate .= "       `url_to_dms` = '$url_to_dms', ";
	$sqlUpdate .= "       `url_to_rms` = '$url_to_rms', ";
	$sqlUpdate .= "       `teamsprint` = '$teamsprint' ";
	$sqlUpdate .= "WHERE  `id` = '1'" ;

	$result = mysql_query($sqlUpdate);

	if(!$result)
	{
		echo "updateConfig: ".mysql_error()."<br/>";
	}
	else
	{
		echo "<br>Configuration changed.<br>\n";
	}

	$_SESSION['settings'] = getSessionWebSettings();

	mysql_close($con);
}
function updateUserPassword($username,$password1, $password2)
{

	if(strcmp($_SESSION['username'],$_REQUEST["usernametoupdate"])==0 || $_SESSION['useradmin']==1)
	{
		if(strcmp($password1,$password2)==0)	{


			$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
			mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

			$username = mysql_real_escape_string($username);

			$md5password = md5($password1);

			$sqlUpdate = "";
			$sqlUpdate .= "UPDATE `members` ";
			$sqlUpdate .= "SET    `password` ='$md5password' ";
			$sqlUpdate .= "WHERE  `members`.`username` = '$username' " ;

			$result = mysql_query($sqlUpdate);

			if($result)
			{
				echo "Password changed\n";
			}
			else
			{
				echo mysql_error();
			}
			mysql_close($con);
		}
		else
		{
			echo  "Passwords does not match, please try again.\n";
		}
	}
}


function createNewUser()
{
	$username = $_REQUEST["username"];
	$password = $_REQUEST["swpassword1"];
	$fullname = $_REQUEST["fullname"];
	$active = 1;
	$admin = 0;
	if(strcmp($_REQUEST["admin"],"yes")==0)
	{
		$admin = 1;
	}

	$superuser = 0;
	if(strcmp($_REQUEST["superuser"],"yes")==0)
	{
		$superuser = 1;
	}


	if($username!="" && $password!="")
	{

		$activeToDb = 0;
		if($active!="")
		{
			$activeToDb = 1;
		}

		$adminToDb = 0;
		if($admin!="")
		{
			$adminToDb = 1;
		}

		$superuserToDb = 0;
		if($superuser!="")
		{
			$superuserToDb = 1;
		}

		$md5password = md5($password);

		$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
		mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

		$username = mysql_real_escape_string($username);
		$fullname = mysql_real_escape_string($fullname);
		$activeToDb = mysql_real_escape_string($activeToDb);
		$adminToDb = mysql_real_escape_string($adminToDb);
		$superuserToDb = mysql_real_escape_string($superuserToDb);

		$sqlInsert = "";
		$sqlInsert .= "INSERT INTO `members` ";
		$sqlInsert .= "            (`username`, ";
		$sqlInsert .= "             `password`, ";
		$sqlInsert .= "             `fullname`, ";
		$sqlInsert .= "             `active`, ";
		$sqlInsert .= "             `admin`, ";
		$sqlInsert .= "             `superuser`) ";
		$sqlInsert .= "VALUES      ('$username', ";
		$sqlInsert .= "             '$md5password', ";
		$sqlInsert .= "             '$fullname', ";
		$sqlInsert .= "             '$activeToDb', ";
		$sqlInsert .= "             '$adminToDb', ";
		$sqlInsert .= "             '$superuserToDb')" ;

		$result = mysql_query($sqlInsert);

		if($result)
		{
			echo "User added\n";
		}
		else
		{
			echo mysql_error();
		}

		mysql_close($con);
	}
	else
	{
		echo "Please try again, username and password is mandatory\n";
	}
}

function updateUserSettings($userToChange,$active,$admin,$superuser)
{
	$activeToDb = 0;
	if($active!="")
	{
		$activeToDb = 1;
	}

	$adminToDb = 0;
	if($admin!="")
	{
		$adminToDb = 1;
	}

	$superuserToDb = 0;
	if($superuser!="")
	{
		$superuserToDb = 1;
	}

	$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
	mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

	$activeToDb = mysql_real_escape_string($activeToDb);
	$adminToDb = mysql_real_escape_string($adminToDb);
	$superuserToDb = mysql_real_escape_string($superuserToDb);
	$userToChange = mysql_real_escape_string(urldecode($userToChange));

	$sqlUpdate = "";
	$sqlUpdate .= "UPDATE `members` ";
	$sqlUpdate .= "SET    `active` = '$activeToDb', ";
	$sqlUpdate .= "       `admin` = '$adminToDb', ";
	$sqlUpdate .= "       `superuser` = '$superuserToDb' ";
	$sqlUpdate .= "WHERE  `username` = '$userToChange'" ;

	$result = mysql_query($sqlUpdate);

	if($result)
	{
		echo "User settings changed\n";
	}
	else
	{
		echo mysql_error();
	}

	mysql_close($con);
}



?>