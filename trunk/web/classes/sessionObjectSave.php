<?php
require_once 'dbHelper.php';

/**
 * Class to manage save of a sessionObject. Should only be used by a sessionObject
 * $file and $line is the filename __FILE__ and the linenumber __LINE__ that the logger will use to create traceability
 * User: mcyrrer
 * Date: 2012-10-15
 * Time: 11:35
 */
if (!isset($basePath)) {
    $basePath = "./";
}
$rootPath = checkIfRootFolder("");
include_once 'sessionObject.php';
/** @noinspection PhpIncludeInspection */
include_once $rootPath . "/config/db.php.inc";
/** @noinspection PhpIncludeInspection */
include_once $rootPath . "/classes/logging.php";
/** @noinspection PhpIncludeInspection */
include_once $rootPath . "/classes/dbHelper.php";
include_once $rootPath . "/classes/sessionHelper.php";
/** @noinspection PhpIncludeInspection */
include_once $rootPath . "/include/db.php";


/** @noinspection PhpUndefinedClassInspection */
class sessionObjectSave
{
    private $logger;
    private $dbHelper;
    private $sessionHelper;

    /**
     * @param null $sessionid
     */
    function __construct($sessionid = null)
    {
        $this->logger = new logging();
        $this->dbHelper = new dbHelper();
        $this->sessionHelper = new sessionHelper();
    }

