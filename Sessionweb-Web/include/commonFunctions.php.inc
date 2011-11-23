<?php

include_once('session_database_functions.php.inc');

function checkForMaxAttachmentSize($updateJsFile = false)
{
    $maxsizebytes = getMaxUploadSize() * 1024 * 1024;
    echo "<b>Attachment size setup<br></b>";
    echo "Attachments max size according to php and mysql settings <b>" . getMaxUploadSize() . " mb</b><br>";
    $sessionwebPath = str_replace("\\", "/", getcwd());
    $sessionwebPath = substr($sessionwebPath, 0, strlen($sessionwebPath) - 8);
    $sessionwebPath = $sessionwebPath . "/include/filemanagement/application.js";
    if (updateOneLineInTextFile($sessionwebPath, "maxFileSize:", "        maxFileSize: $maxsizebytes"))
        echo "Updated /include/filemanagement/application.js with correct value";
    else
    {
        echo "<b>Failed to update /include/filemanagement/application.js with correct value, please update it manually</b>";
        echo 'Synchronize this with the parameter maxFileSize in include\filemanagement\application.js. Parameter <i><b>maxFileSize</b></i> should be <b>' . $maxsizebytes . '</b> in the js file.<br>';
        echo '<br>If the size is to small or large change the upload_max_filesize, post_max_size, memory_limit php configuration values in php.ini and synchronize this with the max_allowed_packet config value in mysql settings.<br>';
    }


}

