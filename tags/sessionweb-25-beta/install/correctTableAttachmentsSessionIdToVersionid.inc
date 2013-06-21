<?php
/**
 * Script to correct that mission_attachments have sessionid instead of versionid in column  mission_versionid
 */
require_once('../classes/logging.php');
require_once('../classes/dbHelper.php');

require_once ('../config/db.php.inc');

$logger = new logging();
$dbHelper = new dbHelper();

$conMysqli = $dbHelper->db_getMySqliConnection();
$sqlGetAttachmentsId = 'SELECT id, mission_versionid as sessionId FROM mission_attachments';
$result = dbHelper::sw_mysqli_execute($conMysqli, $sqlGetAttachmentsId, __FILE__, __LINE__);
//$attachmentToBeCorrectedList = mysqli_fetch_all($result, MYSQLI_ASSOC); //Avaliable from PHP 5.3
$attachmentToBeCorrectedList = $dbHelper->sw_mysqli_fetch_all($result);

foreach ($attachmentToBeCorrectedList as $aAttachmentRecordToFix) {
    $realVersionId = getRealVersionIdForAttachment($conMysqli, $aAttachmentRecordToFix['sessionId']);
    if ($realVersionId != null) {
        $sqlUpdate = "update mission_attachments set mission_versionid = $realVersionId where id = " . $aAttachmentRecordToFix['id'];
        $result = dbHelper::sw_mysqli_execute($conMysqli, $sqlUpdate, __FILE__, __LINE__);
    }

}
mysqli_close($conMysqli);

echo "Correction of mysql table mission_attachments finished (changed to correct versionid).<br>";

function getRealVersionIdForAttachment($conMysqli, $sessionid)
{
    $sql = "SELECT versionid from mission WHERE sessionid=" . $sessionid;
    $result = dbHelper::sw_mysqli_execute($conMysqli, $sql, __FILE__, __LINE__);
    $versionid = mysqli_fetch_row($result);
    return $versionid[0];
}

?>