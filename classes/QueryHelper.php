<?php
require_once 'logging.php';
require_once 'dbHelper.php';


class QueryHelper
{
    private $logger;
    private $dbh;

    function __construct()
    {
        $this->logger = new logging();
        $this->dbh = new dbHelper();
    }

    /**
     * Return active(in table teamname) teamnames from database as an array.
     * @return array
     */
    public function getTeamNamesActive()
    {
        $project = $_SESSION['project'];

        $sqlSelect = "";
        $sqlSelect .= "SELECT teamname ";
        $sqlSelect .= "FROM   teamnames ";
        $sqlSelect .= "WHERE project = '$project' ";
        $sqlSelect .= "ORDER  BY `teamname` ASC ";

        $con = $this->dbh->connectToLocalDb();

        $result = $this->dbh->executeQuery($con, $sqlSelect);
        $result = $this->dbh->executeQuery($con, $sqlSelect);
        $toReturn = array();
        if ($result) {
            while ($row = mysqli_fetch_array($result)) {
                $toReturn[$row['teamname']] = $row['teamname'];
            }
        } else {
            $this->logger->error("Error getting teamnames", __FILE__, __LINE__);
            $this->logger->error($sqlSelect);
        }

        return $toReturn;
    }

    public function getAreasActive()
    {
        $project = $_SESSION['project'];

        $sqlSelect = "SELECT areaname FROM areas  ";
        $sqlSelect .= "WHERE project = '$project' ";
        $sqlSelect .= "ORDER  BY `areaname` ASC ";

        $con = $this->dbh->connectToLocalDb();

        $result = $this->dbh->executeQuery($con, $sqlSelect);
        $toReturn = array();
        if ($result) {
            while ($row = mysqli_fetch_array($result)) {
                $toReturn[$row['areaname']] = $row['areaname'];
            }
        } else {
            $this->logger->error("Error getting areanames", __FILE__, __LINE__);
            $this->logger->error($sqlSelect);
        }

        return $toReturn;
    }

    public function getSprintNamesActive()
    {
        $project = $_SESSION['project'];

        $sqlSelect = "";
        $sqlSelect .= "SELECT sprintname ";
        $sqlSelect .= "FROM   sprintnames ";
        $sqlSelect .= "WHERE project = '$project' ";
        $sqlSelect .= "ORDER  BY `sprintname` ASC ";

        $con = $this->dbh->connectToLocalDb();

        $result = $this->dbh->executeQuery($con, $sqlSelect);
        $toReturn = array();
        if ($result) {
            while ($row = mysqli_fetch_array($result)) {
                $toReturn[$row['sprintname']] = $row['sprintname'];
            }
        } else {
            $this->logger->error("Error getting sprintnames", __FILE__, __LINE__);
            $this->logger->error($sqlSelect);
        }
        

        return $toReturn;
    }

    public function getEnvironmentsNames()
    {
        $project = $_SESSION['project'];

        $sqlSelect = "SELECT name FROM testenvironment WHERE project = " . $project . " ORDER BY name ASC";

        $con = $this->dbh->connectToLocalDb();

        $result = $this->dbh->executeQuery($con, $sqlSelect);
        $toReturn = array();
        if ($result) {
            while ($row = mysqli_fetch_array($result)) {
                $toReturn[$row['name']] = $row['name'];
            }
        } else {
            $this->logger->error("Error getting environment name", __FILE__, __LINE__);
            $this->logger->error($sqlSelect);
        }
        

        return $toReturn;
    }

    public function getCustomFieldNames($custom_items)
    {
        $project = $_SESSION['project'];

        $sqlSelect = "SELECT name FROM custom_items WHERE tablename = '" . $custom_items . "' ORDER BY name ASC";

        $con = $this->dbh->connectToLocalDb();

        $result = $this->dbh->executeQuery($con, $sqlSelect);
        $toReturn = array();
        if ($result) {
            while ($row = mysqli_fetch_array($result)) {
                $toReturn[$row['name']] = $row['name'];
            }
        } else {
            $this->logger->error("Error getting custom field names", __FILE__, __LINE__);
            $this->logger->error($sqlSelect);
        }

        return $toReturn;
    }

    public function getAdditionalTester()
    {
        $project = $_SESSION['project'];
        $sqlSelect = "";
        $sqlSelect .= "SELECT m.fullname, ";
        $sqlSelect .= "       us.username ";
        $sqlSelect .= "FROM   user_settings AS us, ";
        $sqlSelect .= "       members AS m ";
        $sqlSelect .= "WHERE  project = 0 ";
        $sqlSelect .= "       AND m.username = us.username ";
        $sqlSelect .= "       AND m.active = 1 ";
        $sqlSelect .= "       AND deleted = 0 ";
        $sqlSelect .= "ORDER BY us.username;";

        $con = $this->dbh->connectToLocalDb();

        $result = $this->dbh->executeQuery($con, $sqlSelect);
        $toReturn = array();
        if ($result) {
            while ($row = mysqli_fetch_array($result)) {
                $toReturn[$row['username']] = $row['fullname'];
            }
        } else {
            $this->logger->error("Error getting AdditionalTester", __FILE__, __LINE__);
            $this->logger->error($sqlSelect);
        }

        return $toReturn;
    }

    public function isTestEnvoronmentUrlDefined()
    {
        $sql = 'SELECT * FROM testenvironment WHERE url is not null AND url!="" ';
        $con = $this->dbh->connectToLocalDb();

        $result = $this->dbh->executeQuery($con, $sql);
        $nbrOfRows = mysqli_num_rows($result);
        $this->logger->debug("Test env url count " . $nbrOfRows, __FILE__, __LINE__);


        if ($nbrOfRows > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function getAreasFromAreaTable()
    {
        $dbm = new dbHelper();
        $logger = new logging();
        $con = $dbm->connectToLocalDb();
        $sqlSelectSessionStatus = "";
        $sqlSelectSessionStatus .= "SELECT areaname ";
        $sqlSelectSessionStatus .= "FROM areas";

        $resultSessionAreas = $dbm->executeQuery($con,$sqlSelectSessionStatus);

        if (!$resultSessionAreas) {
            echo "getAreas: " . mysqli_error($con) . "<br/>";
            $logger->error(mysqli_error($con));
            $logger->debug($sqlSelectSessionStatus());
        }

        $result = array();
        while ($row = mysqli_fetch_array($resultSessionAreas)) {
            $result[$row["areaname"]] = $row["areaname"];
        }

        return $result;
    }

    public static function getApplicationsFromAreaNames()
    {
        $dbm = new dbHelper();
        $logger = new logging();
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

    public static function getTesterFullName($username)
    {
        $dbm = new dbHelper();
        $con = $dbm->connectToLocalDb();
        $sqlSelect = "";
        $sqlSelect .= "SELECT fullname ";
        $sqlSelect .= "FROM   members ";
        $sqlSelect .= "WHERE username = '$username' ";
        $sqlSelect .= "ORDER  BY fullname ASC";

        $result = $dbm->executeQuery($con,$sqlSelect);

        $row = mysqli_fetch_row($result);

        return $row[0];
    }

    public static function doesUserExist($username)
    {
        $dbm = new dbHelper();
        $con = $dbm->connectToLocalDb();

        $username = mysqli_real_escape_string($con,$username);
        $sql = "SELECT * FROM members WHERE username LIKE  '$username'";
        $result = $dbm->executeQuery($con,$sql);

        if (mysqli_num_rows($result) == 1) {
            $result = true;
        } else {
            $result = false;
        }

        return $result;
    }
}

?>