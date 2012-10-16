<?php
if (!isset($basePath)) {
    $basePath="./";
}

include_once 'sessionObjectSave.php';
/**
 * Class to create/load sessions and to manipulate data.
 */
class sessionObject extends sessionObjectSave
{
    private $additional_testers; //Array
    private $areas; //Array
    private $attachments; //Array
    private $bug_percent; //Boolean 0,1
    private $bugs; //Array
    private $charter; //Text
    private $closed; //Boolean 0,1
    private $custom_fields; //Array
    private $debrief_notes; //Text
    private $debriefed; //Boolean 0,1
    private $debriefed_timestamp; //Mysql TimeStamp
    private $debriefedby; //Text
    private $depricated; //Boolean 0,1
    private $duration_time; //Int
    private $executed; //Boolean 0,1
    private $executed_timestamp; //Mysql TimeStamp
    private $lastupdatedby; //Text
    private $linked_from_session; //Array
    private $linked_to_session; //Array
    private $masterdibriefed; //Boolean 0,1
    private $mood; //Int 0-4
    private $notes; //Text
    private $opportunity_percent; //Int
    private $project; //Text
    private $publickey; //Text
    private $requirements; //Array
    private $sessionid; //Int
    private $setup_percent; //Int
    private $software; //Text
    private $softwareuseautofetched; //Array
    private $sprintname; //Text
    private $teamname; //Text
    private $teamsprintname; //Text
    private $test_percent; //Int
    private $testenvironment; //Text
    private $title; //Text
    private $updated; //Mysql TimeStamp
    private $username; //Text
    private $versionid; //Int

    /**
     * @param null $sessionid sessionid to create a object of, if null then create a empty one.
     */
    function __construct($sessionid = null)
    {
        if ($sessionid == null) {
            $this->createEmptySessionObject();
        } else {
            $this->getSessionData($sessionid);
        }
    }

    /**
     * Create a new sessionObject that only have a valid sessionid and public key.
     */
    private function createEmptySessionObject()
    {


        $this->generatePublickey();
        $this->generateSessionid();

    }


