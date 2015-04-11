<?php

class HtmlFunctions
{
    private $logger;
    private $dbh;

    function __construct()
    {
        $this->logger = new logging();
        $this->dbh = new dbHelper();
    }

    public static function echoTesterFullNameSelect($name, $removeInactiveUsers = false, $onlyShowUserThatHaveLoggedASession = false, $addUserName = false)
    {
        $dbh = new dbHelper();
        $con = $dbh->connectToLocalDb();
        if ($onlyShowUserThatHaveLoggedASession == true) {
            $sqlSelect = "SELECT DISTINCT mb.fullname, mb.username FROM members as mb, mission as m  WHERE mb.deleted = false AND mb.username LIKE m.username ORDER BY m.username ASC";
        } else {
            $sqlSelect = "";
            $sqlSelect .= "SELECT username,fullname ";
            $sqlSelect .= "FROM   members ";
            $sqlSelect .= "WHERE deleted = FALSE "; // fix for ISSUE 15 user not visible in list.php when the user is inactivated
            if ($removeInactiveUsers)
                $sqlSelect .= "AND active = TRUE "; // fix for ISSUE 15 user not visible in list.php when the user is inactivated
            $sqlSelect .= "ORDER  BY fullname ASC";
        }

        $result = $dbh->executeQuery($con,$sqlSelect);

        echo "                                  <select id=\"select_tester\" name=\"tester\" class='formChanged'>\n";
        echo "                                      <option></option>\n";

        while ($row = mysqli_fetch_array($result)) {
            $userName = $row['username'];
            $fullName = $row['fullname'];

            if ($addUserName) {
                $nameToDisplay = $fullName . "(" . $userName . ")";
            } else {
                $nameToDisplay = $fullName;
            }
            if (strcmp($name, $row['username']) == 0) {
                echo "                                      <option value='$userName' selected=\"selected\">$nameToDisplay</option>\n";
            } else {
                echo "                                      <option value='$userName'>$nameToDisplay</option>\n";
            }
        }

        echo "                                  </select>\n";

    }


    public static function echoSprintSelect($sprint, $history = false, $id_name = "select_sprint")
    {
        $dbh = new dbHelper();
        $con = $dbh->connectToLocalDb();

        if (!$history) {
            $sqlSelect = "";
            $sqlSelect .= "SELECT sprintname ";
            $sqlSelect .= "FROM   sprintnames ";
            $sqlSelect .= "ORDER  BY sprintname ASC";
        } else {
            $sqlSelect = "SELECT sprintname FROM mission where sprintname NOT LIKE 'null' group by sprintname ASC";
        }
        $result = $dbh->executeQuery($con,$sqlSelect);

        echo "                                  <select id=\"$id_name\" name=\"sprint\">\n";


        echo "                                      <option></option>\n";

        while ($row = mysqli_fetch_array($result)) {
            if (strcmp($sprint, $row['sprintname']) == 0) {
                echo "                                      <option selected=\"selected\">" . htmlspecialchars($row['sprintname']) . "</option>\n";
            } else {
                echo "                                      <option>" . htmlspecialchars($row['sprintname']) . "</option>\n";
            }
        }

        echo "                                  </select>\n";

    }

    public static function echoTeamSelect($team, $history = false, $id_name = "select_team")
    {
        $dbh = new dbHelper();

            $con = $dbh->connectToLocalDb();

        if (!$history) {
            $sqlSelect = "";
            $sqlSelect .= "SELECT teamname ";
            $sqlSelect .= "FROM   teamnames ";
            $sqlSelect .= "ORDER  BY `teamname` ASC ";
        } else
            $sqlSelect = "SELECT teamname FROM mission where teamname NOT LIKE 'null' group by teamname ASC ";

        $result = $dbh->executeQuery($con,$sqlSelect);

        echo "                                  <select id=\"$id_name\" name=\"team\">\n";


        echo "                                      <option></option>\n";


        while ($row = mysqli_fetch_array($result)) {
            if (strcmp($team, $row['teamname']) == 0) {
                echo "                                      <option selected=\"selected\">" . htmlspecialchars($row['teamname']) . "</option>\n";
            } else {
                echo "                                      <option>" . htmlspecialchars($row['teamname']) . "</option>\n";
            }
        }

        echo "                                  </select>\n";

    }

    public static function echoAreaSelectSingel($area, $history = false, $id_name = "select_area")
    {
        $dbm = new dbHelper();
        $con = $dbm->connectToLocalDb();
        if (!is_array($area)) {
            $areaTmp = array();
            $areaTmp[] = $area;
            $area = $areaTmp;
        }


        if (!$history) {
            $sqlSelect = "";
            $sqlSelect .= "SELECT areaname ";
            $sqlSelect .= "FROM   areas ";
            $sqlSelect .= "ORDER  BY `areaname` ASC ";
        } else {
            $sqlSelect = "SELECT areaname FROM mission_areas where areaname not like 'null' group by areaname ASC;";
        }

        $result = $dbm->executeQuery($con,$sqlSelect);

        echo "                                  <select id=\"$id_name\" name=\"area\">\n";


        if (count($area) == 0) {

            echo "                                      <option value=\"\" selected=\"selected\"></option>\n";
        } else {
            echo "                                      <option value=\"\"></option>\n";
        }


        while ($row = mysqli_fetch_array($result)) {
            if ($area != null) {
                if (in_array($row['areaname'], $area)) {
                    echo "                                      <option selected=\"selected\" value=\"" . $row['areaname'] . "\">" . htmlspecialchars($row['areaname']) . "</option>\n";
                } else {
                    echo "                                      <option value=\"" . $row['areaname'] . "\">" . htmlspecialchars($row['areaname']) . "</option>\n";
                }
            } else {
                echo "                                      <option value=\"" . $row['areaname'] . "\">" . htmlspecialchars($row['areaname']) . "</option>\n";
            }
        }

        echo "                                  </select>\n";

    }

    public function echoStatusTypes($statusToSelect)
    {
        echo "<select id=\"select_status_type\" class=\"metricoption\" name=\"status\">\n";

        if (strcmp($statusToSelect, "") == 0) {
            echo "      <option selected=\"selected\"></option>\n";
        } else {
            echo "      <option></option>\n";
        }
        if (strcmp($statusToSelect, "Not Executed") == 0 || strcmp($statusToSelect, "1") == 0) {
            echo "      <option value='1' selected=\"selected\">Not Executed</option>\n";
        } else {
            echo "      <option value='1'>Not Executed</option>\n";
        }

        if (strcmp($statusToSelect, "In progress") == 0 || strcmp($statusToSelect, "2") == 0) {
            echo "      <option value='2' selected=\"selected\">In progress</option>\n";
        } else {
            echo "      <option value='2'>In progress</option>\n";
        }

        if (strcmp($statusToSelect, "Executed") == 0 || strcmp($statusToSelect, "3") == 0) {
            echo "      <option value='3' selected=\"selected\">Executed</option>\n";
        } else {
            echo "      <option value='3'>Executed</option>\n";
        }

        if (strcmp($statusToSelect, "Debriefed") == 0 || strcmp($statusToSelect, "4") == 0) {
            echo "      <option value='4' selected=\"selected\">Debriefed</option>\n";
        } else {
            echo "      <option value='4'>Debriefed</option>\n";
        }

        if (strcmp($statusToSelect, "Closed") == 0 || strcmp($statusToSelect, "5") == 0) {
            echo "      <option value='5' selected=\"selected\">Closed</option>\n";
        } else {
            echo "      <option value='5'>Closed</option>\n";
        }
        echo "</select>\n";
    }


}
