<?php
require_once 'logging.php';
require_once 'dbHelper.php';
require_once 'QueryHelper.php';
require_once 'sessionObject.php';

class sessionHelper
{
    private $logger;
    private $queryHelper;
    private $dbm;

    function __construct()
    {
        $this->logger = new logging();
        $this->queryHelper = new QueryHelper();
        $this->dbm = new dbHelper();
    }

    function getUserName()
    {
        return $_SESSION['username'];
    }

    /**
     * @param null $userName if null then current users full name is returned.
     * @return mixed Full name of user
     */

    function getUserFullName($userName = null)
    {
        if ($userName == null)
            return $_SESSION['user'];
        else {

            $con = $this->dbm ->connectToLocalDb();

            $sqlSelect = "";
            $sqlSelect .= "SELECT fullname ";
            $sqlSelect .= "FROM   members ";
            $sqlSelect .= "WHERE username = '$userName' ";
            $sqlSelect .= "ORDER  BY fullname ASC";

            $result = $this->dbm ->executeQuery($con, $sqlSelect);

            $row = mysqli_fetch_row($result);
            return $row[0];
        }

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

    function isUserAllowedToDebriefSession($sessionObject)
    {
        $sessionId = $sessionObject->getSessionid();
        $users = $sessionObject->getAdditional_testers();
        $users[] = $sessionObject->getUsername();
        if ($this->isSuperUser() == 1 || $this->isAdmin() == 1) {
            return true;
        } else {
            $this->logger->debug("User not allowed to debrief session " . $sessionId, __FILE__, __LINE__);
            return false;
        }
    }

    function getSessionIdFromVersionId($versionId, $mysqli_con)
    {
        $sql = "SELECT sessionid FROM mission WHERE versionid = " . $versionId;
        $result = $this->dbm->executeQuery($mysqli_con, $sql);
        $row = mysqli_fetch_row($result);

        return $row[0];
    }

    function getVersionIdFromSessionId($sessionId, $mysqli_con)
    {
        $sql = "SELECT versionid FROM mission WHERE sessionid = " . $sessionId;
        $result = $this->dbm->executeQuery($mysqli_con, $sql);

        $row = mysqli_fetch_row($result);

        return $row[0];
    }

    function getSessionStatus($versionid, $mysqli_con)
    {
        $sqlSelectSessionStatus = "";
        $sqlSelectSessionStatus .= "SELECT * ";
        $sqlSelectSessionStatus .= "FROM   mission_status ";
        $sqlSelectSessionStatus .= "WHERE  versionid = $versionid";
        $resultSessionStatus = $this->dbm->executeQuery($mysqli_con, $sqlSelectSessionStatus);;
        if (!$resultSessionStatus) {
            $this->logger->error("Could not fetch sessionstatus for versionid " . $versionid, __FILE__, __LINE__);
            echo "error: Check log!!";
            die();
        }

        return mysqli_fetch_array($resultSessionStatus, MYSQLI_ASSOC);
    }

    /**
     * Prints percent (belongs to a HTML select item) to screen. E.g 5,10,15,20...
     *
     */
    function echoPercentSelection($htmlId, $htmlClass = "", $htmlName = "")
    {
        echo '<select id="' . $htmlId . '" class="' . $htmlClass . '" name="' . $htmlName . '">';
        for ($index = 0; $index <= 100; $index = $index + 5) {
            echo "<option>$index</option>\n";
        }
        echo '</select>';
    }

    /**
     * Prints duration option (belongs to a HTML select item) to screen
     *
     */
    function echoDurationSelection($htmlId, $htmlClass = "", $htmlName = "")
    {
        echo '<select id="' . $htmlId . '" class="' . $htmlClass . '" name="' . $htmlName . '">';
        for ($index = 15; $index <= 480; $index = $index + 15) {
            echo "<option>$index</option>\n";
        }
        echo '</select>';

    }


    function isAdmin()
    {
        if ($_SESSION['useradmin'] == 1) {
            return true;
        } else {
            return false;
        }
    }

    function isSuperUser()
    {
        if ($_SESSION['useradmin'] == 1 || $_SESSION['superuser'] == 1) {
            return true;
        } else {
            return false;
        }
    }

    function requirementsExist(sessionObject $so)
    {
        if (count($so->getRequirements()) > 0) {
            return true;
        }
    }


    /**
     * ToDo... add this to deltesession....
     * @param sessionObject $so
     *
     *
     */


    function updateRemoteStatusForCharter(sessionObject $so)
    {
        if ($this->requirementsExist($so)) {
            $patToWebRoot = $this->getRootFolder("");

            if (file_exists($patToWebRoot . 'include/customfunctions.php.inc')) {
                require_once($patToWebRoot . 'include/customfunctions.php.inc');

                $reqArray = $so->getRequirements();
                foreach ($reqArray as $requirementIdentifier) {
                    updateCharterStatusOnRemoteServer($so->getSessionid(), $requirementIdentifier, $so->getTitle(), $so->getFullUsername(), $so->getStatusAsText(), $so->getUpdated());
                    $this->logger->debug("Updated remote server with new information about session " . $so->getSessionid(), __FILE__, __LINE__);
                }
            } else {
                $this->logger->debug("Could not find customfunctions.php.inc" . $so->getSessionid(), __FILE__, __LINE__);

            }
        } else {
            $this->logger->debug("No req exists, will not update remote server...." . $so->getSessionid(), __FILE__, __LINE__);
        }

    }

    function updateRemoteStatusForCharterSetDeleted(sessionObject $so, $requirementIdentifier)
    {
        if ($this->requirementsExist($so)) {
            $patToWebRoot = $this->getRootFolder("");

            if (file_exists($patToWebRoot . 'include/customfunctions.php.inc')) {
                require_once($patToWebRoot . 'include/customfunctions.php.inc');

                updateCharterStatusOnRemoteServer($so->getSessionid(), $requirementIdentifier, $so->getTitle(), $so->getFullUsername(), "Deleted/unlinked", $so->getUpdated());
                $this->logger->debug("Updated remote server with new information about session(Requirement unlinked) " . $so->getSessionid(), __FILE__, __LINE__);

            } else {
                $this->logger->debug("Could not find customfunctions.php.inc" . $so->getSessionid(), __FILE__, __LINE__);

            }
        } else {
            $this->logger->debug("No req exists, will not update remote server...." . $so->getSessionid(), __FILE__, __LINE__);
        }

    }

    function getRootFolder($pathToRoot)
    {
        if (file_exists($pathToRoot . 'about.php')) {
            return "./" . $pathToRoot;
        } else {
            $pathToRoot .= "../";
            $pathToRoot = ValidateSession::checkIfRootFolder($pathToRoot);
        }
        return $pathToRoot;
    }
}

?>