<?php
$rootPath = checkIfRootFolder("");
require_once 'logging.php';
require_once $rootPath . '/config/db.php.inc';

/**
 * Class to help out with upload and download of attachment. is used togeather with https://github.com/blueimp/jQuery-File-Upload/
 */
class uploadManager
{
    private $logger;


    function __construct()
    {
        $this->logger = new logging();
    }


    public function testUpload()
    {
        if ($_FILES["file"]["error"] > 0)
        {
            $this->logger->error("Error: " . $_FILES["file"]["error"],__FILE__,__LINE__);
        }
        else
        {
//            $this->logger->arraylog($_REQUEST);
//            $this->logger->arraylog($_FILES);

            $this->logger->debug("Upload: " . $_FILES["file"]["name"]);
            $this->logger->debug("Type: " . $_FILES["file"]["type"]);
            $this->logger->debug("Size: " . ($_FILES["file"]["size"] / 1024) . " kB");
            $this->logger->debug("Stored in: " . $_FILES["file"]["tmp_name"]);

        }
    }
    private function getAttachment()
    {
    }

    private function saveAttachment()
    {
    }

    private function attachmentHasThumbnail()
    {
    }

    private function createThumnail($picture)
    {
    }
    private function getThumbnail($picture)
    {
    }

    private function saveThumbnail()
    {
    }

}

?>