    /**
     * Save data to table mission
     * @param $missionDataArray sessionobject data to save in an array with key = mysql table column.
     * @return bool true if success or false on failure
     */
    protected function saveToMissionTable($missionDataArray)
    {
        $createNewSession = false;
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $con = getMySqliConnection();

        $sql = "SELECT sessionid FROM mission WHERE sessionid=" . $missionDataArray['sessionid'];

        /** @noinspection PhpVoidFunctionResultUsedInspection */
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

    /**
     * Create a new item in database
     * @param $missionDataArray
     * @param $con
     * @return bool
     */
    private function saveMissionTable_Insert($missionDataArray, $con)
    {

        $this->logger->debug("USERNAME: " . $missionDataArray["username"], __FILE__, __LINE__);

        $sqlInsert = "";
        $sqlInsert .= "INSERT INTO mission ";
        $sqlInsert .= "            (`sessionid`, ";
        $sqlInsert .= "             `title`, ";
        $sqlInsert .= "             `charter`, ";
        $sqlInsert .= "             `notes`, ";
        $sqlInsert .= '             `username`, ';
        $sqlInsert .= "             `sprintname`, ";
        $sqlInsert .= "             `testenvironment`, ";
        $sqlInsert .= "             `software`, ";
        $sqlInsert .= "             `teamname`, ";
        $sqlInsert .= "             `lastupdatedby`, ";
        $sqlInsert .= "             `project`, ";
        $sqlInsert .= "             `publickey`) ";
        $sqlInsert .= "VALUES      ('" . $missionDataArray["sessionid"] . "', ";
        $sqlInsert .= "             '" . dbHelper::escape($con, $missionDataArray["title"]) . "', ";
        $sqlInsert .= "             '" . dbHelper::escape($con, $missionDataArray["charter"]) . "', ";
        $sqlInsert .= "             '" . dbHelper::escape($con, $missionDataArray["notes"]) . "', ";
        $sqlInsert .= "             '" . dbHelper::escape($con, $missionDataArray["username"]) . "', ";
        if ($missionDataArray['sprintname'] == "") {
            $sqlInsert .= "             null, ";
        } else {
            $sqlInsert .= "             '" . dbHelper::escape($con, $missionDataArray['sprintname']) . "', ";
        }
        if ($missionDataArray['testenvironment'] == "") {
            $sqlInsert .= "             null, ";
        } else {
            $sqlInsert .= "             '" . dbHelper::escape($con, $missionDataArray['testenvironment']) . "', ";
        }
        if ($missionDataArray['software'] == "") {
            $sqlInsert .= "             null, ";
        } else {
            $sqlInsert .= "             '" . dbHelper::escape($con, $missionDataArray['software']) . "', ";
        }
        if ($missionDataArray['teamname'] == "") {
            $sqlInsert .= "             null, ";
        } else {
            $sqlInsert .= "             '" . dbHelper::escape($con, $missionDataArray['teamname']) . "', ";
        }
        $sqlInsert .= "             '" . $missionDataArray['project'] . "', ";
        $sqlInsert .= "             '" . dbHelper::escape($con, $missionDataArray["username"]) . "', ";
        $sqlInsert .= "             '" . $missionDataArray["publickey"] . "' ";
        $sqlInsert .= ") ";

        return $this->executeInsert($sqlInsert, $con, __FILE__, __LINE__);
    }

    /**
     * Update an item in database
     * @param $missionDataArray
     * @param $con
     * @return bool
     */
    private function saveMissionTable_Update($missionDataArray, $con)
    {
        if ($missionDataArray["title"] == "") {
            $date = date('m/d/Y h:i:s a', time());
            $missionDataArray["title"] = "Unnamed Session created at $date";
        }

        $sqlUpdate = "";
        $sqlUpdate .= "UPDATE mission ";
        $sqlUpdate .= "SET    `title` = '" . dbHelper::escape($con, $missionDataArray["title"]) . "', ";
        $sqlUpdate .= "       `charter` = '" . dbHelper::escape($con, $missionDataArray["charter"]) . "', ";
        $sqlUpdate .= "       `notes` = '" . dbHelper::escape($con, $missionDataArray["notes"]) . "', ";
        $sqlUpdate .= "       `project` = '" . dbHelper::escape($con, $missionDataArray["project"]) . "', ";
        $sqlUpdate .= "       `lastupdatedby` = '" . dbHelper::escape($con, $missionDataArray["username"]) . "', ";
        if (isset($missionDataArray['sprintname']) && $missionDataArray['sprintname'] != "") {
            $sqlUpdate .= "       `sprintname` = '" . dbHelper::escape($con, $missionDataArray['sprintname']) . "', ";
        } else {
            $sqlUpdate .= "       `sprintname` = null, ";
        }


        if ($missionDataArray['testenvironment'] == "") {
            $sqlUpdate .= "       `testenvironment` = null, ";
        } else {
            $sqlUpdate .= "       `testenvironment` = '" . dbHelper::escape($con, $missionDataArray['testenvironment']) . "', ";
        }

        if ($missionDataArray['software'] == "") {
            $sqlUpdate .= "       `software` = null, ";
        } else {
            $sqlUpdate .= "       `software` = '" . dbHelper::escape($con, $missionDataArray['software']) . "', ";
        }

        if ($missionDataArray['teamname'] == "") {
            $sqlUpdate .= "       `teamname` = null ";
        } else {
            $sqlUpdate .= "       `teamname` = '" . dbHelper::escape($con, $missionDataArray['teamname']) . "' ";
        }
        $sqlUpdate .= "WHERE sessionid='" . $missionDataArray['sessionid'] . "'";

        return $this->executeUpdate($sqlUpdate, $con, __FILE__, __LINE__);
    }

    /**
     *  Save data to table misson_status
     * @param $missionDataArray sessionobject data to save in an array with key = mysql table column.
     * @return bool true if success or false on failure
     */
    protected function saveToMissionStatusTable($missionDataArray)
    {
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $con = getMySqliConnection();
        $sql = "SELECT versionid FROM mission_status WHERE versionid=" . $missionDataArray['versionid'];
//        $this->logger->debug($sql,__FILE__,__LINE__);

        if (strcmp($missionDataArray['versionid'], "") != 0) {
            /** @noinspection PhpVoidFunctionResultUsedInspection */
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
//            $this->logger->debug("New session status",__FILE__,__LINE__);
//            echo "new session status";
        } else {
            $this->saveToMissionStatusTable_Update($missionDataArray, $con);
//            $this->logger->debug("Updated of session status",__FILE__,__LINE__);

//            echo "update session status";
        }
        mysqli_close($con);
        return true;

    }

    /**
     * Create a new item in database
     * @param $missionDataArray
     * @param $con
     * @return bool
     */
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
        if (strcasecmp($missionDataArray['debriefed_timestamp'], "NOW()") == 0)
            $sqlInsert .= "             " . $missionDataArray['debriefed_timestamp'] . ", ";
        else
            $sqlInsert .= "             '" . $missionDataArray['debriefed_timestamp'] . "', ";
        if (strcasecmp($missionDataArray['executed_timestamp'], "NOW()") == 0)
            $sqlInsert .= "             " . $missionDataArray['executed_timestamp'] . ")";
        else
            $sqlInsert .= "             '" . $missionDataArray['executed_timestamp'] . "')";

        return $this->executeInsert($sqlInsert, $con, __FILE__, __LINE__);
    }

