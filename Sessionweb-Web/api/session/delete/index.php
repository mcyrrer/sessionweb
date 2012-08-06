<?php
session_start();
error_reporting(0);
require_once('../../../include/validatesession.inc');
require_once ('../../../include/db.php');
include_once('../../../config/db.php.inc');
include_once ('../../../include/commonFunctions.php.inc');
include_once('../../../include/session_database_functions.php.inc');
require_once ('../../../include/loggingsetup.php');

$con = getMySqlConnection();
$sessionInfo = (getSessionData($_REQUEST["sessionid"]));
$title = $sessionInfo["title"];

$sessionid = $_REQUEST["sessionid"];

if(!isset($_REQUEST['delete']))
{
    $sessionid = $_REQUEST["sessionid"];
    echo "<center>";
    echo "<img src='../../../pictures/user-trash-full-3.png' alt=''>";
    echo "<h2>Delete session</h2>\n";
    echo "<p>Title: $title</p>";
    echo "<p>Are you sure that you want to delete session?</p>\n";
    echo "<a href='index.php?sessionid=$sessionid&delete=yes'>Delete</a>\n";

    echo "</center>";
    die();
}
if (strcmp($_SESSION['username'],$sessionInfo['username'])== 0 || $_SESSION['superuser']==1 || $_SESSION['useradmin']==1) {
    $title = getSessionTitle(getSessionVersionId($sessionid));
    deleteSessionFromDatabase($sessionid);
    $logger->info($_SESSION['username']." deleted session ($sessionid): $title");

    $title = $sessionInfo["title"];
    echo "<center>";
    echo "<img src='../../../pictures/user-trash-full-3.png' alt=''>";

    echo "<h2>Deleted session</h2>";
    echo "<p> $title</p>";

    echo "</center>";
}
else
{
    echo "<center>";
    echo "<img src='../../../pictures/user-trash-full-3.png' alt=''>";

    echo "<h2>Could not delete session $title</h2>";
    echo "Session " . $_REQUEST["sessionid"] . " could not be deleted.<br>
    You are not the owner of the session.";

    echo "</center>";


}




?>