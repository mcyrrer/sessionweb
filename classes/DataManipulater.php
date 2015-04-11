<?php

class DataManipulater
{

    function __construct()
    {

    }

    public static function getApplicationsFromAreaNames()
    {
        $dbm = new dbHelper();

        $con = $dbm->connectToLocalDb();
        $sql = "select areaname from `areas` GROUP BY areaname";
        $result = $dbm->executeQuery($con,$sql);
        $appNames = array();
        while ($rowTeamAreas = mysqli_fetch_array($result)) {
            $areaName = $rowTeamAreas['areaname'];
            $areaName = explode('-', $areaName, 2);
            $areaName = str_replace(" ", "", $areaName[0]);
            $appNames[strtoupper($areaName)] = $areaName;
        }
        return $appNames;
    }
}
