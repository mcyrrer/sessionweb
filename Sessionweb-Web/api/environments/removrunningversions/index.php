<?php
session_start();
if (!session_is_registered(myusername)) {
    header("HTTP/1.0 403 Forbidden");
    echo "No valid user session is active";
}

include_once('../../../config/db.php.inc');
include_once ('../../../include/commonFunctions.php.inc');

$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("cannot connect");
mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

$id = $_REQUEST['id'];


$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("cannot connect");
mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");
$content = mysql_real_escape_string($content);
$sql = "DELETE FROM softwareuseautofetched WHERE id='$id'";
//echo $sql;
$result = mysql_query($sql);
mysql_close($con);


//Get id for added software
//echo it to div tag (implement new api called viewswrunnung....

?>