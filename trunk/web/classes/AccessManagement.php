<?php
require_once 'PathHelper.php';
$rootPath = PathHelper::getRootPath("");
require_once $rootPath . '/classes/logging.php';
require_once $rootPath . '/classes/sessionObject.php';
require_once $rootPath . '/classes/StringHelper.php';

class AccessManagement
{
    public static function isCurrentUserAllowedToDebiref()
    {
        if (strcmp($_SESSION['superuser'], "1") == 0 || strcmp($_SESSION['useradmin'], "1") == 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function isPublicKeyValid($sessionid, $publicKey)
    {
        $logger = new logging();
        if (is_integer($sessionid)) {
            $so = new sessionObject($sessionid);
            if(StringHelper::str_IsEqual($so->getPublickey(),$publicKey))
            {
              return true;
            }
            else
            {
                $logger->warn("isPublicKeyValid: wrong public key used for session ". $sessionid);
                return false;
            }
        } else {
            $logger->error("isPublicKeyValid: Sessionid is not an integer", __FILE__, __LINE__);
        }
    }
}
