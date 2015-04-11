<?php
require_once('classes/autoloader.php');

if (UserSettings::isAdmin()) {
    echo "<h1>Sessionweb System check</h1>";

    echo "<h2>Database</h2>";
    echo "<p>Database name: " . DB_NAME_SESSIONWEB . "</p>";



//checkFoldersForRW();
    SystemCheck::checkForMaxAttachmentSize();
    debugInfo();

    echo "<h2>PHP info</h2>";

    phpinfo();
} else {
    echo "You are not allowed to access this page";
}


function debugInfo()
{
    echo "Browser: " . $_SERVER['HTTP_USER_AGENT'] . "<br>";
    echo "PHP Memory peek usage: " . (memory_get_peak_usage() / 1024 / 1024) . "mb<br>";
}


?>