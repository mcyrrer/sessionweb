<?php
session_start();
if(!session_is_registered(myusername)){
    header("location:index.php");
}
include("include/header.php.inc");
include_once('config/db.php.inc');
include_once 'include/commonFunctions.php.inc';

echo "<table width=\"1024\" border=\"1\">\n";
echo "  <tr>\n";
echo "      <td>SessionId</td>\n";
echo "      <td>Title</td>\n";
echo "      <td>User</td>\n";
echo "      <td>Sprint</td>\n";
echo "      <td>Team</td>\n";
echo "      <td>Updated</td>\n";
echo "  </tr>\n";



$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

$sqlSelect = "";
$sqlSelect .= "SELECT * ";
$sqlSelect .= "FROM   `mission` ";
$sqlSelect .= "LIMIT  0, 30 " ;

$result = mysql_query($sqlSelect);

if($result)
{
    while($row = mysql_fetch_array($result)) {
        echo "  <tr>\n";
        echo "      <td>".$row["sessionid"]."</td>\n";
        echo "      <td><a href=\"session.php?sessionid=".$row["sessionid"]."\">".$row["title"]."</a></td>\n";
        echo "      <td>".$row["username"]."</td>\n";
        echo "      <td>".$row["sprintname"]."</td>\n";
        echo "      <td>".$row["teamname"]."</td>\n";
        echo "      <td>".$row["updated"]."</td>\n";
        echo "  </tr>\n";
    }
}
else
{
    echo "saveSession_GetSessionIdForNewSession: ".mysql_error()."<br>";
}
echo "</table>\n";
include("include/footer.php.inc");
