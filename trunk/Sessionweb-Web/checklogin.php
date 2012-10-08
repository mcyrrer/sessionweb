<?php
ob_start();
//require_once('include/loggingsetup.php');
include 'config/db.php.inc';
include 'config/auth.php.inc';
include 'classes/authentication.php';
include_once 'include/commonFunctions.php.inc';
//include_once 'include/db.php';
sleep(0.5); //brute force of password mitigation. It will take too long to brute force if we add a sleep. end user will not detect it.

$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("cannot connect");
mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

mysql_set_charset('utf8');

$registred = validateUserAsLdapUser();

if (!$registred) {
    validateUserAsSessionwebUser();
}

mysql_close($con);
ob_end_flush();

function validateUserAsLdapUser()
{
    if (LDAP_ENABLED) {
        $ldap = new authentication();
        $resultLdap = $ldap->getUserInfoThroughLdap($_POST['myusername'], $_POST['mypassword']);
        if (is_array($resultLdap)) {
            $myusername = mysql_real_escape_string($_POST['myusername']);

            $sql = "SELECT * FROM members WHERE username LIKE '" . $myusername . "'";

            $result = mysql_query($sql);

            if (mysql_numrows($result) == 1) {
                //VALID USER AND ACCOUNT EXIST IN SW
                $sql = "";
                $sql .= "SELECT * ";
                $sql .= "FROM   members ";
                $sql .= "WHERE  username = '$myusername' ";
                $sql .= "       AND active = 1 ";

                $result = mysql_query($sql);
                registrateSession($result, $myusername);
                return true;

            } else {
                //VALID USER BUT ACCOUNT DOES NOT EXIST IN SW
                $fullname = $resultLdap['name'];
                $username = $resultLdap['samaccountname'];
                $md5password = md5(rand());
                $sqlInsert = "";
                $sqlInsert .= "INSERT INTO `members` ";
                $sqlInsert .= "            (`username`, ";
                $sqlInsert .= "             `password`, ";
                $sqlInsert .= "             `fullname`, ";
                $sqlInsert .= "             `adaccount`, ";
                $sqlInsert .= "             `active`, ";
                $sqlInsert .= "             `admin`, ";
                $sqlInsert .= "             `superuser`) ";
                $sqlInsert .= "VALUES      ('$username', ";
                $sqlInsert .= "             '$md5password', ";
                $sqlInsert .= "             '$fullname', ";
                $sqlInsert .= "             '1', ";
                $sqlInsert .= "             '1', ";
                $sqlInsert .= "             '0', ";
                $sqlInsert .= "             '0')";
                $result = mysql_query($sqlInsert);

                $sqlInsert = "";
                $sqlInsert .= "INSERT INTO `user_settings` ";
                $sqlInsert .= "            (`username`, ";
                $sqlInsert .= "             `teamname`, ";
                $sqlInsert .= "             `default_team`, ";
                $sqlInsert .= "             `list_view`) ";
                $sqlInsert .= "VALUES      ('$username', ";
                $sqlInsert .= "             '', ";
                $sqlInsert .= "             '', ";
                $sqlInsert .= "             'all')";
                $result = mysql_query($sqlInsert);

                $sql = "";
                $sql .= "SELECT * ";
                $sql .= "FROM   members ";
                $sql .= "WHERE  username = '$myusername' ";
                $sql .= "       AND active = 1 ";

                $result = mysql_query($sql);
                registrateSession($result, $myusername);
                return true;
            }

        }
    }
}

function validateUserAsSessionwebUser()
{ //
//$con = getMySqlConnection();
// Define $myusername and $mypassword
    $myusername = $_POST['myusername'];
    $mypassword = $_POST['mypassword'];
// encrypt password
    $encrypted_mypassword = md5($mypassword);
//echo   $encrypted_mypassword;
// To protect MySQL injection (more detail about MySQL injection)
    $myusername = stripslashes($myusername);
    $mypassword = stripslashes($mypassword);
    $myusername = mysql_real_escape_string($myusername);
    $mypassword = mysql_real_escape_string($mypassword);

//echo "Password:".$mypassword;

//encrypt password
    $mypassword = md5($mypassword);
//echo "[".$mypassword."]<br>";

    $sql = "";
    $sql .= "SELECT * ";
    $sql .= "FROM   members ";
    $sql .= "WHERE  username = '$myusername' ";
    $sql .= "       AND PASSWORD = '$mypassword' ";
    $sql .= "       AND active = 1 ";


    $result = mysql_query($sql);

    if ($result != FALSE) {
        $count = mysql_num_rows($result);
    }

// If result matched $myusername and $mypassword, table row must be 1 row


    if ($count == 1) {
        //$logger->info("Loggin for $myusername passed");
        // Register $myusername, $mypassword and redirect to file "index.php"
        registrateSession($result, $myusername);
    } else {
        //$logger->info("Loggin for $myusername failed");
        //ob_clean();
        //echo "failed!!";
        header("location:index.php?login=failed");
        //echo "Wrong Username or Password";
    }
}

function registrateSession($result, $myusername)
{
    session_start();
    $row = mysql_fetch_array($result);

    $_SESSION['user'] = $row['fullname'];
    $_SESSION['superuser'] = $row['superuser'];
    $_SESSION['useradmin'] = $row['admin'];
    $_SESSION['username'] = $myusername;
    $_SESSION['settings'] = getSessionWebSettings();
    $_SESSION['active'] = $row['active'];
    $_SESSION['project'] = "0";


    //session_register("myusername");
    //session_register("mypassword");
    //ob_clean();
    //echo "User OK!";
    header("location:index.php");
}

?>