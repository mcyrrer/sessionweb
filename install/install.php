<?php
session_start();

include_once ('MySqlExecuter.php');
include_once ('../include/commonFunctions.php.inc');
require_once ('../classes/logging.php');
include ('headerinstall.php');
$logger = new logging();

define("INSTALLATION_SCRIPT", "SessionwebDbLayout_25.sql");
?>
<div id="container">
    <div><H1>Installation of Sessionweb</H1></div>
    <?php
    if (file_exists('../config/db.php.inc')) {
        if (isset($_SESSION['useradmin']) && $_SESSION['useradmin'] != 1) {
            $logger->info('Access of installation page not granted! User does not have admin priviliges.', __FILE__, __LINE__);
            echo "<br><br><br>Admin privilege needed to be able to reinstall sessionweb. Please login using a user that have admin rights.<br>";
            echo "To avoid this message you can delete include/db.php. This will allow you to do a clean install.<br><br><br><br>";
        } elseif (isset($_SESSION['useradmin']) && $_SESSION['useradmin'] == 1) {
            if (!isset($_POST['dbadminuser']) || !isset($_POST['dbadminpassword']) || !isset($_POST['dbsessionwebuser']) || !isset($_POST['dbsessionwebpassword'])) {
                echo "<p>WARNING: sessionweb is already installed, by installing it again you will loose your database content!!!</p>";
                echoForm();

            } else {
                install();
            }
        }
        else {
            $logger->info('Access of installation page not granted! User not logged in.', __FILE__, __LINE__);
            echo "<br><br><br>Admin privilege needed to be able to reinstall sessionweb. Please login using a user that have admin rights.<br><br><br><br>";
        }

    } else {

        //echo $sessionwebPath;
        if (!isset($_POST['dbadminuser']) || !isset($_POST['dbadminpassword']) || !isset($_POST['dbsessionwebuser']) || !isset($_POST['dbsessionwebpassword']))
            echoForm();
        else {
            install();
        }
    }
    ?>
</div>
</body>
</html>
<?php

function install()
{
    $logger = new logging();
    $adminuser = $_POST['dbadminuser'];
    $adminpassword = $_POST['dbadminpassword'];
    $dbuser = $_POST['dbsessionwebuser'];
    $dbpassword = $_POST['dbsessionwebpassword'];
    $dbname = $_POST['dbname'];
    $dbcreateuser = $_POST['dbcreateuser'];
    $dbcreatedb = $_POST['dbcreatedb'];
    if (strcmp($dbcreatedb, "true") === 0)
        $createDb = true;
    else
        $createDb = false;

    echo '<form action="install.php?install=yes" method="post" class="niceform">
            <fieldset>
                <legend>Installation</legend>
                <dl>
                    <dd>';

    if (tryDbConnection($adminuser, $adminpassword)) {
        $con = @ mysql_connect("localhost", $adminuser, $adminpassword);
        mysql_query("SET NAMES utf8");
        mysql_query("SET CHARACTER SET utf8");
        $mysqlExecuter = new MySqlExecuter();
        $logger->debug("Will install sessionweb with file " . INSTALLATION_SCRIPT, __FILE__, __LINE__);
        $resultOfSql = $mysqlExecuter->multiQueryFromFile(INSTALLATION_SCRIPT, $dbname, $createDb);

        if (sizeof($resultOfSql) == 0) {
            echo "Database created and installed<br>";
            $logger->info("INSTALLATION: Database created");
            createDbConfigFile($dbuser, $dbpassword, $dbname);
            if (strcmp($dbcreateuser, "true") == 0)
                createDbUser($dbuser, $dbpassword, $dbname);
            else {
                echo "User not created since checkbox was unchecked.<br>";
                $logger->info("Database user not created since checkbox was unchecked");
            }
            echo "Delete this folder to make sure that no one can destroy your database!.<br>";
            echo "Use username <b>admin</b> and password <b>admin</b> to login.<br>";
            echo "<br><br>";
            echo "<a href='../index.php'>Go to Sessionweb login page</a> ";


        } else {
            $logger->error("INSTALLATION: Error during installation", __FILE__, __LINE__);
            foreach ($resultOfSql as $oneError) {
                echo "--------------ERROR--------------<br>";
                $logger->error($oneError);
                echo $oneError . "<br>";
            }
        }

    }
    echo'         </dd>
                </dl>
            </fieldset>
       <fieldset>
                <legend>Attachment information</legend>
                <dl>
                    <dd>';
    checkForMaxAttachmentSize(true);
    echo '</dd>
                </dl>
            </fieldset>

        </form>';
    if (isset($con))
        mysql_close($con);
}

