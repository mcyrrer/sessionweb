<?php

include_once('session_database_functions.php.inc');
include_once('db.php');

function checkForMaxAttachmentSize($updateJsFile = false)
{
    $maxsizebytes = getMaxUploadSize() * 1024 * 1024;
    echo "<b>Attachment size setup<br></b>";
    echo "Attachments max size according to php and mysql settings <b>" . getMaxUploadSize() . " mb</b><br>";
    $sessionwebPath = str_replace("\\", "/", getcwd());
    $sessionwebPath = substr($sessionwebPath, 0, strlen($sessionwebPath) - 8);
    $sessionwebPath = $sessionwebPath . "/include/filemanagement/application.js";
    if ($updateJsFile) {
        if (updateOneLineInTextFile($sessionwebPath, "maxFileSize:", "        maxFileSize: $maxsizebytes"))
            echo "Was able to automatically update /include/filemanagement/application.js with correct attachment size value according to your php and mysql config paramateters";
        else {
            echo "<b>Failed to update /include/filemanagement/application.js with correct value, please update it manually</b>";
            echo 'Synchronize this with the parameter maxFileSize in include\filemanagement\application.js. Parameter <i><b>maxFileSize</b></i> should be <b>' . $maxsizebytes . '</b> in the js file.<br>';
            echo '<br>If the size is to small or large change the upload_max_filesize, post_max_size, memory_limit php configuration values in php.ini and synchronize this with the max_allowed_packet config value in mysql settings.<br>';
        }
    } else {
        echo 'Synchronize this with the parameter maxFileSize in include\filemanagement\application.js. Parameter <i><b>maxFileSize</b></i> should be <b>' . $maxsizebytes . '</b> in the js file.<br>';
        echo '<br>If the size is to small or large change the upload_max_filesize, post_max_size, memory_limit php configuration values in php.ini and synchronize this with the max_allowed_packet config value in mysql settings.<br>';
    }
    echo "<br>Debug info:<br>";
    echo echoMaxUploadSizePhp();
    echo "MySql max_allowed_packet:" . getSqlMaxAllowedPacketAsMb() . "mb<br>";


}

function updateOneLineInTextFile($file, $needle, $replace)
{

    if (is_file($file) && is_writable($file)) {
        $lines = file($file);
        $fileContentToSave = "";
        foreach ($lines as $line_num => $line) {
            if (strstr($line, $needle) == false)
                $fileContentToSave = $fileContentToSave . $line . "";
            else
                $fileContentToSave = $fileContentToSave . $replace . "\n";

        }
        $fh = fopen($file, 'w') or die("can't open file");
        fwrite($fh, $fileContentToSave);
        fclose($fh);
        return true;
    } else {
        if (!is_file($file)) {
            echo "Not a file ";
        }
        if (!is_writable($file)) {
            echo "Not writeable ";
        }
        return false;
    }


}

function checkFoldersForRW()
{
    echo "<b>Check for Read Write access for certain folders.</b><br>";
    $foldersToCheckRW = array("config/", "include/filemanagement/files/", "include/filemanagement/thumbnails/", "log/");
    $foldersOk = true;
    foreach ($foldersToCheckRW as $aFolder) {
        try {
            $ourFileName = $aFolder . "testFile.txt";

            $fh = fopen($ourFileName, 'w');
            fwrite($fh, "TestString\n");
            fclose($fh);
            if (file_exists($ourFileName)) {
                echo "folder $aFolder is RW => OK<br>";
                unlink($ourFileName);
            } else {
                echo "folder $aFolder is RW => NOK (file could not be created)<br>";
                $foldersOk = false;
            }
        } catch (Exception $e) {
            echo "folder $aFolder is RW => NOK<br>";
            //echo 'Error: ', $e->getMessage(), "\n";
            echo "Please change folder $aFolder to allow read write for the www user (chmod 664)<br>";
        }
    }

    if (!$foldersOk) {
        echo "Pleas make sure that NOK folders above have read and write access for the WWW user";
        echo "In ubuntu/linux you can use the chown command to make the www user e.g. 'chown -R www-data:www-data include/filemanagement/files/' ";
        return false;
    } else {
        echo "<br><br>";
        return true;
    }
}

function getMaxUploadSize()
{
    $max_upload = (int)(ini_get('upload_max_filesize'));
    $max_post = (int)(ini_get('post_max_size'));
    $memory_limit = (int)(ini_get('memory_limit'));
    $sql_limit = getSqlMaxAllowedPacketAsMb();
    $upload_mb = min($max_upload, $max_post, $memory_limit, $sql_limit);
    return $upload_mb;

}

function echoMaxUploadSizePhp()
{
    echo "PHP upload_max_filesize:" . (int)(ini_get('upload_max_filesize')) . "mb<br>";
    echo "PHP post_max_size:" . $max_post = (int)(ini_get('post_max_size')) . "mb<br>";
    echo "PHP memory_limit:" . (int)(ini_get('memory_limit')) . "mb<br>";
}


/**
 * Get the max_allowed_packaet from mysql db and return the value as bytes.
 * @return bytes|bool
 */
