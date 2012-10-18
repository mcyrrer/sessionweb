<?php
/**
 * Class to manage save of a sessionObject.
 * User: mcyrrer
 * Date: 2012-10-15
 * Time: 11:35
 * To change this template use File | Settings | File Templates.
 */
if (!isset($basePath)) {
    $basePath = "./";
}
include_once 'sessionObject.php';
include_once "config/db.php.inc";
include_once "classes/logging.php";
include_once "include/db.php";


class sessionObjectSave
{
    private $logger;

    function __construct($sessionid = null)
    {
        $this->logger = new logging();
    }

    /**
     * Save data to table mission
     * @param $missionDataArray data to save in an array with key = mysql table column.
     * @return bool true if success or false on failure
     */
    protected function saveToMissionTable($missionDataArray)
    {
        $createNewSession = false;
        $con = getMySqliConnection();

        $sql = "SELECT sessionid FROM mission WHERE sessionid=" . $missionDataArray['sessionid'];
        $resultSessionExist = mysqli_query($con, $sql);

        if (mysqli_num_rows($resultSessionExist) == 0) {
            $createNewSession = true;
        }

        if ($createNewSession) {
            $result = $this->saveMissionTable_Insert($missionDataArray, $con);

        } else {
            $result = $this->saveMissionTable_Update($missionDataArray, $con);
        }
        mysqli_close($con);
        return $result;
    }

    private function saveMissionTable_Insert($missionDataArray, $con)
    {

        $sqlInsert = "";
        $sqlInsert .= "INSERT INTO mission ";
        $sqlInsert .= "            (`sessionid`, ";
        $sqlInsert .= "             `title`, ";
        $sqlInsert .= "             `charter`, ";
        $sqlInsert .= "             `notes`, ";
        $sqlInsert .= "             `username`, ";
        $sqlInsert .= "             `sprintname`, ";
        //$sqlInsert .= "             `teamsprintname`, ";
        $sqlInsert .= "             `testenvironment`, ";
        $sqlInsert .= "             `software`, ";
        $sqlInsert .= "             `teamname`, ";
        $sqlInsert .= "             `lastupdatedby`, ";
        $sqlInsert .= "             `projects`, ";
        $sqlInsert .= "             `publickey`) ";
        $sqlInsert .= "VALUES      ('" . $missionDataArray["sessionid"] . "', ";
        $sqlInsert .= "             '" . mysql_real_escape_string($missionDataArray["title"]) . "', ";
        $sqlInsert .= "             '" . mysql_real_escape_string($missionDataArray["charter"]) . "', ";
        $sqlInsert .= "             '" . mysql_real_escape_string($missionDataArray["notes"]) . "', ";
        $sqlInsert .= "             '" . $_SESSION['username'] . "', ";
        if ($missionDataArray['sprintname'] == "") {
            $sqlInsert .= "             null, ";
        } else {
            $sqlInsert .= "             '" . mysql_real_escape_string($missionDataArray['sprintname']) . "', ";
        }
//        if ($missionDataArray['teamsprint'] == "") {
//            $sqlInsert .= "             null, ";
//        } else {
//
//            $sqlInsert .= "             '" . mysql_real_escape_string($missionDataArray['teamsprint']) . "', ";
//        }
        if ($missionDataArray['testenvironment'] == "") {
            $sqlInsert .= "             null, ";
        } else {
            $sqlInsert .= "             '" . mysql_real_escape_string($missionDataArray['testenvironment']) . "', ";
        }
        if ($missionDataArray['software'] == "") {
            $sqlInsert .= "             null, ";
        } else {
            $sqlInsert .= "             '" . mysql_real_escape_string($missionDataArray['software']) . "', ";
        }
        if ($missionDataArray['teamname'] == "") {
            $sqlInsert .= "             null, ";
        } else {
            $sqlInsert .= "             '" . mysql_real_escape_string($missionDataArray['teamname']) . "', ";
        }
        $sqlInsert .= "             '" . $missionDataArray['project'] . "', ";
        $sqlInsert .= "             '" . $_SESSION['username'] . "', ";
        $sqlInsert .= "             '" . $missionDataArray["publickey"] . "' ";
        $sqlInsert .= ") ";

        return $this->executeInsert($sqlInsert, $con, __FILE__, __LINE__);
    }

