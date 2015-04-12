<?php
ob_start();
include 'classes/autoloader.php';
$dbm = new dbHelper();
$con = $dbm->connectToLocalDb();
$logger = new logging();

sleep(0.5); //brute force of password mitigation. It will take too long to brute force if we add a sleep. end user will not detect it.


$authHelper = new authentication();

$myusername = $_POST['myusername'];
$myusername = stripslashes($myusername);
$myusername = mysqli_real_escape_string($con,$myusername);
$userStatus = $authHelper->isUserLdapUser($myusername, $con);

if ($userStatus == 1 || $userStatus == 2) {
    $registred = validateUserAsLdapUser($con);
    if (!$registred) {

        validateUserAsSessionwebUser($con);

    } else {
        $logger->debug("user logged in as a AD user", __FILE__, __LINE__);
    }
} else {
    validateUserAsSessionwebUser($con);
}

ob_end_flush();
function validateUserAsLdapUser($con)
{

    $logger = new logging();
    if (LDAP_ENABLED) {


        $ldap = new authentication();
        $resultLdap = $ldap->getUserInfoThroughLdap($_POST['myusername'], $_POST['mypassword']);

        if (is_array($resultLdap)) {
            $myusername = mysqli_real_escape_string($con,$_POST['myusername']);

            $sql = "SELECT * FROM members WHERE username LIKE '" . $myusername . "'";

            $result = mysqli_query($con,$sql);

            if (mysqli_num_rows($result) == 1) {
                //VALID USER AND ACCOUNT EXIST IN SW
                $sql = "";
                $sql .= "SELECT * ";
                $sql .= "FROM   members ";
                $sql .= "WHERE  username = '$myusername' ";
                $sql .= "       AND active = 1 ";
                $logger->sql($sql, __FILE__, __LINE__);

                $result = mysqli_query($con,$sql);

                $row = mysqli_fetch_array($result);

                $active = $row['active'];
                $deleted = $row['deleted'];
                if (!$active || $deleted) {
                    if (!$active)
                        $logger->debug("User $myusername is marked as inactive and/or deleted in database and is not allowed to log in", __FILE__, __LINE__);
                    else
                        $logger->error("Should not end up here!!!!", __FILE__, __LINE__);
                    return false;
                } else {
                    registrateSession($result, $myusername,$con, $row);
                }
                return true;

            } else {
                //VALID USER BUT ACCOUNT DOES NOT EXIST IN SW
                $fullname = $resultLdap['name'];
                $username = $resultLdap['samaccountname'];
                $md5password = md5(rand());
                $sqlInsert = "";
                $sqlInsert .= "INSERT INTO members ";
                $sqlInsert .= "            (username, ";
                $sqlInsert .= "             password, ";
                $sqlInsert .= "             fullname, ";
                $sqlInsert .= "             adaccount, ";
                $sqlInsert .= "             active, ";
                $sqlInsert .= "             admin, ";
                $sqlInsert .= "             superuser) ";
                $sqlInsert .= "VALUES      ('$username', ";
                $sqlInsert .= "             '$md5password', ";
                $sqlInsert .= "             '$fullname', ";
                $sqlInsert .= "             '1', ";
                $sqlInsert .= "             '1', ";
                $sqlInsert .= "             '0', ";
                $sqlInsert .= "             '0')";
                $logger->sql($sqlInsert, __FILE__, __LINE__);
                $result = mysqli_query($con,$sqlInsert);
                if (!$result) {
                    $logger->error(mysqli_error($con), __FILE__, __LINE__);
                    $logger->error($sqlInsert, __FILE__, __LINE__);
                    die("AD/LDAP user $myusername not created, check log");
                }

                $sqlInsert = "";
                $sqlInsert .= "INSERT INTO user_settings ";
                $sqlInsert .= "            (username, ";
                $sqlInsert .= "             teamname, ";
                $sqlInsert .= "             default_team, ";
                $sqlInsert .= "             list_view) ";
                $sqlInsert .= "VALUES      ('$username', ";
                $sqlInsert .= "             '', ";
                $sqlInsert .= "             '', ";
                $sqlInsert .= "             'all')";
                $logger->sql($sqlInsert, __FILE__, __LINE__);
                $result = mysqli_query($con,$sqlInsert);


                if (!$result) {
                    $logger->error(mysqli_error($con), __FILE__, __LINE__);
                    $logger->error($sqlInsert, __FILE__, __LINE__);
                    die("AD/LDAP user $myusername not created");
                }

                $sql = "";
                $sql .= "SELECT * ";
                $sql .= "FROM   members ";
                $sql .= "WHERE  username = '$myusername' ";
                $sql .= "       AND active = 1 ";
                $logger->sql($sql, __FILE__, __LINE__);
                $result = mysqli_query($con,$sql);

                if (!$result) {
                    $logger->error(mysqli_error($con), __FILE__, __LINE__);
                    $logger->error($sql, __FILE__, __LINE__);
                }

                if (mysqli_num_rows($con,$result) == 1) {
                    $logger->info("AD/LDAP user $myusername created", __FILE__, __LINE__);
                } else {
                    $logger->error("AD/LDAP user $myusername not created", __FILE__, __LINE__);

                    die("AD/LDAP user $myusername not created");
                }

                registrateSession($result, $myusername,$con);
                return true;
            }

        }
    }
}