function getSqlMaxAllowedPacketAsMb()
{
    $sql = "SHOW GLOBAL VARIABLES LIKE  'max_allowed_packet'";
    $result = mysql_query($sql);
    if (!$result) {
        return false;
    }
    $row = mysql_fetch_array($result);
    return $row['Value'] / 1024 / 1024;
}

function getSessionWebSettings()
{
    $sqlSelect = "";
    $sqlSelect .= "SELECT * ";
    $sqlSelect .= "FROM   settings ";

    $result = mysql_query($sqlSelect);

    if (!$result) {
        echo "getSessionWebSettings: " . mysql_error() . "<br/>";
        $logger->error(mysql_error());
        $logger->debug($sqlSelect());
        return null;
    } else {
        $row = mysql_fetch_array($result);
        return $row;
    }
}


function getSessionMetrics($versionid)
{
    //	$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");

    $sqlSelectSessionMetrics = "";
    $sqlSelectSessionMetrics .= "SELECT * ";
    $sqlSelectSessionMetrics .= "FROM   mission_sessionmetrics ";
    $sqlSelectSessionMetrics .= "WHERE  versionid = $versionid";
    $resultSessionMetrics = mysql_query($sqlSelectSessionMetrics);


    if (!$resultSessionMetrics) {
        echo "getSessionMetrics: " . mysql_error() . "<br/>";
        //        $logger->error(mysql_error());
        //        $logger->debug($sqlSelectSessionMetrics());
    }

    return mysql_fetch_array($resultSessionMetrics);
}

function getSessionDebrief($versionid)
{


    $sqlSelect = "";
    $sqlSelect .= "SELECT * ";
    $sqlSelect .= "FROM   mission_debriefnotes ";
    $sqlSelect .= "WHERE  versionid = $versionid";

    $resultSession = mysql_query($sqlSelect);

    if (!$resultSession) {
        //$logger->error("getSessionDebrief: " . mysql_error());
        return null;
    }

    return mysql_fetch_array($resultSession);
}


function getSessionData($sessionid)
{
    $sqlSelectSession = "";
    $sqlSelectSession .= "SELECT * ";
    $sqlSelectSession .= "FROM   mission ";
    $sqlSelectSession .= "WHERE  sessionid = $sessionid";
    $resultSession = mysql_query($sqlSelectSession);
    if (!$resultSession) {
        echo "getSessionData: " . mysql_error() . "<br/>$sqlSelectSession<br>";
        //$logger->error(mysql_error());
        //$logger->debug($sqlSelectSession());
    }

    return mysql_fetch_array($resultSession);
}

function getSessionNotesAncCarters($sql)
{

    $resultSession = mysql_query($sql);

    if (!$resultSession) {
        echo "getSessionData: " . mysql_error() . "<br/>";
    }

    return $resultSession;
}

function getSessionDataFromVersionId($versionid)
{
    $sqlSelectSession = "";
    $sqlSelectSession .= "SELECT * ";
    $sqlSelectSession .= "FROM   mission ";
    $sqlSelectSession .= "WHERE  versionid = $versionid";
    $resultSession = mysql_query($sqlSelectSession);

    if (!$resultSession) {
        echo "getSessionDataFromVersionId: " . mysql_error() . "<br/>";
        $logger->error(mysql_error());
        $logger->debug($sqlSelectSession());
    }

    return mysql_fetch_array($resultSession);
}

function getSessionPublicKeyBasedOnSessionId($sessionid)
{
    $sessionData = getSessionData($sessionid);
    return $sessionData['publickey'];
}

/**
 * Get the versionid for the given sessionid
 * @param  $sessionid
 * @return versionid as string
 */
function getSessionVersionId($sessionid)
{
    $result = getSessionData($sessionid);
    return $result['versionid'];
}

/**
 * Get the sessionid for the given versionid
 * @param  $versionid
 * @return sessionid as string
 */
function getSessionSessionId($versionid)
{
    $result = getSessionDataFromVersionId($versionid);
    return $result['sessionid'];
}

function doesSessionNotesExist($versionid)
{

    $sqlSelect = "";
    $sqlSelect .= "SELECT * ";
    $sqlSelect .= "FROM   mission_debriefnotes ";
    $sqlSelect .= "WHERE  versionid = $versionid";

    $result = mysql_query($sqlSelect);
    if (!$result) {
        return false;
    }

    $result = mysql_fetch_array($result);

    if ($result != null) {
        return true;
    } else {
        return false;
    }
}


function getSessionStatus($versionid)
{
    $sqlSelectSessionStatus = "";
    $sqlSelectSessionStatus .= "SELECT * ";
    $sqlSelectSessionStatus .= "FROM   mission_status ";
    $sqlSelectSessionStatus .= "WHERE  versionid = $versionid";
    $resultSessionStatus = mysql_query($sqlSelectSessionStatus);
    if (!$resultSessionStatus) {
        echo "getSessionStatus: " . mysql_error() . "<br/>";
    }

    return mysql_fetch_array($resultSessionStatus);
}