    /**
     * Update an item in database
     * @param $missionDataArray
     * @param $con
     * @return bool
     */
    private function saveToMissionStatusTable_Update($missionDataArray, $con)
    {
        $sqlUpdate = "";
        $sqlUpdate .= "UPDATE mission_status ";
        $sqlUpdate .= "SET    `executed` = '" . $missionDataArray['executed'] . "', ";
        $sqlUpdate .= "       `debriefed` = '" . $missionDataArray['debriefed'] . "', ";
        $sqlUpdate .= "       `closed` = '" . $missionDataArray['closed'] . "', ";
        $sqlUpdate .= "       `masterdibriefed` = '" . $missionDataArray['masterdibriefed'] . "', ";
        if (strcasecmp($missionDataArray['debriefed_timestamp'], "NOW()") == 0)
            $sqlUpdate .= "       `debriefed_timestamp` = " . $missionDataArray['debriefed_timestamp'] . ", ";
        else
            $sqlUpdate .= "       `debriefed_timestamp` = '" . $missionDataArray['debriefed_timestamp'] . "', ";
        if (strcasecmp($missionDataArray['executed_timestamp'], "NOW()") == 0)
            $sqlUpdate .= "       `executed_timestamp` = " . $missionDataArray['executed_timestamp'] . " ";
        else
            $sqlUpdate .= "       `executed_timestamp` = '" . $missionDataArray['executed_timestamp'] . "' ";
        $sqlUpdate .= "WHERE versionid='" . $missionDataArray['versionid'] . "'";

        return $this->executeUpdate($sqlUpdate, $con, __FILE__, __LINE__);
    }


    /**
     *  Save data to table misson_area
     * @param $missionDataArray sessionobject data to save in an array with key = mysql table column.
     * @return bool true if success or false on failure
     */
    protected function saveToMissionAreaTable($missionDataArray)
    {
        $this->logger->arraylog($missionDataArray, __FILE__, __LINE__);
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $con = getMySqliConnection();
        $result = $this->saveToMissionStatusTable_Execute($missionDataArray, $con);
        mysqli_close($con);
        return $result;
    }

    /**
     * Update/Insert an item in database
     * @param $missionDataArray
     * @param $con
     * @return bool
     */
    private function saveToMissionStatusTable_Execute($missionDataArray, $con)
    {
        $versionId = $missionDataArray['versionid'];
        $sqlDelete = "DELETE FROM mission_areas WHERE mission_areas.versionid = $versionId";
        $this->executeDelete($sqlDelete, $con, __FILE__, __LINE__);
        if (is_array($missionDataArray['areas'])) {
            foreach ($missionDataArray['areas'] as $area) {
                $sqlInsert = "INSERT INTO mission_areas (versionid, areaname) VALUES ('$versionId', '$area')";
                $this->executeInsert($sqlInsert, $con, __FILE__, __LINE__);
            }
        }
        return true;
    }


