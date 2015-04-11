<?php
require_once('../../../classes/autoloader.php');
require_once('../../../include/apistatuscodes.inc');

$logger = new logging();
$sHelper = new sessionHelper();
$dbm = new dbHelper();

$picture_mimetypes = array("jpg" => "image/jpeg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");


list($attachmentId, $filename, $mimeType, $size, $data) = getAttachmentFromDb($dbm, $logger);

header("Content-length: " . $size);
header("Content-type: " . $mimeType);

if (!in_array($mimeType, $picture_mimetypes)) {
    $logger->debug("Attachment download:[$attachmentId] Is not a picture, will add content-disposition header", __FILE__, __LINE__);
    header("Content-Disposition: attachment; filename=" . $filename);
}

echo $data;

exit;

/**
 * @param $dbm
 * @param $logger
 * @return array
 */
function getAttachmentFromDb($dbm, $logger)
{
    $con = $dbm->connectToLocalDb();

    $attachmentId = dbHelper::escape($con, $_REQUEST['id']);

    $sql = "SELECT id, mission_versionid, filename, mimetype, size, thumbnail, OCTET_LENGTH(thumbnail) as thumbsize FROM `mission_attachments` WHERE `id` = " . $_GET['id'];

    $result = $dbm->executeQuery($con, $sql);

    $row = mysqli_fetch_row($result);


    $dbId = $row[0];
    $versionId = $row[1];
    $filename = $row[2];
    $mimeType = $row[3];
    $size = $row[4];
    $thumbnailData = $row[5];
    $thumbnailSize = $row[6];


    $logger->debug("Thumbnail download:[$attachmentId] id:" . $dbId . " mission_versionid:" . $versionId . " filename: " . $filename . " mimetype: " . $mimeType . " size: " . $thumbnailSize, __FILE__, __LINE__);
    return array($attachmentId, $filename, $mimeType, $thumbnailSize, $thumbnailData);
}

?>