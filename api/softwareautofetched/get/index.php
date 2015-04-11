<?php
/**
 * Api to get a stored version of the environment given in $_REQUEST['id']
 */

require_once('../../../classes/autoloader.php');
require_once('../../../include/apistatuscodes.inc');

$logger = new logging();
$sHelper = new sessionHelper();
$dbm = new dbHelper();
$con = $dbm->connectToLocalDb();

$id = $_REQUEST['id'];

$sql = "SELECT versions FROM softwareuseautofetched WHERE id='$id' ORDER BY id DESC LIMIT 0,1";

$result = $dbm->executeQuery($con, $sql);

$row = mysqli_fetch_row($result);
echo $row[0];

?>