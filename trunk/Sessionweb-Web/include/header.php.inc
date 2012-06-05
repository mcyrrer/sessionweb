<?php
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_http_input('UTF-8');
mb_language('uni');
mb_regex_encoding('UTF-8');
ob_start('mb_output_handler');

echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
echo "<html>\n";
echo "  <head>\n";
echo "      <meta http-equiv=\"Content-type\" content=\"text/html;charset=utf-8\">\n";
echo "      <title>Sessionweb</title>\n";

echo "      <link rel=\"stylesheet\" type=\"text/css\" href=\"css/sessionwebcss.css\">\n";
echo "      <link rel=\"stylesheet\" type=\"text/css\" href=\"css/colorbox.css\">\n";
echo "      <link rel=\"stylesheet\" type=\"text/css\" href=\"js/jqueryui/jquery-ui-1.8.20.custom.css\">\n";
echo "     <script src=\"js/jquery-1.7.1.js\" type=\"text/javascript\"></script>\n";
echo "     <script src=\"js/jqueryui/jquery-ui-1.8.20.custom.min.js\" type=\"text/javascript\"></script>\n";

if (strstr($_SERVER['PHP_SELF'], "list2.php") != false) {
    echo "      <link rel=\"stylesheet\" type=\"text/css\" href=\"css/flexigrid.css\">\n";
    echo "      <link rel=\"stylesheet\" type=\"text/css\" href=\"css/flexigrid-sessionweb.css\">\n";
    echo "     <script src=\"js/flexigrid.js\" type=\"text/javascript\"></script>\n";
    echo "     <script src=\"js/flexigrid-sessionweb.js\" type=\"text/javascript\"></script>\n";


}

if (strstr($_SERVER['PHP_SELF'], "settings2.php") != false) {

    echo "     <script src=\"js/sessionweb-settings-v20.js\" type=\"text/javascript\"></script>\n";
}
echo "     <script src=\"js/jquery.autosave.js\" type=\"text/javascript\"></script>\n";
echo "     <script src=\"js/jquery.validate.js\" type=\"text/javascript\"></script>\n";

echo "     <script src=\"js/jquery.getparams.js\" type=\"text/javascript\"></script>\n";
echo "     <script src=\"js/jquery.colorbox-min.js\" type=\"text/javascript\"></script>\n";
if (strstr($_SERVER['PHP_SELF'], "session.php") != false) {
    echo "     <script type=\"text/javascript\" src=\"editor/ckeditor.js\"></script>\n";
    echo "     <script type=\"text/javascript\" src=\"editor/adapters/jquery.js\"></script>\n";

}
echo "     <script type=\"text/javascript\" src=\"js/sessionwebjs-v19.js\"></script>\n";
echo "     <script type=\"text/javascript\" src=\"js/sessionwebjquery-v19.js\"></script>\n";


echo "              <script type=\"text/javascript\">\n";
echo "              var mySessionlinks = \"\";\n";
echo "              var url_to_dms = \"" . $_SESSION['settings']['url_to_dms'] . "\";\n";
echo "              var url_to_rms = \"" . $_SESSION['settings']['url_to_rms'] . "\";\n";

echo "              </script>\n";

if (isset($_SESSION['settings'])) {
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
}
echo "  </head>\n";
echo "  <body>\n";
echo "<div id='outer'>";
echo "<div id='header' " . isThisDevlopmentVersion() . ">
        <div id='headercontent'>
            <h1>Sessionweb<sup>SBTM made easy" . printDevelopmentVersionDebugText() . "</sup></h1>
        </div>
      </div>";
echo "<div id='menu'>";
if (isset($_SESSION['user'] ) && $_SESSION['user'] != "") {
    echo "      [" . $_SESSION['user'] . "]";
}
//if (doesUserHaveNotifications()) {
//    echo "<img src='pictures/star_smal.png' alt='Notifications' class='uploadajax' href='include/notification.php'>";
//}
echo "      <a href=\"index.php\" id=\"url_index\">Main page</a> |";
echo "      <a href=\"session.php?command=edit\" id=\"url_newsession\">New session</a> |";
echo "      <a href=\"list2.php\" id=\"url_list\">List sessions</a> |";
echo "      <a href='statistics.php' id=\"url_statistics\">Statistics</a> |";
echo "      <a href=\"settings2.php\" id=\"url_settings\">Settings</a> |";


if (isset($_SESSION['username'])) {
    echo "      <a href=\"index.php?logout=yes\" id=\"url_logout\">Log out</a> |";
}
echo "      <a class='counterstring' href='include/counterstring.html'>&copy;</a>";
echo "</div>";
echo "<div id='menubottom'></div>";


/**
 * Function that will print out development if config/debug.txt exist. Is used to easy spot that this is a dev
 * version of sessionweb.
 * @return string
 */
function printDevelopmentVersionDebugText()
{
    if (file_exists("config/developmentversion.txt")) {
        return " DEVELOPMENT VERSION OF SW";
    }
}

function isThisDevlopmentVersion()
{
    if (file_exists("config/developmentversion.txt")) {
        return "class='development'";
    }
}

?>