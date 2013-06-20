<?php
if (!isset($basePath)) {
    $basePath = "./";
}

include_once 'sessionObjectSave.php';
require_once 'dbHelper.php';
require_once 'logging.php';


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
    private $test_percent; //Int
    private $testenvironment; //Text
    private $title; //Text
    private $updated; //Mysql TimeStamp
    private $username; //Text
    private $versionid; //Int
    private $sessionExist; //Validate if a session exists or not. should be checked for true before accessing any get..
    private $mindMaps; //Array

    private $logger;
    private $dbHelper;

    /**
     * @param null $sessionid sessionid to create a object of, if null then create a empty one.
     */
    function __construct($sessionid = null)
    {
        $this->logger = new logging();
        $this->dbHelper = new dbHelper();

        //     if(!is_int($sessionid) && !is_null($sessionid))
        //   {
        //       $this->logger->error("sessionid is not an integer. Sessionid=".$sessionid,__FILE__,__LINE__);
        //        return false;
        //    }


        if ($sessionid == null) {
            $this->createEmptySessionObject();
            $this->logger->debug("Created a new charter with sessionid " . $this->getSessionid(), __FILE__, __LINE__);
            $this->setSessionExist(true);
            $this->setUsername($_SESSION['username']);
        } else {
            if ($this->doesSessionExist($sessionid)) {
                $this->setSessionExist(true);
                $this->getSessionData($sessionid);
            } else {
                $this->logger->warn("Sessionid $sessionid does not exist", __FILE__, __LINE__);
                $this->setSessionExist(false);
            }
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

    private function doesSessionExist($sessionid)
    {
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $con = getMySqliConnection();
        $sessionid = mysqli_real_escape_string($con, $sessionid);

        //mission data
        $sqlSelectSession = "SELECT sessionid ";
        $sqlSelectSession .= "FROM   mission ";
        $sqlSelectSession .= "WHERE  sessionid = $sessionid";

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $result = $this->dbHelper->sw_mysqli_execute($con, $sqlSelectSession, __FILE__, __LINE__);
//        $result = mysqli_query($con, $sqlSelectSession);
        if (mysqli_num_rows($result) == 0) {
            $this->logger->error("Session id $sessionid does not exist", __FILE__, __LINE__);
            mysqli_close($con);
            return false;
        } else {
            mysqli_close($con);
            return true;
        }

    }

    /**
     * Populate the sessionobject with data from db
     * @param $sessionid
     */
    private function getSessionData($sessionid)
    {

        $this->logger->debug("TEST!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!",__FILE__,__LINE__,true);
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $con = getMySqliConnection();

        $sessionid = dbHelper::escape($con, $sessionid);

        //mission data
        $sqlSelectSession = "SELECT * ";
        $sqlSelectSession .= "FROM   mission ";
        $sqlSelectSession .= "WHERE  sessionid = $sessionid";

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $result = $this->dbHelper->sw_mysqli_execute($con, $sqlSelectSession, __FILE__, __LINE__);
        //$result = mysqli_query($con, $sqlSelectSession);
        if (mysqli_num_rows($result) == 0) {
            die("no session exist with sessionid $sessionid");
        }
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $data = mysqli_fetch_array($result);

        $this->setVersionid($data['versionid']);
        $this->setSessionid($data['sessionid']);
        $this->setTitle($data['title']);
        $this->setCharter($data['charter']);
        $this->setNotes($data['notes']);
        $this->setUsername($data['username']);
        $this->setTeamname($data['teamname']);
        //$this->logger->debug($data['teamname'],__FILE__,__LINE__);
        $this->setSprintname($data['sprintname']);
        $this->setDepricated($data['depricated']);
        $this->setUpdated($data['updated']);
        $this->setPublickey($data['publickey']);
        $this->setTestenvironment($data['testenvironment']);
        $this->setSoftware(str_replace("\n","<br>",$data['software']));
        $this->setLastupdatedby($data['lastupdatedby']);

        $versionid = $this->getVersionid();

        //mission areas
        $tmpAreaArray = array();
        $sqlSelectSessionStatus = "";
        $sqlSelectSessionStatus .= "SELECT areaname ";
        $sqlSelectSessionStatus .= "FROM   mission_areas ";
        $sqlSelectSessionStatus .= "WHERE  versionid = $versionid";

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $result = $this->dbHelper->sw_mysqli_execute($con, $sqlSelectSessionStatus, __FILE__, __LINE__);
        //$result = mysqli_query($con, $sqlSelectSessionStatus);
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
        $result = $this->dbHelper->sw_mysqli_execute($con, $sql, __FILE__, __LINE__);
//        $result = mysqli_query($con, $sql);
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

            $tmpAreaArray[] = $row['bugid'];
        }
        $this->setBugs($tmpAreaArray);


        //mission requirements
        $tmpAreaArray = array();
        $sqlSelect = "";
        $sqlSelect .= "SELECT * ";
        $sqlSelect .= "FROM   mission_requirements ";
        $sqlSelect .= "WHERE  versionid = $versionid";
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $result = $this->dbHelper->sw_mysqli_execute($con, $sqlSelect, __FILE__, __LINE__);
//        $result = mysqli_query($con, $sqlSelect);
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        while ($row = mysqli_fetch_array($result)) {
            $tmpAreaArray[] = $row['requirementsid'];
        }
        $this->setRequirements($tmpAreaArray);

        //mission custom fields
        $tmpAreaArray = array();
        $tmpAreaArray2 = array();
        $sql = "SELECT * from mission_custom WHERE versionid=$versionid";
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $result = $this->dbHelper->sw_mysqli_execute($con, $sql, __FILE__, __LINE__);
//        $result = mysqli_query($con, $sql);
        if ($result != false) {
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            while ($row = mysqli_fetch_array($result)) {
                foreach ($row as $key => $value) {
                    if (!is_int($key)) {
                        $tmpAreaArray2[$key] = $value;
                    }

                }
                $tmpName = $tmpAreaArray2['customtablename'] . '_name';
                $tmpAreaArray2['realcustomname'] = $_SESSION['settings'][$tmpName];
                $tmpAreaArray[$row['id']] = $tmpAreaArray2;
            }
        }
        $this->setCustom_fields($tmpAreaArray);

        //mission mission_debriefnotes
        $sql = "SELECT notes as debrief_notes, debriefedby ";
        $sql .= "FROM   mission_debriefnotes ";
        $sql .= "WHERE  versionid = $versionid";
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $result = $this->dbHelper->sw_mysqli_execute($con, $sql, __FILE__, __LINE__);
//        $result = mysqli_query($con, $sql);
        if (mysqli_num_rows($result) > 0) {
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            $data = mysqli_fetch_array($result);
            $this->setDebrief_notes($data['debrief_notes']);
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
        $result = $this->dbHelper->sw_mysqli_execute($con, $sql, __FILE__, __LINE__);
//        $result = mysqli_query($con, $sql);
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

            $tmp["id"] = $row['id'];
            $tmp["environment"] = $row['environment'];
            $tmp["updated"] = $row['updated'];
            $tmpAreaArray[$tmp["id"]] = $tmp;
        }
        //print_r($tmpAreaArray);

        $this->setSoftwareUseAutoFetched($tmpAreaArray);


        //mission mission_sessionsconnections
        $tmpAreaArray = array();

        $sqlSelect = "";
        $sqlSelect .= "SELECT ms.id, ";
        $sqlSelect .= "       ms.linked_from_versionid, ";
        $sqlSelect .= "       ms.linked_to_versionid, ";
        $sqlSelect .= "       m.sessionid ";
        $sqlSelect .= "FROM   mission_sessionsconnections AS ms, ";
        $sqlSelect .= "       mission AS m ";
        $sqlSelect .= "WHERE  ms.linked_to_versionid = m.versionid ";
        $sqlSelect .= "       AND ms.linked_to_versionid = $versionid;";


        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $result = $this->dbHelper->sw_mysqli_execute($con, $sqlSelect, __FILE__, __LINE__);
//        $result = mysqli_query($con, $sqlSelect);
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        while ($row = mysqli_fetch_array($result)) {
            $tmpAreaArray[] = $row['sessionid'];
        }
        $this->setLinked_from_session($tmpAreaArray);

        $tmpAreaArray = array();
//        $sqlSelect = "";
//        $sqlSelect .= "SELECT * ";
//        $sqlSelect .= "FROM   mission_sessionsconnections ";
//        $sqlSelect .= "WHERE  linked_from_versionid = $versionid";

        $sqlSelect = "";
        $sqlSelect .= "SELECT ms.id, ";
        $sqlSelect .= "       ms.linked_from_versionid, ";
        $sqlSelect .= "       ms.linked_to_versionid, ";
        $sqlSelect .= "       m.sessionid ";
        $sqlSelect .= "FROM   mission_sessionsconnections AS ms, ";
        $sqlSelect .= "       mission AS m ";
        $sqlSelect .= "WHERE  ms.linked_to_versionid = m.versionid ";
        $sqlSelect .= "       AND ms.linked_from_versionid = $versionid";

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $result = $this->dbHelper->sw_mysqli_execute($con, $sqlSelect, __FILE__, __LINE__);
//        $result = mysqli_query($con, $sqlSelect);
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        while ($row = mysqli_fetch_array($result)) {
            //  print_r($row);


            $tmpAreaArray[] = $row['sessionid'];

        }
        $this->setLinked_to_session($tmpAreaArray);

        //mission mission_status
        $sql = "SELECT executed,debriefed,closed,masterdibriefed,executed_timestamp,debriefed_timestamp ";
        $sql .= "FROM   mission_status ";
        $sql .= "WHERE  versionid = $versionid";
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $result = $this->dbHelper->sw_mysqli_execute($con, $sql, __FILE__, __LINE__);
//        $result = mysqli_query($con, $sql);
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
        $result = $this->dbHelper->sw_mysqli_execute($con, $sqlSelect, __FILE__, __LINE__);
//        $result = mysqli_query($con, $sqlSelect);
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        while ($row = mysqli_fetch_array($result)) {
            $tmpArray[] = $row['tester'];
        }
        $this->setAdditional_testers($tmpArray);

        //mission mind_maps
        $tmpArray = array();
        $sqlSelect = "";
        $sqlSelect .= "SELECT * ";
        $sqlSelect .= "FROM   mission_mindmaps ";
        $sqlSelect .= "WHERE  versionid = $versionid";
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $result = $this->dbHelper->sw_mysqli_execute($con, $sqlSelect, __FILE__, __LINE__);
//        $result = mysqli_query($con, $sqlSelect);
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        while ($row = mysqli_fetch_array($result)) {
            $tmpMindMapArray['map_id'] = $row['map_id'];
            $tmpMindMapArray['title'] = $row['map_title'];
            $tmpMindMapArray['url'] = $_SESSION['settings']['wisemapping_url'] . '/c/maps/' . $row['map_id'] . '/edit';
            $tmpMindMapArray['url_view'] = $_SESSION['settings']['wisemapping_url'] . '/c/maps/' . $row['map_id'] . '/view';
            $tmpMindMapArray['url_delete'] = $_SESSION['settings']['wisemapping_url'] . '/c/restful/maps/batch?ids=' . $row['map_id'];
            $tmpArray[] = $tmpMindMapArray;
        }
        $this->setMindMaps($tmpArray);

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
        $result = $this->dbHelper->sw_mysqli_execute($con, $sqlInsert, __FILE__, __LINE__);
//        $result = mysqli_query($con, $sqlInsert);

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
        $result = $this->dbHelper->sw_mysqli_execute($con, $sqlSelect, __FILE__, __LINE__);
//        $result = mysqli_query($con, $sqlSelect);

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
        if ($this->getSessionExist()) {
            $save = new sessionObjectSave();
            $sessiondata = $this->toArray();
            if (!$save->saveToMissionTable($sessiondata)) {
                $this->logger->error("Could not save data to table mission",__FILE__,__LINE__);
                die("Could not save data to table mission");
            } else {
//                $this->logger->arraylog($sessiondata,__FILE__,__LINE__);

                $this->validateVersionIdExistAndSetItIfNot($sessiondata);
//                $this->logger->arraylog($sessiondata,__FILE__,__LINE__);

            }
            if (!$save->saveToMissionStatusTable($sessiondata)) {
                $this->logger->error("Could not save data to table mission_status",__FILE__,__LINE__);
                die("Could not save data to table mission_status");
            }
            if (!$save->saveToMissionAreaTable($sessiondata)) {
                $this->logger->error("Could not save data to table mission_area",__FILE__,__LINE__);
                die("Could not save data to table mission_area");
            }
            if (!$save->saveToMissionBugsTable($sessiondata)) {
                $this->logger->error("Could not save data to table mission_bugs",__FILE__,__LINE__);
                die("Could not save data to table mission_bugs");
            }
            if (!$save->saveToMissionRequirementsTable($sessiondata)) {
                $this->logger->error("Could not save data to table mission_requirements",__FILE__,__LINE__);
                die("Could not save data to table mission_requirements");
            }
            if (!$save->saveToMissionDebriefNotesTable($sessiondata)) {
                $this->logger->error("Could not save data to table mission_debriefnotes",__FILE__,__LINE__);
                die("Could not save data to table mission_debriefnotes");
            }
            if (!$save->saveToMissionMetricsTable($sessiondata)) {
                $this->logger->error("Could not save data to table mission_debriefnotes",__FILE__,__LINE__);
                die("Could not save data to table mission_debriefnotes");
            }
            $this->logger->debug("Saved sessionid " . $this->getSessionid() . " ", __FILE__, __LINE__);
        } else {
            $this->logger->error("Tried to save a session that does not exist", __FILE__, __LINE__);

        }

    }

    private function validateVersionIdExistAndSetItIfNot(&$sessiondata)
    {
        if ($this->getVersionid() == null || strcmp($this->getVersionid(), "") == 0) {

            $con = getMySqliConnection();
            $sql = "SELECT versionid FROM mission WHERE username = '" . $this->getUsername() . "' ORDER BY versionid DESC LIMIT 0,1";
//            $this->logger->debug($sql,__FILE__,__LINE__);

            $result = $this->dbHelper->sw_mysqli_execute($con, $sql, __FILE__, __LINE__);

            $row = mysqli_fetch_row($result);
            foreach ($row as $oneRow) {
                $this->setVersionid($oneRow);
                echo $oneRow;
            }
            $sessiondata = $this->toArray();
        }

    }

    function getAdditional_testers()
    {
        return $this->additional_testers;
    }

    function getAreas()
    {
        if ($this->areas != null) {
            return $this->areas;
        } else
            return array();
    }

    function getAttachments()
    {
        return $this->attachments;
    }

    function getBug_percent()
    {
        return $this->bug_percent;
    }

    /**
     * Get list of bugs
     * @return array list of bugs, if none then a empty array will be returned.
     */
    function getBugs()
    {
        if ($this->bugs != null) {
            return $this->bugs;
        } else
            return array();
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

    function isDebriefed()
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

    /**
     * get Requirements
     * @return array Requirements as array
     */
    function getRequirements()
    {
        if ($this->requirements != null) {
            return $this->requirements;
        } else
            return array();
    }

    function getMindMaps()
    {
        if ($this->mindMaps != null) {
            return $this->mindMaps;
        } else
            return array();
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

    /**
     * Sets areas
     * @param $x areas to save as an array where the value will be stored.
     * will always overwrite the old value
     * @return bool true if success and false on failure (like not $x not an array)
     */
    function setAreas($x)
    {
        if (is_array($x)) {
            $this->areas = $x;
            return true;
        } else false;
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

    /**
     * Set bugs
     * @param $x array of bugs
     * @return bool true if ok, false on failure (eg. not an array)
     */
    function setBugs($x)
    {
        if (is_array($x)) {
            $this->bugs = $x;
            return true;
        } else false;
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
        $this->project = $x;
    }

    /**
     * Set Requirements
     * @param $x array of Requirements
     * @return bool true if ok, false on failure (eg. not an array)
     */
    function setRequirements($x)
    {
        if (is_array($x)) {
            $this->requirements = $x;
            return true;
        } else false;
    }

    function setMindMaps($x)
    {
        if (is_array($x)) {
            $this->mindMaps = $x;
            return true;
        } else false;
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

    /**
     * Export the object to an array representation
     * @return array
     */
    private function toArray()
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
        $sessionDataAsArray['mindmaps'] = $this->mindMaps; //Boolean 0,1
        $sessionDataAsArray['mood'] = $this->mood; //Int 0-4
        $sessionDataAsArray['notes'] = $this->notes; //Text
        $sessionDataAsArray['opportunity_percent'] = $this->opportunity_percent; //Int
        $sessionDataAsArray['project'] = $this->project; //Text
        $sessionDataAsArray['publickey'] = $this->publickey; //Text
        $sessionDataAsArray['requirements'] = $this->requirements; //Array
        $sessionDataAsArray['sessionid'] = $this->sessionid; //Int
        $sessionDataAsArray['setup_percent'] = $this->setup_percent; //Int
        $sessionDataAsArray['software'] = $this->software; //Text
        $sessionDataAsArray['softwareuseautofetched'] = $this->softwareuseautofetched; //Text
        $sessionDataAsArray['sprintname'] = $this->sprintname; //Text
        $sessionDataAsArray['teamname'] = $this->teamname; //Text
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
        $version = explode('.', phpversion());
        $PHP_MAJOR_VERSION=$version[0];
        $PHP_MINOR_VERSION=$version[1];
        $PHP_RELEASE_VERSION=$version[2];

//        $this->logger->arraylog($this->toArray(),__FILE__,__LINE__);
//        echo print_r($this->toArray(),true);
        //Pretty print is supported from version 5.4->
        if($PHP_MAJOR_VERSION>=5 && $PHP_MINOR_VERSION>=4)
        {
            $this->logger->debug("Using JSON pretty print since php version is ". phpversion(),__FILE__,__LINE__);
            $json = json_encode($this->toArray(), JSON_PRETTY_PRINT);
//            $this->logger->debug("HI!!",__FILE__,__LINE__);

//            $this->logger->debug($json,__FILE__,__LINE__);
            return $json;
        }
        else
        {
            $this->logger->debug("Do not use JSON pretty print since php version is ". phpversion(),__FILE__,__LINE__);
            $json =  json_encode($this->toArray());
//            $this->logger->debug("HI!!",__FILE__,__LINE__);
//            $this->logger->debug($json,__FILE__,__LINE__);
            return $json;

        }
    }

    /**
     * Export the object to a XML representation
     * @return string
     */
    public function toXML()
    {
        include 'ArrayToXML.php';
        $xmlObj = new ArrayToXML();
        return $xmlObj->toXml($this->toArray());
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
        echo "opportunity_percent:" . $this->opportunity_percent . "\n"; //Int
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
        echo "test_percent:" . $this->test_percent . "\n"; //Int
        echo "testenvironment:" . $this->testenvironment . "\n"; //Text
        echo "title:" . $this->title . "\n"; //Text
        echo "updated:" . $this->updated . "\n"; //Mysql TimeStamp
        echo "username:" . $this->username . "\n"; //Text
        echo "versionid:" . $this->versionid . "\n"; //Int
    }

    private function setSessionExist($sessionExist)
    {
        $this->sessionExist = $sessionExist;
    }

    public function getSessionExist()
    {
        return $this->sessionExist;
    }

    /**
     * Delete a session from the database by delete all rows in all tables that have information about the session
     */
    public function deleteFromDatabase()
    {
        $con = $this->dbHelper->db_getMySqliConnection();

        $versionid = $this->getVersionid();
        $sessionId =  $this->getSessionid();

        $sqlDeleteAttachments = "";
        $sqlDeleteAttachments .= "DELETE FROM mission_attachments ";
        $sqlDeleteAttachments .= "WHERE  mission_versionid = $versionid ";
        $this->dbHelper->sw_mysqli_execute($con, $sqlDeleteAttachments, __FILE__, __LINE__);

        $sqlDeleteMissionStatus = "";
        $sqlDeleteMissionStatus .= "DELETE FROM mission_status ";
        $sqlDeleteMissionStatus .= "WHERE  versionid = $versionid ";
        $this->dbHelper->sw_mysqli_execute($con, $sqlDeleteMissionStatus, __FILE__, __LINE__);

        $sqlDeleteMissionMetrics = "";
        $sqlDeleteMissionMetrics .= "DELETE FROM mission_sessionmetrics ";
        $sqlDeleteMissionMetrics .= "WHERE  versionid = $versionid ";
        $this->dbHelper->sw_mysqli_execute($con, $sqlDeleteMissionMetrics, __FILE__, __LINE__);

        $sqlDeleteMissionRequirements = "";
        $sqlDeleteMissionRequirements .= "DELETE FROM mission_requirements ";
        $sqlDeleteMissionRequirements .= "WHERE  versionid = $versionid ";
        $this->dbHelper->sw_mysqli_execute($con, $sqlDeleteMissionRequirements, __FILE__, __LINE__);

        $sqlDeleteMissionDebriefNotes = "";
        $sqlDeleteMissionDebriefNotes .= "DELETE FROM mission_debriefnotes ";
        $sqlDeleteMissionDebriefNotes .= "WHERE  versionid = $versionid ";
        $this->dbHelper->sw_mysqli_execute($con, $sqlDeleteMissionDebriefNotes, __FILE__, __LINE__);

        $sqlDeleteMissionBugs = "";
        $sqlDeleteMissionBugs .= "DELETE FROM mission_bugs ";
        $sqlDeleteMissionBugs .= "WHERE  versionid = $versionid ";
        $this->dbHelper->sw_mysqli_execute($con, $sqlDeleteMissionBugs, __FILE__, __LINE__);

        $sqlDeleteMissionAreas = "";
        $sqlDeleteMissionAreas .= "DELETE FROM mission_areas ";
        $sqlDeleteMissionAreas .= "WHERE  versionid = $versionid ";
        $this->dbHelper->sw_mysqli_execute($con, $sqlDeleteMissionAreas, __FILE__, __LINE__);

        $sqlDeleteMissionConnectionsFrom = "";
        $sqlDeleteMissionConnectionsFrom .= "DELETE FROM mission_sessionsconnections ";
        $sqlDeleteMissionConnectionsFrom .= "WHERE  linked_from_versionid = $versionid ";
        $this->dbHelper->sw_mysqli_execute($con, $sqlDeleteMissionConnectionsFrom, __FILE__, __LINE__);

        $sqlDeleteMissionConnectionsTo = "";
        $sqlDeleteMissionConnectionsTo .= "DELETE FROM mission_sessionsconnections ";
        $sqlDeleteMissionConnectionsTo .= "WHERE  linked_to_versionid = $versionid ";
        $this->dbHelper->sw_mysqli_execute($con, $sqlDeleteMissionConnectionsTo, __FILE__, __LINE__);

        $sqlDeleteMission = "";
        $sqlDeleteMission .= "DELETE FROM mission ";
        $sqlDeleteMission .= "WHERE  versionid = $versionid ";
        $this->dbHelper->sw_mysqli_execute($con, $sqlDeleteMission, __FILE__, __LINE__);

        $sqlDeleteSessionId = "";
        $sqlDeleteSessionId .= "DELETE FROM sessionid ";
        $sqlDeleteSessionId .= "WHERE  sessionid = $sessionId ";
        $this->dbHelper->sw_mysqli_execute($con, $sqlDeleteSessionId, __FILE__, __LINE__);


        $sqlDeleteSessionId = "";
        $sqlDeleteSessionId .= "DELETE FROM mission_mindmaps ";
        $sqlDeleteSessionId .= "WHERE  versionid = $versionid ";
        $this->dbHelper->sw_mysqli_execute($con, $sqlDeleteSessionId, __FILE__, __LINE__);

        $this->logger->info("Deleted session " . $sessionId .  " from database");
        mysqli_close($con);

    }
}
