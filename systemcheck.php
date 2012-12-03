<?php
require_once('include/loggingsetup.php');
session_start();
require_once('include/validatesession.inc');
require_once('include/commonFunctions.php.inc');

include_once('config/db.php.inc');

echo "<h1>Sessionweb System check</h1>";
$con = getMySqlConnection();

checkFoldersForRW();
checkForMaxAttachmentSize();
debugInfo();
mysql_close();
echo "<h2>PHP info</h2>";

phpinfo();


function debugInfo()
{
    echo "Browser: " . $_SERVER['HTTP_USER_AGENT'] . "<br>";
    echo "PHP Memory peek usage: " . (memory_get_peak_usage() / 1024 / 1024) . "mb<br>";
}
?>