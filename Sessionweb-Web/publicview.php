<?php
require_once('include/loggingsetup.php');
include_once('config/db.php.inc');

include_once ('include/session_view_functions.php.inc');
include_once ('include/session_database_functions.php.inc');
include_once ('include/commonFunctions.php.inc');
include_once ('include/session_common_functions.php.inc');
if (is_file("include/customfunctions.php.inc")) {
	include "include/customfunctions.php.inc";
}

//TODO: Add check that public key == key for sessionid.
//SELECT sessionid,publickey FROM `mission` where sessionid = 46;
$con = getMySqlConnection();



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

$_SESSION['settings'] = $settings;
mysql_close($con);
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
echo "<html>\n";
echo "  <head>\n";
echo "      <meta http-equiv=\"Content-type\" content=\"text/html;charset=UTF-8\">\n";
echo "      <title>Sessionweb</title>\n";
echo "  </head>\n";
echo "  <body>\n";

echo "<img src=\"pictures/logo.png\" alt=\"Sessionweb logo\">";
if($_GET['publickey']==$row['publickey'])
{

	echoViewSession();
}
else
{
	echo "Public key provided not valid for session ".$_GET['sessionid'];
}
echo "  </body>\n";
echo "</html>\n";

?>