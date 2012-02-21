<?php 
echo "<br><div id='boldline'></div>";

$con=getMySqlConnection();
mysql_select_db(DB_NAME_SESSIONWEB)or die("Database not available. Please install <a href='install/'>sessionweb</a>");
$result = mysql_query("SELECT * FROM `version`;");

$row = mysql_fetch_array($result);
$versionInstalled = $row['versioninstalled'];
mysql_close($con);
if (file_exists("install"))
{
    echo "<H3>NOTE: Installation directory exist (install/). Please delete it to prevent data lost.</H3>";
}
echo "<div id='footer'>";
echo "Sessionweb ver $versionInstalled  | ";
echo "<a href=\"about.php\"id=\"url_about\">About</a> | ";
echo "<a href=\"http://code.google.com/p/sessionweb/\" id=\"url_google_code_sessionweb\">Project Home Page</a> | ";
echo "<a href=\"http://code.google.com/p/sessionweb/issues/list\" id=\"url_submintbug\">Submit a bug report</a></h6><br>\n";
echo "</div>";
echo "</div>";
echo "  </body>\n";
echo "</html>\n";

?>