function updateOneLineInTextFile($file, $needle, $replace)
{

    if (is_file($file) && is_writable($file)) {
        $lines = file($file);

        echo $needle . "<br>";
        echo $replace . "<br>";
        $fileContentToSave = "";
        foreach ($lines as $line_num => $line) {
            if (strstr($line,$needle) == false)
                $fileContentToSave = $fileContentToSave . $line. "";
            else
                $fileContentToSave = $fileContentToSave . $replace . "\n";

        }
        echo "______________________________________________<br>";
        echo $fileContentToSave;

        $fh = fopen($file, 'w') or die("can't open file");
        fwrite($fh, $fileContentToSave);
        fclose($fh);
        return true;
    }
    else
    {
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
    foreach ($foldersToCheckRW as $aFolder)
    {
        try
        {
            $ourFileName = $aFolder . "testFile.txt";

            $fh = fopen($ourFileName, 'w');
            fwrite($fh, "TestString\n");
            fclose($fh);
            if (file_exists($ourFileName))
                echo "folder $aFolder is RW => OK<br>";
            else
            {
                echo "folder $aFolder is RW => NOK (file could not be created)<br>";
                $foldersOk = false;
            }
        }
        catch (Exception $e) {
            echo "folder $aFolder is RW => NOK<br>";
            //echo 'Error: ', $e->getMessage(), "\n";
            echo "Please change folder $aFolder to allow read write for the www user (chmod 664)<br>";
        }
    }

    if (!$foldersOk) {
        echo "Pleas make sure that NOK folders above have read and write access for the WWW user";
        echo "In ubuntu/linux you can use the chown command to make the www user e.g. 'chown -R www-data:www-data include/filemanagement/files/' ";
        return false;
    }
    else
    {
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

    }
    else
    {
        $row = mysql_fetch_array($result);
        return $row;
    }
}


function getSessionMetrics($versionid)
{
    //	$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
    mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

    $sqlSelectSessionMetrics = "";
    $sqlSelectSessionMetrics .= "SELECT * ";
    $sqlSelectSessionMetrics .= "FROM   mission_sessionmetrics ";
    $sqlSelectSessionMetrics .= "WHERE  versionid = $versionid";
    $resultSessionMetrics = mysql_query($sqlSelectSessionMetrics);

    if (!$resultSessionMetrics) {
        echo "getSessionMetrics: " . mysql_error() . "<br/>";
        $logger->error(mysql_error());
        $logger->debug($sqlSelectSessionMetrics());
    }

    return mysql_fetch_array($resultSessionMetrics);
}

function getSessionDebrief($versionid)
{
    //	$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
    mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

    $sqlSelect = "";
    $sqlSelect .= "SELECT * ";
    $sqlSelect .= "FROM   mission_debriefnotes ";
    $sqlSelect .= "WHERE  versionid = $versionid";
    $resultSession = mysql_query($sqlSelect);

    if (!$resultSession) {
        echo "getSessionDebrief: " . mysql_error() . "<br/>";
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
        echo "getSessionData: " . mysql_error() . "<br/>";
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
    }
    else
    {
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
        //Importera sessionid från version -> lägg det till arrayen nedan!!!
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

    return $resultFromReturn;
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

function createPercentPieChart($setup, $test, $bug, $oppertunity)
{

    $totalTime = $setup + $test + $bug + $oppertunity;

    $imgURL = "http://chart.apis.google.com/chart?cht=p3&amp;chd=s:Uf9a&amp;chs=450x150&amp;chd=t:$setup,$test,$bug,$oppertunity&amp;chl=Setup($setup%)|Test($test%)|Bug inv($bug%)|Opportunity($oppertunity%)|&amp;chco=FF0000|00FF00|0000FF|000000&";
    //	echo "<img alt=\"pie chart\" src=\"$imgURL\" /\">";
    return $imgURL;
}

function echoTeamSelect($team)
{
    $con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("cannot connect");
    mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

    $sqlSelect = "";
    $sqlSelect .= "SELECT teamname ";
    $sqlSelect .= "FROM   teamnames ";
    $sqlSelect .= "ORDER  BY `teamname` ASC ";

    $result = mysql_query($sqlSelect);

    echo "                                  <select id=\"select_team\" name=\"team\">\n";


    echo "                                      <option></option>\n";


    while ($row = mysql_fetch_array($result))
    {
        if (strcmp($team, $row['teamname']) == 0) {
            echo "                                      <option selected=\"selected\">" . htmlspecialchars($row['teamname']) . "</option>\n";
        }
        else
        {
            echo "                                      <option>" . htmlspecialchars($row['teamname']) . "</option>\n";
        }
    }

    echo "                                  </select>\n";

    mysql_close($con);
}

function echoTestEnvironmentSelect($testenvironment)
{
    $con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("cannot connect");
    mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

    $sqlSelect = "";
    $sqlSelect .= "SELECT name ";
    $sqlSelect .= "FROM   testenvironment ";
    $sqlSelect .= "ORDER  BY `name` ASC ";

    $result = mysql_query($sqlSelect);

    echo "                                  <select id=\"select_testenv\" name=\"testenv\">\n";


    echo "                                      <option></option>\n";


    while ($row = mysql_fetch_array($result))
    {
        if (strcmp($testenvironment, $row['name']) == 0) {
            echo "                                      <option selected=\"selected\">" . htmlspecialchars($row['name']) . "</option>\n";
        }
        else
        {
            echo "                                      <option>" . htmlspecialchars($row['name']) . "</option>\n";
        }
    }

    echo "                                  </select>\n";

    mysql_close($con);
}

function echoAreaSelect($area)
{
    $con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("cannot connect");
    mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

    $sqlSelect = "";
    $sqlSelect .= "SELECT areaname ";
    $sqlSelect .= "FROM   areas ";
    $sqlSelect .= "ORDER  BY `areaname` ASC ";

    $result = mysql_query($sqlSelect);

    echo "                                  <select id=\"select_area\" name=\"area[]\" multiple=\"multiple\" size=\"4\">\n";


    if (count($area) == 0) {

        echo "                                      <option value=\"\" selected=\"selected\"></option>\n";
    }
    else
    {
        echo "                                      <option value=\"\"></option>\n";
    }

    if (!is_array($area)) {
        $oneArea = $area;
        $area = array();
        $area[] = $oneArea;
    }
    while ($row = mysql_fetch_array($result))
    {
        if (in_array($row['areaname'], $area)) {
            echo "                                      <option selected=\"selected\" value=\"" . $row['areaname'] . "\">" . htmlspecialchars($row['areaname']) . "</option>\n";
        }
        else
        {
            echo "                                      <option value=\"" . $row['areaname'] . "\">" . htmlspecialchars($row['areaname']) . "</option>\n";
        }
    }

    echo "                                  </select>\n";

    mysql_close($con);
}

function echoAreaSelectSingel($area)
{
    $con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("cannot connect");
    mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

    $sqlSelect = "";
    $sqlSelect .= "SELECT areaname ";
    $sqlSelect .= "FROM   areas ";
    $sqlSelect .= "ORDER  BY `areaname` ASC ";

    $result = mysql_query($sqlSelect);

    echo "                                  <select id=\"select_area\" name=\"area\">\n";


    if (count($area) == 0) {

        echo "                                      <option value=\"\" selected=\"selected\"></option>\n";
    }
    else
    {
        echo "                                      <option value=\"\"></option>\n";
    }


    while ($row = mysql_fetch_array($result))
    {
        if ($area != null) {
            if (in_array($row['areaname'], $area)) {
                echo "                                      <option selected=\"selected\" value=\"" . $row['areaname'] . "\">" . htmlspecialchars($row['areaname']) . "</option>\n";
            }
            else
            {
                echo "                                      <option value=\"" . $row['areaname'] . "\">" . htmlspecialchars($row['areaname']) . "</option>\n";
            }
        }
        else
        {
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
    }
    else
    {
        echo "      <option</option>\n";
    }
    if (strcmp($statusToSelect, "Not Executed") == 0) {
        echo "      <option selected=\"selected\">Not Executed</option>\n";
    }
    else
    {
        echo "      <option>Not Executed</option>\n";
    }

    if (strcmp($statusToSelect, "Executed") == 0) {
        echo "      <option selected=\"selected\">Executed</option>\n";
    }
    else
    {
        echo "      <option>Executed</option>\n";
    }

    if (strcmp($statusToSelect, "Debriefed") == 0) {
        echo "      <option selected=\"selected\">Debriefed</option>\n";
    }
    else
    {
        echo "      <option>Debriefed</option>\n";
    }
    echo "</select>\n";
}

function echoTesterSelect($name)
{
    $con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("cannot connect");
    mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

    $sqlSelect = "";
    $sqlSelect .= "SELECT username ";
    $sqlSelect .= "FROM   members ";
    //$sqlSelect .= "WHERE active = TRUE "; // fix for ISSUE 15 user not visible in list.php when the user is inactivated
    $sqlSelect .= "ORDER  BY fullname ASC";

    $result = mysql_query($sqlSelect);

    echo "                                  <select id=\"select_tester\" name=\"tester\">\n";
    echo "                                      <option></option>\n";

    while ($row = mysql_fetch_array($result))
    {
        if (strcmp($name, $row['username']) == 0) {
            echo "                                      <option selected=\"selected\">" . htmlspecialchars($row['username']) . "</option>\n";
        }
        else
        {
            echo "                                      <option>" . htmlspecialchars($row['username']) . "</option>\n";
        }
    }

    echo "                                  </select>\n";

    mysql_close($con);
}

function echoTesterFullNameSelect($name)
{
    $con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("cannot connect");
    mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

    $sqlSelect = "";
    $sqlSelect .= "SELECT username,fullname ";
    $sqlSelect .= "FROM   members ";
    //$sqlSelect .= "WHERE active = TRUE "; // fix for ISSUE 15 user not visible in list.php when the user is inactivated
    $sqlSelect .= "ORDER  BY fullname ASC";

    $result = mysql_query($sqlSelect);

    echo "                                  <select id=\"select_tester\" name=\"tester\">\n";
    echo "                                      <option></option>\n";

    while ($row = mysql_fetch_array($result))
    {
        $userName = $row['username'];
        $fullName = $row['fullname'];

        if (strcmp($name, $row['username']) == 0) {
            echo "                                      <option value='$userName' selected=\"selected\">$fullName</option>\n";
        }
        else
        {
            echo "                                      <option value='$userName'>$fullName</option>\n";
        }
    }

    echo "                                  </select>\n";

    mysql_close($con);
}

function echoSprintSelect($sprint)
{
    $con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("cannot connect");
    mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

    $sqlSelect = "";
    $sqlSelect .= "SELECT sprintname ";
    $sqlSelect .= "FROM   sprintnames ";
    $sqlSelect .= "ORDER  BY sprintname ASC";

    $result = mysql_query($sqlSelect);

    echo "                                  <select id=\"select_sprint\" name=\"sprint\">\n";


    echo "                                      <option></option>\n";

    while ($row = mysql_fetch_array($result))
    {
        if (strcmp($sprint, $row['sprintname']) == 0) {
            echo "                                      <option selected=\"selected\">" . htmlspecialchars($row['sprintname']) . "</option>\n";
        }
        else
        {
            echo "                                      <option>" . htmlspecialchars($row['sprintname']) . "</option>\n";
        }
    }

    echo "                                  </select>\n";

    mysql_close($con);
}

function echoTeamSprintSelect($teamsprint)
{
    $con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("cannot connect");
    mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

    $sqlSelect = "";
    $sqlSelect .= "SELECT teamsprintname ";
    $sqlSelect .= "FROM   teamsprintnames ";
    $sqlSelect .= "ORDER  BY teamsprintname ASC";

    $result = mysql_query($sqlSelect);

    echo "                                  <select id=\"select_teamsprint\" name=\"teamsprint\">\n";


    echo "                                      <option></option>\n";

    while ($row = mysql_fetch_array($result))
    {
        if (strcmp($teamsprint, $row['teamsprintname']) == 0) {
            echo "                                      <option selected=\"selected\">" . htmlspecialchars($row['teamsprintname']) . "</option>\n";
        }
        else
        {
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
    }
    else
    {
        $row = mysql_fetch_array($result);
        $publickey = $row["publickey"];
    }
    return $publickey;
}

function getUserSettings()
{
    $con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("cannot connect");
    mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

    $sqlSelect = "";
    $sqlSelect .= "SELECT * ";
    $sqlSelect .= "FROM   user_settings ";
    $sqlSelect .= "WHERE  username = '" . $_SESSION['username'] . "'";


    $result = mysql_query($sqlSelect);

    if (!$result) {
        echo "getUserSettings: " . mysql_error() . "<br/>";
    }

    mysql_close($con);
    return mysql_fetch_array($result);
}

function getSessionWebVersion()
{
    $con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("cannot connect");
    mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

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

?>