får dufår dfasdsdf<fdsaasd<?php

require_once 'sessionReadObject.php';
require_once '../include/session_database_functions.php.inc';
/**
 *
 */
class sessionObject
{
    //var $oSessionData = null;
    var $additional_testers;        //Array
    var $areas;                     //Array
    var $attachments;               //Array
    var $bug_percent;               //Boolean 0,1
    var $bugs;                      //Array
    var $charter;                   //Text
    var $closed;                    //Boolean 0,1
    var $custom_fields;             //Array
    var $debrief_notes;             //Text
    var $debriefed;                 //Boolean 0,1
    var $debriefed_timestamp;       //Mysql TimeStamp
    var $debriefedby;               ////Text
    var $depricated;                //Boolean 0,1
    var $duration_time;             //Int
    var $executed;                  //Boolean 0,1
    var $executed_timestamp;        //Mysql TimeStamp
    var $lastupdatedby;             //Text
    var $linked_from_session;       //Array
    var $linked_to_session;         //Array
    var $masterdibriefed;           //Boolean 0,1
    var $mood;                      //Int 0-4
    var $notes;                     //Text
    var $opportunity_percent;       //Int
    var $projects;                  //Text
    var $publickey;                 //Text
    var $requirements;              //Array
    var $sessionid;                 //Int
    var $setup_percent;             //Int
    var $software;                  //Text
    var $sprintname;                //Text
    var $teamname;                  //Text
    var $teamsprintname;            //Text
    var $test_percent;              //Int
    var $testenvironment;           //Text
    var $title;                     //Text
    var $updated;                   //Mysql TimeStamp
    var $username;                  //Text
    var $versionid;                 //Int

    /**
     * @param null $sessionid sessionid to create a object of, if null then create a empty one.
     */
    function __construct($sessionid = null)
    {
        if ($sessionid == null) {
            $this->oSessionData = $this->createEmptySessionObject();
        } else {

            $this->oSessionData = new sessionReadObject($sessionid);

        }
    }

    /**
     * Create a sessionObject that is empty
     */
    function createEmptySessionObject()
    {

        $this->generatePublickey();
        $this->generateSessionid();
    }

