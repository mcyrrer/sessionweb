<?php
error_reporting(0);

session_start();

require_once('../../include/loggingsetup.php');
require_once('../../include/validatesession.inc');

//include "../../config/db.php.inc";
//require_once("../../include/db.php");

$con = getMySqlConnection();

$sql = "DELETE FROM mission_attachments WHERE id = " . $_GET['id'];
$result = mysql_query($sql) or die('Error, query failed');
$logger->debug('File with ID :' . $_GET['id'] . ' deleted by ' . $_SESSION['user']);

mysql_close();

header('Content-type: application/json');
echo json_encode(true);

exit;

?>


