<?php
ob_start();
include 'config/db.php.inc';
include 'config/auth.php.inc';
include 'classes/authentication.php';
include_once 'include/commonFunctions.php.inc';
require_once 'classes/logging.php';

$logger = new logging();

sleep(0.5); //brute force of password mitigation. It will take too long to brute force if we add a sleep. end user will not detect it.

$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("cannot connect");
mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

mysql_set_charset('utf8');

$registred = validateUserAsLdapUser($con);

if (!$registred) {
    validateUserAsSessionwebUser();
}

mysql_close($con);
ob_end_flush();

function validateUserAsLdapUser($con)
{
    $logger = new logging();
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
                $logger->sql($sql,__FILE__,__LINE__);
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
                $logger->sql($sqlInsert,__FILE__,__LINE__);
                $result = mysql_query($sqlInsert);
                if(!$result)
                {
                    $logger->error(mysqli_error($con),__FILE__,__LINE__);
                    $logger->error($sqlInsert,__FILE__,__LINE__);
                }

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
                $logger->sql($sqlInsert,__FILE__,__LINE__);
                $result = mysql_query($sqlInsert);


                if(!$result)
                {
                    $logger->error(mysqli_error($con),__FILE__,__LINE__);
                    $logger->error($sqlInsert,__FILE__,__LINE__);
                }

                $sql = "";
                $sql .= "SELECT * ";
                $sql .= "FROM   members ";
                $sql .= "WHERE  username = '$myusername' ";
                $sql .= "       AND active = 1 ";
                $logger->sql($sql,__FILE__,__LINE__);
                $result = mysql_query($sql);

                if(!$result)
                {
                    $logger->error(mysqli_error($con),__FILE__,__LINE__);
                    $logger->error($sql,__FILE__,__LINE__);
                }

                if(mysql_num_rows($result)==1)
                {
                    $logger->info("AD/LDAP user $myusername created",__FILE__,__LINE__);
                }
                else
                {
                    $logger->error("AD/LDAP user $myusername not created",__FILE__,__LINE__);
                }

                registrateSession($result, $myusername);
                return true;
            }

        }
    }
}

function validateUserAsSessionwebUser()
{
    $logger = new logging();
    $myusername = $_POST['myusername'];
    $mypassword = $_POST['mypassword'];
    $encrypted_mypassword = md5($mypassword);
    $myusername = stripslashes($myusername);
    $mypassword = stripslashes($mypassword);
    $myusername = mysql_real_escape_string($myusername);
    $mypassword = mysql_real_escape_string($mypassword);

    $mypassword = md5($mypassword);

    $sql = "";
    $sql .= "SELECT * ";
    $sql .= "FROM   members ";
    $sql .= "WHERE  username = '$myusername' ";
    $sql .= "       AND PASSWORD = '$mypassword' ";
    $sql .= "       AND active = 1 ";
    $logger->sql($sql,__FILE__,__LINE__);


    $result = mysql_query($sql);

    if ($result != FALSE) {
        $count = mysql_num_rows($result);
    }

// If result matched $myusername and $mypassword, table row must be 1 row

    if ($count == 1) {
        registrateSession($result, $myusername);
    } else {
        header("location:index.php?login=failed");
        $logger->debug("$myusername failed to log in (wrong password or non-existing user)",__FILE__,__LINE__);
    }
}

function registrateSession($result, $myusername)
{
    $logger = new logging();

    session_start();
    $row = mysql_fetch_array($result);

    $_SESSION['user'] = $row['fullname'];
    $_SESSION['superuser'] = $row['superuser'];
    $_SESSION['useradmin'] = $row['admin'];
    $_SESSION['username'] = $myusername;
    $_SESSION['settings'] = getSessionWebSettings();
    $_SESSION['active'] = $row['active'];
    $_SESSION['project'] = "0";

    $logger->debug("User logged in",__FILE__,__LINE__);

    header("location:index.php");
}

?>