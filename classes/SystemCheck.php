<?php

class SystemCheck
{
    private $logger;

    function __construct()
    {
        $this->logger = new logging();
        $this->dbHelper = new dbHelper();
    }

    public static function checkForMaxAttachmentSize($updateJsFile = false)
    {
        $maxsizebytes = self::getMaxUploadSize() * 1024 * 1024;
        echo "<b>Attachment size setup<br></b>";
        echo "Attachments max size according to php and mysql settings <b>" . self::getMaxUploadSize() . " mb</b><br>";
        echo "<br>Debug info:<br>";
        echo self::echoMaxUploadSizePhp();
        echo "MySql max_allowed_packet:" . self::getSqlMaxAllowedPacketAsMb() . "mb<br>";
    }


    public static function checkFoldersForRW()
    {
        echo "<b>Check for Read Write access for certain folders.</b><br>";
        $foldersToCheckRW = array("config/", "include/filemanagement/files/", "include/filemanagement/thumbnails/", "log/");
        $foldersOk = true;
        foreach ($foldersToCheckRW as $aFolder) {
            try {
                $ourFileName = $aFolder . "testFile.txt";

                $fh = fopen($ourFileName, 'w');
                fwrite($fh, "TestString\n");
                fclose($fh);
                if (file_exists($ourFileName)) {
                    echo "folder $aFolder is RW => OK<br>";
                    unlink($ourFileName);
                } else {
                    echo "folder $aFolder is RW => NOK (file could not be created)<br>";
                    $foldersOk = false;
                }
            } catch (Exception $e) {
                echo "folder $aFolder is RW => NOK<br>";
                //echo 'Error: ', $e->getMessage(), "\n";
                echo "Please change folder $aFolder to allow read write for the www user (chmod 664)<br>";
            }
        }

        if (!$foldersOk) {
            echo "Pleas make sure that NOK folders above have read and write access for the WWW user";
            echo "In ubuntu/linux you can use the chown command to make the www user e.g. 'chown -R www-data:www-data include/filemanagement/files/' ";
            return false;
        } else {
            echo "<br><br>";
            return true;
        }
    }

    public static function getMaxUploadSize()
    {
        $max_upload = (int)(ini_get('upload_max_filesize'));
        $max_post = (int)(ini_get('post_max_size'));
        $memory_limit = (int)(ini_get('memory_limit'));
        $sql_limit = self::getSqlMaxAllowedPacketAsMb();
        $upload_mb = min($max_upload, $max_post, $memory_limit, $sql_limit);
        return $upload_mb;

    }

    public static function echoMaxUploadSizePhp()
    {
        echo "PHP upload_max_filesize:" . (int)(ini_get('upload_max_filesize')) . "mb<br>";
        echo "PHP post_max_size:" . $max_post = (int)(ini_get('post_max_size')) . "mb<br>";
        echo "PHP memory_limit:" . (int)(ini_get('memory_limit')) . "mb<br>";
    }


    /**
     * Get the max_allowed_packaet from mysql db and return the value as bytes.
     * @return bytes|bool
     */
    public static function getSqlMaxAllowedPacketAsMb()
    {
        $dbm = new dbHelper();
        $con = $dbm->connectToLocalDb();
        $sql = "SHOW GLOBAL VARIABLES LIKE  'max_allowed_packet'";
        $result = $dbm->executeQuery($con, $sql);
        if (!$result) {
            return false;
        }
        $row = mysqli_fetch_array($result);
        return $row['Value'] / 1024 / 1024;
    }

    public static function check_moudles_dependency()
    {
        $modules = array();
        $modules[] = "mysqli";
        $modules[] = "ldap";

        foreach ($modules as $aModule) {
            if (self::checkIfModuleExist($aModule)) {
                echo "Php module ". $aModule ." installed => NOK <br>";
            } else {
                echo "Php module ". $aModule ." _NOT_ installed => NOK<br>";
            }
        }
    }

    private static function checkIfModuleExist($module)
    {
        if (in_array($module, get_loaded_extensions()))
            return true;
        else
            return false;

    }

}
