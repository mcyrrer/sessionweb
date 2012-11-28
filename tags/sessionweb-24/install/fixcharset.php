<?php
include_once ('MySqlExecuter.php');
include_once ('../include/commonFunctions.php.inc');

define("INSTALLATION_SCRIPT", "SessionwebDbLayout_1.7.sql");
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
    <div><H1>Set language(collation) to support in Mysql</H1></div>
    <?php

    //echo $sessionwebPath;
    if (!isset($_POST['dbadminuser']) || !isset($_POST['dbadminpassword']) || !isset($_POST['collation']))
        echoForm();
    else {
        update();

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
    $adminuser = $_POST['dbadminuser'];
    $adminpassword = $_POST['dbadminpassword'];
    $collation = $_POST['collation'];


    echo '
            <fieldset>
                <legend>Upate of MySql collation to ' . $collation . '</legend>
                <dl>
                    <dd>';

    if (tryDbConnection($adminuser, $adminpassword)) {
        changeCharsetAndCollation('sessionwebos', 'utf8', $collation, 'localhost', $adminuser, $adminpassword);
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
    if (isset($_GET['install']) && strstr($_GET['install'], "yes") != false) {
        echo "<p>Some of the fields was empty, please fill all fields and try again.</p>";
    }
    if (isset($_POST['dbadminuser']))
        $adminuser = $_POST['dbadminuser'];
    else
        $adminuser="";
    if (isset($_POST['dbadminpassword']))
        $adminpassword = $_POST['dbadminpassword'];
    else
        $adminpassword="";
    if (isset($_POST['dbsessionwebuser']))
        $dbuser = $_POST['dbsessionwebuser'];
    if (isset($_POST['dbsessionwebpassword']))
        $dbpassword = $_POST['dbsessionwebpassword'];
    if (isset($_POST['dbname']))
        $dbname = $_POST['dbname'];
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

?>
