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
            echo "NEW SESSION";
            $this->saveMissionTable_Insert($missionDataArray, $con);

        } else {
            echo "UPDATE SESSION";
            $this->saveMissionTable_Update($missionDataArray, $con);
        }
        mysqli_close($con);
        return true;
    }

    private function getLogMessagePrefix()
    {
        return "User:" . $_SESSION['username'] . " File:" . __FILE__ . ":" . __LINE__;
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
        $sqlInsert .= "             `publickey`) ";
        $sqlInsert .= "VALUES      ('".$missionDataArray["sessionid"]."', ";
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
        $sqlInsert .= "             '" . $_SESSION['username'] . "', ";
        $sqlInsert .= "             '" . $missionDataArray["publickey"] . "' ";
        $sqlInsert .= ") ";

        $this->logger->sql($sqlInsert,__FILE__);

        $result = mysqli_query($con,$sqlInsert);

        if (!$result) {
            $this->logger->error("Could not insert new session data to mission table",__FILE__);
            $this->logger->error(mysqli_error($result),__FILE__);
        }
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
        $this->logger->sql($sqlUpdate, __FILE__);
        $result = mysqli_query($con, $sqlUpdate);

        if (!$result) {
            $this->logger->error($this->getLogMessagePrefix() . " Mysql Code:" . $sqlUpdate);
            $this->logger->error($this->getLogMessagePrefix() . " Mysql error:" . mysqli_error($con));
            mysqli_close($con);
            return false;
        }
    }


}