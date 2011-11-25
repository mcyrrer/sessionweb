<?php
include_once ('MySqlExecuter.php');
include_once ('../include/commonFunctions.php.inc');

define("INSTALLATION_SCRIPT", "SessionwebDbLayout_1.4.sql");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Install Sessionweb</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script language="javascript" type="text/javascript" src="../js/niceforms/niceforms.js"></script>
    <link rel="stylesheet" type="text/css" media="all" href="../js/niceforms/niceforms-default.css"/>
</head>

<body>
<div id="container">
    <div><H1>Installation of Sessionweb</H1></div>
<?php

    echo $sessionwebPath;
    if ($_POST['dbadminuser'] == null || $_POST['dbadminpassword'] == null || $_POST['dbsessionwebuser'] == null || $_POST['dbsessionwebpassword'] == null)
        echoForm();
    else
    {
        install();

    }
    ?>
</div>
</body>
</html>
<?php

function install()
{
    $adminuser = $_POST['dbadminuser'];
    $adminpassword = $_POST['dbadminpassword'];
    $dbuser = $_POST['dbsessionwebuser'];
    $dbpassword = $_POST['dbsessionwebpassword'];
    echo '<form action="install.php?install=yes" method="post" class="niceform">
            <fieldset>
                <legend>Installation</legend>
                <dl>
                    <dd>';

    if (tryDbConnection($adminuser, $adminpassword)) {
        $con = @ mysql_connect("localhost", $adminuser, $adminpassword);
        $mysqlExecuter = new MySqlExecuter();
        $resultOfSql = $mysqlExecuter->multiQueryFromFile(INSTALLATION_SCRIPT);

        if (sizeof($resultOfSql) == 0) {
            echo "Database created and installed<br>";
            createDbConfigFile($dbuser, $dbpassword);
            createDbUser($dbuser, $dbpassword);
            echo "Delete this folder to make sure that no one can destroy your database!.<br>";
            echo "Use username <b>admin</b> and password <b>admin</b> to login.<br>";
            echo "<br><br>";
            echo "<a href='../index.php'>Go to Sessionweb login page</a> ";


        }
        else
        {
            foreach ($resultOfSql as $oneError)
            {
                echo "--------------ERROR--------------<br>";
                echo $oneError . "<br>";
            }
        }

    }
    echo'         </dd>
                </dl>
            </fieldset>
       <fieldset>
                <legend>Attachment setup</legend>
                <dl>
                    <dd>';
    checkForMaxAttachmentSize(true);
    echo '</dd>
                </dl>
            </fieldset>

        </form>';
    mysql_close($con);
}

function createDbUser($dbuser, $dbpassword)
{
    $sqlCreateUser = "CREATE USER '$dbuser'@'localhost' IDENTIFIED BY '$dbpassword'";
    $sqlGrantUsage = "GRANT USAGE ON * . * TO  '$dbuser'@'localhost' IDENTIFIED BY  '$dbpassword' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0";
    $sqlGrantSessionweb = "GRANT SELECT , INSERT , UPDATE , DELETE ON  `sessionwebos` . * TO  '$dbuser'@'localhost'";
    if (mysql_query($sqlCreateUser) === false) {
        echo "failed to create $dbuser user<br>";
    }
    else
    {
        echo "Created $dbuser user<br>";
    }
    if (mysql_query($sqlGrantUsage) === false) {
        echo "failed to grant usage for $dbuser user<br>";
    }
    else
    {
        echo "Added grant usage for $dbuser user<br>";
    }
    if (mysql_query($sqlGrantSessionweb) === false) {
        echo "failed to grant usage for sessionweb for $dbuser user<br>";
    }
    else
    {
        echo "Added grant usage for sessionwebos db for $dbuser user<br>";
    }
}

function createDbConfigFile($dbuser, $dbpassword)
{
    $configfileString = "<?php
        define('DB_HOST_SESSIONWEB', 'localhost');
        define('DB_USER_SESSIONWEB', '$dbuser');
        define('DB_PASS_SESSIONWEB', '$dbpassword');
        define('DB_NAME_SESSIONWEB', 'sessionwebos');
        ?>";
    $sessionwebPath = str_replace("\\", "/", getcwd());
    $sessionwebPath = substr($sessionwebPath, 0, strlen($sessionwebPath) - 8);
    $myFile = $sessionwebPath . "/config/db.php.inc";
    $fh = fopen($myFile, 'w');
    fwrite($fh,
           $configfileString);
    fclose($fh);
}

function tryDbConnection($user, $password, $host = 'localhost')
{
    try
    {
        $con = @ mysql_connect($host, $user, $password);
        if ($con) {
            mysql_close($con);
            return true;
        }
        else
        {
            echo "Could not connect to MySql database, please check your user and password";
            return false;
        }
    }
    catch (Exception $e) {
        echo "Could not connect to MySql database, please check your user and password";
        return false;
    }
}

function echoForm()
{
    if (strstr($_GET['install'], "yes") != false) {
        echo "<p>Some of the fields was empty, please fill all fields and try again.</p>";
    }
    $adminuser = $_POST['dbadminuser'];
    $adminpassword = $_POST['dbadminpassword'];
    $dbuser = $_POST['dbsessionwebuser'];
    $dbpassword = $_POST['dbsessionwebpassword'];


    echo '<form action="install.php?install=yes" method="post" class="niceform">
            <fieldset>
                <legend>Checking read write for some folders and setup of attachments</legend>
                <dl>
                    <dd>';

    checkFoldersForRWDuringInstallation();


    echo '  </dd>

                </dl>
            </fieldset>
            <fieldset>
                <legend>Database admin credentials</legend>
                <dl>
                    <dd>This is the user that will create database sessionwebos and create all tables etc in the database. Username ande password is NOT saved or used after installation.
                    </dd>

                </dl>
                <dl>
                    <dt><label for="dbadminuser">Username</label></dt>
                    <dd><input type="text" name="dbadminuser" id="dbadminuser" value="' . $adminuser . '" size="32" maxlength="128"/></dd>
                </dl>
                <dl>
                    <dt><label for="dbadminpassword">Password:</label></dt>
                    <dd><input type="password" name="dbadminpassword" id="dbadminpassword" value="' . $adminpassword . '" size="32" maxlength="32"/></dd>
                </dl>
            </fieldset>


           <fieldset>
                <legend>Sessionweb Database Credentials</legend>
                <dl>
                    <dd>This is the user that sessionweb will use for all normal access like SELECT, INSERT and DELETE.
                        Should not be the same as the admin user.
                    </dd>
                </dl>
                <dl>
                    <dt><label for="dbsessionwebuser">Username</label></dt>
                    <dd><input type="text" name="dbsessionwebuser" id="dbsessionwebuser" value="' . $dbuser . '" size="32" maxlength="128"/></dd>
                </dl>
                <dl>
                    <dt><label for="dbsessionwebpassword">Password:</label></dt>
                    <dd><input type="password" name="dbsessionwebpassword" id="dbsessionwebpassword" value="' . $dbpassword . '" size="32"
                               maxlength="32"/></dd>
                </dl>
            </fieldset>

            <fieldset class="action">
                <input type="submit" name="submit" id="submit" value="Install"/>
            </fieldset>
        </form>';

}

function checkFoldersForRWDuringInstallation()
{
    echo "<b>Check for Read Write access for certain folders.</b><br>";
    $foldersToCheckRW = array("../config/", "../include/filemanagement/files/", "../include/filemanagement/thumbnails/", "../log/");
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
            {
                echo "folder $aFolder is RW => OK<br>";
                unlink($ourFileName);
            }
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

?>
