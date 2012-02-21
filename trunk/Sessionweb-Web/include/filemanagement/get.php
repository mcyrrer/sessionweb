<?php
require_once('../../include/loggingsetup.php');
include_once("../../include/loggedincheck.php");

include "../../config/db.php.inc";
require_once("../../include/db.php");

$picture_mimetypes = array("jpg" => "image/jpeg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");

$con=getMySqlConnection();

$sql = "SELECT * FROM `mission_attachments` WHERE `id` = " . $_GET['id'];
$result = mysql_query($sql) or die($sql . 'Error, query failed');
$row = mysql_fetch_array($result);

mysql_close();

header("Content-length: " . $row['size']);
header("Content-type: " . $row['mimetype']);
if (!in_array($row['mimetype'], $picture_mimetypes) ){
    header("Content-Disposition: attachment; filename=" . $row['filename']);
}


echo $row['data'];
exit;

?>
