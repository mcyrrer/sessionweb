<?php
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
echo "<html>\n";
echo "  <head>\n";
echo "      <meta http-equiv=\"Content-type\" content=\"text/html;charset=latin1\">\n";
echo "      <title>Sessionweb</title>\n";

echo "      <link rel=\"stylesheet\" type=\"text/css\" href=\"css/sessionwebcss.css\">\n";

echo "     <script src=\"js/jquery-1.4.4.js\" type=\"text/javascript\"></script>\n";
echo "     <script src=\"js/jquery.autosave.js\" type=\"text/javascript\"></script>\n";
echo "     <script src=\"js/jquery.getparams.js\" type=\"text/javascript\"></script>\n";
if (strstr($_SERVER['PHP_SELF'], "session.php") != false) {
    echo "<script type=\"text/javascript\" src=\"editor/ckeditor.js\"></script>\n";
    echo "     <script type=\"text/javascript\" src=\"editor/adapters/jquery.js\"></script>\n";
}
echo "     <script type=\"text/javascript\" src=\"js/sessionwebjs.js\"></script>\n";
echo "     <script type=\"text/javascript\" src=\"js/sessionwebjquery.js\"></script>\n";
echo "              <script type=\"text/javascript\">\n";
echo "              var mySessionlinks = \"\";\n";
echo "              var url_to_dms = \"" . $_SESSION['settings']['url_to_dms'] . "\";\n";
echo "              var url_to_rms = \"" . $_SESSION['settings']['url_to_rms'] . "\";\n";

echo "              </script>\n";

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
if ($_SESSION['user'] != "") {
    echo "      [" . $_SESSION['user'] . "]";
}
echo "      <a href=\"index.php\" id=\"url_index\">Main page</a> |";
echo "      <a href=\"session.php?command=new\" id=\"url_newsession\">New session</a> |";
echo "      <a href=\"list.php\" id=\"url_list\">List sessions</a> |";
echo "      <a href=\"settings.php\" id=\"url_settings\">Settings</a> |";


if (isset($_SESSION['user'])) {
    echo "      <a href=\"index.php?logout=yes\" id=\"url_logout\">Log out</a> |";
}


echo "<p><img src=\"pictures/logo.png\" alt=\"Sessionweb logo\"/></p>\n";
?>
