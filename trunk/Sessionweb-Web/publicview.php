<?php
require_once('include/loggingsetup.php');
include_once('config/db.php.inc');
include_once ('include/db.php');
include_once ('include/session_view_functions.php.inc');
include_once ('include/session_database_functions.php.inc');
include_once ('include/commonFunctions.php.inc');
include_once ('include/session_common_functions.php.inc');
if (is_file("include/customfunctions.php.inc")) {
    include "include/customfunctions.php.inc";
}

$con = getMySqlConnection();

$settings = getSessionWebSettings();

//TODO: Add check that public key == key for sessionid.
//SELECT sessionid,publickey FROM `mission` where sessionid = 46;


//$versionid = getSessionVersionId($_GET['sessionid']);
//$publickey = GetSessionPublicKey($versionid);

$sqlSelect = "";
$sqlSelect .= "SELECT * ";
$sqlSelect .= "FROM   mission ";
$sqlSelect .= "WHERE  sessionid = " . $_GET['sessionid'];

$resultSession = mysql_query($sqlSelect);

if (!$resultSession) {
    echo "publicview.php: " . mysql_error() . "<br/>";
}

$row = mysql_fetch_array($resultSession);


$_SESSION['settings'] = $settings;
mysql_close($con);
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
echo "<html>\n";
echo "  <head>\n";
echo "      <meta http-equiv=\"Content-type\" content=\"text/html;charset=utf-8\">\n";
echo "      <title>Sessionweb</title>\n";

echo "      <link rel=\"stylesheet\" type=\"text/css\" href=\"css/sessionwebcss.css\">\n";
echo "      <link rel=\"stylesheet\" type=\"text/css\" href=\"css/colorbox.css\">\n";
echo "     <script src=\"js/jquery-1.7.1.js\" type=\"text/javascript\"></script>\n";
echo "     <script src=\"js/jquery.getparams.js\" type=\"text/javascript\"></script>\n";
echo "     <script src=\"js/jquery.colorbox-min.js\" type=\"text/javascript\"></script>\n";
echo "     <script type=\"text/javascript\" src=\"js/sessionwebjs.js\"></script>\n";
echo "     <script type=\"text/javascript\" src=\"js/sessionwebjquery.js\"></script>\n";

if ($_SESSION['settings']['analyticsid'] != "") {
    echo " <script type=\"text/javascript\">\n";

    echo "   var _gaq = _gaq || [];\n";
    echo "   _gaq.push(['_setAccount', '" . $_SESSION['settings']['analyticsid'] . "']);\n";
    echo "   _gaq.push(['_trackPageview']);\n";

    echo "   (function() {\n";
    echo "     var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;\n";
    echo "     ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';\n";
    echo "     var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);\n";
    echo "   })();\n";

    echo " </script>\n";
}
echo "  </head>\n";
echo "  <body>\n";
echo "<div id='outer'>";
echo "<div id='header'>
        <div id='headercontent'>
            <h1>Sessionweb<sup>SBTM made easy</sup></h1>
        </div>
      </div>";
echo "<div id='menu'></div>";


if ($settings['publicview']==1) {

    if ($_GET['publickey'] == $row['publickey']) {

        echoViewSession();
    }
    else
    {
        echo "Public key provided not valid for session " . $_GET['sessionid'];
    }
}
else
{
    echo "Public view is not activated in settings.";
}
echo "  </body>\n";
echo "</html>\n";

?>