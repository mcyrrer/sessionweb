<?php
require_once('../../include/loggingsetup.php');
session_start();
require_once('../../include/validatesession.inc');
//include_once("../../include/db.php");
include "../../config/db.php.inc";

$picture_mimetypes = array("jpg" => "image/jpeg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");


//$con = getMySqlConnection();
$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("cannot connect");
mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");
mysql_set_charset('utf8');

$attachmentId = $_GET['id'];

$sql = "SELECT * FROM `mission_attachments` WHERE `id` = " . $_GET['id'];
$result = mysql_query($sql);
if (!$result) {
    $logger->error("Attachment download[$attachmentId]: " . mysql_error());
    $logger->debug("Attachment download:[$attachmentId] " . $sql);
}
else {
    $logger->debug("Attachment download:[$attachmentId] File downloaded from database");
}
$logger->debug("Attachment download:[$attachmentId] Number of rows in result: ". mysql_num_rows($result));

$logger->debug("Attachment download:[$attachmentId] Fetching Mysql result");
$row = mysql_fetch_array($result);

mysql_close();
$logger->debug("Attachment download:[$attachmentId] id:".$row['id']." mission_versionid:".$row['mission_versionid']." filename: ".$row['filename']." mimetype: ".$row['mimetype']." size: ".$row['size']);

$logger->debug("Attachment download:[$attachmentId] Adding Content-length header");
header("Content-length: " . $row['size']);
$logger->debug("Attachment download:[$attachmentId] Adding Content-type header");
header("Content-type: " . $row['mimetype']);
if (!in_array($row['mimetype'], $picture_mimetypes) ){
    $logger->debug("Attachment download:[$attachmentId] Is not a picture, will skip content-disposition header");
    header("Content-Disposition: attachment; filename=" . $row['filename']);
}
else
{
    $logger->debug("Attachment download:[$attachmentId] Is a picture, will add content-disposition header");
}
echo $row['data'];
exit;
?>