<?php
require_once('../../../classes/autoloader.php');
require_once('../../../include/apistatuscodes.inc');

$logger = new logging();
$sHelper = new sessionHelper();
$dbm = new dbHelper();

$con = $dbm->connectToLocalDb();
$sessionInfo = getSessionData($_REQUEST["sessionid"]);
$sessionid = $_REQUEST["sessionid"];
$publickey = $sessionInfo["publickey"];


$title = $sessionInfo["title"];
if (isset($_REQUEST["urlonly"])) {
    echo "publicview.php?sessionid=$sessionid&command=view&publickey=$publickey";

} else {
    echo "<center>";
    echo "<img src='pictures/sharethis.png' alt=''>";

    echo "<h2>Share session</h2>";
    echo "<p>Title: $title</p>";
    echo "Share this link to make it possible to view the session without a password";
    echo "<p><a href='publicview.php?sessionid=$sessionid&command=view&publickey=$publickey' target='_blank'>Link to session</a></p>";


    echo "</center>";
}

?>