    private function saveMissionTable_Update($missionDataArray, $con)
    {
        if ($missionDataArray["title"] == "") {
            $date = date('m/d/Y h:i:s a', time());
            $missionDataArray["title"] = "Unnamed Session created at $date";
        }

        $sqlUpdate = "";
        $sqlUpdate .= "UPDATE mission ";
        $sqlUpdate .= "SET    `title` = '" . mysql_real_escape_string($missionDataArray["title"]) . "', ";
        $sqlUpdate .= "       `charter` = '" . mysql_real_escape_string($missionDataArray["charter"]) . "', ";
        $sqlUpdate .= "       `notes` = '" . mysql_real_escape_string($missionDataArray["notes"]) . "', ";
        $sqlUpdate .= "       `projects` = '" . mysql_real_escape_string($missionDataArray["project"]) . "', ";
        $sqlUpdate .= "       `lastupdatedby` = '" . $_SESSION['username'] . "', ";
        if (isset($missionDataArray['sprint']) && $missionDataArray['sprint'] != "") {
            $sqlUpdate .= "       `sprintname` = '" . mysql_real_escape_string($missionDataArray['sprint']) . "', ";
        } else {
            $sqlUpdate .= "       `sprintname` = null, ";
        }


        if ($missionDataArray['testenvironment'] == "") {
            $sqlUpdate .= "       `testenvironment` = null, ";
        } else {
            $sqlUpdate .= "       `testenvironment` = '" . mysql_real_escape_string($missionDataArray['testenvironment']) . "', ";
        }

        if ($missionDataArray['software'] == "") {
            $sqlUpdate .= "       `software` = null, ";
        } else {
            $sqlUpdate .= "       `software` = '" . mysql_real_escape_string($missionDataArray['software']) . "', ";
        }

        if ($missionDataArray['teamname'] == "") {
            $sqlUpdate .= "       `teamname` = null ";
        } else {
            $sqlUpdate .= "       `teamname` = '" . mysql_real_escape_string($missionDataArray['teamname']) . "' ";
        }
        $sqlUpdate .= "WHERE sessionid='" . $missionDataArray['sessionid'] . "'";

        return $this->executeUpdate($sqlUpdate, $con, __FILE__, __LINE__);
    }

    /**
     *  Save data to table misson_status
     * @param $missionDataArray data to save in an array with key = mysql table column.
     * @return bool true if success or false on failure
     */
    protected function saveToMissionStatusTable($missionDataArray)
    {
        $createNewSession = false;
        $con = getMySqliConnection();
        $sql = "SELECT versionid FROM mission_status WHERE versionid=" . $missionDataArray['versionid'];
        if (strcmp($missionDataArray['versionid'], "") != 0) {
            $resultSessionExist = mysqli_query($con, $sql);
            if (mysqli_num_rows($resultSessionExist) == 0) {
                $createNewSession = true;
            } else {
                $createNewSession = false;
            }
        } else {
            $createNewSession = true;
        }

        if ($createNewSession) {
            $this->saveToMissionStatusTable_Insert($missionDataArray, $con);
            echo "new session status";
        } else {
            $this->saveToMissionStatusTable_Update($missionDataArray, $con);
            echo "update session status";
        }
        mysqli_close($con);
        return true;

    }

