<?php
session_start();
if(!session_is_registered(myusername)){
	header("location:index.php");
}
include("include/header.php.inc");
include_once('config/db.php.inc');


$user = $_GET["user"];

if($_SESSION['useradmin']==TRUE)
{
	if($user!="")
	{
		echoChangeUserInfo($user);
	}
	else
	{
		echoAllUsersInfo();
	}
}
else
{
	echo "         You are not an admin and have not access to this part of the site";
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
	echo "<input type=\"hidden\" name=\"user\" value=\"<?php echo $username; ?>\">";

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
		echo "<td><input type=\"checkbox\" name=\"active\" value=\"no\" checked=\"checked\"></td>";
	}
	else
	{
		echo "<td><input type=\"checkbox\" name=\"active\" value=\"yes\"></td>";
	}
	if ($row['admin']=="1")
	{
		echo "<td><input type=\"checkbox\" name=\"admin\" value=\"no\" checked=\"checked\"></td>";
	}
	else
	{
		echo "<td><input type=\"checkbox\" name=\"admin\" value=\"yes\"></td>";
	}
	if ($row['superuser']=="1")
	{
		echo "<td><input type=\"checkbox\" name=\"superuser\" value=\"no\" checked=\"checked\"></td>";
	}
	else
	{
		echo "<td><input type=\"checkbox\" name=\"superuser\" value=\"yes\"></td>";
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
	echo "    <input type=\"hidden\" name=\"type\" value=\"changepassword\">\n";
	echo "    <tr>\n";
	echo "        <td align=\"left\">\n";
	echo "            New password\n";
	echo "        </td>\n";
	echo "        <td><input type=\"password\" size=\"50\" value=\"\" name=\"swpassword\">\n";
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
?>