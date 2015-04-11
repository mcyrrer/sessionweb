<?php

class ApplicationSettings
{
    private $logger;

    function __construct()
    {
        $this->logger = new logging();
        $this->dbh = new dbHelper();
    }

    public static function getSettings()
    {
        $dbh = new dbHelper();
        $con = $dbh->connectToLocalDb();
        $sqlSelect = "";
        $sqlSelect .= "SELECT * ";
        $sqlSelect .= "FROM   settings ";


        $result = $dbh->executeQuery($con,$sqlSelect);

        if (!$result) {
            echo "getSettings: " . mysqli_error($con) . "<br/>";
        }

        return mysqli_fetch_array($result);
    }

    public static function getSessionWebVersion()
    {
        $dbm = new dbHelper();
        $con = $dbm->connectToLocalDb();


        $sqlSelect = "";
        $sqlSelect .= "SELECT * ";
        $sqlSelect .= "FROM   version ";

        $result = $dbm->executeQuery($con,$sqlSelect);

        if (!$result) {
            return null;
            //        echo "getSessionVersion: " . mysql_error() . "<br/>";
        }


        $row = mysqli_fetch_array($result);
        $versionInstalled = $row['versioninstalled'];
        return $versionInstalled;
    }
}
