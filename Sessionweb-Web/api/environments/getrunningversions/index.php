<?php
session_start();
if (!session_is_registered(myusername)) {
    header("HTTP/1.0 403 Forbidden");
    echo "No valid user session is active";
}

include_once('../../../config/db.php.inc');
include_once ('../../../include/commonFunctions.php.inc');

$con = getMySqlConnection();


$id = $_REQUEST['id'];


$con = getMySqlConnection();

$content = mysql_real_escape_string($content);
$sql = "SELECT versions FROM softwareuseautofetched WHERE id='$id' ORDER BY id DESC LIMIT 0,1";
//echo $sql;
$result = mysql_query($sql);
$row = mysql_fetch_row($result);
echo $row[0];
mysql_close($con);


//Get id for added software
//echo it to div tag (implement new api called viewswrunnung....

?>