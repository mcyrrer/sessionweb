<?php
echo "<br><div id='boldline'></div>";
$dbmF = new dbHelper();
$conF = $dbmF->connectToLocalDb();

$result = mysqli_query($conF,"SELECT * FROM `version`;");

$row = mysqli_fetch_array($result);
$versionInstalled = $row['versioninstalled'];
mysqli_close($conF);
if (file_exists("install")) {
    echo "<H3>NOTE: Installation directory exist (install/). Please delete it to prevent data lost.</H3>";
}
echo "<div id='footer'>";
echo "Sessionweb ver $versionInstalled  | ";
echo "<a href=\"about.php\"id=\"url_about\">About</a> | ";
echo "<a href=\"http://www.sessionweb.org\" id=\"url_sessionweb_prj_page\">Project Home Page</a> | ";
echo "<a href=\"http://code.google.com/p/sessionweb/issues/list\" id=\"url_submintbug\">Submit a bug report</a><br>\n";
echo "</div>";
echo "</div>";
echo "  </body>\n";
echo "</html>\n";

?>