function validateUserAsSessionwebUser($con)
{
    $logger = new logging();
    $myusername = $_POST['myusername'];
    $mypassword = $_POST['mypassword'];
    $encrypted_mypassword = md5($mypassword);
    $myusername = stripslashes($myusername);
    $mypassword = stripslashes($mypassword);
    $myusername = mysqli_real_escape_string($con,$myusername);
    $mypassword = mysqli_real_escape_string($con,$mypassword);

    $mypassword = md5($mypassword);

    $sql = "";
    $sql .= "SELECT * ";
    $sql .= "FROM   members ";
    $sql .= "WHERE  username = '$myusername' ";
    $sql .= "       AND PASSWORD = '$mypassword' ";
    $sql .= "       AND active = 1 ";
    $logger->sql($sql, __FILE__, __LINE__);


    $result = mysqli_query($con,$sql);

    if ($result != FALSE) {
        $count = mysqli_num_rows($result);
    }

// If result matched $myusername and $mypassword, table row must be 1 row

    if ($count == 1) {
        registrateSession($result, $myusername,$con);
    } else {
        header("location:index.php?login=failed");
        $logger->debug("$myusername failed to log in (wrong password or non-existing user)", __FILE__, __LINE__);
    }
}

function registrateSession($result, $myusername,$con, $rowAlreadyClaimed = false)
{
    $logger = new logging();

    if (!$rowAlreadyClaimed) {
        $row = mysqli_fetch_array($result);
    } else {
        $row = $rowAlreadyClaimed;
    }

    $_SESSION['user'] = $row['fullname'];
    $_SESSION['superuser'] = $row['superuser'];
    $_SESSION['useradmin'] = $row['admin'];
    $_SESSION['username'] = $myusername;
    $_SESSION['settings'] = ApplicationSettings::getSettings();
    $_SESSION['active'] = $row['active'];
    $_SESSION['project'] = "0";

    unset($_SERVER['PHP_AUTH_USER']);

    $logger->info("User logged in", __FILE__, __LINE__);

    if (isset($_REQUEST["ref"]) && strstr($_REQUEST["ref"], "redir") != false) {
        $ref = $_REQUEST["ref"];
        $refUri = explode("?", $ref);

        if (count($refUri) > 1) {
            $purifiedUri = explode("=", $refUri[1]);
            $subUri = urldecode($purifiedUri[1]);
            $baseUri = $_SERVER["HTTP_ORIGIN"];
            $uri = $baseUri . $subUri;
            $logger->debug("Will redirect to $uri", __FILE__, __LINE__);
            header("location:$uri");

        } else {
            header("location:index.php");
        }
    } else {
        header("location:index.php");
    }
}

?>