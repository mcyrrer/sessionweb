<?php

/**
 * DEPRICATED!!! use sessionObject instead.
 * Class that gets all session data for one session
 */
class sessionReadObject
{
    var $sessionData = array();
    var $dbm;

    /**
     * Constructor that create the sessionObject that can be returned by getSession()
     * @param $sessionid sessionId for a session to create a object on
     */
    function __construct($sessionid)
    {
        $this->dbm = new dbHelper();
        $con = $this->dbm->connectToLocalDb();

        //mission data
        $sqlSelectSession = "SELECT * ";
        $sqlSelectSession .= "FROM   mission ";
        $sqlSelectSession .= "WHERE  sessionid = $sessionid";
        $result = $this->dbm->executeQuery($con, $sqlSelectSession);
        $data = mysqli_fetch_array($result);

        foreach ($data as $key => $value) {
            if (!is_int($key)) {
                $this->sessionData[$key] = $value;

            }
        }

        $versionid = $this->sessionData['versionid'];

        //mission areas
        $tmpAreaArray = array();
        $sqlSelectSessionStatus = "";
        $sqlSelectSessionStatus .= "SELECT areaname ";
        $sqlSelectSessionStatus .= "FROM   mission_areas ";
        $sqlSelectSessionStatus .= "WHERE  versionid = $versionid";
        $result = $this->dbm->executeQuery($con, $sqlSelectSessionStatus);
        while ($row = mysqli_fetch_array($result)) {

            $tmpAreaArray[] = $row['areaname'];

        }
        $this->sessionData['areas'] = $tmpAreaArray;

        //mission attachments
        $tmpAreaArray = array();
        $tmpAreaArray2 = array();
        $sql = "SELECT id,mission_versionid, filename, size, mimetype FROM `mission_attachments` WHERE `mission_versionid` = $sessionid";
        $result = $this->dbm->executeQuery($con, $sql);
        if ($result != null) {
            while ($row = mysqli_fetch_array($result)) {

                foreach ($row as $key => $value) {
                    if (!is_int($key)) {
                        $tmpAreaArray2[$key] = $value;
                    }
                    $tmpAreaArray[$row['id']] = $tmpAreaArray2;
                }
            }
        }
        $this->sessionData['attachments'] = $tmpAreaArray;

        //mission bugs
        $tmpAreaArray = array();
        $sqlSelect = "";
        $sqlSelect .= "SELECT * ";
        $sqlSelect .= "FROM   mission_bugs ";
        $sqlSelect .= "WHERE  versionid = $versionid";
        $result = $this->dbm->executeQuery($con, $sqlSelect);
        while ($row = mysqli_fetch_array($result)) {

            $tmpAreaArray[$row['bugid']] = $row['bugid'];

        }
        $this->sessionData['bugs'] = $tmpAreaArray;


        //mission requirements
        $tmpAreaArray = array();
        $sqlSelect = "";
        $sqlSelect .= "SELECT * ";
        $sqlSelect .= "FROM   mission_requirements ";
        $sqlSelect .= "WHERE  versionid = $versionid";
        $result = $this->dbm->executeQuery($con, $sqlSelect);
        while ($row = mysqli_fetch_array($result)) {

            $tmpAreaArray[$row['requirementsid']] = $row['requirementsid'];

        }
        $this->sessionData['requirements'] = $tmpAreaArray;

        //mission custom fields
        $tmpAreaArray = array();
        $tmpAreaArray2 = array();
        $sql = "select * from `mission_custom` WHERE versionid=$versionid";
        $result = $this->dbm->executeQuery($con, $sql);
        while ($row = mysqli_fetch_array($result)) {
            foreach ($row as $key => $value) {
                if (!is_int($key)) {
                    $tmpAreaArray2[$key] = $value;
                }
                $tmpAreaArray[$row['id']] = $tmpAreaArray2;
            }
        }
        $this->sessionData['custom_fields'] = $tmpAreaArray;

        //mission mission_debriefnotes
        $sql = "SELECT notes as debrief_notes, debriefedby ";
        $sql .= "FROM   mission_debriefnotes ";
        $sql .= "WHERE  versionid = $versionid";
        $result = $this->dbm->executeQuery($con, $sql);
        if (mysqli_num_rows($result) > 0) {
            $data = mysqli_fetch_array($result);

            foreach ($data as $key => $value) {
                if (!is_int($key)) {
                    $this->sessionData[$key] = $value;
                }
            }
        } else {
            $this->sessionData['debrief_notes'] = null;
            $this->sessionData['debriefedby'] = null;

        }

        //mission metrics
        $sql = "SELECT setup_percent,test_percent,bug_percent,opportunity_percent,duration_time,mood ";
        $sql .= "FROM   mission_sessionmetrics ";
        $sql .= "WHERE  versionid = $versionid";
        $result = $this->dbm->executeQuery($con, $sql);
        if (mysqli_num_rows($result) > 0) {
            $data = mysqli_fetch_array($result);

            foreach ($data as $key => $value) {
                if (!is_int($key)) {
                    $this->sessionData[$key] = $value;
                }
            }
        }

        //mission mission_sessionsconnections
        $tmpAreaArray = array();
        $sqlSelect = "";
        $sqlSelect .= "SELECT * ";
        $sqlSelect .= "FROM   mission_sessionsconnections ";
        $sqlSelect .= "WHERE  linked_to_versionid = $versionid";
        $result = $this->dbm->executeQuery($con, $sqlSelect);
        while ($row = mysqli_fetch_array($result)) {

            $tmpAreaArray[$row['linked_from_versionid']] = $row['linked_from_versionid'];

        }
        $this->sessionData['linked_from_session'] = $tmpAreaArray;

        $tmpAreaArray = array();
        $sqlSelect = "";
        $sqlSelect .= "SELECT * ";
        $sqlSelect .= "FROM   mission_sessionsconnections ";
        $sqlSelect .= "WHERE  linked_from_versionid = $versionid";
        $result = $this->dbm->executeQuery($con, $sqlSelect);
        while ($row = mysqli_fetch_array($result)) {

            $tmpAreaArray[$row['linked_to_versionid']] = $row['linked_to_versionid'];

        }
        $this->sessionData['linked_to_session'] = $tmpAreaArray;

        //mission mission_sessionsconnections
        $sql = "SELECT executed,debriefed,closed,masterdibriefed,executed_timestamp,debriefed_timestamp ";
        $sql .= "FROM   mission_status ";
        $sql .= "WHERE  versionid = $versionid";
        $result = $this->dbm->executeQuery($con, $sql);
        if (mysqli_num_rows($result) > 0) {
            $data = mysqli_fetch_array($result);

            foreach ($data as $key => $value) {
                if (!is_int($key)) {
                    $this->sessionData[$key] = $value;
                }
            }
        }

        //mission mission_testers
        $tmpArray = array();
        $sqlSelect = "";
        $sqlSelect .= "SELECT id,versionid,tester ";
        $sqlSelect .= "FROM   mission_testers ";
        $sqlSelect .= "WHERE  versionid = $versionid";
        $result = $this->dbm->executeQuery($con, $sqlSelect);
        while ($row = mysqli_fetch_array($result)) {

            $tmpArray[$row['tester']] = $row['tester'];

        }
        $this->sessionData['additional_testers'] = $tmpArray;


        ksort($this->sessionData);

        //print_r($tmpArray);

    }

    /**
     * Get all data for this object
     * @return array Multidimension array containing all session data for one session
     */
    public function getSession()
    {
        return $this->sessionData;
    }
}