    /**
     * Populate the sessionobject with data from db
     * @param $sessionid
     */
    private function getSessionData($sessionid)
    {

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $con = getMySqliConnection();

        //mission data
        $sqlSelectSession = "SELECT * ";
        $sqlSelectSession .= "FROM   mission ";
        $sqlSelectSession .= "WHERE  sessionid = $sessionid";

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $result = mysqli_query($con, $sqlSelectSession);
        /** @noinspection PhpVoidFunctionResultUsedInspection */
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

        $versionid = $this->getVersionid();

        //mission areas
        $tmpAreaArray = array();
        $sqlSelectSessionStatus = "";
        $sqlSelectSessionStatus .= "SELECT areaname ";
        $sqlSelectSessionStatus .= "FROM   mission_areas ";
        $sqlSelectSessionStatus .= "WHERE  versionid = $versionid";

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $result = mysqli_query($con, $sqlSelectSessionStatus);
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        while ($row = mysqli_fetch_array($result)) {
            $tmpAreaArray[] = $row['areaname'];
        }
        $this->setAreas($tmpAreaArray);

        //mission attachments
        $tmpAreaArray = array();
        $tmpAreaArray2 = array();
        $sql = "SELECT id,mission_versionid, filename, size, mimetype FROM `mission_attachments` WHERE `mission_versionid` = $versionid";
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $result = mysqli_query($con, $sql);
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        while ($row = mysqli_fetch_array($result)) {

            foreach ($row as $key => $value) {
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
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $result = mysqli_query($con, $sqlSelect);
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        while ($row = mysqli_fetch_array($result)) {

            $tmpAreaArray[$row['bugid']] = $row['bugid'];
        }
        $this->setBugs($tmpAreaArray);


        //mission requirements
        $tmpAreaArray = array();
        $sqlSelect = "";
        $sqlSelect .= "SELECT * ";
        $sqlSelect .= "FROM   mission_requirements ";
        $sqlSelect .= "WHERE  versionid = $versionid";
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $result = mysqli_query($con, $sqlSelect);
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        while ($row = mysqli_fetch_array($result)) {
            $tmpAreaArray[$row['requirementsid']] = $row['requirementsid'];
        }
        $this->setRequirements($tmpAreaArray);

        //mission custom fields
        $tmpAreaArray = array();
        $tmpAreaArray2 = array();
        $sql = "select * from `mission_custom` WHERE versionid=$versionid";
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $result = mysqli_query($con, $sql);
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        while ($row = mysqli_fetch_array($result)) {
            foreach ($row as $key => $value) {
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
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $result = mysqli_query($con, $sql);
        if (mysqli_num_rows($result) > 0) {
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            $data = mysqli_fetch_array($result);

            $this->setDebrief_notes($data['notes']);
            $this->setDebriefedby(($data['debriefedby']));
        } else {
            $this->setDebrief_notes("");
            $this->setDebriefedby("");

        }

        //mission metrics
        $sql = "SELECT setup_percent,test_percent,bug_percent,opportunity_percent,duration_time,mood ";
        $sql .= "FROM   mission_sessionmetrics ";
        $sql .= "WHERE  versionid = $versionid";
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $result = mysqli_query($con, $sql);
        if (mysqli_num_rows($result) > 0) {
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            $data = mysqli_fetch_array($result);
            $this->setSetup_percent($data['setup_percent']);
            $this->setTest_percent($data['test_percent']);
            $this->setBug_percent($data['bug_percent']);
            $this->setOpportunity_percent($data['opportunity_percent']);
            $this->setDuration_time($data['duration_time']);
            $this->setMood($data['mood']);
        } else {
            $this->setSetup_percent(0);
            $this->setTest_percent(0);
            $this->setBug_percent(0);
            $this->setOpportunity_percent(0);
            $this->setDuration_time(0);
            $this->setMood(0);
        }

        //mission softwareuseautofetched
        $tmpAreaArray = array();
        $sqlSelect = "";
        $sqlSelect .= "SELECT * ";
        $sqlSelect .= "FROM   softwareuseautofetched ";
        $sqlSelect .= "WHERE  versionid = $versionid";
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $result = mysqli_query($con, $sqlSelect);
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        while ($row = mysqli_fetch_array($result)) {
            $tmpAreaArray2[] = array();
            $tmpAreaArray2[] = $row['versions'];
            $tmpAreaArray2[] = $row['updated'];
            $tmpAreaArray2[] = $row['environment'];
            $tmpAreaArray[] = $tmpAreaArray2;
        }
        $this->setSoftwareUseAutoFetched($tmpAreaArray);


        //mission mission_sessionsconnections
        $tmpAreaArray = array();
        $sqlSelect = "";
        $sqlSelect .= "SELECT * ";
        $sqlSelect .= "FROM   mission_sessionsconnections ";
        $sqlSelect .= "WHERE  linked_to_versionid = $versionid";
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $result = mysqli_query($con, $sqlSelect);
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        while ($row = mysqli_fetch_array($result)) {
            $tmpAreaArray[$row['linked_from_versionid']] = $row['linked_from_versionid'];
        }
        $this->setLinked_from_session($tmpAreaArray);

        $tmpAreaArray = array();
        $sqlSelect = "";
        $sqlSelect .= "SELECT * ";
        $sqlSelect .= "FROM   mission_sessionsconnections ";
        $sqlSelect .= "WHERE  linked_from_versionid = $versionid";
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $result = mysqli_query($con, $sqlSelect);
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        while ($row = mysqli_fetch_array($result)) {

            $tmpAreaArray[$row['linked_to_versionid']] = $row['linked_to_versionid'];

        }
        $this->setLinked_to_session($tmpAreaArray);

        //mission mission_status
        $sql = "SELECT executed,debriefed,closed,masterdibriefed,executed_timestamp,debriefed_timestamp ";
        $sql .= "FROM   mission_status ";
        $sql .= "WHERE  versionid = $versionid";
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $result = mysqli_query($con, $sql);
        if (mysqli_num_rows($result) > 0) {
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            $data = mysqli_fetch_array($result);
            $this->setExecuted($data['executed']);
            $this->setDebriefed($data['debriefed']);
            $this->setClosed($data['closed']);
            $this->setMasterdibriefed($data['masterdibriefed']);
            $this->setExecuted_timestamp($data['executed_timestamp']);
            $this->setDebriefed_timestamp($data['debriefed_timestamp']);
        } else {
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
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $result = mysqli_query($con, $sqlSelect);
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        while ($row = mysqli_fetch_array($result)) {
            $tmpArray[$row['tester']] = $row['tester'];
        }
        $this->setAdditional_testers($tmpArray);

        mysqli_close($con);
    }

    /**
     * Create a sessionid and return the value created
     * @return mixed sessionid on success else null
     */
    private function swCreateNewSessionId()
    {
        $sqlInsert = "";
        $sqlInsert .= "INSERT INTO sessionid ";
        $sqlInsert .= "            (`createdby`) ";
        $sqlInsert .= "VALUES      ('" . $_SESSION['username'] . "') ";

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $con = getMySqliConnection();
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $result = mysqli_query($con, $sqlInsert);

        if (!$result) {
            echo "DB Error: " . mysqli_error($con) . "<br/>";
            debug_backtrace();
            return null;
        }

        $sqlSelect = "";
        $sqlSelect .= "SELECT * ";
        $sqlSelect .= "FROM   sessionid ";
        $sqlSelect .= "WHERE  createdby = '" . $_SESSION['username'] . "' ";
        $sqlSelect .= "ORDER  BY sessionid DESC ";
        $sqlSelect .= "LIMIT  1";

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $result = mysqli_query($con, $sqlSelect);

        if ($result) {
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            $row = mysqli_fetch_array($result);
            $sessionid = $row["sessionid"];
        } else {
            echo "DB Error: " . mysqli_error($con) . "<br/>";
            debug_backtrace();
            return null;
        }
        mysqli_close($con);
        return $sessionid;
    }

    public function saveObjectToDb()
    {

        $save = new sessionObjectSave();
        $missionDataArray["title"] = $this->getTitle();
        $missionDataArray["charter"] = $this->getCharter();
        $missionDataArray["notes"] = $this->getNotes();
        $missionDataArray['sprint'] = $this->getSprintname();
        $missionDataArray['testenv'] = $this->getTestenvironment();
        $missionDataArray['software'] = $this->getSoftware();
        $missionDataArray['teamname'] = $this->getTeamname();
        $missionDataArray['sessionid'] = $this->getSessionid();
        $missionDataArray['testenvironment'] = $this->getTestenvironment();
        $missionDataArray['publickey'] = $this->getPublickey();
        $sessiondata = $this->generateSessionDataArray();

        if(!$save->saveToMissionTable($sessiondata))
        {
            die("Could not save data to table mission");
        }

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

    function getProject()
    {
        return $this->project;
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

    function getSoftwareUseAutoFetched()
    {
        return $this->softwareuseautofetched;
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

    function addAdditional_testers($x)
    {
        $this->additional_testers[] = $x;
    }

    function setAreas($x)
    {
        $this->areas = $x;
    }

    function addAreas($x)
    {
        $this->areas[] = $x;
    }

    function setAttachments($x)
    {
        $this->attachments = $x;
    }

    function addAttachments($x)
    {
        $this->attachments[] = $x;
    }

    function setBug_percent($x)
    {
        $this->bug_percent = $x;
    }

    function setBugs($x)
    {
        $this->bugs = $x;
    }

    function addBug($x)
    {
        $this->bugs[] = $x;
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

    function addCustom_fields($x)
    {
        $this->custom_fields[] = $x;
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

    function addLinked_from_session($x)
    {
        $this->linked_from_session[] = $x;
    }

    function setLinked_to_session($x)
    {
        $this->linked_to_session = $x;
    }

    function addLinked_to_session($x)
    {
        $this->linked_to_session[] = $x;
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

    function setProject($x)
    {
        $this->project1 = $x;
    }

    function setRequirements($x)
    {
        $this->requirements = $x;
    }

    function addRequirements($x)
    {
        $this->requirements[] = $x;
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

    function setSoftwareUseAutoFetched($x)
    {
        $this->softwareuseautofetched = $x;
    }

    function addSoftwareUseAutoFetched($x)
    {
        $this->softwareuseautofetched[] = $x;
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
        $this->sessionid = $this->swCreateNewSessionId();
    }

    private function generatePublickey()
    {
        $this->publickey = md5(rand());
    }

    private function generateSessionDataArray()
    {
        $sessionDataAsArray = array();
        $sessionDataAsArray['additional_testers'] = $this->additional_testers; //Array
        $sessionDataAsArray['areas'] = $this->areas; //Array
        $sessionDataAsArray['attachments'] = $this->attachments; //Array
        $sessionDataAsArray['bug_percent'] = $this->bug_percent; //Boolean 0,1
        $sessionDataAsArray['bugs'] = $this->bugs; //Array
        $sessionDataAsArray['charter'] = $this->charter; //Text
        $sessionDataAsArray['closed'] = $this->closed; //Boolean 0,1
        $sessionDataAsArray['custom_fields'] = $this->custom_fields; //Array
        $sessionDataAsArray['debrief_notes'] = $this->debrief_notes; //Text
        $sessionDataAsArray['debriefed'] = $this->debriefed; //Boolean 0,1
        $sessionDataAsArray['debriefed_timestamp'] = $this->debriefed_timestamp; //Mysql TimeStamp
        $sessionDataAsArray['debriefedby'] = $this->debriefedby; //Text
        $sessionDataAsArray['depricated'] = $this->depricated; //Boolean 0,1
        $sessionDataAsArray['duration_time'] = $this->duration_time; //Int
        $sessionDataAsArray['executed'] = $this->executed; //Boolean 0,1
        $sessionDataAsArray['executed_timestamp'] = $this->executed_timestamp; //Mysql TimeStamp
        $sessionDataAsArray['lastupdatedby'] = $this->lastupdatedby; //Text
        $sessionDataAsArray['linked_from_session'] = $this->linked_from_session; //Array
        $sessionDataAsArray['linked_to_session'] = $this->linked_to_session; //Array
        $sessionDataAsArray['masterdibriefed'] = $this->masterdibriefed; //Boolean 0,1
        $sessionDataAsArray['mood'] = $this->mood; //Int 0-4
        $sessionDataAsArray['notes'] = $this->notes; //Text
        $sessionDataAsArray['opportunity_percent'] = $this->opportunity_percent; //Int
        $sessionDataAsArray['projects'] = $this->project; //Text
        $sessionDataAsArray['publickey'] = $this->publickey; //Text
        $sessionDataAsArray['requirements'] = $this->requirements; //Array
        $sessionDataAsArray['sessionid'] = $this->sessionid; //Int
        $sessionDataAsArray['setup_percent'] = $this->setup_percent; //Int
        $sessionDataAsArray['software'] = $this->software; //Text
        $sessionDataAsArray['softwareuseautofetched'] = $this->softwareuseautofetched; //Text
        $sessionDataAsArray['sprintname'] = $this->sprintname; //Text
        $sessionDataAsArray['teamname'] = $this->teamname; //Text
        $sessionDataAsArray['teamsprintname'] = $this->teamsprintname; //Text
        $sessionDataAsArray['test_percent'] = $this->test_percent; //Int
        $sessionDataAsArray['testenvironment'] = $this->testenvironment; //Text
        $sessionDataAsArray['title'] = $this->title; //Text
        $sessionDataAsArray['updated'] = $this->updated; //Mysql TimeStamp
        $sessionDataAsArray['username'] = $this->username; //Text
        $sessionDataAsArray['versionid'] = $this->versionid; //Int
        return $sessionDataAsArray;
    }

    /**
     * Export the object to a json representation
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->generateSessionDataArray());
    }

    /**
     * Export the object to a XML representation
     * @return string
     */
    public function toXML()
    {
        include 'ArrayToXML.php';
        $xmlObj = new ArrayToXML();
        return $xmlObj->toXml($this->generateSessionDataArray());
    }

    /**
     * Print the session object to screen for debug purpose.
     */
    public function printObject()
    {
        echo "areas:";
        print_r($this->areas);
        echo "\n"; //Array
        echo "attachments:";
        print_r($this->attachments);
        echo "\n"; //Array
        echo "bug_percent:" . $this->bug_percent . "\n"; //Boolean 0,1
        echo "bugs:";
        print_r($this->bugs);
        echo "\n"; //Array
        echo "charter:" . $this->charter . "\n"; //Text
        echo "closed:" . $this->closed . "\n"; //Boolean 0,1
        echo "custom_fields:";
        print_r($this->custom_fields);
        echo "\n"; //Array
        echo "debrief_notes:" . $this->debrief_notes . "\n"; //Text
        echo "debriefed:" . $this->debriefed . "\n"; //Boolean 0,1
        echo "debriefed_timestamp:" . $this->debriefed_timestamp . "\n"; //Mysql TimeStamp
        echo "debriefedby:" . $this->debriefedby . "\n"; //Text
        echo "depricated:" . $this->depricated . "\n"; //Boolean 0,1
        echo "duration_time:" . $this->duration_time . "\n"; //Int
        echo "executed:" . $this->executed . "\n"; //Boolean 0,1
        echo "executed_timestamp:" . $this->executed_timestamp . "\n"; //Mysql TimeStamp
        echo "lastupdatedby:" . $this->lastupdatedby . "\n"; //Text
        echo "linked_from_session:";
        print_r($this->linked_from_session);
        echo "\n"; //Array
        echo "linked_to_session:";
        print_r($this->linked_to_session);
        echo "\n"; //Array
        echo "masterdibriefed:" . $this->masterdibriefed . "\n"; //Boolean 0,1
        echo "mood:" . $this->mood . "\n"; //Int 0-4
        echo "notes:" . $this->notes . "\n"; //Text
        echo " opportunity_percent:" . $this->opportunity_percent . "\n"; //Int
        echo "projects:" . $this->project . "\n"; //Text
        echo "publickey:" . $this->publickey . "\n"; //Text
        echo "requirements:";
        print_r($this->requirements);
        echo "\n"; //Array
        echo "sessionid:" . $this->sessionid . "\n"; //Int
        echo "setup_percent:" . $this->setup_percent . "\n"; //Int
        echo "software:" . $this->software . "\n"; //Text
        echo "softwareuseautofetched:";
        print_r($this->softwareuseautofetched);
        echo "\n";
        echo "sprintname:" . $this->sprintname . "\n"; //Text
        echo "teamname:" . $this->teamname . "\n"; //Text
        echo "teamsprintname:" . $this->teamsprintname . "\n"; //Text
        echo "test_percent:" . $this->test_percent . "\n"; //Int
        echo "testenvironment:" . $this->testenvironment . "\n"; //Text
        echo "title:" . $this->title . "\n"; //Text
        echo "updated:" . $this->updated . "\n"; //Mysql TimeStamp
        echo "username:" . $this->username . "\n"; //Text
        echo "versionid:" . $this->versionid . "\n"; //Int
    }
}
