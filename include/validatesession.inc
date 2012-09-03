<?php
$rootPath = checkIfRootFolder("");
require_once ("$rootPath/include/db.php");
require_once("$rootPath/config/db.php.inc");
require_once ("$rootPath/include/apistatuscodes.inc");


if (strstr($_SERVER['PHP_SELF'], "api/") == false) {
    if (!isset($_SESSION['username'])) {
        $rootPath = checkIfRootFolder("");

        header("Location:" . $rootPath . "index.php");
        echo "Location:" . $rootPath . "index.php";
        echo "You are not logged in!";
        die();
    }
}
else
{
    if (isset($_SERVER['PHP_AUTH_USER'])) {
        if (!checkIfValidUserAndPasswordWithHttpBasicAuth()) {
            header('WWW-Authenticate: Basic realm="Sessionweb"');
            header("HTTP/1.0 401 Unauthorized");
            echo "Not a valid user/password";
            die;
        }
    }
    else
        if (!isset($_SESSION['username'])) {
            header("HTTP/1.0 401 Unauthorized");
            $response['code'] = UNAUTHORIZED;
            $response['text'] = "UNAUTHORIZED";
            echo json_encode($response);
            die();
        }
}

function checkIfRootFolder($pathToRoot)
{
    if (file_exists($pathToRoot . 'about.php')) {
        //echo "Found root at " . $pathToRoot . "about.php<br>";
        return "./".$pathToRoot;
    }
    else
    {
        //echo "Not Root<br>";
        $pathToRoot .= "../";
        $pathToRoot = checkIfRootFolder($pathToRoot);
    }
    return $pathToRoot;
}

/***
 * Check if a user/password if they where submitted through Http Basic Auth
 * @return bool
 */
function checkIfValidUserAndPasswordWithHttpBasicAuth()
{
    $myusername = $_SERVER['PHP_AUTH_USER'];
    $mypassword = $_SERVER['PHP_AUTH_PW'];
    $con = getMySqlConnection();
    //encrypt password
    $mypassword = md5($mypassword);

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
    // Mysql_num_row is counting table row


    // If result matched $myusername and $mypassword, table row must be 1 row

    if ($count == 1) {
        return true;
    }
   else
   {
       return false;
   }
}

?>