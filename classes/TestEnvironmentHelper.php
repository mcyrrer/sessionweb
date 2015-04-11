<?php


class TestEnvironmentHelper
{


    function __construct()
    {

    }

    public static function getTestEnvironmentInformation($testenvironment)
    {
        $dbm = new dbHelper();
        $con = $dbm->connectToLocalDb();
        $testenvironment = mysqli_real_escape_string($con,$testenvironment);

        $sqlSelect = "";
        $sqlSelect .= "SELECT * ";
        $sqlSelect .= "FROM testenvironment WHERE name ='$testenvironment' ";

        $result = $dbm->executeQuery($con, $sqlSelect);
        $row = mysqli_fetch_row($result);
        return $row;
    }

}