    private function  saveToMissionStatusTable_Insert($missionDataArray, $con)
    {
        $sqlInsert = "";
        $sqlInsert .= "INSERT INTO mission_status ";
        $sqlInsert .= "            (`versionid`, ";
        $sqlInsert .= "             `executed`, ";
        $sqlInsert .= "             `closed`, ";
        $sqlInsert .= "             `debriefed`, ";
        $sqlInsert .= "             `masterdibriefed`, ";
        $sqlInsert .= "             `debriefed_timestamp`, ";
        $sqlInsert .= "             `executed_timestamp` ) ";
        $sqlInsert .= "VALUES      ('" . $missionDataArray['versionid'] . "', ";
        $sqlInsert .= "             '" . $missionDataArray['executed'] . "', ";
        $sqlInsert .= "             '" . $missionDataArray['closed'] . "', ";
        $sqlInsert .= "             '" . $missionDataArray['debriefed'] . "', ";
        $sqlInsert .= "             '" . $missionDataArray['masterdibriefed'] . "', ";
        if (strcasecmp($missionDataArray['debriefed_timestamp'], "NOW()")==0)
            $sqlInsert .= "             " . $missionDataArray['debriefed_timestamp'] . ", ";
        else
            $sqlInsert .= "             '" . $missionDataArray['debriefed_timestamp'] . "', ";
        if (strcasecmp($missionDataArray['executed_timestamp'], "NOW()")==0)
            $sqlInsert .= "             " . $missionDataArray['executed_timestamp'] . ")";
        else
            $sqlInsert .= "             '" . $missionDataArray['executed_timestamp'] . "')";

        return $this->executeInsert($sqlInsert, $con, __FILE__, __LINE__);
    }

    private function saveToMissionStatusTable_Update($missionDataArray, $con)
    {
        $sqlUpdate = "";
        $sqlUpdate .= "UPDATE mission_status ";
        $sqlUpdate .= "SET    `executed` = '" . $missionDataArray['executed'] . "', ";
        $sqlUpdate .= "       `debriefed` = '" . $missionDataArray['debriefed'] . "', ";
        $sqlUpdate .= "       `closed` = '" . $missionDataArray['closed'] . "', ";
        $sqlUpdate .= "       `masterdibriefed` = '" . $missionDataArray['masterdibriefed'] . "', ";
        if (strcasecmp($missionDataArray['debriefed_timestamp'], "NOW()")==0)
            $sqlUpdate .= "       `debriefed_timestamp` = " . $missionDataArray['debriefed_timestamp'] . " ";
        else
            $sqlUpdate .= "       `debriefed_timestamp` = '" . $missionDataArray['debriefed_timestamp'] . "' ";
        if (strcasecmp($missionDataArray['executed_timestamp'], "NOW()")==0)
            $sqlUpdate .= "       `executed_timestamp` = " . $missionDataArray['executed_timestamp'] . " ";
        else
            $sqlUpdate .= "       `executed_timestamp` = '" . $missionDataArray['executed_timestamp'] . "' ";
        $sqlUpdate .= "WHERE versionid='" . $missionDataArray['versionid'] . "'";

        return $this->executeUpdate($sqlUpdate, $con, __FILE__, __LINE__);
    }

    private
    function executeInsert($sqlInsert, $con, $file, $line)
    {
        $this->logger->sql($sqlInsert, $file, $line);

        $result = mysqli_query($con, $sqlInsert);

        if (!$result) {
            $this->logger->error(" Mysql Code:" . $sqlInsert, __FILE__, __LINE__);
            $this->logger->error(mysqli_error($con), __FILE__, __LINE__);
            return false;
        } else
            return true;
    }

    private
    function executeUpdate($sqlUpdate, $con, $file, $line)
    {
        $this->logger->sql($sqlUpdate, $file, $line);
        $result = mysqli_query($con, $sqlUpdate);

        if (!$result) {
            $this->logger->error(" Mysql Code:" . $sqlUpdate, __FILE__, __LINE__);
            $this->logger->error(" Mysql error:" . mysqli_error($con), __FILE__, __LINE__);
            return false;
        } else
            return true;
    }
}