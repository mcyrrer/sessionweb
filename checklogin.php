<?php

include 'config/db.php.inc';

ob_start();

// Connect to server and select databse.
$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

// Define $myusername and $mypassword
$myusername=urlencode($_POST['myusername']);
$mypassword=urlencode($_POST['mypassword']);
// encrypt password
$encrypted_mypassword=md5($mypassword);

// To protect MySQL injection (more detail about MySQL injection)
$myusername = stripslashes($myusername);
$mypassword = stripslashes($mypassword);
$myusername = mysql_real_escape_string($myusername);
$mypassword = mysql_real_escape_string($mypassword);

//encrypt password
$mypassword = md5($mypassword);

$sql = "";
$sql .= "SELECT * ";
$sql .= "FROM   members ";
$sql .= "WHERE  username = '$myusername' ";
$sql .= "       AND PASSWORD = '$mypassword' ";
$sql .= "       AND active = 1 " ;

$result=mysql_query($sql);

if($result!=FALSE)
{
	$count=mysql_num_rows($result);

}
// Mysql_num_row is counting table row



// If result matched $myusername and $mypassword, table row must be 1 row

if($count==1){
	// Register $myusername, $mypassword and redirect to file "index.php"
	session_start();
	$row = mysql_fetch_array($result);

	$_SESSION['user'] = $row['fullname'];
	$_SESSION['superuser'] = $row['superuser'];
	$_SESSION['useradmin'] = $row['admin'];
	$_SESSION['username'] = $myusername;
	
	$_SESSION['settings'] = getSessionWebSettings();

	session_register("myusername");
	session_register("mypassword");
	header("location:index.php");
}
else {
	echo "Wrong Username or Password";
}

mysql_close($con);
ob_end_flush();

function getSessionWebSettings()
{
    $sqlSelect = "";
    $sqlSelect .= "SELECT * ";
    $sqlSelect .= "FROM   settings ";

    $result = mysql_query($sqlSelect);

    if(!$result)
    {
        echo "getSessionWebSettings: ".mysql_error()."<br>";
    }
    else
    {
        $row = mysql_fetch_array($result);
        return $row;
    }
}

?>