    function getSessionData($sessionid)
    {
        $con = getMySqliConnection();

        //mission data
        $sqlSelectSession = "SELECT * ";
        $sqlSelectSession .= "FROM   mission ";
        $sqlSelectSession .= "WHERE  sessionid = $sessionid";
        $result = mysqli_query($con, $sqlSelectSession);
        $data = mysqli_fetch_array($result);

        $this->setVersionid($data['versionid']);
        $this->setSessionid($data['sessionid']);
        $this->setTitle($data['title']);
        $this->setCharter($data['charter']);
        $this->setNotes($data['notes']);
        $this->setUsername($data['username']);
        $this->setTeamname($data['teamname']);
        $this->setSprintname($data['sprintname']);
        $this->setDepricated($data['depricated']);
        $this->setUpdated($data['updated']);
        $this->setPublickey($data['publickey']);
        $this->setTestenvironment($data['testenvironment']);
        $this->setSoftware($data['software']);
        $this->setLastupdatedby($data['lastupdatedby']);


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
        $this->setAreas($tmpAreaArray);

        //mission attachments
        $tmpAreaArray = array();
        $tmpAreaArray2 = array();
        $sql = "SELECT id,mission_versionid, filename, size, mimetype FROM `mission_attachments` WHERE `mission_versionid` = $sessionid";
        $result = mysqli_query($con, $sql);
        while ($row = mysqli_fetch_array($result))         {

            foreach ($row as $key => $value)
            {
                if (!is_int($key)) {
                    $tmpAreaArray2[$key] = $value;
                }
                $tmpAreaArray[$row['id']] = $tmpAreaArray2;
            }
        }
        $this->setAttachments($tmpAreaArray);

        //mission bugs
        $tmpAreaArray = array();
        $sqlSelect = "";
        $sqlSelect .= "SELECT * ";
        $sqlSelect .= "FROM   mission_bugs ";
        $sqlSelect .= "WHERE  versionid = $versionid";
        $result = mysqli_query($con, $sqlSelect);
        while ($row = mysqli_fetch_array($result))        {

            $tmpAreaArray[$row['bugid']] = $row['bugid'];
        }
        $this->setBugs($tmpAreaArray);


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
        $this->setRequirements($tmpAreaArray);

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
                $tmpAreaArray[$row['id']] = $tmpAreaArray2;
            }
        }
        $this->setCustom_fields($tmpAreaArray);

        //mission mission_debriefnotes
        $sql = "SELECT notes as debrief_notes, debriefedby ";
        $sql .= "FROM   mission_debriefnotes ";
        $sql .= "WHERE  versionid = $versionid";
        $result = mysqli_query($con, $sql);
        if (mysqli_num_rows($result) > 0) {
            $data = mysqli_fetch_array($result);

            $this->setDebrief_notes($data['notes']);
            $this->setDebriefedby(($data['debriefedby']));
        }
        else
        {
            $this->setDebrief_notes("");
            $this->setDebriefedby("");

        }

        //mission metrics
        $sql = "SELECT setup_percent,test_percent,bug_percent,opportunity_percent,duration_time,mood ";
        $sql .= "FROM   mission_sessionmetrics ";
        $sql .= "WHERE  versionid = $versionid";
        $result = mysqli_query($con, $sql);
        if (mysqli_num_rows($result) > 0) {
            $data = mysqli_fetch_array($result);
            $this->setSetup_percent($data['setup_percent']);
            $this->setTest_percent($data['test_percent']);
            $this->setBug_percent($data['bug_percent']);
            $this->setOpportunity_percent($data['opportunity_percent']);
            $this->setDuration_time($data['duration_time']);
            $this->setMood($data['mood']);
        }
        else
        {
            $this->setSetup_percent(0);
            $this->setTest_percent(0);
            $this->setBug_percent(0);
            $this->setOpportunity_percent(0);
            $this->setDuration_time(0);
            $this->setMood(0);
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
        $this->setLinked_from_session($tmpAreaArray);

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
        $this->setLinked_to_session($tmpAreaArray);

        //mission mission_status
        $sql = "SELECT executed,debriefed,closed,masterdibriefed,executed_timestamp,debriefed_timestamp ";
        $sql .= "FROM   mission_status ";
        $sql .= "WHERE  versionid = $versionid";
        $result = mysqli_query($con, $sql);
        if (mysqli_num_rows($result) > 0) {
            $data = mysqli_fetch_array($result);
            $this->setExecuted($data['executed']);
            $this->setDebriefed($data['debriefed']);
            $this->setClosed($data['closed']);
            $this->setMasterdibriefed($data['masterdibriefed']);
            $this->setExecuted_timestamp($data['executed_timestamp']);
            $this->setDebriefed_timestamp($data['debriefed_timestamp']);
        }
        else
        {
            $this->setExecuted(0);
            $this->setDebriefed(0);
            $this->setClosed(0);
            $this->setMasterdibriefed(0);
            $this->setExecuted_timestamp(0);
            $this->setDebriefed_timestamp(0);
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
        $this->setAdditional_testers($tmpArray);

        mysqli_close($con);
    }

    function getAdditional_testers()
    {
        return $this->additional_testers;
    }

    function getAreas()
    {
        return $this->areas;
    }

    function getAttachments()
    {
        return $this->attachments;
    }

    function getBug_percent()
    {
        return $this->bug_percent;
    }

    function getBugs()
    {
        return $this->bugs;
    }

    function getCharter()
    {
        return $this->charter;
    }

    function getClosed()
    {
        return $this->closed;
    }

    function getCustom_fields()
    {
        return $this->custom_fields;
    }

    function getDebrief_notes()
    {
        return $this->debrief_notes;
    }

    function getDebriefed()
    {
        return $this->debriefed;
    }

    function getDebriefed_timestamp()
    {
        return $this->debriefed_timestamp;
    }

    function getDebriefedby()
    {
        return $this->debriefedby;
    }

    function getDepricated()
    {
        return $this->depricated;
    }

    function getDuration_time()
    {
        return $this->duration_time;
    }

    function getExecuted()
    {
        return $this->executed;
    }

    function getExecuted_timestamp()
    {
        return $this->executed_timestamp;
    }

    function getLastupdatedby()
    {
        return $this->lastupdatedby;
    }

    function getLinked_from_session()
    {
        return $this->linked_from_session;
    }

    function getLinked_to_session()
    {
        return $this->linked_to_session;
    }

    function getMasterdibriefed()
    {
        return $this->masterdibriefed;
    }

    function getMood()
    {
        return $this->mood;
    }

    function getNotes()
    {
        return $this->notes;
    }

    function getOpportunity_percent()
    {
        return $this->opportunity_percent;
    }

    function getProjects()
    {
        return $this->projects;
    }

    function getPublickey()
    {
        return $this->publickey;
    }

    function getRequirements()
    {
        return $this->requirements;
    }

    function getSessionid()
    {
        return $this->sessionid;
    }

    function getSetup_percent()
    {
        return $this->setup_percent;
    }

    function getSoftware()
    {
        return $this->software;
    }

    function getSprintname()
    {
        return $this->sprintname;
    }

    function getTeamname()
    {
        return $this->teamname;
    }

    function getTeamsprintname()
    {
        return $this->teamsprintname;
    }

    function getTest_percent()
    {
        return $this->test_percent;
    }

    function getTestenvironment()
    {
        return $this->testenvironment;
    }

    function getTitle()
    {
        return $this->title;
    }

    function getUpdated()
    {
        return $this->updated;
    }

    function getUsername()
    {
        return $this->username;
    }

    function getVersionid()
    {
        return $this->versionid;
    }

    function setAdditional_testers($x)
    {
        $this->additional_testers = $x;
    }

    function setAreas($x)
    {
        $this->areas = $x;
    }

    function setAttachments($x)
    {
        $this->attachments = $x;
    }

    function setBug_percent($x)
    {
        $this->bug_percent = $x;
    }

    function setBugs($x)
    {
        $this->bugs = $x;
    }

    function setCharter($x)
    {
        $this->charter = $x;
    }

    function setClosed($x)
    {
        $this->closed = $x;
    }

    function setCustom_fields($x)
    {
        $this->custom_fields = $x;
    }

    function setDebrief_notes($x)
    {
        $this->debrief_notes = $x;
    }

    function setDebriefed($x)
    {
        $this->debriefed = $x;
    }

    function setDebriefed_timestamp($x)
    {
        $this->debriefed_timestamp = $x;
    }

    function setDebriefedby($x)
    {
        $this->debriefedby = $x;
    }

    function setDepricated($x)
    {
        $this->depricated = $x;
    }

    function setDuration_time($x)
    {
        $this->duration_time = $x;
    }

    function setExecuted($x)
    {
        $this->executed = $x;
    }

    function setExecuted_timestamp($x)
    {
        $this->executed_timestamp = $x;
    }

    function setLastupdatedby($x)
    {
        $this->lastupdatedby = $x;
    }

    function setLinked_from_session($x)
    {
        $this->linked_from_session = $x;
    }

    function setLinked_to_session($x)
    {
        $this->linked_to_session = $x;
    }

    function setMasterdibriefed($x)
    {
        $this->masterdibriefed = $x;
    }

    function setMood($x)
    {
        $this->mood = $x;
    }

    function setNotes($x)
    {
        $this->notes = $x;
    }

    function setOpportunity_percent($x)
    {
        $this->opportunity_percent = $x;
    }

    function setProjects($x)
    {
        $this->projects = $x;
    }

    function setRequirements($x)
    {
        $this->requirements = $x;
    }

    private function setPublickey($x)
    {
        $this->publickey = $x;
    }

    private function setSessionid($x)
    {
        $this->sessionid = $x;
    }

    function setSetup_percent($x)
    {
        $this->setup_percent = $x;
    }

    function setSoftware($x)
    {
        $this->software = $x;
    }

    function setSprintname($x)
    {
        $this->sprintname = $x;
    }

    function setTeamname($x)
    {
        $this->teamname = $x;
    }

    function setTeamsprintname($x)
    {
        $this->teamsprintname = $x;
    }

    function setTest_percent($x)
    {
        $this->test_percent = $x;
    }

    function setTestenvironment($x)
    {
        $this->testenvironment = $x;
    }

    function setTitle($x)
    {
        $this->title = $x;
    }

    function setUpdated($x)
    {
        $this->updated = $x;
    }

    function setUsername($x)
    {
        $this->username = $x;
    }

    function setVersionid($x)
    {
        $this->versionid = $x;
    }

    private function generateSessionid()
    {
        $this->sessionid = swCreateNewSessionId();
    }

    private function generatePublickey()
    {
        $this->publickey = md5(rand());
    }


}
