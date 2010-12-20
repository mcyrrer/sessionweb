<?php
session_start();
if(!session_is_registered(myusername)){
	header("location:index.php");
}
include("include/header.php.inc");
include_once('config/db.php.inc');


$user = $_GET["user"];
$command = $_GET["command"];
$command_post = $_REQUEST["command"];

$userToChange = $_REQUEST["usernametoupdate"];
$password1 = $_REQUEST["swpassword1"];
$password2 = $_REQUEST["swpassword2"];
$usersettings = $_REQUEST["usersettings"];
$active = $_REQUEST["active"];
$admin = $_REQUEST["admin"];
$superuser = $_REQUEST["superuser"];
$username = $_REQUEST["username"];
$fullname = $_REQUEST["fullname"];


echo "<h1>Settings</h1>\n";
if($userToChange!="")
{
	if(strcmp($_SESSION['username'],$userToChange)==0)
	{
		if($password1!="")
		{
			updateUserPassword($userToChange,$password1, $password2);
		}
	}
	else if($_SESSION['useradmin']==1)
	{
		if($password1!="")
		{
			updateUserPassword($userToChange,$password1, $password2);
		}
		if($usersettings!="")
		{
			updateUserSettings($userToChange,$active,$admin,$superuser);
		}
	}
}
else
{

	if($_SESSION['useradmin']==1)
	{
		echo "<div><a href=\"settings.php?command=listusers\">List users</a> | <a href=\"settings.php?command=adduser\">Add user</a></div>";

		if(strcmp($command,"listusers")==0)
		{
			echoAllUsersInfo();
		}
		elseif (strcmp($command,"adduser")==0)
		{
			echoAddUser();
		}
		elseif (strcmp($command_post,"insertusertodb")==0)
		{
			createNewUser($username,$password1,$fullname,1,$admin,$superuser);
		}
		if($user!="")
		{
			echoChangeUserInfo($user);
		}

	}
	else
	{
		echoChangePassword($_SESSION['username']);
	}
}
include("include/footer.php.inc");


//*************************************************************************************
//Function is located below
//*************************************************************************************s
function echoChangeUserInfo($username)
{
	$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
	mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

	$sqlSelect = "";
	$sqlSelect .= "SELECT * ";
	$sqlSelect .= "FROM   `members` ";
	$sqlSelect .= "WHERE  `username` = '$username' ";
	$sqlSelect .= "ORDER  BY `fullname` ASC " ;

	$result = mysql_query($sqlSelect);

	$row = mysql_fetch_array($result);

	echo "<h2>Edit user ". $row['fullname'] ."</h2>";
	echo "<form name=\"userinfo\" action=\"settings.php\" method=\"POST\">";
	echo " <input type=\"hidden\" name=\"usernametoupdate\" value=\"$username\">\n";
	echo " <input type=\"hidden\" name=\"usersettings\" value=\"true\">\n";
	echo "<table style=\"text-align: left; width: 1000px;\" border=\"1\" cellpadding=\"0\" cellspacing=\"0\">";
	echo "<tr>";
	echo "<td><b>Name</b></td>";
	echo "<td><b>User name</b></td>";
	echo "<td><b>Active</b></td>";
	echo "<td><b>Admin</b></td>";
	echo "<td><b>Superuser</b></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td>".  $row['fullname']. "</td>";
	echo "<td>" . $row['username'] . "</td>";
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
		echo "<td><a href=\"settings.php?user=".urlencode($row['username']). "\">".$row['fullname']. "</a></td>";
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
	echo "    <input type=\"hidden\" name=\"usernametoupdate\" value=\"$username\">\n";
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

function updateUserPassword($username,$password1, $password2)
{
	if(strcmp($password1,$password2)==0)	{


		$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
		mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

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


function createNewUser($username,$password,$fullname,$active,$admin,$superuser)
{
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

		$sqlInsert = "";
		$sqlInsert .= "INSERT INTO `sessionwebopensource`.`members` ";
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

		echo $sqlUpdate;

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

	$sqlUpdate = "";
	$sqlUpdate .= "UPDATE `sessionwebopensource`.`members` ";
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