<?php
$dbm = new dbHelper();
$con = $dbm->connectToLocalDb();
echo "<br><div id='boldline'></div>";

if (file_exists('../config/db.php.inc')) {
    require_once ('../config/db.php.inc');


mysqli_select_db($con,DB_NAME_SESSIONWEB)or die("Database not available. Please install <a href='install/'>sessionweb</a>");
$result = $dbm->executeQuery($con,"SELECT * FROM `version`;");

$row = mysqli_fetch_array($result);
$versionInstalled = $row['versioninstalled'];

}
else
{
    $versionInstalled = ": not yet installed :)";
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