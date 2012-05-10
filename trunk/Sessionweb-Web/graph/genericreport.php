<?php
session_start();
require_once('../include/validatesession.inc');
include_once('../config/db.php.inc');
include_once('../include/db.php');
include_once ('../include/commonFunctions.php.inc');
include_once ('../include/session_database_functions.php.inc');
include_once ('../include/session_common_functions.php.inc');


echo '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
          <meta http-equiv="Content-type" content="text/html;charset=utf-8">
      <title>Sessionweb</title>
           <link rel="stylesheet" type="text/css" href="../css/sprintreport.css">
           <link rel="stylesheet" type="text/css" href="../css/sessionwebcss.css">
           <link rel="stylesheet" type="text/css" href="../js/jqueryui/jquery-ui-1.8.20.custom.css">
           <script src="../js/jquery-1.7.1.js" type="text/javascript"></script>
           <script src="../js/jqueryui/jquery-ui-1.8.20.custom.min.js" type="text/javascript"></script>
           <script src="../js/sessionweb-graph-generic-v20.js" type="text/javascript"></script>
           <script type="text/javascript" src="https://www.google.com/jsapi"></script>
  </head>
<body>
<a name="top"></a>
';

if (isset($_REQUEST['target'])) {
    $con1 = getMySqlConnection();

    generateReport();
    mysql_close($con1);
}
else
{
    echo '<form method="post" action="sprintreport.php">';

    echo "<h1>Generic Report:</h1>";
    echo "<h2>What should the report output be based on</h2>";
    echo '<input type="radio" name="target" value="Application" />Application*<br />';
    echo '<input type="radio" name="target" value="Team" />Team';
    echo "<h2>Filter the result by choosing different values below:</h2>";
    echo "<div>Sprint: ";
    echoSprintSelect("", true);
    echo "</div>";
//    echo "<div>Application: ";
//    echoApplicationBasedOnAreasSelect("", false, "select_app");
//    echo "</div>";

    echo '<label for="from">From</label>';
    echo '<input type="text" id="from" name="from"/>';
    echo '<label for="to">to</label>';
    echo '<input type="text" id="to" name="to"/><br>';

    echo '<input type="submit" name="Submit" value="Generate report">';
    echo '<div>*=Applications is filtred out from areas where the paradigm "appname-areaname" is used.</div>';
}

echo '</body>
</html>';

function generateReport($sprint)
{


}


?>