<?php
require_once('../../../classes/autoloader.php');
require_once('../../../include/apistatuscodes.inc');

$logger = new logging();
$sHelper = new sessionHelper();
$dbm = new dbHelper();

$con = $dbm->connectToLocalDb();

$oldSo = new sessionObject($_REQUEST["sessionid"]);
$newSo = new sessionObject();

$newSo->setTitle('(Copy)'.$oldSo->getTitle());
$newSo->setAreas($oldSo->getAreas());
$newSo->setCharter($oldSo->getCharter());
$newSo->setRequirements($oldSo->getRequirements());
$newSo->saveObjectToDb();

$logger->info("Copied session " . $oldSo->getSessionid() . " into new sessionid " . $newSo->getSessionid(), __FILE__, __LINE__);

echo "<center>";
echo "<img src='../../../pictures/edit-copy-9-medium.png' alt=''>";

echo "<h2>Copy session</h2>";
echo "<p>Title of copy: " . $newSo->getTitle() . "</p>";
echo "<p>Copy created</p>";
echo "<div id='editCopy'><a href='../../../edit.php?sessionid=".$newSo->getSessionid()."' target='_top'>Edit session</a></div>";
echo "</center>";

?>