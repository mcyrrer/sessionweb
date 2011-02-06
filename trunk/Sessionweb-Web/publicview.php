<?php
include_once('config/db.php.inc');

include_once ('include/session_view_functions.php.inc');
include_once ('include/session_database_functions.php.inc');
include_once ('include/commonFunctions.php.inc');
include_once ('include/session_common_functions.php.inc');
if (is_file("include/customfunctions.php.inc")) {
	include "include/customfunctions.php.inc";
}

//TODO: Add check that public key == key for sessionid.
//SELECT sessionid,publickey FROM `sessionwebos`.`mission` where sessionid = 46;
$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB ,DB_PASS_SESSIONWEB) or die("cannot connect");
mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");


//$versionid = getSessionVersionId($_GET['sessionid']);
//$publickey = GetSessionPublicKey($versionid);

$sqlSelect = "";
$sqlSelect .= "SELECT * ";
$sqlSelect .= "FROM   mission ";
$sqlSelect .= "WHERE  sessionid = ".$_GET['sessionid'];

$resultSession = mysql_query($sqlSelect);

if(!$resultSession)
{
	echo "publicview.php: ".mysql_error()."<br/>";
}

$row =  mysql_fetch_array($resultSession);

$settings = getSessionWebSettings();
mysql_close($con);

if($_GET['publickey']==$row['publickey'])
{

	echoViewSession();
}
else
{
	echo "Public key provided not valid for session ".$_GET['sessionid'];
}

?>