function getSessionNotes($versionid)
{
    $sqlSelect = "";
    $sqlSelect .= "SELECT * ";
    $sqlSelect .= "FROM   mission ";
    $sqlSelect .= "WHERE  versionid = $versionid";
    $result = mysql_query($sqlSelect);
    if (!$result) {
        echo "getSessionNotes: " . mysql_error() . "<br/>";
        $logger->error(mysql_error());
        $logger->debug($sqlSelect());
    }
    $row = mysql_fetch_array($result);
    return $row['notes'];
}

function getSessionRequirements($versionid)
{
    $sqlSelect = "";
    $sqlSelect .= "SELECT requirementsid ";
    $sqlSelect .= "FROM   mission_requirements ";
    $sqlSelect .= "WHERE  versionid = $versionid";
    $result = mysql_query($sqlSelect);

    if (!$result) {
        echo "getSessionRequirements: " . mysql_error() . "<br/>";
        $logger->error(mysql_error());
        $logger->debug($sqlSelect());
    }

    $resultToReturn = array();
    while ($row = mysql_fetch_array($result)) {
        $resultToReturn[] = $row["requirementsid"];
    }

    return $resultToReturn;
}

function getSessionBugs($versionid)
{
    $sqlSelect = "";
    $sqlSelect .= "SELECT * ";
    $sqlSelect .= "FROM   mission_bugs ";
    $sqlSelect .= "WHERE  versionid = $versionid";
    $result = mysql_query($sqlSelect);
    if (!$result) {
        echo "getSessionBugs: " . mysql_error() . "<br/>";
        $logger->error(mysql_error());
        $logger->debug($sqlSelect());
    }

    $resultToReturn = array();
    while ($row = mysql_fetch_array($result)) {
        $resultToReturn[] = $row["bugid"];
    }

    return $resultToReturn;
}


function getConnectedSessions($versionid)
{
    $sqlSelect = "";
    $sqlSelect .= "SELECT * ";
    $sqlSelect .= "FROM   mission_sessionsconnections ";
    $sqlSelect .= "WHERE  linked_from_versionid = $versionid";

    $result = mysql_query($sqlSelect);
    if (!$result) {
        echo "getConnectedSessions: " . mysql_error() . "<br/>";
        $logger->error(mysql_error());
        $logger->debug($sqlSelect());
    }

    $resultToReturn = array();
    while ($row = mysql_fetch_array($result)) {
        $sessionid = GetSessionIdFromVersionId($row["linked_to_versionid"]);
        //Importera sessionid fr?n version -> l?gg det till arrayen nedan!!!
        $resultToReturn[] = $sessionid;
        //        $resultToReturn[] = $row["linked_to_versionid"];
    }

    return $resultToReturn;
}

function getSessionsLinkedFrom($versionid)
{
    $sqlSelect = "";
    $sqlSelect .= "SELECT * ";
    $sqlSelect .= "FROM   mission_sessionsconnections ";
    $sqlSelect .= "WHERE  linked_to_versionid = $versionid";

    $result = mysql_query($sqlSelect);
    if (!$result) {
        echo "getSessionsLinkedFrom: " . mysql_error() . "<br/>";
        $logger->error(mysql_error());
        $logger->debug($sqlSelect());
    }

    $resultToReturn = array();
    while ($row = mysql_fetch_array($result)) {
        $resultFromReturn[] = $row["linked_from_versionid"];
    }

    if (isset($resultFromReturn)) {
        return $resultFromReturn;
    }
    return null;
}

/**
 * @param  $versionid
 * @return array
 */
function getSessionAreas($versionid)
{
    $sqlSelectSessionStatus = "";
    $sqlSelectSessionStatus .= "SELECT areaname ";
    $sqlSelectSessionStatus .= "FROM   mission_areas ";
    $sqlSelectSessionStatus .= "WHERE  versionid = $versionid";

    $resultSessionAreas = mysql_query($sqlSelectSessionStatus);

    if (!$resultSessionAreas) {
        echo "getSessionAreas: " . mysql_error() . "<br/>";
        $logger->error(mysql_error());
        $logger->debug($sqlSelectSessionStatus());
    }

    $result = array();
    while ($row = mysql_fetch_array($resultSessionAreas)) {
        $result[] = $row["areaname"];
    }

    return $result;
}

function getAreas()
{
    $sqlSelectSessionStatus = "";
    $sqlSelectSessionStatus .= "SELECT areaname ";
    $sqlSelectSessionStatus .= "FROM   mission_areas";

    $resultSessionAreas = mysql_query($sqlSelectSessionStatus);

    if (!$resultSessionAreas) {
        echo "getSessionAreas: " . mysql_error() . "<br/>";
        $logger->error(mysql_error());
        $logger->debug($sqlSelectSessionStatus());
    }

    $result = array();
    while ($row = mysql_fetch_array($resultSessionAreas)) {
        $result[] = $row["areaname"];
    }

    return $result;
}

function getSessionAdditionalTester($versionid)
{
    $sqlSelect = "SELECT * FROM mission_testers WHERE versionid = $versionid";

    $resultSessionAreas = mysql_query($sqlSelect);

    if (!$resultSessionAreas) {
        echo "getSessionAdditionalTester: " . mysql_error() . "<br/>";
        $logger->error(mysql_error());
        $logger->debug($sqlSelect());
    }

    $result = array();
    while ($row = mysql_fetch_array($resultSessionAreas)) {
        $result[] = $row["tester"];
    }

    return $result;
}