    /**
     *  Save data to table misson_bugs
     * @param $missionDataArray sessionobject data to save in an array
     * @return bool true if success or false on failure
     */
    protected function saveToMissionBugsTable($missionDataArray)
    {
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $con = getMySqliConnection();
        $result = $this->saveToMissionBugsTable_Execute($missionDataArray, $con);
        mysqli_close($con);
        return $result;
    }

    /**
     * Update/Insert an item in database
     * @param $missionDataArray
     * @param $con
     * @return bool
     */
    private function saveToMissionBugsTable_Execute($missionDataArray, $con)
    {
        $versionId = $missionDataArray['versionid'];
        $sqlDelete = "DELETE FROM mission_bugs WHERE mission_bugs.versionid = $versionId";
        $this->executeDelete($sqlDelete, $con, __FILE__, __LINE__);
        if (is_array($missionDataArray['bugs'])) {
            foreach ($missionDataArray['bugs'] as $bug) {
                $sqlInsert = "INSERT INTO mission_bugs (versionid, bugid) VALUES ('$versionId', '$bug')";
                $this->executeInsert($sqlInsert, $con, __FILE__, __LINE__);
            }
        }
        return true;
    }

    /**
     *  Save data to table mission_requirements
     * @param $missionDataArray sessionobject data to save in an array
     * @return bool true if success or false on failure
     */
    protected function saveToMissionRequirementsTable($missionDataArray)
    {
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $con = getMySqliConnection();
        $result = $this->saveToMissionRequirementsTable_Execute($missionDataArray, $con);
        mysqli_close($con);
        return $result;
    }

    /**
     * Update/Insert an item in database
     * @param $missionDataArray
     * @param $con
     * @return bool
     */
    private function saveToMissionRequirementsTable_Execute($missionDataArray, $con)
    {
        $versionId = $missionDataArray['versionid'];
        $sqlDelete = "DELETE FROM mission_requirements WHERE mission_requirements.versionid = $versionId";
        $this->executeDelete($sqlDelete, $con, __FILE__, __LINE__);
        if (is_array($missionDataArray['requirements'])) {
            foreach ($missionDataArray['requirements'] as $req) {
                $sqlInsert = "INSERT INTO mission_requirements (versionid, requirementsid) VALUES ('$versionId', '$req')";
                $this->executeInsert($sqlInsert, $con, __FILE__, __LINE__);
            }
        }
        return true;
    }

    protected function saveToMissionMetricsTable($missionDataArray)
    {
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $con = getMySqliConnection();
        $result = $this->saveToMissionMetricsTable_Execute($missionDataArray, $con);
        mysqli_close($con);
        return $result;
    }

    private function saveToMissionMetricsTable_Execute($missionDataArray, $con)
    {
        $versionId = $missionDataArray['versionid'];
        $sqlDelete = "DELETE FROM mission_sessionmetrics WHERE mission_sessionmetrics.versionid = $versionId";
        $this->executeDelete($sqlDelete, $con, __FILE__, __LINE__);

        $setup = $missionDataArray['setup_percent'];
        $test = $missionDataArray['test_percent'];
        $bug = $missionDataArray['bug_percent'];
        $opp = $missionDataArray['opportunity_percent'];
        $duration = $missionDataArray['duration_time'];
        $mood = $missionDataArray['mood'];

        $this->executeDelete($sqlDelete, $con, __FILE__, __LINE__);

        $sqlInsert = "";
        $sqlInsert .= "INSERT INTO mission_sessionmetrics ";
        $sqlInsert .= "            (versionid, ";
        $sqlInsert .= "             setup_percent, ";
        $sqlInsert .= "             test_percent, ";
        $sqlInsert .= "             bug_percent, ";
        $sqlInsert .= "             opportunity_percent, ";
        $sqlInsert .= "             duration_time, ";
        $sqlInsert .= "             mood) ";
        $sqlInsert .= "VALUES      ('$versionId', ";
        $sqlInsert .= "             '$setup', ";
        $sqlInsert .= "             '$test', ";
        $sqlInsert .= "             '$bug', ";
        $sqlInsert .= "             '$opp', ";
        $sqlInsert .= "             '$duration', ";
        $sqlInsert .= "             '$mood')";

        $this->executeInsert($sqlInsert, $con, __FILE__, __LINE__);

        return true;
    }

