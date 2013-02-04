<?php
require_once 'logging.php';
require_once 'dbHelper.php';
require_once 'queryHelper.php';
require_once 'sessionObject.php';

class sessionHelper
{
    private $logger;
    private $queryHelper;

    function __construct()
    {
        $this->logger = new logging();
        $this->queryHelper = new queryHelper();
    }

    function isUserAllowedToEditSession($sessionObject)
    {

        $users = $sessionObject->getAdditional_testers();
        $users[] = $sessionObject->getUsername();
        if (in_array($_SESSION['username'], $users)) {
            return true;
        } else {

            $this->logger->debug("User not allowed to edit session " . $sessionId, __FILE__, __LINE__);
            return false;
        }
    }


}

?>