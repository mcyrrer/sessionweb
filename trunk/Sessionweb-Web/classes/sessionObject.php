<?php

require_once 'sessionReadObject.php';
require_once '../include/session_database_functions.php.inc';
/**
 *
 */
class sessionObject
{
    var $oSessionData = null;

    /**
     * @param null $sessionid sessionid to create a object of, if null then create a empty one.
     */
    function __construct($sessionid = null)
    {
        if ($sessionid == null) {
            $this->oSessionData = $this->createEmptySessionObject();
        }
        else
        {
            $this->oSessionData = new sessionReadObject($sessionid);

        }
    }

    /**
     * Create a sessionObject that is empty
     */
    function createEmptySessionObject()
    {
        $this->oSessionData = array(
            "additional_testers" => null,
            "areas" => null,
            "attachments" => null,
            "bug_percent" => 0,
            "bugs" => null,
            "charter" => "",
            "closed" => false,
            "custom_fields" => array(),
            "debrief_notes" => "",
            "debriefed" => false,
            "debriefed_timestamp" => null,
            "debriefedby" => null,
            "depricated" => null,
            "duration_time" => null,
            "executed" => false,
            "executed_timestamp" => null,
            "lastupdatedby" => null,
            "linked_from_session" => null,
            "linked_to_session" => null,
            "masterdibriefed" => null,
            "mood" => null,
            "notes" => "",
            "opportunity_percent" => 0,
            "projects" => $_SESSION['project'],
            "publickey" => md5(rand()),
            "requirements" => null,
            "sessionid" => swCreateNewSessionId(),
            "setup_percent" => 0,
            "software" => null,
            "sprintname" => null,
            "teamname" => null,
            "teamsprintname" => null,
            "test_percent" => 0,
            "testenvironment" => null,
            "title" => "",
            "updated" => null,
            "username" => $_SESSION['username'],
            "versionid" => null
        );
    }

    /**
     * emulate print_r for a sessionObject without the data. This is used to get a overview of
     * the object structure.
     */
    function printObjectStructure()
    {
        foreach ($this->oSessionData->getSession() as $structure => $data) {
            echo $structure . " =><br />";
        }
    }
}