function createPercentPieChart($setup, $test, $bug, $oppertunity, $sizeX = 450, $sizeY = 150, $labels = null)
{

    if ($labels == null) {
        $labels = array();
        $labels[] = "Setup($setup%)";
    }
    $totalTime = $setup + $test + $bug + $oppertunity;

    $imgURL = "http://chart.apis.google.com/chart?cht=p3&amp;chd=s:Uf9a&amp;chs=" . $sizeX . "x" . $sizeY . "&amp;chd=t:$setup,$test,$bug,$oppertunity&amp;chl=Setup($setup%)|Test($test%)|Bug inv($bug%)|Opportunity($oppertunity%)|&amp;chco=FF0000|00FF00|0000FF|000000&";
    //	echo "<img alt=\"pie chart\" src=\"$imgURL\" /\">";
    return $imgURL;
}

/**
 * @param array $valuesAndLables key value array, key = lables.
 * @param int $sizeX
 * @param int $sizeY
 * @return string
 */
function createChartPie($valuesAndLables, $title = null, $sizeX = 450, $sizeY = 150, $colors = array('FF0000', '00FF00', '0000FF'))
{
    $barTitles = "";
    $barValues = "";
    foreach ($valuesAndLables as $key => $value) {
        $barTitles = $barTitles . "|" . $key;
        $barValues = $barValues . "," . $value;
    }
    //echo $barValues;
    $barTitles = substr($barTitles, 1, strlen($barTitles));
    $barValues = substr($barValues, 1, strlen($barValues));

    $colors = implode("|", $colors);

    if ($title != null) {
        $title = "chtt=$title";
    } else {
        $title = "";
    }


    $imgURL = "http://chart.apis.google.com/chart?cht=p3&amp;chd=s:Uf9a&amp;chs=" . $sizeX . "x" . $sizeY . "&amp;chd=t:$barValues&amp;chl=$barTitles&amp;chco=$colors&$title";
    //	echo "<img alt=\"pie chart\" src=\"$imgURL\" /\">";
    return $imgURL;
}

function echoTeamSelect($team, $createConnection = true, $history = false, $id_name = "select_team")
{
    if ($createConnection)
        $con = getMySqlConnection();

    if (!$history) {
        $sqlSelect = "";
        $sqlSelect .= "SELECT teamname ";
        $sqlSelect .= "FROM   teamnames ";
        $sqlSelect .= "ORDER  BY `teamname` ASC ";
    } else
        $sqlSelect = "SELECT teamname FROM mission where teamname NOT LIKE 'null' group by teamname ASC ";

    $result = mysql_query($sqlSelect);

    echo "                                  <select id=\"$id_name\" name=\"team\">\n";


    echo "                                      <option></option>\n";


    while ($row = mysql_fetch_array($result)) {
        if (strcmp($team, $row['teamname']) == 0) {
            echo "                                      <option selected=\"selected\">" . htmlspecialchars($row['teamname']) . "</option>\n";
        } else {
            echo "                                      <option>" . htmlspecialchars($row['teamname']) . "</option>\n";
        }
    }

    echo "                                  </select>\n";

    if ($createConnection)
        mysql_close($con);
}


function addNotification($sessionid, $user)
{
    $con = getMySqlConnection();

    $versionid = getSessionVersionId($sessionid);
    $sql = "INSERT INTO `user_sessionsnotification` (`versionid`, `username`) VALUES ('$versionid', '$user')";

    $result = mysql_query($sql);
    if (!$result) {
        return false;
    }

    mysql_close($con);
    return true;
}

function echoTestEnvironmentSelect($testenvironment)
{
    $con = getMySqlConnection();


    $sqlSelect = "";
    $sqlSelect .= "SELECT name ";
    $sqlSelect .= "FROM   testenvironment ";
    $sqlSelect .= "ORDER  BY `name` ASC ";

    $result = mysql_query($sqlSelect);

    echo "                                  <select id=\"select_testenv\" name=\"testenv\">\n";


    echo "                                      <option></option>\n";


    while ($row = mysql_fetch_array($result)) {
        if (strcmp($testenvironment, $row['name']) == 0) {
            echo "                                      <option selected=\"selected\">" . htmlspecialchars($row['name']) . "</option>\n";
        } else {
            echo "                                      <option>" . htmlspecialchars($row['name']) . "</option>\n";
        }
    }

    echo "                                  </select>\n";

    mysql_close($con);
}

function getTestEnvironmentInformation($testenvironment)
{
    $con = getMySqlConnection();


    $sqlSelect = "";
    $sqlSelect .= "SELECT * ";
    $sqlSelect .= "FROM testenvironment WHERE name ='$testenvironment' ";

    $result = mysql_query($sqlSelect);
    $row = mysql_fetch_row($result);
    mysql_close($con);
    return $row;
}

