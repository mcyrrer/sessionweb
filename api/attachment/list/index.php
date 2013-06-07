<?php
session_start();

require_once('../../../include/validatesession.inc');

//error_reporting(0);

require_once('../../../config/db.php.inc');
require_once ('../../../include/commonFunctions.php.inc');
require_once ('../../../include/db.php');
require_once('../../../classes/sessionHelper.php');
require_once('../../../classes/logging.php');
require_once('../../../classes/dbHelper.php');
require_once ('../../../include/apistatuscodes.inc');


$logger = new logging();
$sHelper = new sessionHelper();
$dbManager = new dbHelper();
$picture_mimetypes = array("jpg" => "image/jpeg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");

if (isset($_REQUEST['sessionid']) != null) {
    $con = $dbManager->db_getMySqliConnection();
    $sessionid = dbHelper::escape($con, $_REQUEST['sessionid']);
    $so = new sessionObject($sessionid);
    $scriptUrl = get_full_url() . '/';


    $sql = "select id, mission_versionid, filename, mimetype, size, thumbnail from mission_attachments WHERE mission_versionid= " . $so->getVersionid() . " ORDER BY filename";

    $result = $dbManager->sw_mysqli_execute($con, $sql, __FILE__, __LINE__);
    $filesArray = array();
    if ($result) {
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            // print_r($row);

            $tmpFileInfo['name'] = $row['filename'];
            $tmpFileInfo['size'] = $row['size'];
            $tmpFileInfo['url'] = $scriptUrl . "../download/index.php?id=" . $row['id'];
            if ($row['thumbnail']!=null && in_array($row['mimetype'], $picture_mimetypes)) {
                $tmpFileInfo['thumbnail_url'] = $scriptUrl . "../getThumb/index.php?id=" . $row['id'];
            }
            else
            {
                $path_parts = pathinfo($tmpFileInfo['name']);
                $extension = $path_parts['extension'];

                $thumpPath = $tmpFileInfo['thumbnail_url'] = $scriptUrl . "/../../../../pictures/mimetypes/$extension.png";
                $scriptPath = pathinfo(__FILE__);
                if(file_exists($scriptPath['dirname']."/../../../pictures/mimetypes/$extension.png"))
                {
                    $tmpFileInfo['thumbnail_url'] = $thumpPath;
                }
                else
                {
                    $tmpFileInfo['thumbnail_url'] = $scriptUrl . "../../../pictures/mimetypes/unknown.png";
                }
            }
            $tmpFileInfo['delete_url'] = $scriptUrl . "../delete/index.php?id=" . $row['id'];
            $tmpFileInfo['delete_type'] = "DELETE";
            $filesArray[] = $tmpFileInfo;
            unset($tmpFileInfo);
            /*"name": "picture1.jpg",
              "size": 902604,
              "url": "http:\/\/example.org\/files\/picture1.jpg",
              "thumbnail_url": "http:\/\/example.org\/files\/thumbnail\/picture1.jpg",
              "delete_url": "http:\/\/example.org\/files\/picture1.jpg",
              "delete_type": "DELETE"

                      [id] => 406
              [mission_versionid] => 110
              [filename] => geek-wallpaper.png
              [mimetype] => image/png
              [size] => 471359*/
        }
        $filesArrayAsObject['files'] = $filesArray;
        echo json_encode($filesArrayAsObject);
        exit;

    } else {
        $logger->error(mysql_error(), __FILE__, __LINE__);
    }

} else {
    $logger->debug("Tried to create a requirement but one of the parameters is bad", __FILE__, __LINE__);
    header("HTTP/1.0 400 Bad Request");
    $response['code'] = PARAMETER_NOT_PROVIDED_IN_REQUEST;
    $response['text'] = "PARAMETER_NOT_PROVIDED_IN_REQUEST";
    echo json_encode($response);
    exit;
}


function get_full_url()
{
    $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    return
        ($https ? 'https://' : 'http://') .
        (!empty($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'] . '@' : '') .
        (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'] .
        ($https && $_SERVER['SERVER_PORT'] === 443 ||
        $_SERVER['SERVER_PORT'] === 80 ? '' : ':' . $_SERVER['SERVER_PORT']))) .
        substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/'));
}