    protected function saveToMissionDebriefNotesTable($missionDataArray)
    {
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $con = getMySqliConnection();
        $result = $this->saveToMissionDebriefNotesTable_Execute($missionDataArray, $con);
        mysqli_close($con);
        return $result;
    }

    private function saveToMissionDebriefNotesTable_Execute($missionDataArray, $con)
    {
        $versionId = $missionDataArray['versionid'];
        $sqlDelete = "DELETE FROM mission_debriefnotes WHERE mission_debriefnotes.versionid = $versionId";
        $this->executeDelete($sqlDelete, $con, __FILE__, __LINE__);

        $notes = $missionDataArray['notes'];
        $debriefedBy = $missionDataArray['debriefedby'];
        $this->executeDelete($sqlDelete, $con, __FILE__, __LINE__);
        $sqlInsert = "INSERT INTO mission_debriefnotes (versionid, notes, debriefedby) VALUES ('$versionId', '$notes', '$debriefedBy')";
        $this->executeInsert($sqlInsert, $con, __FILE__, __LINE__);

        return true;
    }


    /**
     * Execute the insert to Database
     * @param $sqlInsert
     * @param $con
     * @param $file
     * @param $line
     * @return bool True on success False on failure
     */
    private function executeInsert($sqlInsert, $con, $file, $line)
    {
        $this->logger->debug($sqlInsert, $file, $line);

        return $this->dbHelper->sw_mysqli_execute($con, $sqlInsert, __FILE__, __LINE__);

//        /** @noinspection PhpVoidFunctionResultUsedInspection */
//        $result = $this->dbHelper->sw_mysqli_execute($con, $sqlInsert, $file, $line);
//        //$result = mysqli_query($con, $sqlInsert);
//        if (!$result) {
//            $this->logger->error(" Mysql Code:" . $sqlInsert, $file, $line);
//            /** @noinspection PhpVoidFunctionResultUsedInspection */
//            $this->logger->error(mysqli_error($con), $file, $line);
//            $this->dieAndPrintErrorMessage();
//            return false;
//        } else
//            return true;
    }

    /**
     * @param $sqlUpdate
     * @param $con
     * @param $file
     * @param $line
     * @return bool True on success False on failure
     */
    private function executeUpdate($sqlUpdate, $con, $file, $line)
    {
        $this->logger->sql($sqlUpdate, $file, $line);
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $result = $this->dbHelper->sw_mysqli_execute($con, $sqlUpdate, $file, $line);
//        $result = mysqli_query($con, $sqlUpdate);
        if (!$result) {
            $this->logger->error(" Mysql Code:" . $sqlUpdate, $file, $line);
            $this->logger->error(" Mysql error:" . mysqli_error($con), $file, $line);
            $this->dieAndPrintErrorMessage();
            return false;
        } else
            return true;
    }

    /**
     * Execute delete to Database
     * @param $sqlDelete
     * @param $con
     * @param $file
     * @param $line
     * @return bool True on success False on failure
     */
    private function executeDelete($sqlDelete, $con, $file, $line)
    {
        $this->logger->sql($sqlDelete, $file, $line);
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $result = $this->dbHelper->sw_mysqli_execute($con, $sqlDelete, $file, $line);
        //$result = mysqli_query($con, $sqlDelete);
        if (!$result) {
            $this->logger->error(" Mysql Code:" . $sqlDelete, $file, $line);
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            $this->logger->error(mysqli_error($con), $file, $line);
            $this->dieAndPrintErrorMessage();
            return false;
        } else
            return true;
    }

    /**
     * Print an error message to end user and then die
     */
    private function dieAndPrintErrorMessage()
    {
        die("SQL ERROR. Ask admin to check logfile for more information");
    }

}