<?php
require_once 'logging.php';
require_once 'dbHelper.php';
require_once 'queryHelper.php';
require_once 'sessionObject.php';

class sessionHelper
{
    private $logger;
    private $queryHelper;

    function __construct()
    {
        $this->logger = new logging();
        $this->queryHelper = new queryHelper();
    }

    function isUserAllowedToEditSession($sessionObject)
    {
        $sessionId = $sessionObject->getSessionid();
        $users = $sessionObject->getAdditional_testers();
        $users[] = $sessionObject->getUsername();
        if (in_array($_SESSION['username'], $users) || $_SESSION['superuser'] == 1 || $_SESSION['useradmin'] == 1) {
            return true;
        } else {

            $this->logger->debug("User not allowed to edit session " . $sessionId, __FILE__, __LINE__);
            return false;
        }
    }

    function getSessionIdFromVersionId($versionId, $mysqli_con)
    {
      $sql = "SELECT sessionid FROM mission WHERE versionid = "+$versionId;
      $result = dbHelper::sw_mysqli_execute($mysqli_con,$sql,__FILE__,__LINE__);
      $row = mysqli_fetch_row($result);
      return $row[0];
    }

    function getSessionStatus($versionid, $mysqli_con)
    {
        $sqlSelectSessionStatus = "";
        $sqlSelectSessionStatus .= "SELECT * ";
        $sqlSelectSessionStatus .= "FROM   mission_status ";
        $sqlSelectSessionStatus .= "WHERE  versionid = $versionid";
        $resultSessionStatus = dbHelper::sw_mysqli_execute($mysqli_con,$sqlSelectSessionStatus,__FILE__,__LINE__);
       if (!$resultSessionStatus) {
            $this->logger->error("Could not fetch sessionstatus for versionid ".$versionid,__FILE__,__LINE__);
           echo "error: Check log!!";
           die();
        }

        return mysqli_fetch_array($resultSessionStatus,MYSQLI_ASSOC);
    }
}

?>