<?php
require_once('include/loggingsetup.php');


const SESSIONWEB_DB_LAYOUT_LATEST_SQL = "SessionwebDbLayout_1.3.sql";
const SESSIONWEB_DB_LAYOUT_1_0_SQL = "SessionwebDbLayout_1.0.sql";
const SESSIONWEB_DB_LAYOUT_DELTA_1_0_1_2_SQL = "SessionwebDbLayoutDelta_1.2-_1.3.sql";
const SESSIONWEB_LATEST_VERSION = "1.3";

include_once('config/db.php.inc');
include_once ('include/commonFunctions.php.inc');
if ($_REQUEST[adm_user] != "") {

    $adm_user = $_REQUEST[adm_user];
    $adm_password = $_REQUEST[adm_password];
    $user_name = $_REQUEST[user_name];
    $user_password = $_REQUEST[user_password];

    //Check that adm user and password is correct.
    $con = mysql_connect('localhost', $adm_user, $adm_password) or die("Can not connect to Mysql db, please check your user and password");
    mysql_close($con);

    if ($_REQUEST['type'] == "upgrade") {
        echo "<br><br>To upgrade and add support for UTF-8 please backup your data in the database useing these step-by-step.";
        echo "<br>1. 'mysqldump --no-create-info -u root -p sessionwebos > outputfile.txt'";
        echo "<br>2. Install sessionweb 1.2 as a clean installtion.";
        echo "<br>3. execute 'mysql -u root -p sessionwebos < outputfile.txt'";
        echo "<br> this will load the data into the database again.";
        /* $versionArray = getSessionWebVersion();
                //Upgrade to 1.1
                if ($versionArray['versioninstalled'] == null) {
                    $loadDbWithSessionWebSql = "mysql -u$adm_user -p$adm_password < config/" . SESSIONWEB_DB_LAYOUT_DELTA_1_0_1_2_SQL . "";
                    echo $loadDbWithSessionWebSql . "<br>";

                    exec($loadDbWithSessionWebSql, $result);

                    $sql = "INSERT INTO sessionwebos.version (versioninstalled) VALUES (".SESSIONWEB_LATEST_VERSION.")";
                    echo $sql . "<br>";
                    $con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("cannot connect");
                    mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");
                    $result = mysql_query($sql);
                    mysql_close($con);
                    print_r($result);
                    echo "Database sessionwebos updated to ".SESSIONWEB_LATEST_VERSION." layout<br>";

        */

        //Upgrade to next version
        //        if($versionArray['versioninstalled']=="1.1")
        //        {
        //
        //        }


    }
    elseif ($_REQUEST['type'] == "install") {


        $loadDbWithSessionWebSql = "mysql -u$adm_user -p$adm_password < config/" . SESSIONWEB_DB_LAYOUT_LATEST_SQL . "";

        exec($loadDbWithSessionWebSql, $result);

        echo "Database sessionwebos created<br>";

        $createDatabaseSql = "mysql -u$adm_user -p$adm_password -e \"CREATE USER '$user_name'@'localhost' IDENTIFIED BY '$user_password';\" ";

        exec($createDatabaseSql, $result);
        echo "user $user_name created<br>";


        $setupGranSqlStep1 = "mysql -u$adm_user -p$adm_password -e \"GRANT USAGE ON * . * TO  '$user_name'@'localhost' IDENTIFIED BY  '$user_password' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;;\" ";
        exec($setupGranSqlStep1, $result);

        $setupGranSqlStep2 = "mysql -u$adm_user -p$adm_password -e \"GRANT SELECT , INSERT , UPDATE , DELETE ON  `sessionwebos` . * TO  '$user_name'@'localhost';;\" ";
        exec($setupGranSqlStep2, $result);

        echo "Access rights to sessionwebos for user $user_name is set to SELECT , INSERT , UPDATE , DELETE<br>";

        $configfileString = "<?php
        define('DB_HOST_SESSIONWEB', 'localhost');
        define('DB_USER_SESSIONWEB', '$user_name');
        define('DB_PASS_SESSIONWEB', '$user_password');
        define('DB_NAME_SESSIONWEB', 'sessionwebos');
        ?>";
        $myFile = "config/db.php.inc";
        $fh = fopen($myFile, 'w');
        fwrite($fh,
               $configfileString);
        fclose($fh);
        echo "<br>Installation finished. Please <b>remove</b> this file (install.php) and go to <a href=\"index.php\">Sessionweb</a> and use username <b>admin</b> and password <b>admin</b>.";
    }
    exit();
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Install Sessionweb</title>
    <link rel="stylesheet" type="text/css" href="css/install.css"
          media="all">

</head>
<body id="main_body">
<?php
//chown -R www-data:www-data files
$foldersToCheckRW = array("config/", "include/filemanagement/files/", "include/filemanagement/thumbnails/", "log/");
foreach ($foldersToCheckRW as $aFolder)
{
    $foldersOk = true;
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

if(!$foldersOk)
    exit();
?>
<div id="form_container">
    <h1><a>Install Sessionweb</a></h1>

    <form id="form_92890" class="appnitro" method="post" action="">
        <div class="form_description">
            <h2>Install Sessionweb</h2>

<?php
           if ($_REQUEST['type'] == "") {
            echo "<a href='?type=install'>New Installation</a><br>";
            echo "<a href='?type=upgrade'>Upgrade</a>";


            exit();
        }

            if ($_REQUEST['type'] == "upgrade") {
                echo "<br><br>To upgrade and add support for UTF-8 please follow these step-by-step.";
                echo "<br>1. Execute (change mysql admin user and password in the file you will use before executing it.) the '<b>sh conf/migrateToUtf8.sh</b>' for *nix system or '<b>config/migrateToUtf8.bat</b>' for windows system.";
                echo "<br> <br> This will upgrade the database to 1.2 version and migrate it to UTF-8.<br>";
                echo "<br> Please make sure that you have a valid backup before executing this script!<br> <br> <br> ";
                exit();
                /*
                $versionArray = getSessionWebVersion();

                if ($versionArray != null) {
                    echo "Current version installed: " . $versionArray['versioninstalled'] . "<br><br>";
                    if ($versionArray['versioninstalled'] == SESSIONWEB_LATEST_VERSION) {
                        echo "You already have the latest version installed<br><br>";
                        exit();
                    }
                }
                else
                {
                    echo "Current version installed: 1.0<br><br>";
                    echo "<b>Will upgrade to ".SESSIONWEB_LATEST_VERSION."</b><br><br>";

                }*/
            }

            ?>
            <p>Add information below to automatically create/upgrade the MySql database and load
                it with sessionweb tables on this server (localhost) <br>For installation on a linux/unix server please
                make sure
                that file
                conf/db.php.inc is possible to write to for the web server user. If not
                change file permission by executing <b>chmod 666 conf/db.php.inc</b>

            </p>

            <p>If you would like to create the database by yourself please edit
                conf/db.php.inc and import latest version of
                conf/mysql_sessionweb_layout.x.x.sql to your database <br> Please note
                if you would like to change the database name you will need to edit the
                databasename mysql_sessionweb_layout.x.x.sql

            </p>

            <p>If sessionweb database already is created and conf/db.php.inc is up
                to date please remove install.php to start to use sessionweb. Default user created is admin with
                password admin.</p>
        </div>
        <ul>
            <!--	<li id="li_1"><label class="description" for="element_1"> MySql host </label>-->
            <!--	<div><input id="element_1" name="mysqlhost" class="element text medium"-->
            <!--		type="text" maxlength="255" value="" /></div>-->
            <!--	</li>-->
            <li id="li_2"><label class="description" for="element_2"> MySql user
                with administration rights (e.g. root) </label>

                <div><input id="element_2" name="adm_user" class="element text medium"
                            type="text" maxlength="255" value=""/></div>
            </li>
            <li id="li_3"><label class="description" for="element_3"> MySql
                password for user above </label>

                <div><input id="element_3" name="adm_password"
                            class="element text medium" type="text" maxlength="255" value=""/></div>
            </li>
<?php
if ($_REQUEST['type'] == "install") {
            echo "<li id='li_5'>\n";
            echo "    <label class='description' >Mysql sessionweb user (e.g. sessionweb) - will be created </label>\n";
            echo "    <div>\n";
            echo "        <input id='element_5' name='user_name' class='element text medium' type='text' maxlength='255' value=''/>\n";
            echo "    </div>\n";
            echo "</li>\n";

            echo "<li id='li_6'>\n";
            echo "    <label class='description' > MySql password for user above </label>\n";
            echo "    <div>\n";
            echo "       <input id='element_6' name='user_password' class='element text medium' type='text' maxlength='255' value=''/>\n";
            echo "    </div>\n";
            echo "</li>\n";
        }
            ?>
            <li class="buttons"><input type="hidden" name="form_id" value="92890"/><input
                    id="saveForm" class="button_text" type="submit" name="submit"
                    value="Install"/></li>
        </ul>
    </form>
</div>
</body>
</html>
