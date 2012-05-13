<?php
session_start();
require_once('../include/validatesession.inc');
include_once('../config/db.php.inc');
include_once('../include/db.php');
include_once ('../include/commonFunctions.php.inc');
include_once ('../include/session_database_functions.php.inc');
include_once ('../include/session_common_functions.php.inc');
include_once ('../include/graphcommon.inc');
include_once ('../classes/session.php');


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
    <script type="text/javascript" src="../js/highcharts/highcharts.js"></script>
    <script type="text/javascript" src="../js/highcharts/modules/exporting.js"></script>
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
    echo '<form method="post" action="genericreport.php">';

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

/**
 *
 */
function generateReport()
{

    $sql = generateSql();
    $allSessions = generateSessionObjects($sql);

    //print_r($allSessions);
    echo "<h1>Generic report</h1>";

    echo '<div class="demo">

<div id="tabs">
	<ul>
		<li><a href="#tabs-1">Summary</a></li>
		<li><a href="#tabs-2">Detailed</a></li>
		<li><a href="#tabs-3">Aenean lacinia</a></li>
	</ul>
	<div id="tabs-1">
		<p>' . generateOverviewTabContent($allSessions) . '</p>
	</div>
	<div id="tabs-2">
		<p>Morbi tincidunt, dui sit amet facilisis feugiat, odio metus gravida ante, ut pharetra massa metus id nunc. Duis scelerisque molestie turpis. Sed fringilla, massa eget luctus malesuada, metus eros molestie lectus, ut tempus eros massa ut dolor. Aenean aliquet fringilla sem. Suspendisse sed ligula in ligula suscipit aliquam. Praesent in eros vestibulum mi adipiscing adipiscing. Morbi facilisis. Curabitur ornare consequat nunc. Aenean vel metus. Ut posuere viverra nulla. Aliquam erat volutpat. Pellentesque convallis. Maecenas feugiat, tellus pellentesque pretium posuere, felis lorem euismod felis, eu ornare leo nisi vel felis. Mauris consectetur tortor et purus.</p>
	</div>
	<div id="tabs-3">
		<p>Mauris eleifend est et turpis. Duis id erat. Suspendisse potenti. Aliquam vulputate, pede vel vehicula accumsan, mi neque rutrum erat, eu congue orci lorem eget lorem. Vestibulum non ante. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Fusce sodales. Quisque eu urna vel enim commodo pellentesque. Praesent eu risus hendrerit ligula tempus pretium. Curabitur lorem enim, pretium nec, feugiat nec, luctus a, lacus.</p>
		<p>Duis cursus. Maecenas ligula eros, blandit nec, pharetra at, semper at, magna. Nullam ac lacus. Nulla facilisi. Praesent viverra justo vitae neque. Praesent blandit adipiscing velit. Suspendisse potenti. Donec mattis, pede vel pharetra blandit, magna ligula faucibus eros, id euismod lacus dolor eget odio. Nam scelerisque. Donec non libero sed nulla mattis commodo. Ut sagittis. Donec nisi lectus, feugiat porttitor, tempor ac, tempor vitae, pede. Aenean vehicula velit eu tellus interdum rutrum. Maecenas commodo. Pellentesque nec elit. Fusce in lacus. Vivamus a libero vitae lectus hendrerit hendrerit.</p>
	</div>
</div>

</div><!-- End demo -->
';
}

function generateSessionObjects($sql)
{

    $allSessions = array();
    $result = mysql_query($sql);

    while ($row = mysql_fetch_array($result))
    {
        $aSessionObject = new session($row['sessionid']);
        $allSessions[$row['sessionid']] = $aSessionObject->getSession();
    }
    return $allSessions;
}

function generateSql()
{
    $sql = "SELECT sessionid FROM mission ";

    return $sql;
}

function generateOverviewTabContent($allSessions)
{
    $htmlString = "<table border='0' width='100%'>";
    $htmlString .= "<tr>";
    $htmlString .= "<td width=50%>";

    $htmlString .= "Number of sessions in report: " . count($allSessions);
    $htmlString .= "</td>";
    $htmlString .= "<td>";

    $htmlString .= getPieCharTimeDistribution($allSessions, "timeDistcontainer");
    $htmlString .= '<div id="timeDistcontainer"></div>';

    $htmlString .= "</td>";
    $htmlString .= "</tr>";

    $htmlString .= "</table>";
    return $htmlString;
}

?>