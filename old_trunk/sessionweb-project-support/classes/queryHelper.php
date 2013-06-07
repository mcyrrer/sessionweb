<?php
require_once 'logging.php';
require_once 'dbHelper.php';


class queryHelper
{
    private $logger;
    private $dbHelper;

    function __construct()
    {
        $this->logger = new logging();
        $this->dbHelper = new dbHelper();
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

        $con = $this->dbHelper->db_getMySqliConnection();

        $result = $this->dbHelper->sw_mysqli_execute($con, $sqlSelect, __FILE__, __LINE__);
        $toReturn = array();
        if ($result) {
            while ($row = mysqli_fetch_array($result)) {
                $toReturn[$row['teamname']] = $row['teamname'];
            }
        } else {
            $this->logger->error("Error getting teamnames", __FILE__, __LINE__);
            $this->logger->error($sqlSelect);
        }
        mysqli_close($con);

        return $toReturn;
    }

    public function getAreasActive()
    {
        $project = $_SESSION['project'];

        $sqlSelect = "SELECT areaname FROM areas  ";
        $sqlSelect .= "WHERE project = '$project' ";
        $sqlSelect .= "ORDER  BY `areaname` ASC ";

        $con = $this->dbHelper->db_getMySqliConnection();

        $result = $this->dbHelper->sw_mysqli_execute($con, $sqlSelect, __FILE__, __LINE__);
        $toReturn = array();
        if ($result) {
            while ($row = mysqli_fetch_array($result)) {
                $toReturn[$row['areaname']] = $row['areaname'];
            }
        } else {
            $this->logger->error("Error getting areanames", __FILE__, __LINE__);
            $this->logger->error($sqlSelect);
        }
        mysqli_close($con);

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

        $con = $this->dbHelper->db_getMySqliConnection();

        $result = $this->dbHelper->sw_mysqli_execute($con, $sqlSelect, __FILE__, __LINE__);
        $toReturn = array();
        if ($result) {
            while ($row = mysqli_fetch_array($result)) {
                $toReturn[$row['sprintname']] = $row['sprintname'];
            }
        } else {
            $this->logger->error("Error getting sprintnames", __FILE__, __LINE__);
            $this->logger->error($sqlSelect);
        }
        mysqli_close($con);

        return $toReturn;
    }

    public function getEnvironmentsNames()
    {
        $project = $_SESSION['project'];

        $sqlSelect = "SELECT name FROM testenvironment WHERE project = ".$project." ORDER BY name ASC";

        $con = $this->dbHelper->db_getMySqliConnection();

        $result = $this->dbHelper->sw_mysqli_execute($con, $sqlSelect, __FILE__, __LINE__);
        $toReturn = array();
        if ($result) {
            while ($row = mysqli_fetch_array($result)) {
                $toReturn[$row['name']] = $row['name'];
            }
        } else {
            $this->logger->error("Error getting environment name", __FILE__, __LINE__);
            $this->logger->error($sqlSelect);
        }
        mysqli_close($con);

        return $toReturn;
    }

    public function getCustomFieldNames($custom_items)
    {
        $project = $_SESSION['project'];

        $sqlSelect = "SELECT name FROM custom_items WHERE tablename = '".$custom_items."' ORDER BY name ASC";

        $con = $this->dbHelper->db_getMySqliConnection();

        $result = $this->dbHelper->sw_mysqli_execute($con, $sqlSelect, __FILE__, __LINE__);
        $toReturn = array();
        if ($result) {
            while ($row = mysqli_fetch_array($result)) {
                $toReturn[$row['name']] = $row['name'];
            }
        } else {
            $this->logger->error("Error getting custom field names", __FILE__, __LINE__);
            $this->logger->error($sqlSelect);
        }
        mysqli_close($con);

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
        $sqlSelect .= "       AND deleted = 0 " ;
        $sqlSelect .= "ORDER BY us.username;" ;

        $con = $this->dbHelper->db_getMySqliConnection();

        $result = $this->dbHelper->sw_mysqli_execute($con, $sqlSelect, __FILE__, __LINE__);
        $toReturn = array();
        if ($result) {
            while ($row = mysqli_fetch_array($result)) {
                $toReturn[$row['username']] = $row['fullname'];
            }
        } else {
            $this->logger->error("Error getting AdditionalTester", __FILE__, __LINE__);
            $this->logger->error($sqlSelect);
        }
        mysqli_close($con);

        return $toReturn;
    }

}

?>