<?php
include_once ('MySqlExecuter.php');
include_once ('../include/commonFunctions.php.inc');
include ('headerinstall.php');
require_once('../classes/logging.php');
session_start();



?>

<div id="container">
    <div><H1>Set/change language(collation) support in Mysql</H1></div>
    <?php

    $logger = new logging();

if (isset($_SESSION['useradmin'])) {
    if ($_SESSION['useradmin'] != 1) {
        $logger->info('Access of mysql collation page not granted! User does not have admin priviliges.', __FILE__, __LINE__);
        echo "<br><br><br>Admin privilege needed to be able to change database collation. Please login using a user that have admin rights.<br><br><br><br>";

    } else {
        if (!isset($_POST['dbadminuser']) || !isset($_POST['dbadminpassword']) || !isset($_POST['collation']))
            echoForm();
        else {
            update();

        }
    }
} else {
    $logger->info('Access of mysql collation page not granted! User is not logged in.', __FILE__, __LINE__);
    echo "<br><br><br>Admin privilege needed to be able to change database collation. Please login using a user that have admin rights.<br><br><br><br>";

}

    ?>
</div>
</body>
</html>
<?php
require_once('../config/db.php.inc');
require_once('../include/db.php');

function update()
{
    $logger = new logging();
    if (isset($_POST['dbadminuser'])) {
        $adminuser = $_POST['dbadminuser'];
    } else {
        $adminuser = "";
    }
    if (isset($_POST['dbadminpassword'])) {
        $adminpassword = $_POST['dbadminpassword'];
    } else {
        $adminpassword = "";
    }

    if (isset($_POST['collation'])) {
        $collation = $_POST['collation'];
    } else {
        $collation = "";
    }

    echo '
            <fieldset>
                <legend>Upate of MySql collation to ' . $collation . '</legend>
                <dl>
                    <dd>';

    if (tryDbConnection($adminuser, $adminpassword)) {
        $logger->info("Will try to change mysql collation to $collation",__FILE__,__LINE__);
        changeCharsetAndCollation('sessionwebos', 'utf8', $collation, 'localhost', $adminuser, $adminpassword);
        $logger->info("Changed mysql collation to $collation",__FILE__,__LINE__);

    }
    echo'         </dd>
                </dl>
            </fieldset>';
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

    if (isset($_REQUEST['install']) && strstr($_REQUEST['install'], "yes") != false) {
        echo "<p>Some of the fields was empty, please fill all fields and try again.</p>";
    }
    if (isset($_REQUEST['dbadminuser']))
        $adminuser = $_REQUEST['dbadminuser'];
    else
        $adminuser="";
    if (isset($_REQUEST['dbadminpassword']))
        $adminpassword = $_REQUEST['dbadminpassword'];
    else
        $adminpassword="";
    if (isset($_REQUEST['dbsessionwebuser']))
        $dbuser = $_REQUEST['dbsessionwebuser'];
    if (isset($_REQUEST['dbsessionwebpassword']))
        $dbpassword = $_REQUEST['dbsessionwebpassword'];
    if (isset($_REQUEST['dbname']))
        $dbname = $_REQUEST['dbname'];
    if (!isset($dbname)) {
        $dbname = "sessionwebos";
    }


    echo '<form action="fixcharset.php?install=yes" method="post" class="niceform">

            <fieldset>
                <legend>Credentials and collation</legend>
                <dl>
                    <dd>First of all: Make sure you have a backup of the database!!<br>
                    This functionality is not yet fully tested but it "should" work according to the sanity that have been made.
                    <br><br>Reason to set this other the default one (already set to utf8_general_ci) is that mysql will
                    treat a=å=ä and  o=ö when it search through the database. To avoid that it will treat å=a etc please
                      set the collation that best match your language. If you want to read more about this please go to
                      http://bugs.mysql.com/bug.php?id=57877
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
                <dl>
                    <dt><label for="collation_name">Collation:</label></dt>
                    <dd>
                        <select id="collation" name="collation">
                                        <option>utf8_czech_ci</option>
                                        <option>utf8_danish_ci</option>
                                        <option>utf8_esperanto_ci</option>
                                        <option>utf8_estonian_ci</option>
                                        <option SELECTED>utf8_general_ci</option>
                                        <option>utf8_hungarian_ci</option>
                                        <option>utf8_icelandic_ci</option>
                                        <option>utf8_latvian_ci</option>
                                        <option>utf8_lithuanian_ci</option>
                                        <option>utf8_persian_ci</option>
                                        <option>utf8_polish_ci</option>
                                        <option>utf8_roman_ci</option>
                                        <option>utf8_romanian_ci</option>
                                        <option>utf8_sinhala_ci</option>
                                        <option>utf8_slovak_ci</option>
                                        <option>utf8_slovenian_ci</option>
                                        <option>utf8_spanish2_ci</option>
                                        <option>utf8_spanish_ci</option>
                                        <option>utf8_swedish_ci</option>
                                        <option>utf8_turkish_ci</option>
                                        <option>utf8_unicode_ci</option>
                                  </select>
                    </dd>
                </dl>
            </fieldset>

            <fieldset class="action">
                <input type="submit" name="submit" id="submit" value="Update db"/>
            </fieldset>
        </form>';

}
include ('footerinstall.php');
?>
