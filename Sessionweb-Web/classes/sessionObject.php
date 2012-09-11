<?php

require_once 'sessionReadObject.php';
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
            "bug_percent" => null,
            "bugs" => null,
            "charter" => null,
            "closed" => null,
            "custom_fields" => null,
            "debrief_notes" => null,
            "debriefed" => null,
            "debriefed_timestamp" => null,
            "debriefedby" => null,
            "depricated" => null,
            "duration_time" => null,
            "executed" => null,
            "executed_timestamp" => null,
            "lastupdatedby" => null,
            "linked_from_session" => null,
            "linked_to_session" => null,
            "masterdibriefed" => null,
            "mood" => null,
            "notes" => null,
            "opportunity_percent" => null,
            "projects" => null,
            "publickey" => null,
            "requirements" => null,
            "sessionid" => null,
            "setup_percent" => null,
            "software" => null,
            "sprintname" => null,
            "teamname" => null,
            "teamsprintname" => null,
            "test_percent" => null,
            "testenvironment" => null,
            "title" => null,
            "updated" => null,
            "username" => null,
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
