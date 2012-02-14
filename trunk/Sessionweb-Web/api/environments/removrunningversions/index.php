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
$sql = "DELETE FROM softwareuseautofetched WHERE id='$id'";
//echo $sql;
$result = mysql_query($sql);
mysql_close($con);


//Get id for added software
//echo it to div tag (implement new api called viewswrunnung....

?>