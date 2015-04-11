<?php

class UserSettings
{
    private $logger;

    function __construct()
    {
        $this->logger = new logging();
        $this->dbHelper = new dbHelper();
    }

    public static function getUserSettings($createConnection = true)
    {
        $dbm = new dbHelper();
        $con = $dbm->connectToLocalDb();

        $sqlSelect = "";
        $sqlSelect .= "SELECT * ";
        $sqlSelect .= "FROM   user_settings ";
        $sqlSelect .= "WHERE  username = '" . $_SESSION['username'] . "'";


        $result = $dbm->executeQuery($con,$sqlSelect);

        if (!$result) {
            echo "getUserSettings: " . mysqli_error($con) . "<br/>";
        }

        return mysqli_fetch_array($result, MYSQLI_ASSOC);
    }

    public static function isAdmin()
    {
        if ($_SESSION['useradmin'] == 1) {
            return true;
        } else {
            return false;
        }
    }

    public static function isSuperUser()
    {
        if ($_SESSION['useradmin'] == 1 || $_SESSION['superuser'] == 1) {
            return true;
        } else {
            return false;
        }
    }

    public static function getUserName()
    {
        return $_SESSION['username'];
    }
}
