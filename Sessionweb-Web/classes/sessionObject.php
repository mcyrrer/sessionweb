<?php

require_once 'sessionReadObject.php';
require_once '../include/session_database_functions.php.inc';
/**
 *
 */
class sessionObject
{
    var $oSessionData = null;
    var $additional_testers;
    var $areas;
    var $attachments;
    var $bug_percent;
    var $bugs;
    var $charter;
    var $closed;
    var $custom_fields;
    var $debrief_notes;
    var $debriefed;
    var $debriefed_timestamp;
    var $debriefedby;
    var $depricated;
    var $duration_time;
    var $executed;
    var $executed_timestamp;
    var $lastupdatedby;
    var $linked_from_session;
    var $linked_to_session;
    var $masterdibriefed;
    var $mood;
    var $notes;
    var $opportunity_percent;
    var $projects;
    var $publickey;
    var $requirements;
    var $sessionid;
    var $setup_percent;
    var $software;
    var $sprintname;
    var $teamname;
    var $teamsprintname;
    var $test_percent;
    var $testenvironment;
    var $title;
    var $updated;
    var $username;
    var $versionid;

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