function echoAdditionalTesterSelect($testers)
{
    $con = getMySqlConnection();

    $sqlSelect = "";
    $sqlSelect .= "SELECT username,fullname ";
    $sqlSelect .= "FROM   members ";
    $sqlSelect .= "WHERE active = TRUE "; // fix for ISSUE 15 user not visible in list.php when the user is inactivated
    $sqlSelect .= "ORDER  BY fullname ASC";

    $result = mysql_query($sqlSelect);

    echo "                                  <select id=\"additionalTester\" name=\"additionalTester[]\" multiple=\"multiple\" size=\"4\">\n";


    if (count($testers) == 0) {

        echo "                                      <option value=\"\" selected=\"selected\"></option>\n";
    } else {
        echo "                                      <option value=\"\"></option>\n";
    }

    if (!is_array($testers)) {
        $oneTester = $testers;
        $testers = array();
        $testers[] = $oneTester;
    }
    while ($row = mysql_fetch_array($result)) {
        if (in_array($row['username'], $testers)) {
            echo "                                      <option selected=\"selected\" value=\"" . $row['username'] . "\">" . htmlspecialchars($row['fullname']) . "</option>\n";
        } else {
            echo "                                      <option value=\"" . $row['username'] . "\">" . htmlspecialchars($row['fullname']) . "</option>\n";
        }
    }

    echo "                                  </select>\n";

    mysql_close($con);
}

function echoAreaSelect($area)
{
    $con = getMySqlConnection();


    $sqlSelect = "";
    $sqlSelect .= "SELECT areaname ";
    $sqlSelect .= "FROM   areas ";
    $sqlSelect .= "ORDER  BY `areaname` ASC ";
    $result = mysql_query($sqlSelect);

    echo "                                  <select id=\"select_area\" name=\"area[]\" multiple=\"multiple\" size=\"4\" >\n";


    if (count($area) == 0) {

        echo "                                      <option value=\"\" selected=\"selected\"></option>\n";
    } else {
        echo "                                      <option value=\"\"></option>\n";
    }

    if (!is_array($area)) {
        $oneArea = $area;
        $area = array();
        $area[] = $oneArea;
    }
    while ($row = mysql_fetch_array($result)) {
        if (in_array($row['areaname'], $area)) {
            echo "                                      <option selected=\"selected\" value=\"" . $row['areaname'] . "\">" . htmlspecialchars($row['areaname']) . "</option>\n";
        } else {
            echo "                                      <option value=\"" . $row['areaname'] . "\">" . htmlspecialchars($row['areaname']) . "</option>\n";
        }
    }

    echo "                                  </select>\n";

    mysql_close($con);
}

