<?php
require_once 'PathHelper.php';
$rootPath = PathHelper::getRootPath("");
require_once $rootPath.'/classes/logging.php';

class AccessManagement
{
    public static function IsCurrentUserAllowedToDebiref()
    {
        if (strcmp($_SESSION['superuser'], "1") == 0 || strcmp($_SESSION['useradmin'], "1") == 0) {
            return true;
        } else {
            return false;
        }
    }
}
