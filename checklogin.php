<?php

include 'config/db.php.inc';
//echo $dbpassword;
ob_start();

$tbl_name="members"; // Table name

// Connect to server and select databse.
mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
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

$sql="SELECT * FROM $tbl_name WHERE username='$myusername' and password='$mypassword' and active=1";
$result=mysql_query($sql);

// Mysql_num_row is counting table row
$count=mysql_num_rows($result);
// If result matched $myusername and $mypassword, table row must be 1 row

if($count==1){
	// Register $myusername, $mypassword and redirect to file "login_success.php"
	session_start();
	$row = mysql_fetch_array($result);

	$_SESSION['user'] = $row['fullname'];
	$_SESSION['superuser'] = $row['superuser'];
	$_SESSION['useradmin'] = $row['admin'];
	$_SESSION['username'] = $myusername;
	
	session_register("myusername");
	session_register("mypassword");
	header("location:index.php");
}
else {
	echo "Wrong Username or Password";
	//echo "input: $mypassword<br>";
	//echo "sql: $sql";
}

ob_end_flush();

?>