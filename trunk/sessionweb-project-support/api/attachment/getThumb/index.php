<?php


session_start();

require_once('../../../include/validatesession.inc');

require_once('../../../config/db.php.inc');
require_once ('../../../include/commonFunctions.php.inc');
require_once ('../../../include/db.php');
require_once('../../../classes/sessionHelper.php');
require_once('../../../classes/logging.php');
require_once('../../../classes/dbHelper.php');

$logger = new logging();
$sHelper = new sessionHelper();
$dbManager = new dbHelper();

$picture_mimetypes = array("jpg" => "image/jpeg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");


list($attachmentId, $filename, $mimeType, $size, $data) = getAttachmentFromDb($dbManager, $logger);

header("Content-length: " . $size);
header("Content-type: " . $mimeType);

if (!in_array($mimeType, $picture_mimetypes)) {
    $logger->debug("Attachment download:[$attachmentId] Is not a picture, will add content-disposition header",__FILE__,__LINE__);
    header("Content-Disposition: attachment; filename=" . $filename);
}

echo $data;

exit;

/**
 * @param $dbManager
 * @param $logger
 * @return array
 */
function getAttachmentFromDb($dbManager, $logger)
{
    $con = $dbManager->db_getMySqliConnection();

    $attachmentId = dbHelper::escape($con, $_REQUEST['id']);

    $sql = "SELECT id, mission_versionid, filename, mimetype, size, thumbnail, OCTET_LENGTH(thumbnail) as thumbsize FROM `mission_attachments` WHERE `id` = " . $_GET['id'];
    //$sql = "SELECT id,mimetype,filename,thumbnail as content, OCTET_LENGTH(thumbnail) as size FROM `mission_attachments` WHERE `id` = " . $_GET['id'];

    $result = $dbManager->sw_mysqli_execute($con, $sql, __FILE__, __LINE__);

    $row = mysqli_fetch_row($result);


    $dbId = $row[0];
    $versionId = $row[1];
    $filename = $row[2];
    $mimeType = $row[3];
    $size = $row[4];
    $thumbnailData = $row[5];
    $thumbnailSize = $row[6];

    mysqli_close($con);

    $logger->debug("Thumbnail download:[$attachmentId] id:" . $dbId . " mission_versionid:" . $versionId . " filename: " . $filename . " mimetype: " . $mimeType . " size: " . $thumbnailSize, __FILE__, __LINE__);
    return array($attachmentId, $filename, $mimeType, $thumbnailSize, $thumbnailData);
}

?>