function echoAreaSelectSingel($area, $history = false, $id_name = "select_area")
{
    if (!is_array($area)) {
        $areaTmp = array();
        $areaTmp[] = $area;
        $area = $areaTmp;
    }
    $con = getMySqlConnection();

    if (!$history) {
        $sqlSelect = "";
        $sqlSelect .= "SELECT areaname ";
        $sqlSelect .= "FROM   areas ";
        $sqlSelect .= "ORDER  BY `areaname` ASC ";
    } else {
        $sqlSelect = "SELECT areaname FROM mission_areas where areaname not like 'null' group by areaname ASC;";
    }

    $result = mysql_query($sqlSelect);

    echo "                                  <select id=\"$id_name\" name=\"area\">\n";


    if (count($area) == 0) {

        echo "                                      <option value=\"\" selected=\"selected\"></option>\n";
    } else {
        echo "                                      <option value=\"\"></option>\n";
    }


    while ($row = mysql_fetch_array($result)) {
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

    mysql_close($con);
}

function echoNumberOfRowToDisplay()
{
    echo "<select id=\"select_norowtodisplay\" class=\"metricoption\" name=\"norowdisplay\">\n";
    echo "      <option>30</option>\n";
    echo "      <option>60</option>\n";
    echo "      <option>100</option>\n";
    echo "      <option>200</option>\n";
    echo "      <option>500</option>\n";
    echo "      <option>1000</option>\n";
    echo "      <option>2000</option>\n";
    echo "</select>\n";
}

function echoStatusTypes($statusToSelect)
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

function echoTesterSelect($name)
{
    $con = getMySqlConnection();


    $sqlSelect = "";
    $sqlSelect .= "SELECT username ";
    $sqlSelect .= "FROM   members ";
    //$sqlSelect .= "WHERE active = TRUE "; // fix for ISSUE 15 user not visible in list.php when the user is inactivated
    $sqlSelect .= "ORDER  BY fullname ASC";

    $result = mysql_query($sqlSelect);

    echo "                                  <select id=\"select_tester\" name=\"tester\">\n";
    echo "                                      <option></option>\n";

    while ($row = mysql_fetch_array($result)) {
        if (strcmp($name, $row['username']) == 0) {
            echo "                                      <option selected=\"selected\">" . htmlspecialchars($row['username']) . "</option>\n";
        } else {
            echo "                                      <option>" . htmlspecialchars($row['username']) . "</option>\n";
        }
    }

    echo "                                  </select>\n";

    mysql_close($con);
}

function getTesterFullName($username,$createMysqlCon=false)
{
    if($createMysqlCon)
        $con = getMySqlConnection();
    $sqlSelect = "";
    $sqlSelect .= "SELECT fullname ";
    $sqlSelect .= "FROM   members ";
    $sqlSelect .= "WHERE username = '$username' ";
    $sqlSelect .= "ORDER  BY fullname ASC";

    $result = mysql_query($sqlSelect);

    $row = mysql_fetch_row($result);
    if($createMysqlCon)
        mysql_close($con);

        return $row[0];
}

function echoTesterFullNameSelect($name, $removeInactiveUsers = false, $onlyShowUserThatHaveLoggedASession = false, $addUserName = false)
{
    $con = getMySqlConnection();
    if ($onlyShowUserThatHaveLoggedASession == true) {
        $sqlSelect = "SELECT DISTINCT mb.fullname, mb.username FROM sessionwebos.members as mb, sessionwebos.mission as m  WHERE mb.deleted = false AND mb.username LIKE m.username ORDER BY m.username ASC";
    } else {
        $sqlSelect = "";
        $sqlSelect .= "SELECT username,fullname ";
        $sqlSelect .= "FROM   members ";
        $sqlSelect .= "WHERE deleted = FALSE "; // fix for ISSUE 15 user not visible in list.php when the user is inactivated
        if ($removeInactiveUsers)
            $sqlSelect .= "AND active = TRUE "; // fix for ISSUE 15 user not visible in list.php when the user is inactivated
        $sqlSelect .= "ORDER  BY fullname ASC";
    }

    $result = mysql_query($sqlSelect);

    echo "                                  <select id=\"select_tester\" name=\"tester\" class='formChanged'>\n";
    echo "                                      <option></option>\n";

    while ($row = mysql_fetch_array($result)) {
        $userName = $row['username'];
        $fullName = $row['fullname'];

        if($addUserName)
        {
            $nameToDisplay = $fullName."(".$userName.")";
        }
        else
        {
          $nameToDisplay = $fullName;
        }
        if (strcmp($name, $row['username']) == 0) {
            echo "                                      <option value='$userName' selected=\"selected\">$nameToDisplay</option>\n";
        } else {
            echo "                                      <option value='$userName'>$nameToDisplay</option>\n";
        }
    }

    echo "                                  </select>\n";

    mysql_close($con);
}

function getApplicationsFromAreaNames()
{
    $con = getMySqlConnection();
    $sql = "select areaname from `areas` GROUP BY areaname";
    $result = mysql_query($sql);
    $appNames = array();
    while ($rowTeamAreas = mysql_fetch_array($result)) {
        $areaName = $rowTeamAreas['areaname'];
        $areaName = explode('-', $areaName, 2);
        $areaName = str_replace(" ", "", $areaName[0]);
        $appNames[strtoupper($areaName)] = $areaName;
    }
    mysql_close($con);
    return $appNames;
}

function getApplicationNameFromAreaName($areaName)
{
    $areaName = explode('-', $areaName, 2);
    $areaName = str_replace(" ", "", $areaName[0]);
    return $areaName;
}

function echoApplicationBasedOnAreasSelect($selectedapp, $history = false, $id_name = "select_app")
{
    $con = getMySqlConnection();
    $sql = "select areaname from `areas` GROUP BY areaname";
    $result = mysql_query($sql);
    $appNames = array();
    while ($rowTeamAreas = mysql_fetch_array($result)) {
        $areaName = $rowTeamAreas['areaname'];
        $areaName = explode('-', $areaName, 2);
        $areaName = str_replace(" ", "", $areaName[0]);
        $appNames[strtoupper($areaName)] = $areaName;
    }
    mysql_close($con);

    echo "                                  <select id=\"$id_name\" name=\"sprint\">\n";


    echo "                                      <option></option>\n";

    foreach ($appNames as $anApp) {
        if (strcmp($selectedapp, $anApp) == 0) {
            echo "                                      <option selected=\"selected\">" . htmlspecialchars($anApp) . "</option>\n";
        } else {
            echo "                                      <option>" . htmlspecialchars($anApp) . "</option>\n";
        }
    }

    echo "                                  </select>\n";

}

function echoSprintSelect($sprint, $history = false, $id_name = "select_sprint")
{
    $con = getMySqlConnection();

    if (!$history) {
        $sqlSelect = "";
        $sqlSelect .= "SELECT sprintname ";
        $sqlSelect .= "FROM   sprintnames ";
        $sqlSelect .= "ORDER  BY sprintname ASC";
    } else {
        $sqlSelect = "SELECT sprintname FROM mission where sprintname NOT LIKE 'null' group by sprintname ASC";
    }
    $result = mysql_query($sqlSelect);

    echo "                                  <select id=\"$id_name\" name=\"sprint\">\n";


    echo "                                      <option></option>\n";

    while ($row = mysql_fetch_array($result)) {
        if (strcmp($sprint, $row['sprintname']) == 0) {
            echo "                                      <option selected=\"selected\">" . htmlspecialchars($row['sprintname']) . "</option>\n";
        } else {
            echo "                                      <option>" . htmlspecialchars($row['sprintname']) . "</option>\n";
        }
    }

    echo "                                  </select>\n";

    mysql_close($con);
}

function echoCustomSelect($customTableName, $customSelectedArray)
{
    $con = getMySqlConnection();
    $sql = "SELECT * FROM custom_items WHERE tablename = '$customTableName'";
    $result = mysql_query($sql);
    if ($_SESSION['settings'][$customTableName . '_multiselect'] == 1) {
        echo "<select id='select_$customTableName' multiple='multiple' name='" . $customTableName . "[]'>\n";

    } else {
        echo "<select id='select_$customTableName' name='$customTableName'>\n";
    }
    echo "<option value=\"\"></option>\n";

    while ($row = mysql_fetch_array($result)) {
        if (in_array($row['name'], $customSelectedArray)) {
            echo "<option selected=\"selected\" value=\"" . $row['name'] . "\">" . htmlspecialchars($row['name']) . "</option>\n";
        } else {
            echo "<option value=\"" . $row['name'] . "\">" . htmlspecialchars($row['name']) . "</option>\n";
        }
    }
    echo "</select>\n";

    mysql_close($con);
}

function echoTeamSprintSelect($teamsprint)
{
    $con = getMySqlConnection();


    $sqlSelect = "";
    $sqlSelect .= "SELECT teamsprintname ";
    $sqlSelect .= "FROM   teamsprintnames ";
    $sqlSelect .= "ORDER  BY teamsprintname ASC";

    $result = mysql_query($sqlSelect);

    echo "                                  <select id=\"select_teamsprint\" name=\"teamsprint\">\n";


    echo "                                      <option></option>\n";

    while ($row = mysql_fetch_array($result)) {
        if (strcmp($teamsprint, $row['teamsprintname']) == 0) {
            echo "                                      <option selected=\"selected\">" . htmlspecialchars($row['teamsprintname']) . "</option>\n";
        } else {
            echo "                                      <option>" . htmlspecialchars($row['teamsprintname']) . "</option>\n";
        }
    }

    echo "                                  </select>\n";

    mysql_close($con);
}

function GetSessionPublicKey($versionid)
{

    $sqlSelect = "";
    $sqlSelect .= "SELECT publickey  ";
    $sqlSelect .= "FROM   `mission` ";
    $sqlSelect .= "WHERE  `versionid` = $versionid ";

    $result = mysql_query($sqlSelect);

    if (!$result) {
        echo "GetSessionPublicKey: " . mysql_error() . "<br/>";
        $logger->error(mysql_error());
        $logger->debug($sqlSelect());
    } else {
        $row = mysql_fetch_array($result);
        $publickey = $row["publickey"];
    }
    return $publickey;
}

function getUserSettings($createConnection = true)
{
    if ($createConnection)
        $con = getMySqlConnection();

    $sqlSelect = "";
    $sqlSelect .= "SELECT * ";
    $sqlSelect .= "FROM   user_settings ";
    $sqlSelect .= "WHERE  username = '" . $_SESSION['username'] . "'";


    $result = mysql_query($sqlSelect);

    if (!$result) {
        echo "getUserSettings: " . mysql_error() . "<br/>";
    }

    if ($createConnection)
        mysql_close($con);
    return mysql_fetch_array($result);
}

function getSettings($createConnection = true)
{
    if ($createConnection)
        $con = getMySqlConnection();


    $sqlSelect = "";
    $sqlSelect .= "SELECT * ";
    $sqlSelect .= "FROM   settings ";


    $result = mysql_query($sqlSelect);

    if (!$result) {
        echo "getSettings: " . mysql_error() . "<br/>";
    }

    if ($createConnection)
        mysql_close($con);
    return mysql_fetch_array($result);
}

function getSettingsDoNotCreateMysqlConnection()
{


    $sqlSelect = "";
    $sqlSelect .= "SELECT * ";
    $sqlSelect .= "FROM   settings ";


    $result = mysql_query($sqlSelect);

    if (!$result) {
        echo "getSettings: " . mysql_error() . "<br/>";
    }

    return mysql_fetch_array($result);
}

function getSessionWebVersion()
{
    $con = getMySqlConnection();


    $sqlSelect = "";
    $sqlSelect .= "SELECT * ";
    $sqlSelect .= "FROM   version ";

    $result = mysql_query($sqlSelect);

    if (!$result) {
        return null;
        //        echo "getSessionVersion: " . mysql_error() . "<br/>";
    }

    mysql_close($con);
    $row = mysql_fetch_array($result);
    $versionInstalled = $row['versioninstalled'];
    return $versionInstalled;
}

function echoSessionStatusTypesSelect()
{
    echo "<select id='sessionstatus'>\n";
    echo "    <option value='' ></option>\n";
    echo "    <option value='notexe' >Not Executed</option>\n";
    echo "    <option value='exe' >Executed</option>\n";
    echo "    <option value='deb' >Debriefed</option>\n";
    echo "</select>\n";
}

function insertAutomaticGoBackOnePage()
{
    echo "<script type='text/JavaScript'>\n";
    echo "setTimeout(\"location.href = '" . $_SERVER['HTTP_REFERER'] . "';\",3000);\n";
    echo "</script>\n";
}

function doesUserHaveNotifications()
{
    $con = getMySqlConnection();


    $sql = "    SELECT COUNT(us.versionid) as COUNT FROM `user_sessionsnotification` as us, mission_status as ms  WHERE us.`username` = '" . $_SESSION['username'] . "' and acknowledge=false and ms.executed=true AND us.versionid=ms.versionid";

    $result = mysql_query($sql);

    if ($result)
        $row = mysql_fetch_row($result);
    if ($con)
        mysql_close($con);

    if (strcmp($row[0], '0') == 0) {
        return false;

    } else {
        return true;

    }
}

function doesUserExist($username, $mysqlConnect = false)
{
    $logger = new logging();
    if (mysqlConnect)
        $con = getMySqlConnection();

    $username = mysql_real_escape_string($username);
    $sql = "SELECT * FROM members WHERE username LIKE  '$username'";
    $logger->sql($sql, __FILE__, __LINE__);
    $result = mysql_query($sql);

    if (mysql_numrows($result) == 1)
    {
        $result = true;
        $logger->debug("$username does exist in member table",__FILE__,__LINE__);

    }
    else {
        $logger->debug("$username does not exist in member table",__FILE__,__LINE__);
        $result = false;
    }
    if (mysqlConnect)
        mysql_close($con);
    return $result;
}


function getNotifications()
{
    $con = getMySqlConnection();


    $sql = "SELECT us.versionid, m.title  FROM `user_sessionsnotification` as us, mission as m, mission_status as ms  WHERE us.`username` = '" . $_SESSION['username'] . "' and acknowledge=false and ms.executed=true AND us.versionid=ms.versionid AND us.versionid=m.versionid";
    $result = mysql_query($sql);


    while ($row = mysql_fetch_array($result)) {
        $sessionid = getSessionSessionId($row['versionid']);
        echo " <p><a href='?sessionid=$sessionid&cmd=ack'><img src = '../pictures/dialog-cancel-3-small.png' id = 'ack_" . $row['versionid'] . "' class = 'acknowledge' alt = 'acknowledge'></a>\n";

        echo "<a href='../session.php?sessionid=$sessionid&command=view' target='_top' class='link_cat_1'>" . $row['title'] . "</a></p>\n";
    }
    mysql_close($con);

}

function removeNotification($versionid)
{
    $con = getMySqlConnection();


    $sql = "DELETE FROM `user_sessionsnotification` WHERE `versionid`='$versionid' AND username='" . $_SESSION['username'] . "';";
    $result = mysql_query($sql);

    mysql_close($con);

}

function isSessionDebriefed($versionId)
{
    $missionStatus = getSessionStatus($versionId);
    if ($missionStatus['debriefed'] == 1)
        return true;
    return null;
}

function isSessionClosed($versionId)
{
    $missionStatus = getSessionStatus($versionId);
    if ($missionStatus['closed'] == 1)
        return true;
    return false;
}

function echoFetchVersionautomatically()
{
    $con = getMySqlConnection();


    $sql = "SELECT * FROM  testenvironment WHERE  `url` LIKE  '%http%'";
    $result = mysql_query($sql);
    $nbrOfRows = mysql_num_rows($result);
    if ($nbrOfRows != 0) {
        echo "                                  <p id='getsoftwarerunning'>\n";
        echo "                                  <a href='#'>Get software version automatically</a></p>\n";
        echo "                                  <p class='boldred' id='getswmsg'></p>";
        echo "                                  <div id='autoswdiv'>";

        if (isset($_GET["sessionid"])) {
            $versionid = getSessionVersionId($_GET["sessionid"]);
            $sql = "SELECT * FROM softwareuseautofetched WHERE versionid = $versionid";
            $result = mysql_query($sql);
            while ($row = mysql_fetch_array($result)) {
                $id = $row['id'];
                $date = $row['updated'];
                $environment = $row['environment'];
                echo "<p  id='swenv_$id'><a class='uploadajax' href='api/environments/getrunningversions/?id=$id'>$environment($date)</a><span id='$id' class='swenvdelete'> [delete]</span></p>";

            }

        }

        echo "                                  </div>";
    }
    mysql_close($con);
}

function echoFetchVersionView()
{


    $sql = "SELECT * FROM  testenvironment WHERE  `url` LIKE  '%http%'";
    $result = mysql_query($sql);
    $nbrOfRows = mysql_num_rows($result);
    if ($nbrOfRows != 0) {
        echo "                                  <div id='autoswdiv'>";

        if (isset($_GET["sessionid"])) {
            $versionid = getSessionVersionId($_GET["sessionid"]);
            $sql = "SELECT * FROM softwareuseautofetched WHERE versionid = $versionid";
            $result = mysql_query($sql);
            while ($row = mysql_fetch_array($result)) {
                $id = $row['id'];
                $date = $row['updated'];
                $environment = $row['environment'];
                echo "<p  id='swenv_$id'><a class='uploadajax' href='api/environments/getrunningversions/?id=$id'>$environment($date)</a></p>";

            }

        }

        echo "                                  </div>";
    }

}

function str_startsWith($haystack, $needle)
{
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

function str_endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    $start = $length * -1; //negative
    return (substr($haystack, $start) === $needle);
}

function isAdmin()
{
    if ($_SESSION['useradmin'] == 1) {
        return true;
    } else {
        return false;
    }
}

function isSuperUser()
{
    if ($_SESSION['useradmin'] == 1 || $_SESSION['superuser'] == 1) {
        return true;
    } else {
        return false;
    }
}

function getUserName()
{
    return $_SESSION['username'];
}


?>