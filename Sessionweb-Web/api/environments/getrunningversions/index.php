<?php
/**
 * Api to get a stored version of the environment given in $_REQUEST['id']
 */

session_start();
include_once('../../../config/db.php.inc');
require_once ('../../../include/db.php');
require_once('../../../include/validatesession.inc');
include_once ('../../../include/commonFunctions.php.inc');

$con = getMySqlConnection();


$id = $_REQUEST['id'];


$con = getMySqlConnection();


$sql = "SELECT versions FROM softwareuseautofetched WHERE id='$id' ORDER BY id DESC LIMIT 0,1";

$result = mysql_query($sql);
$row = mysql_fetch_row($result);
echo $row[0];
mysql_close($con);
?>