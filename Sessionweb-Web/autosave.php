<?php
require_once('include/loggingsetup.php');
session_start();
if (!session_is_registered(myusername)) {
    header("location:index.php");
}
include_once('config/db.php.inc');
include_once ('include/session_edit_functions.php.inc');
include_once ('include/session_view_functions.php.inc');
include_once ('include/session_database_functions.php.inc');
include_once ('include/commonFunctions.php.inc');
include_once ('include/session_common_functions.php.inc');


$conDeleteSession = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("cannot connect");
mysql_select_db(DB_NAME_SESSIONWEB)or die("cannot select DB");

$sessionid = $_REQUEST["sessionid"];
$versionid = getSessionVersionId($sessionid);

autosave_UpdateSession($sessionid);

//saveSession_UpdateSessionDataToDb($sessionid);

//saveSession_UpdateSessionStatusToDb($versionid);


//saveSession_UpdateSessionMetricsToDb($versionid);

//saveSession_UpdateSessionAreasToDb($versionid, $_REQUEST["area"]);

//saveSession_UpdateSessionBugsToDb($versionid);
//saveSession_UpdateSessionRequirementsToDb($versionid);
//saveSession_UpdateSessionLinkedToDb($versionid);

mysql_close($conDeleteSession);

echo "Autosaved: " . date('H:i:s');

function autosave_UpdateSession($sessionid) {

    if ($_REQUEST["title"] == "") {
        $_REQUEST["title"] = "Unnamed Session";
        echo "<b>Warning:</b> Session has no title, it will be named \"Unnamed Session\"<br/>\n";
    }

    $sqlUpdate = "";
    $sqlUpdate .= "UPDATE mission ";
    $sqlUpdate .= "SET    `title` = '" . mysql_real_escape_string($_REQUEST["title"]) . "', ";
    $sqlUpdate .= "       `charter` = '" . mysql_real_escape_string($_REQUEST["charter"]) . "', ";
    $sqlUpdate .= "       `notes` = '" . mysql_real_escape_string($_REQUEST["notes"]) . "', ";
    $sqlUpdate .= "       `username` = '" . $_SESSION['username'] . "' ";

    $sqlUpdate .= "WHERE sessionid='$sessionid'";

   // echo $sqlUpdate."<br>";

    $result = mysql_query($sqlUpdate);

    if (!$result) {
        echo "saveSession_UpdateSessionDataToDb: " . mysql_error() . "<br/>";
    }
}

?>