function createDbUser($dbuser, $dbpassword, $dbname)
{
    $logger = new logging();
    $sqlCreateUser = "CREATE USER '$dbuser'@'localhost' IDENTIFIED BY '$dbpassword'";
    $sqlGrantUsage = "GRANT USAGE ON * . * TO  '$dbuser'@'localhost' IDENTIFIED BY  '$dbpassword' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0";
    $sqlGrantSessionweb = "GRANT SELECT , INSERT , UPDATE , DELETE ON  `$dbname` . * TO  '$dbuser'@'localhost'";
    if (mysql_query($sqlCreateUser) === false) {
        echo "failed to create $dbuser user<br>";
        $logger->error("Failed to create user $dbuser.Does it already exist? ", __FILE__, __LINE__);
        $logger->sql($sqlCreateUser, __FILE__, __LINE__);
    } else {
        echo "Created $dbuser user<br>";
    }
    if (mysql_query($sqlGrantUsage) === false) {
        echo "failed to grant usage for $dbuser user<br>";
        $logger->error("Failed to grant usage for $dbuser", __FILE__, __LINE__);
        $logger->sql($sqlGrantUsage, __FILE__, __LINE__);


    } else {
        echo "Added grant usage for $dbuser user<br>";
    }
    if (mysql_query($sqlGrantSessionweb) === false) {
        echo "failed to grant usage for sessionweb for $dbuser user<br>";
        $logger->error("Failed to usage for sessionweb for $dbuser", __FILE__, __LINE__);
        $logger->sql($sqlGrantSessionweb, __FILE__, __LINE__);


    } else {
        echo "Added grant usage for sessionwebos db for $dbuser user<br>";
    }
}

function createDbConfigFile($dbuser, $dbpassword, $dbname)
{
    $configfileString = "<?php
        define('DB_HOST_SESSIONWEB', 'localhost');
        define('DB_USER_SESSIONWEB', '$dbuser');
        define('DB_PASS_SESSIONWEB', '$dbpassword');
        define('DB_NAME_SESSIONWEB', '$dbname');
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
    try {
        $con = @ mysql_connect($host, $user, $password);
        mysql_query("SET NAMES utf8");
        mysql_query("SET CHARACTER SET utf8");
        if ($con) {
            mysql_close($con);
            return true;
        } else {
            echo "Could not connect to MySql database, please check your user and password";
            return false;
        }
    } catch (Exception $e) {
        echo "Could not connect to MySql database, please check your user and password";
        return false;
    }
}

function echoForm()
{
    if (isset($_GET['install'])) {
        if (strstr($_GET['install'], "yes") != false) {
            echo "<p>Some of the fields was empty, please fill all fields and try again.</p>";
        }
    }
    if (isset($_POST['dbadminuser'])) {
        $adminuser = $_POST['dbadminuser'];
    } else {
        $adminuser = null;
    }
    if (isset($_POST['dbadminpassword'])) {
        $adminpassword = $_POST['dbadminpassword'];
    } else {
        $adminpassword = null;
    }
    if (isset($_POST['dbsessionwebuser'])) {
        $dbuser = $_POST['dbsessionwebuser'];
    } else {
        $dbuser = null;
    }
    if (isset($_POST['dbsessionwebpassword'])) {
        $dbpassword = $_POST['dbsessionwebpassword'];
    } else {
        $dbpassword = null;
    }
    if (isset($_POST['dbname'])) {
        $dbname = $_POST['dbname'];
    } else {
        $dbname = null;
    }

    if ($dbname == null) {
        $dbname = "sessionwebos";
    }


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
                        Should not be the same as the admin user if posible.
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
                <dl>
                     <dt><label for="dbname">Database:</label></dt>
                     <dd><input type="text" name="dbname" id="dbadminpassword" value="' . $dbname . '" size="32" maxlength="32"/></dd>
                </dl>
                <dl>
                     <dd>Create Database: <input type="checkbox" name="dbcreatedb" value="true" checked /></dd>
                     <dd>Create User: <input type="checkbox" name="dbcreateuser" value="true" checked  /></dd>
                </dl>
            </fieldset>

            <fieldset class="action">
                <input type="submit" name="submit" id="submit" value="Install"/>
            </fieldset>
        </form>';

}

function checkFoldersForRWDuringInstallation()
{
    $logger = new logging();
    echo "<b>Check for Read Write access for certain folders.</b><br>";
    $foldersToCheckRW = array("../config/", "../log/"); //"../include/filemanagement/files/", "../include/filemanagement/thumbnails/"
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
            $logger->error("folder $aFolder is RW => NOK");
            $logger->info("Please change folder $aFolder to allow read write for the www user (chmod 664)");

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

?>
<?php
include ('footerinstall.php');
?>
