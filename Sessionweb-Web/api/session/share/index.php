<?php
session_start();
require_once('../../../include/validatesession.inc');
require_once ('../../../include/db.php');
include_once('../../../config/db.php.inc');
include_once ('../../../include/commonFunctions.php.inc');

$con = getMySqlConnection();
$sessionInfo = getSessionData($_REQUEST["sessionid"]);
$sessionid=$_REQUEST["sessionid"];
$publickey = $sessionInfo["publickey"];


$title = $sessionInfo["title"];
echo "<center>";
echo "<img src='../../../pictures/sharethis.png' alt=''>";

echo "<h2>Share session</h2>";
echo "<p>Title: $title</p>";
echo "Share this link to make it possible to view the session without a password";
echo "<p><a href='../../../publicview.php?sessionid=$sessionid&command=view&publickey=$publickey' target='_blank'>Link to session</a></p>";


echo "</center>";


?>