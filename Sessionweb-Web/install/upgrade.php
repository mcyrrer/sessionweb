<?php
require_once ('../include/loggedincheck.php');
include_once ('MySqlExecuter.php');
include_once ('../include/commonFunctions.php.inc');
include_once ('../config/db.php.inc');
if ($_SESSION['useradmin'] != 1) {
   echo "Admin privilege needed to be able to update sessionweb. Please login using a user that have admin rights.";
   exit();
}
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
    <div><H1>Upgrade of Sessionweb</H1></div>
<?php
        if ($_POST['dbadminuser'] == null || $_POST['dbadminpassword'] == null)
    echoForm();
else
{
    echo '                <fieldset>
                <legend>Upgrading to latest sessionweb</legend>
                <dl>
                    <dd>';
    upgrade();
    echo '    </dd>
                </dl>
            </fieldset>';
}
    ?>
</div>
</body>
</html>
<?php


function upgrade()
{

    $adminuser = $_POST['dbadminuser'];
    $adminpassword = $_POST['dbadminpassword'];

    $versions = array();
    $versions['1.0'] = "SessionwebDbLayoutDelta_1.0-_1.2.sql";
    $versions['1.2'] = "SessionwebDbLayoutDelta_1.2-_1.3.sql";
    $versions['1.3'] = "SessionwebDbLayoutDelta_1.3-_1.4.sql";
    $versions['1.4'] = "SessionwebDbLayoutDelta_1.4-_1.5.sql";
    $versions['1.5'] = "SessionwebDbLayoutDelta_1.5-_1.6.sql";

    $currentVersion = getSessionWebVersion();
    if ($currentVersion == null) {
        //Manage 1.0->1.x upgrade.... :(
        $currentVersion = '1.0';
    }

    if (array_key_exists($currentVersion, $versions)) {

        if (tryDbConnection($adminuser, $adminpassword) && file_exists($versions[$currentVersion])) {
            $con = @ mysql_connect("localhost", $adminuser, $adminpassword);
            mysql_query("SET NAMES utf8");
            mysql_query("SET CHARACTER SET utf8");
            $mysqlExecuter = new MySqlExecuter();
            echo "<h2>Upgrade of sessionweb from $currentVersion</h2>";
            
            $resultOfSql = $mysqlExecuter->multiQueryFromFile($versions[$currentVersion],DB_NAME_SESSIONWEB);
            mysql_close($con);

            if (sizeof($resultOfSql) == 0) {
                $versionAfterUpgrade = getSessionWebVersion();
                echo "Upgraded to version <b>$versionAfterUpgrade</b><br>";
                //Do recursive upgrade until latest version...
                if ($versionAfterUpgrade != $currentVersion && array_key_exists($versionAfterUpgrade, $versions)) {
                    upgrade();
                }
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

        elseif (tryDbConnection($adminuser, $adminpassword)) {
            echo "Sql file does not exist " . $versions[$currentVersion] . "<br>";
        }
        else
        {
            echo "Could not connect to MySql database, please check your user and password";

        }

    }
    else
    {
        echo "You already have upgrade to latest version.";
    }

}

function tryDbConnection($user, $password, $host = 'localhost')
{
    try
    {
        $con = @ mysql_connect($host, $user, $password);
        mysql_query("SET NAMES utf8");
        mysql_query("SET CHARACTER SET utf8");
        if ($con) {
            mysql_close($con);
            return true;
        }
        else
        {
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

    echo '<form action="upgrade.php" method="post" class="niceform">
            <fieldset>
                <legend>Information</legend>
                <dl>
                    <dd>';
    $currentversion = getSessionWebVersion();
    if ($currentversion == null)
        $currentversion = "1.0";
    echo "Current sessionweb version: " . $currentversion . "<br>";
    echo "Will upgrade to latest release.";
    echo '          </dd>
                </dl>
            </fieldset>
            <fieldset>
                <legend>Database admin credentials</legend>
                <dl>
                    <dd>This is the user that will update the database sessionwebos and create all tables etc in the database.
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

            <fieldset class="action">
                <input type="submit" name="submit" id="submit" value="Install"/>
            </fieldset>
        </form>';
}

?>
