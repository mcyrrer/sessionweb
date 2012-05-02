<?php

class session
{
    var $sessionData = array();

    function __construct($sessionid)
    {
        $con = getMySqliConnection();

        //mission data
        $sqlSelectSession = "SELECT * ";
        $sqlSelectSession .= "FROM   mission ";
        $sqlSelectSession .= "WHERE  sessionid = $sessionid";
        $result = mysqli_query($con, $sqlSelectSession);
        $data = mysqli_fetch_array($result);

        foreach ($data as $key => $value)
        {
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
        $result = mysqli_query($con, $sqlSelectSessionStatus);
        while ($row = mysqli_fetch_array($result))
        {

            $tmpAreaArray[] = $row['areaname'];

        }
        $this->sessionData['areas'] = $tmpAreaArray;

        //mission attachments
        $tmpAreaArray = array();
        $sql = "SELECT id,mission_versionid, filename, size, mimetype FROM `mission_attachments` WHERE `mission_versionid` = $sessionid";
        $result = mysqli_query($con, $sql);
        while ($row = mysqli_fetch_array($result))
        {

            foreach ($row as $key => $value)
            {
                if (!is_int($key)) {
                    $tmpAreaArray2[$key] = $value;
                }
                $tmpAreaArray[$tmpAreaArray2['id']] = $tmpAreaArray2;
            }
        }
        $this->sessionData['attachments'] = $tmpAreaArray;

        //mission bugs
        $tmpAreaArray = array();
        $sqlSelect = "";
        $sqlSelect .= "SELECT * ";
        $sqlSelect .= "FROM   mission_bugs ";
        $sqlSelect .= "WHERE  versionid = $versionid";
        $result = mysqli_query($con, $sqlSelect);
        while ($row = mysqli_fetch_array($result))
        {

            $tmpAreaArray[$row['bugid']] = $row['bugid'];

        }
        $this->sessionData['bugs'] = $tmpAreaArray;


        //mission requirements
        $tmpAreaArray = array();
        $sqlSelect = "";
        $sqlSelect .= "SELECT * ";
        $sqlSelect .= "FROM   mission_requirements ";
        $sqlSelect .= "WHERE  versionid = $versionid";
        $result = mysqli_query($con, $sqlSelect);
        while ($row = mysqli_fetch_array($result))
        {

            $tmpAreaArray[$row['requirementsid']] = $row['requirementsid'];

        }
        $this->sessionData['requirements'] = $tmpAreaArray;

        //mission custom fields
        $tmpAreaArray = array();
        $tmpAreaArray2 = array();
        $sql = "select * from `mission_custom` WHERE versionid=$versionid";
        $result = mysqli_query($con, $sql);
        while ($row = mysqli_fetch_array($result))
        {
            foreach ($row as $key => $value)
            {
                if (!is_int($key)) {
                    $tmpAreaArray2[$key] = $value;
                }
                $tmpAreaArray[$tmpAreaArray2['id']] = $tmpAreaArray2;
            }
        }
        $this->sessionData['custom_fields'] = $tmpAreaArray;

        //mission mission_debriefnotes
        $sql = "SELECT notes as debrief_notes, debriefedby ";
        $sql .= "FROM   mission_debriefnotes ";
        $sql .= "WHERE  versionid = $versionid";
        $result = mysqli_query($con, $sql);
        if (mysqli_num_rows($result) > 0) {
            $data = mysqli_fetch_array($result);

            foreach ($data as $key => $value)
            {
                if (!is_int($key)) {
                    $this->sessionData[$key] = $value;
                }
            }
        }
        else
        {
            $this->sessionData['debrief_notes'] = null;
            $this->sessionData['debriefedby'] = null;

        }

        //mission metrics
        $sql = "SELECT setup_percent,test_percent,bug_percent,opportunity_percent,duration_time,mood ";
        $sql .= "FROM   mission_sessionmetrics ";
        $sql .= "WHERE  versionid = $versionid";
        $result = mysqli_query($con, $sql);
        if (mysqli_num_rows($result) > 0) {
            $data = mysqli_fetch_array($result);

            foreach ($data as $key => $value)
            {
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
        $result = mysqli_query($con, $sqlSelect);
        while ($row = mysqli_fetch_array($result))
        {

            $tmpAreaArray[$row['linked_from_versionid']] = $row['linked_from_versionid'];

        }
        $this->sessionData['linked_from_session'] = $tmpAreaArray;

        $tmpAreaArray = array();
        $sqlSelect = "";
        $sqlSelect .= "SELECT * ";
        $sqlSelect .= "FROM   mission_sessionsconnections ";
        $sqlSelect .= "WHERE  linked_from_versionid = $versionid";
        $result = mysqli_query($con, $sqlSelect);
        while ($row = mysqli_fetch_array($result))
        {

            $tmpAreaArray[$row['linked_to_versionid']] = $row['linked_to_versionid'];

        }
        $this->sessionData['linked_to_session'] = $tmpAreaArray;

        //mission mission_sessionsconnections
        $sql = "SELECT executed,debriefed,masterdibriefed,executed_timestamp,debriefed_timestamp ";
        $sql .= "FROM   mission_status ";
        $sql .= "WHERE  versionid = $versionid";
        $result = mysqli_query($con, $sql);
        if (mysqli_num_rows($result) > 0) {
            $data = mysqli_fetch_array($result);

            foreach ($data as $key => $value)
            {
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
        $result = mysqli_query($con, $sqlSelect);
        while ($row = mysqli_fetch_array($result))
        {

            $tmpArray[$row['tester']] = $row['tester'];

        }
        $this->sessionData['additional_testers'] = $tmpArray;


        ksort($this->sessionData);
        mysqli_close($con);
    }

    public function getSession()
    {
        return $this->sessionData;
    }
}