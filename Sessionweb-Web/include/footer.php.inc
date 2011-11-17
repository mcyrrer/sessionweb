<?php 
echo "<br><br><br><br>\n";
echo "<img src=\"pictures/line.png\" alt=\"line\"/>\n";
echo "<br><h6>";

$con = mysql_connect(DB_HOST_SESSIONWEB, DB_USER_SESSIONWEB, DB_PASS_SESSIONWEB) or die("cannot connect");
mysql_select_db(DB_NAME_SESSIONWEB)or die("Database not available. Please install <a href='install.php'>sessionweb</a>");
$result = mysql_query("SELECT * FROM `sessionwebos`.`version`;");

$row = mysql_fetch_array($result);
$versionInstalled = $row['versioninstalled'];
mysql_close($con);

echo "Sessionweb ver $versionInstalled  | ";
echo "<a href=\"about.php\"id=\"url_about\">About</a> | ";
echo "<a href=\"http://code.google.com/p/sessionweb/\" id=\"url_google_code_sessionweb\">Project Home Page</a> | ";
echo "<a href=\"http://code.google.com/p/sessionweb/issues/list\" id=\"url_submintbug\">Submit a bug report</a></h6><br>\n";
echo "  </body>\n";
echo "</html>\n";

?>